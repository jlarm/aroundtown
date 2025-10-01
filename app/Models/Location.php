<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\State;
use App\Observers\LocationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(LocationObserver::class)]
final class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'url',
        'menu_url',
        'directions_url',
        'image_path',
        'status',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(LocationCategory::class);
    }

    public function getWebpImagePathAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        $pathInfo = pathinfo($this->image_path);

        return $pathInfo['dirname'].'/'.$pathInfo['filename'].'.webp';
    }

    protected function casts(): array
    {
        return [
            'state' => State::class,
            'status' => 'boolean',
        ];
    }
}
