<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'property_slug' => ['required', 'string', Rule::exists('properties', 'slug')],
            'arrival_date' => ['required', 'date'],
            'departure_date' => ['required', 'date', 'after:arrival_date'],
            'adults_count' => ['required', 'integer', 'min:1', 'max:20'],
            'children_count' => ['nullable', 'integer', 'min:0', 'max:15'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'payment_method' => ['required', 'string', Rule::in([
                Reservation::PAYMENT_METHOD_CASH,
                Reservation::PAYMENT_METHOD_BANK_TRANSFER,
            ])],
            'cash_meeting_date' => [
                Rule::requiredIf(fn () => $this->input('payment_method') === Reservation::PAYMENT_METHOD_CASH),
                'nullable',
                'date',
                'after_or_equal:today',
                'before_or_equal:arrival_date',
            ],
            'cash_meeting_time' => [
                Rule::requiredIf(fn () => $this->input('payment_method') === Reservation::PAYMENT_METHOD_CASH),
                'nullable',
                'date_format:H:i',
            ],
            'cash_meeting_place' => [
                Rule::requiredIf(fn () => $this->input('payment_method') === Reservation::PAYMENT_METHOD_CASH),
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $paymentMethod = $this->input('payment_method', Reservation::PAYMENT_METHOD_BANK_TRANSFER);

        $this->merge([
            'children_count' => $this->input('children_count', 0),
            'payment_method' => $paymentMethod,
            'cash_meeting_place' => $paymentMethod === Reservation::PAYMENT_METHOD_CASH
                ? ($this->input('cash_meeting_place') ?: config('payment.cash.meeting_location'))
                : $this->input('cash_meeting_place'),
        ]);
    }
}
