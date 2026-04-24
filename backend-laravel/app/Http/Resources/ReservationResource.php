<?php

namespace App\Http\Resources;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'source' => $this->source,
            'arrival_date' => optional($this->arrival_date)->toDateString(),
            'departure_date' => optional($this->departure_date)->toDateString(),
            'guests_count' => $this->guests_count,
            'nights_count' => $this->nights_count,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'payment' => [
                'method' => $this->payment_method,
                'method_label' => $this->paymentMethodLabel(),
                'status' => $this->payment_status,
                'status_label' => $this->paymentStatusLabel(),
                'reference' => $this->payment_reference,
                'cash_meeting' => [
                    'at' => optional($this->cash_meeting_at)->toIso8601String(),
                    'date' => optional($this->cash_meeting_at)->format('Y-m-d'),
                    'time' => optional($this->cash_meeting_at)->format('H:i'),
                    'place' => $this->cash_meeting_place,
                    'summary' => $this->cashMeetingSummary(),
                ],
                'bank_transfer' => $this->payment_method === Reservation::PAYMENT_METHOD_BANK_TRANSFER ? [
                    'beneficiary_name' => config('payment.bank_transfer.beneficiary_name'),
                    'bank_name' => config('payment.bank_transfer.bank_name'),
                    'account_number' => config('payment.bank_transfer.account_number'),
                    'iban' => config('payment.bank_transfer.iban'),
                    'swift' => config('payment.bank_transfer.swift'),
                    'note' => config('payment.bank_transfer.note'),
                ] : null,
            ],
            'created_at' => optional($this->created_at)->toIso8601String(),
            'property' => [
                'id' => $this->property_id,
                'slug' => $this->property_slug,
                'name' => $this->property_name,
            ],
            'amounts' => [
                'nightly_rate' => $this->nightly_rate,
                'service_fee' => $this->service_fee,
                'city_tax' => $this->city_tax,
                'total_amount' => $this->total_amount,
                'deposit_amount' => $this->deposit_amount,
            ],
        ];
    }
}
