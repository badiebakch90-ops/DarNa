<?php

namespace Tests\Feature\Api;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_endpoint_returns_stats_spotlight_and_categories(): void
    {
        $featured = Property::factory()->create([
            'slug' => 'riad-al-baraka',
            'type' => 'Riad',
            'name' => 'Riad Al Baraka',
            'city' => 'Marrakech',
            'location' => 'Marrakech, Medina',
            'featured' => true,
        ]);

        $featured->photos()->create([
            'label' => 'Patio',
            'image_url' => 'https://example.com/riad.jpg',
            'position' => 1,
        ]);

        $villa = Property::factory()->create([
            'slug' => 'villa-oasis-bleue',
            'type' => 'Villa',
            'name' => 'Villa Oasis Bleue',
            'city' => 'Agadir',
            'location' => 'Agadir, Bord de mer',
        ]);

        $villa->photos()->create([
            'label' => 'Terrasse',
            'image_url' => 'https://example.com/villa.jpg',
            'position' => 1,
        ]);

        $response = $this->getJson('/api/home');

        $response
            ->assertOk()
            ->assertJsonPath('data.stats.properties_count', 2)
            ->assertJsonPath('data.stats.cities_count', 2)
            ->assertJsonPath('data.spotlight.property.slug', 'riad-al-baraka')
            ->assertJsonPath('data.collections.0.slug', 'riad-al-baraka');

        $categories = collect($response->json('data.categories'))->pluck('count', 'type');

        $this->assertSame(1, $categories->get('riad'));
        $this->assertSame(1, $categories->get('villa'));
    }
}
