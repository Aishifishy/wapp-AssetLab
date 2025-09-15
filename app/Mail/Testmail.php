<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Testmail extends Mailable
{
    use Queueable, SerializesModels;

    public $testData;

    /**
     * Create a new message instance.
     */
    public function __construct($testData = null)
    {
        $this->testData = $testData ?? [
            'title' => 'Gmail SMTP Test Email',
            'user_name' => 'Test User',
            'message' => 'This is a test email to verify that Gmail SMTP is working correctly with AssetLab.',
            'timestamp' => now()->format('F j, Y \a\t g:i A'),
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'AssetLab Gmail SMTP Test - ' . now()->format('M j, Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.test-email',
            with: [
                'testData' => $this->testData,
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
