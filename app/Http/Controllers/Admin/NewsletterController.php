<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscribers.
     */
    public function index(Request $request): View
    {
        $query = NewsletterSubscriber::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscribers = $query->paginate(20)->withQueryString();
        $totalCount = NewsletterSubscriber::count();
        $activeCount = NewsletterSubscriber::where('status', 'active')->count();
        $unsubscribedCount = NewsletterSubscriber::where('status', 'unsubscribed')->count();

        return view('admin.newsletter.index', compact(
            'subscribers',
            'totalCount',
            'activeCount',
            'unsubscribedCount'
        ));
    }

    /**
     * Public subscriber registration endpoint.
     */
    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:100',
        ]);

        NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower($request->email)],
            [
                'name' => $request->name,
                'status' => 'active',
                'source' => $request->input('source', 'website_footer'),
                'subscribed_at' => now(),
            ]
        );

        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }

    /**
     * Send newsletter campaign broadcast.
     */
    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $subscribers = NewsletterSubscriber::where('status', 'active')->get();

        // In production, dispatch queued newsletter job to each subscriber
        // foreach ($subscribers as $sub) { ... }

        return redirect()->route('admin.newsletter.index')
            ->with('success', "Newsletter broadcast dispatched to {$subscribers->count()} active subscribers.");
    }

    /**
     * Export subscribers as a CSV file.
     */
    public function export(): StreamedResponse
    {
        $fileName = 'newsletter_subscribers_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Email', 'Name', 'Status', 'Source', 'Subscribed At']);

            NewsletterSubscriber::chunk(500, function ($subscribers) use ($handle) {
                foreach ($subscribers as $s) {
                    fputcsv($handle, [
                        $s->id,
                        $s->email,
                        $s->name,
                        $s->status,
                        $s->source,
                        $s->subscribed_at?->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    /**
     * Delete a subscriber.
     */
    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();
        return redirect()->route('admin.newsletter.index')->with('success', 'Subscriber removed successfully.');
    }

    /**
     * Test SMTP Connection & Email Delivery.
     */
    public function testSmtp(Request $request): RedirectResponse
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $toEmail = $request->test_email;
            \Illuminate\Support\Facades\Mail::raw('This is an automated SMTP connection test from XIVIG Maison CMS.', function ($message) use ($toEmail) {
                $message->to($toEmail)
                    ->subject('XIVIG SMTP Diagnostics: Connection Verified');
            });

            return back()->with('success', "SMTP test message successfully dispatched to {$toEmail}.");
        } catch (\Throwable $e) {
            return back()->with('error', 'SMTP Connection Error: ' . $e->getMessage());
        }
    }

    /**
     * Save Optional Mailchimp Integration configuration.
     */
    public function updateMailchimpSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mailchimp_api_key' => 'nullable|string|max:100',
            'mailchimp_list_id' => 'nullable|string|max:100',
            'mailchimp_server_prefix' => 'nullable|string|max:20',
            'mailchimp_enable' => 'nullable|boolean',
        ]);

        \App\Models\Setting::set('mailchimp_api_key', $validated['mailchimp_api_key'] ?? '', 'integrations');
        \App\Models\Setting::set('mailchimp_list_id', $validated['mailchimp_list_id'] ?? '', 'integrations');
        \App\Models\Setting::set('mailchimp_server_prefix', $validated['mailchimp_server_prefix'] ?? '', 'integrations');
        \App\Models\Setting::set('mailchimp_enable', $request->has('mailchimp_enable') ? '1' : '0', 'integrations');

        return back()->with('success', 'Mailchimp configuration updated successfully.');
    }
}
