<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

final class LocationController extends Controller
{
    public function show(Location $location): View|Factory
    {
        return view('location.show', [
            'location' => $location,
        ]);
    }
}
