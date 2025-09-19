<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;

class ReservationNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $template;
    public $data;
    public $subject;
    public $body;
    public $bodyText;

    /**
     * Create a new message instance.
     */
    public function __construct(string $templateName, array $data = [])
    {
        $this->template = EmailTemplate::getByName($templateName);
        $this->data = $data;
        
        if ($this->template) {
            $processed = $this->template->process($data);
            $this->subject = $processed['subject'];
            $this->body = $processed['body'];
            $this->bodyText = $processed['body_text'];
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject ?? 'Notificación de Reserva',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.reservation-notification',
            text: 'emails.reservation-notification-text',
            with: [
                'body' => $this->body,
                'bodyText' => $this->bodyText,
                'data' => $this->data
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->subject($this->subject ?? 'Notificación de Reserva');

        if ($this->bodyText) {
            $mail->text('emails.reservation-notification-text', [
                'bodyText' => $this->bodyText,
                'data' => $this->data
            ]);
        }

        return $mail->view('emails.reservation-notification', [
            'body' => $this->body,
            'data' => $this->data
        ]);
    }
}