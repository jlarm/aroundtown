<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    protected function casts(): array
    {
        return [
            'state' => State::class,
            'status' => 'boolean',
        ];
    }
}
