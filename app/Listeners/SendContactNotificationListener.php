<?php

namespace App\Listeners;

use App\Events\ContactSubmitted;
use Illuminate\Support\Facades\Mail;

class SendContactNotificationListener
{
    public function handle(ContactSubmitted $event): void
    {
        $contact = $event->contact;
        $adminEmail = config('mail.from.address', 'admin@xivig.com');

        try {
            Mail::raw("New Message from {$contact->name} ({$contact->email}):\n\nSubject: {$contact->subject}\n\n{$contact->message}", function ($msg) use ($adminEmail, $contact) {
                $msg->to($adminEmail)->subject("Contact Inquiry: {$contact->subject}");
            });
        } catch (\Throwable $e) {}
    }
}
