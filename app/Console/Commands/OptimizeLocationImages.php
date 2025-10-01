<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

final class OptimizeLocationImages extends Command
{
    protected $signature = 'location:optimize-images';

    protected $description = 'Convert existing location images to optimized WebP format';

    public function handle(): void
    {
        $locations = Location::whereNotNull('image_path')->get();

        if ($locations->isEmpty()) {
            $this->comment('No locations with images found.');

            return;
        }

        $this->info("Processing {$locations->count()} location images...");

        $processed = 0;

        foreach ($locations as $location) {
            $this->info("Processing location: {$location->name}");

            $newPath = $this->optimizeImage($location->image_path);

            if ($newPath !== $location->image_path) {
                $location->update(['image_path' => $newPath]);
                $processed++;
            }
        }

        $this->comment("Processed {$processed} images successfully.");
    }

    private function optimizeImage(string $path): string
    {
        $fullPath = Storage::disk('public')->path($path);

        if (! file_exists($fullPath)) {
            $this->warn("File not found: {$path}");

            return $path;
        }

        $pathInfo = pathinfo($path);
        $extension = mb_strtolower($pathInfo['extension'] ?? '');

        $manager = new ImageManager(new Driver);
        $image = $manager->read($fullPath);

        $image->scaleDown(width: 1920, height: 1280);

        $oldSize = filesize($fullPath) ?: 0;

        if ($extension !== 'jpg' && $extension !== 'jpeg') {
            $jpgPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'.jpg';
            $jpgFullPath = Storage::disk('public')->path($jpgPath);
            $image->toJpeg(quality: 90)->save($jpgFullPath);

            Storage::disk('public')->delete($path);

            $this->line("Converted {$extension} to JPG");

            return $jpgPath;
        }
        $image->toJpeg(quality: 90)->save($fullPath);

        $webpPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'.webp';
        $webpFullPath = Storage::disk('public')->path($webpPath);
        $image->toWebp(quality: 80)->save($webpFullPath);

        $webpSize = filesize($webpFullPath) ?: 0;
        $savings = $oldSize > 0 ? round((($oldSize - $webpSize) / $oldSize) * 100) : 0;

        $this->line("Generated WebP: {$savings}% smaller ({$this->formatBytes($oldSize)} → {$this->formatBytes($webpSize)})");

        return $path;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes > 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
