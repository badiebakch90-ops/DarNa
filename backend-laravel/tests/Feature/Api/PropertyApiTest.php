<?php

namespace Tests\Feature\Api;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_properties_endpoint_filters_by_type_destination_and_guests(): void
    {
        Property::factory()->create([
            'slug' => 'villa-cap-spartel',
            'type' => 'Villa',
            'name' => 'Villa Cap Spartel',
            'city' => 'Tanger',
            'location' => 'Tanger, Montagne',
            'max_guests' => 8,
        ]);

        Property::factory()->create([
            'slug' => 'riad-al-baraka',
            'type' => 'Riad',
            'name' => 'Riad Al Baraka',
            'city' => 'Marrakech',
            'location' => 'Marrakech, Medina',
            'max_guests' => 4,
        ]);

        $response = $this->getJson('/api/properties?type=villa&destination=Tanger&guests=6');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'villa-cap-spartel');
    }

    public function test_property_details_endpoint_returns_the_requested_property(): void
    {
        $property = Property::factory()->create([
            'slug' => 'camp-merzouga-etoiles',
            'type' => 'Camp',
            'name' => 'Camp Merzouga Etoiles',
            'city' => 'Merzouga',
            'location' => 'Merzouga, Sahara',
        ]);

        $property->photos()->create([
            'label' => 'Dunes',
            'image_url' => 'https://example.com/camp.jpg',
            'position' => 1,
        ]);

        $response = $this->getJson('/api/properties/camp-merzouga-etoiles');

        $response
            ->assertOk()
            ->assertJsonPath('data.slug', 'camp-merzouga-etoiles')
            ->assertJsonPath('data.name', 'Camp Merzouga Etoiles')
            ->assertJsonPath('data.photos.0.label', 'Dunes');
    }

    public function test_properties_endpoint_searches_tags_amenities_and_normalized_text(): void
    {
        Property::factory()->create([
            'slug' => 'atlas-loft',
            'type' => 'Appartement',
            'name' => 'Atlas Loft',
            'city' => 'Casablanca',
            'location' => 'Casablanca, Centre',
            'summary' => 'Une adresse urbaine claire et confortable.',
            'description' => 'Un appartement pense pour les courts sejours.',
            'story' => 'Ideal pour rester mobile en ville.',
            'amenities' => ['Wi-Fi rapide', 'Parking prive'],
            'listing_tags' => ['Rooftop', 'City stay'],
        ]);

        Property::factory()->create([
            'slug' => 'desert-hideaway',
            'type' => 'Camp',
            'name' => 'Desert Hideaway',
            'city' => 'Merzouga',
            'location' => 'Merzouga, Sahara',
            'summary' => 'Un camp calme au bord des dunes.',
            'description' => 'Une experience immersive dans le desert.',
            'story' => 'Soirees sous les etoiles.',
            'amenities' => ['Diner sous les etoiles'],
            'listing_tags' => ['Dunes'],
        ]);

        $response = $this->getJson('/api/properties?query=wifi');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'atlas-loft');
    }
}
