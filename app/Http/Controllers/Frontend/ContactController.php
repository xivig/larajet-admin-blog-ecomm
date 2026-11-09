<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ContactFormRequest;
use App\Jobs\ProcessQueuedEmail;
use App\Models\AdminEmail;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    /**
     * Show the public contact page.
     */
    public function index(): View
    {
        return view('contact');
    }

    /**
     * Handle submitted contact inquiries.
     */
    public function submit(ContactFormRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $adminEmail = AdminEmail::create([
            'sender_name' => $validated['name'],
            'sender_email' => $validated['email'],
            'recipient_email' => Setting::get('contact_email', 'admin@example.com'),
            'subject' => $validated['subject'] ?? 'New Contact Form Submission',
            'body' => $validated['message'],
            'folder' => 'inbox',
            'is_read' => false,
            'is_starred' => false,
        ]);

        // Dispatch background queue job
        ProcessQueuedEmail::dispatch($adminEmail->id);

        return back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}
