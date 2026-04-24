<?php

namespace Database\Factories;

use App\Models\PropertyPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyPhoto>
 */
class PropertyPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => fake()->randomElement(['Patio', 'Salon', 'Suite', 'Terrasse']),
            'image_url' => fake()->imageUrl(1400, 900, 'travel', true),
            'position' => fake()->numberBetween(1, 6),
        ];
    }
}
