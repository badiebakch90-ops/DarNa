<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => null,
            'property_slug' => fake()->slug(3),
            'property_name' => fake()->company().' Residence',
            'arrival_date' => now()->addWeek()->toDateString(),
            'departure_date' => now()->addDays(10)->toDateString(),
            'adults_count' => 2,
            'children_count' => 0,
            'guests_count' => 2,
            'nights_count' => 3,
            'nightly_rate' => 950,
            'service_fee' => 342,
            'city_tax' => 150,
            'total_amount' => 3342,
            'deposit_amount' => 1003,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'notes' => fake()->sentence(),
            'payment_method' => Reservation::PAYMENT_METHOD_BANK_TRANSFER,
            'payment_status' => Reservation::PAYMENT_STATUS_AWAITING_BANK_TRANSFER,
            'payment_reference' => 'DRN-'.fake()->numerify('######'),
            'cash_meeting_at' => null,
            'cash_meeting_place' => null,
            'status' => 'pending',
            'source' => 'website',
        ];
    }
}
