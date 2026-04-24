<?php

namespace Tests\Feature\Api;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_endpoint_creates_a_pending_reservation_and_returns_amounts(): void
    {
        config([
            'payment.bank_transfer.beneficiary_name' => 'DarNa Operations',
            'payment.bank_transfer.bank_name' => 'Banque Test',
            'payment.bank_transfer.account_number' => '1234567890',
            'payment.bank_transfer.iban' => 'MA6400000000001234567890123',
            'payment.bank_transfer.swift' => 'BTESTMAM',
            'payment.bank_transfer.note' => 'Faites le virement avec la reference.',
        ]);

        Property::factory()->create([
            'slug' => 'appartement-vue-mer',
            'type' => 'Appartement',
            'name' => 'Appartement Vue Mer',
            'city' => 'Casablanca',
            'location' => 'Casablanca, Maarif',
            'nightly_rate' => 620,
        ]);

        $payload = [
            'property_slug' => 'appartement-vue-mer',
            'arrival_date' => '2026-04-14',
            'departure_date' => '2026-04-17',
            'adults_count' => 2,
            'children_count' => 1,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'phone' => '+212600000000',
            'notes' => 'Automated API test',
            'payment_method' => 'bank_transfer',
        ];

        $response = $this->postJson('/api/reservations', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.property.slug', 'appartement-vue-mer')
            ->assertJsonPath('data.amounts.nights', 3)
            ->assertJsonPath('data.amounts.total_amount', 2308)
            ->assertJsonPath('data.amounts.deposit_amount', 692)
            ->assertJsonPath('data.payment.method', 'bank_transfer')
            ->assertJsonPath('data.payment.status', 'awaiting_bank_transfer')
            ->assertJsonPath('data.payment.bank_transfer.bank_name', 'Banque Test');

        $this->assertDatabaseHas('reservations', [
            'property_slug' => 'appartement-vue-mer',
            'first_name' => 'Test',
            'last_name' => 'User',
            'status' => 'pending',
            'source' => 'website',
            'total_amount' => 2308,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'awaiting_bank_transfer',
        ]);
    }

    public function test_cash_reservation_endpoint_saves_payment_appointment_details(): void
    {
        Property::factory()->create([
            'slug' => 'maison-palmiers',
            'name' => 'Maison Palmiers',
            'nightly_rate' => 800,
        ]);

        $payload = [
            'property_slug' => 'maison-palmiers',
            'arrival_date' => '2026-06-20',
            'departure_date' => '2026-06-22',
            'adults_count' => 2,
            'children_count' => 0,
            'first_name' => 'Leila',
            'last_name' => 'Amrani',
            'email' => 'leila@example.com',
            'phone' => '+212600000123',
            'payment_method' => 'cash',
            'cash_meeting_date' => '2026-06-20',
            'cash_meeting_time' => '14:30',
            'cash_meeting_place' => 'Point de remise des cles DarNa',
        ];

        $this->postJson('/api/reservations', $payload)
            ->assertCreated()
            ->assertJsonPath('data.payment.method', 'cash')
            ->assertJsonPath('data.payment.status', 'cash_meeting_scheduled')
            ->assertJsonPath('data.payment.cash_meeting.date', '2026-06-20')
            ->assertJsonPath('data.payment.cash_meeting.time', '14:30')
            ->assertJsonPath('data.payment.cash_meeting.place', 'Point de remise des cles DarNa');

        $this->assertDatabaseHas('reservations', [
            'property_slug' => 'maison-palmiers',
            'payment_method' => 'cash',
            'payment_status' => 'cash_meeting_scheduled',
            'cash_meeting_place' => 'Point de remise des cles DarNa',
        ]);
    }

    public function test_reservation_endpoint_rejects_overlapping_dates_for_an_active_reservation(): void
    {
        $property = Property::factory()->create([
            'slug' => 'riad-al-baraka',
            'type' => 'Riad',
            'name' => 'Riad Al Baraka',
        ]);

        Reservation::factory()->create([
            'property_id' => $property->id,
            'property_slug' => $property->slug,
            'property_name' => $property->name,
            'arrival_date' => '2026-04-10',
            'departure_date' => '2026-04-15',
            'status' => 'confirmed',
        ]);

        $payload = [
            'property_slug' => 'riad-al-baraka',
            'arrival_date' => '2026-04-12',
            'departure_date' => '2026-04-14',
            'adults_count' => 2,
            'children_count' => 0,
            'first_name' => 'Sara',
            'last_name' => 'Benali',
            'email' => 'sara@example.com',
            'phone' => '+212600000000',
            'payment_method' => 'bank_transfer',
        ];

        $this->postJson('/api/reservations', $payload)
            ->assertStatus(422)
            ->assertJsonPath('data.available', false)
            ->assertJsonPath('data.blocked_ranges.0.arrival_date', '2026-04-10')
            ->assertJsonPath('data.blocked_ranges.0.departure_date', '2026-04-15');
    }

    public function test_admin_can_list_latest_reservations(): void
    {
        $property = Property::factory()->create([
            'slug' => 'villa-cap-spartel',
            'name' => 'Villa Cap Spartel',
        ]);

        $admin = User::factory()->admin()->create();

        Reservation::factory()->count(2)->create([
            'property_id' => $property->id,
            'property_slug' => $property->slug,
            'property_name' => $property->name,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->getJson('/api/reservations?property_slug=villa-cap-spartel&status=pending&limit=10')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.property.slug', 'villa-cap-spartel');
    }

    public function test_public_users_cannot_list_reservations(): void
    {
        $this->getJson('/api/reservations')
            ->assertUnauthorized();
    }

    public function test_property_availability_endpoint_returns_conflicts(): void
    {
        $property = Property::factory()->create([
            'slug' => 'camp-merzouga-etoiles',
            'name' => 'Camp Merzouga Etoiles',
        ]);

        Reservation::factory()->create([
            'property_id' => $property->id,
            'property_slug' => $property->slug,
            'property_name' => $property->name,
            'arrival_date' => '2026-05-01',
            'departure_date' => '2026-05-06',
            'status' => 'pending',
        ]);

        $this->getJson('/api/properties/camp-merzouga-etoiles/availability?arrival_date=2026-05-03&departure_date=2026-05-04')
            ->assertOk()
            ->assertJsonPath('data.available', false)
            ->assertJsonPath('data.conflicts_count', 1);
    }
}
