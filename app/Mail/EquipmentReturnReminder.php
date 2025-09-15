<?php

namespace App\Mail;

use App\Models\EquipmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EquipmentReturnReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $equipmentRequest;
    public $daysOverdue;

    /**
     * Create a new message instance.
     */
    public function __construct(EquipmentRequest $equipmentRequest, $daysOverdue = null)
    {
        $this->equipmentRequest = $equipmentRequest;
        $this->daysOverdue = $daysOverdue;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->daysOverdue > 0 
            ? 'Equipment Overdue - Immediate Return Required'
            : 'Equipment Return Reminder - ' . $this->equipmentRequest->equipment->name;

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.equipment.return-reminder',
            with: [
                'equipmentRequest' => $this->equipmentRequest,
                'user' => $this->equipmentRequest->user,
                'equipment' => $this->equipmentRequest->equipment,
                'daysOverdue' => $this->daysOverdue,
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