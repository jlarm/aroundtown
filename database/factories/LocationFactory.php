<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
final class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => str($name)->slug(),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->randomElement(State::cases()),
            'zip' => fake()->postcode(),
            'phone' => fake()->phoneNumber(),
            'url' => fake()->url(),
            'menu_url' => fake()->url(),
            'directions_url' => fake()->url(),
            'image_path' => null,
            'status' => fake()->boolean(80),
        ];
    }
}
