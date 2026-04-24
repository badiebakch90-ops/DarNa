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


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    public const PAYMENT_METHOD_CASH = 'cash';

    public const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';

    public const PAYMENT_STATUS_CASH_MEETING_SCHEDULED = 'cash_meeting_scheduled';

    public const PAYMENT_STATUS_AWAITING_BANK_TRANSFER = 'awaiting_bank_transfer';

    protected $fillable = [
        'property_id',
        'property_slug',
        'property_name',
        'arrival_date',
        'departure_date',
        'adults_count',
        'children_count',
        'guests_count',
        'nights_count',
        'nightly_rate',
        'service_fee',
        'city_tax',
        'total_amount',
        'deposit_amount',
        'first_name',
        'last_name',
        'email',
        'phone',
        'notes',
        'payment_method',
        'payment_status',
        'payment_reference',
        'cash_meeting_at',
        'cash_meeting_place',
        'status',
        'source',
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'cash_meeting_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeOverlapping(Builder $query, string $arrivalDate, string $departureDate): Builder
    {
        return $query
            ->where('arrival_date', '<', $departureDate)
            ->where('departure_date', '>', $arrivalDate);
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            self::PAYMENT_METHOD_CASH => 'Especes',
            self::PAYMENT_METHOD_BANK_TRANSFER => 'Virement bancaire',
            default => 'Paiement',
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_STATUS_CASH_MEETING_SCHEDULED => 'Rendez-vous especes prevu',
            self::PAYMENT_STATUS_AWAITING_BANK_TRANSFER => 'Virement en attente',
            default => 'Paiement en attente',
        };
    }

    public function cashMeetingSummary(): ?string
    {
        if (! $this->cash_meeting_at) {
            return null;
        }

        $summary = $this->cash_meeting_at->format('d/m/Y').' a '.$this->cash_meeting_at->format('H:i');

        if ($this->cash_meeting_place) {
            $summary .= ' - '.$this->cash_meeting_place;
        }

        return $summary;
    }
}
