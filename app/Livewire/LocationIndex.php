<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Location;
use Illuminate\View\View;
use Livewire\Component;

final class LocationIndex extends Component
{
    public function render(): View
    {
        return view('livewire.location-index', [
            'locations' => Location::query()
                ->with('categories')
                ->get(),
        ]);
    }
}
