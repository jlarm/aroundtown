<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

final class LocationObserver
{
    public function saved(Location $location): void
    {
        if ($location->wasChanged('image_path') && $location->image_path) {
            dispatch(function () use ($location): void {
                $this->generateWebpVersion($location->image_path);
            })->afterResponse();
        }
    }

    public function deleted(Location $location): void
    {
        if ($location->image_path) {
            Storage::disk('public')->delete($location->image_path);

            $webpPath = $this->getWebpPath($location->image_path);
            if (Storage::disk('public')->exists($webpPath)) {
                Storage::disk('public')->delete($webpPath);
            }
        }
    }

    private function generateWebpVersion(string $path): void
    {
        $fullPath = Storage::disk('public')->path($path);

        if (! file_exists($fullPath)) {
            return;
        }

        $pathInfo = pathinfo($path);
        $extension = mb_strtolower($pathInfo['extension'] ?? '');

        if ($extension === 'webp') {
            return;
        }

        $manager = new ImageManager(new Driver);
        $image = $manager->read($fullPath);

        $image->scaleDown(width: 1920, height: 1280);

        if ($extension !== 'jpg' && $extension !== 'jpeg') {
            $jpgPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'.jpg';
            $jpgFullPath = Storage::disk('public')->path($jpgPath);
            $image->toJpeg(quality: 90)->save($jpgFullPath);

            Storage::disk('public')->delete($path);
        } else {
            $image->toJpeg(quality: 90)->save($fullPath);
        }

        $webpPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'.webp';
        $webpFullPath = Storage::disk('public')->path($webpPath);
        $image->toWebp(quality: 80)->save($webpFullPath);
    }

    private function getWebpPath(string $path): string
    {
        $pathInfo = pathinfo($path);

        return $pathInfo['dirname'].'/'.$pathInfo['filename'].'.webp';
    }
}
