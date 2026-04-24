<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_includes_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Permissions-Policy', 'camera=(), geolocation=(), microphone=()')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeader('Cross-Origin-Resource-Policy', 'same-origin');
    }

    public function test_http_requests_redirect_to_https_when_forced(): void
    {
        config()->set('security.force_https', true);

        $response = $this->get('/');

        $response->assertStatus(301);
        $this->assertStringStartsWith('https://', (string) $response->headers->get('Location'));
    }

    public function test_secure_requests_receive_hsts_header(): void
    {
        config()->set('security.force_https', false);

        $this->call('GET', 'https://localhost/')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_reservation_creation_is_rate_limited(): void
    {
        Property::factory()->create([
            'slug' => 'riad-rate-limit',
            'name' => 'Riad Rate Limit',
            'nightly_rate' => 500,
        ]);

        $payload = [
            'property_slug' => 'riad-rate-limit',
            'arrival_date' => '2026-07-10',
            'departure_date' => '2026-07-12',
            'adults_count' => 2,
            'children_count' => 0,
            'first_name' => 'Rate',
            'last_name' => 'Limit',
            'email' => 'rate@example.com',
            'phone' => '+212600000100',
        ];

        foreach (range(1, 8) as $attempt) {
            $this->postJson('/api/reservations', $payload);
        }

        $this->postJson('/api/reservations', $payload)->assertStatus(429);
    }
}
