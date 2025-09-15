<?php

namespace App\Mail;

use App\Models\LaboratoryScheduleOverride;
use App\Models\LaboratoryReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScheduleOverrideNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $override;
    public $affectedReservations;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(LaboratoryScheduleOverride $override, $affectedReservations = [], $user = null)
    {
        $this->override = $override;
        $this->affectedReservations = $affectedReservations;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laboratory Schedule Change - ' . $this->override->laboratory->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.laboratory.schedule-override-notification',
            with: [
                'override' => $this->override,
                'affectedReservations' => $this->affectedReservations,
                'user' => $this->user,
                'laboratory' => $this->override->laboratory,
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
}