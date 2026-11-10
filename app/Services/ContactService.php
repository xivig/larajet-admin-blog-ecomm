<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactService
{
    /**
     * Store and process incoming contact inquiry.
     */
    public function storeInquiry(array $data, ?string $ip = null): Contact
    {
        $contact = Contact::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? 'Website Inquiry',
            'message' => $data['message'],
            'status' => 'new',
            'ip_address' => $ip,
        ]);

        try {
            AdminNotification::create([
                'type' => 'contact',
                'title' => 'New Contact Inquiry',
                'message' => "{$contact->name}: " . Str::limit($contact->subject ?: $contact->message, 55),
                'link' => '/admin/contacts',
                'is_read' => false,
            ]);
        } catch (\Throwable $e) {
            // Keep contact submission non-blocking
        }

        // Send email notification to admin if SMTP configured
        try {
            $adminEmail = config('mail.from.address', 'admin@xivig.com');
            Mail::raw("New Contact Message from {$contact->name} ({$contact->email}):\n\nSubject: {$contact->subject}\n\nMessage:\n{$contact->message}", function ($msg) use ($adminEmail, $contact) {
                $msg->to($adminEmail)
                    ->subject("New Inquiry: {$contact->subject}");
            });
        } catch (\Throwable $e) {
            // Silently continue if mail not reachable
        }

        return $contact;
    }

    /**
     * Send direct reply to a contact.
     */
    public function replyToContact(Contact $contact, string $replyMessage): bool
    {
        $contact->update([
            'reply_message' => $replyMessage,
            'status' => 'replied',
            'replied_at' => now(),
        ]);

        try {
            Mail::raw("Hello {$contact->name},\n\nThank you for reaching out to us.\n\n{$replyMessage}\n\nBest regards,\nXIVIG Maison Editorial & Support Desk", function ($msg) use ($contact) {
                $msg->to($contact->email)
                    ->subject("Re: {$contact->subject}");
            });
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Export all contacts to CSV format.
     */
    public function exportCsv(): StreamedResponse
    {
        $fileName = 'contacts_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Status', 'IP', 'Created At', 'Replied At']);

            Contact::orderBy('id', 'desc')->chunk(500, function ($contacts) use ($handle) {
                foreach ($contacts as $c) {
                    fputcsv($handle, [
                        $c->id,
                        $c->name,
                        $c->email,
                        $c->phone,
                        $c->subject,
                        $c->message,
                        $c->status,
                        $c->ip_address,
                        $c->created_at?->toDateTimeString(),
                        $c->replied_at?->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }
}
