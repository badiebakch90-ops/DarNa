<?php

/**
 * ─────────────────────────────────────────────────────────────
 *   DarNa — Plateforme de location authentique au Maroc 🇲🇦
 *   Author    : Abdelbadie Abkhich (@badiebakch90-ops)
 *   Original  : https://github.com/badiebakch90-ops/DarNa
 *   Copyright : © 2026 Abdelbadie Abkhich — All rights reserved
 *   License   : See LICENSE file
 * ─────────────────────────────────────────────────────────────
 */


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\NewReservationReceivedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Reservation::query()
            ->latest()
            ->when(
                $request->string('property_slug')->toString(),
                fn ($query, $propertySlug) => $query->where('property_slug', $propertySlug)
            )
            ->when(
                $request->string('status')->toString(),
                fn ($query, $status) => $query->where('status', $status)
            )
            ->limit(min(max($request->integer('limit', 20), 1), 100))
            ->get();

        return ReservationResource::collection($reservations);
    }

    public function store(StoreReservationRequest $request)
    {
        $payload = $request->validated();

        $property = Property::query()
            ->where('slug', $payload['property_slug'])
            ->firstOrFail();

        $arrival = Carbon::parse($payload['arrival_date']);
        $departure = Carbon::parse($payload['departure_date']);
        $nights = max(1, $arrival->diffInDays($departure));
        $adults = (int) $payload['adults_count'];
        $children = (int) ($payload['children_count'] ?? 0);
        $guests = $adults + $children;

        $conflictingReservations = Reservation::query()
            ->active()
            ->where('property_id', $property->id)
            ->overlapping($arrival->toDateString(), $departure->toDateString())
            ->orderBy('arrival_date')
            ->get();

        if ($conflictingReservations->isNotEmpty()) {
            return response()->json([
                'message' => 'Ce logement n est plus disponible sur cette periode.',
                'errors' => [
                    'arrival_date' => ['Ce logement n est plus disponible sur cette periode.'],
                ],
                'data' => [
                    'available' => false,
                    'blocked_ranges' => $conflictingReservations->map(fn (Reservation $reservation) => [
                        'arrival_date' => $reservation->arrival_date->toDateString(),
                        'departure_date' => $reservation->departure_date->toDateString(),
                        'status' => $reservation->status,
                    ])->values(),
                ],
            ], 422);
        }

        $subtotal = $property->nightly_rate * $nights;
        $serviceFee = (int) round($subtotal * 0.12);
        $cityTax = $nights * $guests * 25;
        $total = $subtotal + $serviceFee + $cityTax;
        $deposit = (int) round($total * 0.30);
        $paymentMethod = $payload['payment_method'];
        $cashMeetingAt = $paymentMethod === Reservation::PAYMENT_METHOD_CASH
            ? Carbon::createFromFormat('Y-m-d H:i', $payload['cash_meeting_date'].' '.$payload['cash_meeting_time'], config('app.timezone'))
            : null;

        $reservation = Reservation::query()->create([
            'property_id' => $property->id,
            'property_slug' => $property->slug,
            'property_name' => $property->name,
            'arrival_date' => $arrival->toDateString(),
            'departure_date' => $departure->toDateString(),
            'adults_count' => $adults,
            'children_count' => $children,
            'guests_count' => $guests,
            'nights_count' => $nights,
            'nightly_rate' => $property->nightly_rate,
            'service_fee' => $serviceFee,
            'city_tax' => $cityTax,
            'total_amount' => $total,
            'deposit_amount' => $deposit,
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'notes' => $payload['notes'] ?? null,
            'payment_method' => $paymentMethod,
            'payment_status' => $this->paymentStatusForMethod($paymentMethod),
            'cash_meeting_at' => $cashMeetingAt,
            'cash_meeting_place' => $paymentMethod === Reservation::PAYMENT_METHOD_CASH
                ? $payload['cash_meeting_place']
                : null,
            'status' => 'pending',
            'source' => 'website',
        ]);

        $reservation->forceFill([
            'payment_reference' => $this->paymentReferenceFor($reservation),
        ])->save();

        $recipients = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->when(
                $property->owner_id,
                fn ($query) => $query->orWhere('id', $property->owner_id)
            )
            ->get()
            ->unique('id')
            ->values();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new NewReservationReceivedNotification($reservation));
        }

        return response()->json([
            'message' => 'Reservation request saved successfully.',
            'data' => [
                ...ReservationResource::make($reservation)->resolve(),
                'amounts' => [
                    'nights' => $nights,
                    'nightly_rate' => $property->nightly_rate,
                    'service_fee' => $serviceFee,
                    'city_tax' => $cityTax,
                    'total_amount' => $total,
                    'deposit_amount' => $deposit,
                ],
            ],
        ], 201);
    }

    private function paymentStatusForMethod(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            Reservation::PAYMENT_METHOD_CASH => Reservation::PAYMENT_STATUS_CASH_MEETING_SCHEDULED,
            default => Reservation::PAYMENT_STATUS_AWAITING_BANK_TRANSFER,
        };
    }

    private function paymentReferenceFor(Reservation $reservation): string
    {
        return 'DRN-'.str_pad((string) $reservation->id, 6, '0', STR_PAD_LEFT);
    }
}
