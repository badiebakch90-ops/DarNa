<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Notifications\NewReservationReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class HostingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_host_pages(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/hosting')
            ->assertOk()
            ->assertSee('Espace hote', false);

        $this->actingAs($user)
            ->get('/hosting/listings/create')
            ->assertOk()
            ->assertSee('Publier un bien', false);
    }

    public function test_guest_cannot_access_host_pages(): void
    {
        $this->get('/hosting')->assertRedirect('/login');
        $this->get('/hosting/listings/create')->assertRedirect('/login');
        $this->post('/hosting/listings', [])->assertRedirect('/login');
    }

    public function test_authenticated_user_can_publish_a_property(): void
    {
        Storage::fake('property_uploads');

        $user = User::factory()->admin()->create();
        $coverPhoto = $this->fakeImageUpload('cover-photo.png');
        $galleryOne = $this->fakeImageUpload('gallery-one.png');
        $galleryTwo = $this->fakeImageUpload('gallery-two.png');

        $this->actingAs($user)
            ->post('/hosting/listings', [
                'type' => 'Villa',
                'eyebrow' => 'Sejour oceanique',
                'name' => 'Villa Horizon Atlantique',
                'city' => 'Casablanca',
                'location' => 'Casablanca, Ain Diab',
                'summary' => 'Une villa lumineuse face a l ocean.',
                'description' => 'Un logement spacieux pour une famille ou un petit groupe.',
                'story' => 'Une adresse pensee pour les week-ends au bord de l eau.',
                'nightly_rate' => 1800,
                'max_guests' => 6,
                'bedrooms_count' => 3,
                'bathrooms_count' => 2,
                'cover_photo' => $coverPhoto,
                'gallery_photos' => [$galleryOne, $galleryTwo],
                'thumbnail_image' => 'https://example.com/villa-main-fallback.jpg',
                'gallery_image_2' => 'https://example.com/villa-2.jpg',
                'amenities_text' => "Wi-Fi\nPiscine",
                'local_spots_text' => "Plage a 5 min\nRestaurants",
                'listing_tags_text' => 'Vue mer, Family',
                'map_label' => 'Proche de la corniche',
                'map_lat' => '33.5983000',
                'map_lng' => '-7.6811000',
            ])
            ->assertRedirect('/hosting')
            ->assertSessionHas('status');

        $property = Property::query()->first();

        $this->assertNotNull($property);
        $this->assertSame($user->id, $property->owner_id);
        $this->assertSame('Villa Horizon Atlantique', $property->name);
        $this->assertSame('villa-horizon-atlantique', $property->slug);
        $this->assertSame(['Wi-Fi', 'Piscine'], $property->amenities);
        $this->assertStringStartsWith('/uploads/properties/', $property->thumbnail_image);
        $this->assertCount(4, $property->photos);
        Storage::disk('property_uploads')->assertExists(Str::after($property->thumbnail_image, '/uploads/'));
    }

    public function test_reservation_sends_notification_to_property_owner_and_admin(): void
    {
        Notification::fake();

        $owner = User::factory()->admin()->create([
            'email' => 'owner@darna.test',
        ]);
        $admin = User::factory()->admin()->create([
            'email' => 'admin@darna.test',
        ]);

        Property::factory()->create([
            'owner_id' => $owner->id,
            'slug' => 'maison-ocean',
            'name' => 'Maison Ocean',
            'location' => 'Rabat, Ocean',
            'nightly_rate' => 900,
        ]);

        $this->postJson('/api/reservations', [
            'property_slug' => 'maison-ocean',
            'arrival_date' => '2026-08-10',
            'departure_date' => '2026-08-13',
            'adults_count' => 2,
            'children_count' => 0,
            'first_name' => 'Leila',
            'last_name' => 'Amrani',
            'email' => 'leila@example.com',
            'phone' => '+212600000400',
            'notes' => 'Merci de confirmer rapidement.',
        ])->assertCreated();

        Notification::assertSentTo($owner, NewReservationReceivedNotification::class);
        Notification::assertSentTo($admin, NewReservationReceivedNotification::class);
    }

    public function test_non_admin_user_cannot_access_host_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/hosting')
            ->assertRedirect('/')
            ->assertSessionHas('status', 'Acces reserve aux administrateurs.');

        $this->actingAs($user)
            ->get('/hosting/listings/create')
            ->assertRedirect('/')
            ->assertSessionHas('status', 'Acces reserve aux administrateurs.');

        $this->actingAs($user)
            ->post('/hosting/listings', [
                'type' => 'Villa',
                'name' => 'Bien non autorise',
                'location' => 'Casablanca',
                'summary' => 'Test',
                'description' => 'Test description',
                'nightly_rate' => 1000,
                'max_guests' => 4,
            ])
            ->assertRedirect('/')
            ->assertSessionHas('status', 'Acces reserve aux administrateurs.');
    }

    private function fakeImageUpload(string $filename): UploadedFile
    {
        $directory = storage_path('framework/testing');

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $path = $directory.'/'.Str::uuid().'-'.$filename;
        file_put_contents(
            $path,
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wn5s3sAAAAASUVORK5CYII='
            )
        );

        return new UploadedFile($path, $filename, 'image/png', null, true);
    }
}
