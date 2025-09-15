<?php

namespace App\Mail;

use App\Models\EquipmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EquipmentRequestStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $equipmentRequest;
    public $previousStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(EquipmentRequest $equipmentRequest, $previousStatus = null)
    {
        $this->equipmentRequest = $equipmentRequest;
        $this->previousStatus = $previousStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusText = match($this->equipmentRequest->status) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'returned' => 'Equipment Returned',
            'cancelled' => 'Cancelled',
            default => 'Status Updated'
        };

        return new Envelope(
            subject: 'Equipment Request ' . $statusText . ' - ' . $this->equipmentRequest->equipment->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.equipment.request-status-changed',
            with: [
                'equipmentRequest' => $this->equipmentRequest,
                'user' => $this->equipmentRequest->user,
                'equipment' => $this->equipmentRequest->equipment,
                'previousStatus' => $this->previousStatus,
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