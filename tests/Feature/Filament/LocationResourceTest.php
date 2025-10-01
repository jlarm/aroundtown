<?php

declare(strict_types=1);

use App\Enums\State;
use App\Filament\Resources\LocationResource\Pages\CreateLocation;
use App\Filament\Resources\LocationResource\Pages\EditLocation;
use App\Models\Location;
use App\Models\LocationCategory;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('can create location', function () {
    $categories = LocationCategory::factory()->count(2)->create();

    Livewire::test(CreateLocation::class)
        ->set('data.name', 'Test Location')
        ->set('data.slug', 'test-location')
        ->set('data.short_description', 'A great place')
        ->set('data.description', 'This is a detailed description of the location.')
        ->set('data.address', '123 Main St')
        ->set('data.city', 'Springfield')
        ->set('data.state', State::ILLINOIS->value)
        ->set('data.phone', '555-123-4567')
        ->set('data.url', 'https://example.com')
        ->set('data.menu_url', 'https://example.com/menu')
        ->set('data.directions_url', 'https://example.com/directions')
        ->set('data.status', true)
        ->set('data.categories', $categories->pluck('id')->toArray())
        ->call('create')
        ->assertHasNoErrors();

    $location = Location::where('slug', 'test-location')->first();

    expect($location)->not->toBeNull()
        ->and($location->name)->toBe('Test Location')
        ->and($location->city)->toBe('Springfield')
        ->and($location->state)->toBe(State::ILLINOIS)
        ->and($location->status)->toBeTrue()
        ->and($location->categories)->toHaveCount(2);
});

test('can update location', function () {
    $location = Location::factory()->create([
        'name' => 'Old Name',
        'slug' => 'old-name',
    ]);

    $categories = LocationCategory::factory()->count(3)->create();

    Livewire::test(EditLocation::class, ['record' => $location->getRouteKey()])
        ->set('data.name', 'Updated Name')
        ->set('data.slug', 'updated-name')
        ->set('data.city', 'New City')
        ->set('data.state', State::MICHIGAN->value)
        ->set('data.categories', $categories->pluck('id')->toArray())
        ->call('save')
        ->assertHasNoErrors();

    $location->refresh();

    expect($location->name)->toBe('Updated Name')
        ->and($location->slug)->toBe('updated-name')
        ->and($location->city)->toBe('New City')
        ->and($location->state)->toBe(State::MICHIGAN)
        ->and($location->categories)->toHaveCount(3);
});

test('can attach categories to location', function () {
    $location = Location::factory()->create();
    $categories = LocationCategory::factory()->count(2)->create();

    $location->categories()->attach($categories);

    expect($location->refresh()->categories)->toHaveCount(2);
});

test('requires name when creating location', function () {
    Livewire::test(CreateLocation::class)
        ->set('data.name', '')
        ->set('data.slug', 'test-location')
        ->call('create')
        ->assertHasErrors(['data.name' => 'required']);
});

test('requires slug when creating location', function () {
    Livewire::test(CreateLocation::class)
        ->set('data.name', 'Test Location')
        ->set('data.slug', '')
        ->call('create')
        ->assertHasErrors(['data.slug' => 'required']);
});

test('slug must be unique when creating location', function () {
    Location::factory()->create(['slug' => 'duplicate-slug']);

    Livewire::test(CreateLocation::class)
        ->set('data.name', 'Test Location')
        ->set('data.slug', 'duplicate-slug')
        ->call('create')
        ->assertHasErrors(['data.slug' => 'unique']);
});

test('can update location categories', function () {
    $location = Location::factory()->create();
    $initialCategories = LocationCategory::factory()->count(2)->create();
    $location->categories()->attach($initialCategories);

    $newCategories = LocationCategory::factory()->count(3)->create();

    Livewire::test(EditLocation::class, ['record' => $location->getRouteKey()])
        ->set('data.categories', $newCategories->pluck('id')->toArray())
        ->call('save')
        ->assertHasNoErrors();

    expect($location->refresh()->categories)->toHaveCount(3)
        ->and($location->categories->pluck('id')->toArray())
        ->toBe($newCategories->pluck('id')->toArray());
});
