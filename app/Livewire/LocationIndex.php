<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Location;
use App\Models\LocationCategory;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

final class LocationIndex extends Component
{
    #[Url]
    public ?string $category = null;

    public function render(): View
    {
        $categories = LocationCategory::query()
            ->has('locations')
            ->withCount('locations')
            ->orderBy('name')
            ->get();

        $locationsQuery = Location::query()
            ->whereStatus(true)
            ->with('categories');

        if ($this->category !== null && $this->category !== '' && $this->category !== '0') {
            $locationsQuery->whereHas('categories', function ($query): void {
                $query->where('location_categories.slug', $this->category);
            });
        }

        return view('livewire.location-index', [
            'categories' => $categories,
            'locations' => $locationsQuery->get(),
        ]);
    }

    public function filterByCategory(?string $categorySlug): void
    {
        $this->category = $categorySlug;
    }
}
