<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => null,
            'slug' => fake()->unique()->slug(3),
            'type' => fake()->randomElement(['Riad', 'Villa', 'Appartement', 'Maison']),
            'eyebrow' => fake()->sentence(3),
            'name' => fake()->company().' Residence',
            'city' => fake()->city(),
            'location' => fake()->city().', Morocco',
            'summary' => fake()->sentence(16),
            'description' => fake()->paragraph(2),
            'story' => fake()->paragraph(3),
            'nightly_rate' => fake()->numberBetween(500, 2600),
            'rating' => fake()->randomFloat(2, 4.2, 5),
            'reviews_count' => fake()->numberBetween(12, 280),
            'max_guests' => fake()->numberBetween(2, 12),
            'bedrooms_count' => fake()->numberBetween(1, 6),
            'bathrooms_count' => fake()->numberBetween(1, 5),
            'thumbnail_image' => fake()->imageUrl(1400, 900, 'travel', true),
            'type_badge_color' => 'rgba(196,98,45,0.85)',
            'gradient' => 'linear-gradient(135deg, rgba(196, 98, 45, 0.9), rgba(97, 42, 18, 0.92))',
            'facts' => [
                ['Voyageurs', fake()->numberBetween(2, 12).' personnes'],
                ['Chambres', fake()->numberBetween(1, 6).' chambres'],
                ['Salles de bain', fake()->numberBetween(1, 5).' bains'],
            ],
            'amenities' => ['Wi-Fi', 'Cuisine equipee', 'Climatisation'],
            'local_spots' => [fake()->sentence(), fake()->sentence()],
            'listing_tags' => ['Patio', 'Piscine', fake()->numberBetween(2, 12).' pers.'],
            'map_label' => fake()->sentence(),
            'map_lat' => fake()->latitude(27, 36),
            'map_lng' => fake()->longitude(-13, -1),
            'featured' => false,
        ];
    }
}
