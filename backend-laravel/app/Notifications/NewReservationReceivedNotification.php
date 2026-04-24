<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReservationReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Reservation $reservation
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $guestName = trim($this->reservation->first_name.' '.$this->reservation->last_name);

        $mail = (new MailMessage)
            ->subject('Nouvelle reservation recue pour '.$this->reservation->property_name)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Une nouvelle demande de reservation vient d arriver pour ton logement.')
            ->line('Logement: '.$this->reservation->property_name)
            ->line('Voyageur: '.$guestName)
            ->line('Periode: '.$this->reservation->arrival_date->format('d/m/Y').' - '.$this->reservation->departure_date->format('d/m/Y'))
            ->line('Paiement choisi: '.$this->reservation->paymentMethodLabel())
            ->line('Statut paiement: '.$this->reservation->paymentStatusLabel())
            ->line('Reference paiement: '.$this->reservation->payment_reference)
            ->line('Total estime: '.number_format($this->reservation->total_amount, 0, ',', ' ').' MAD')
            ->action('Voir mon espace hote', route('hosting.index'))
            ->line('DarNa a enregistre la demande avec le statut pending.');

        if ($this->reservation->payment_method === Reservation::PAYMENT_METHOD_CASH && $this->reservation->cashMeetingSummary()) {
            $mail->line('Rendez-vous especes: '.$this->reservation->cashMeetingSummary());
        }

        if ($this->reservation->payment_method === Reservation::PAYMENT_METHOD_BANK_TRANSFER) {
            $mail->line('Le client doit effectuer un virement bancaire vers le compte configure.');
        }

        return $mail;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $guestName = trim($this->reservation->first_name.' '.$this->reservation->last_name);

        return [
            'title' => 'Nouvelle reservation recue',
            'reservation_id' => $this->reservation->id,
            'property_id' => $this->reservation->property_id,
            'property_slug' => $this->reservation->property_slug,
            'property_name' => $this->reservation->property_name,
            'guest_name' => $guestName,
            'guest_email' => $this->reservation->email,
            'arrival_date' => $this->reservation->arrival_date->toDateString(),
            'departure_date' => $this->reservation->departure_date->toDateString(),
            'status' => $this->reservation->status,
            'total_amount' => $this->reservation->total_amount,
            'payment_method' => $this->reservation->payment_method,
            'payment_method_label' => $this->reservation->paymentMethodLabel(),
            'payment_status' => $this->reservation->payment_status,
            'payment_status_label' => $this->reservation->paymentStatusLabel(),
            'payment_reference' => $this->reservation->payment_reference,
            'cash_meeting_at' => optional($this->reservation->cash_meeting_at)->toIso8601String(),
            'cash_meeting_label' => $this->reservation->cashMeetingSummary(),
            'message' => $guestName.' a demande une reservation pour '.$this->reservation->property_name.' avec paiement '.$this->reservation->paymentMethodLabel().'.',
            'action_url' => route('hosting.index'),
        ];
    }
}
