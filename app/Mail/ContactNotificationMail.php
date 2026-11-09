<?php

namespace App\Mail;

use App\Models\AdminEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public AdminEmail $email)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Inquiry: ' . $this->email->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "<div style='font-family: sans-serif; padding: 20px; line-height: 1.6;'>
                <h2>New Website Inquiry</h2>
                <p><strong>From:</strong> {$this->email->sender_name} ({$this->email->sender_email})</p>
                <p><strong>Subject:</strong> {$this->email->subject}</p>
                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                <div style='background: #f8fafc; padding: 15px; border-radius: 8px;'>
                    " . nl2br(e($this->email->body)) . "
                </div>
            </div>"
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
