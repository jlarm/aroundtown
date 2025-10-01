<?php

declare(strict_types=1);

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');
Route::get('/location/{location:slug}', [LocationController::class, 'show'])->name('location.show');
