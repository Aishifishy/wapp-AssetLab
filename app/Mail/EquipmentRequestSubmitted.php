<?php

namespace App\Mail;

use App\Models\EquipmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EquipmentRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $equipmentRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(EquipmentRequest $equipmentRequest)
    {
        $this->equipmentRequest = $equipmentRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Equipment Borrow Request Submitted - ' . $this->equipmentRequest->equipment->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.equipment.request-submitted',
            with: [
                'equipmentRequest' => $this->equipmentRequest,
                'user' => $this->equipmentRequest->user,
                'equipment' => $this->equipmentRequest->equipment,
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