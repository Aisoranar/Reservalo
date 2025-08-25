<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $status;

    public function __construct(Reservation $reservation, string $status)
    {
        $this->reservation = $reservation;
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $subject = match($this->status) {
            'approved' => 'Tu reserva ha sido aprobada',
            'rejected' => 'Tu reserva ha sido rechazada',
            default => 'Estado de tu reserva actualizado'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-status-changed',
            with: [
                'reservation' => $this->reservation,
                'status' => $this->status
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
