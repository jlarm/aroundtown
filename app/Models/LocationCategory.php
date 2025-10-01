<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class LocationCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }
}
