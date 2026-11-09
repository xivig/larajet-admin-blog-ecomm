<?php

namespace App\Jobs;

use App\Models\AdminEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessQueuedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $emailId)
    {
        $this->onQueue(config('queue.connections.database.queue', 'default'));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = AdminEmail::find($this->emailId);

        if (!$email) {
            Log::warning("ProcessQueuedEmail: AdminEmail #{$this->emailId} not found.");
            return;
        }

        try {
            // If Mail driver is log/smtp, send out or log
            Mail::raw($email->body ?? '', function ($message) use ($email) {
                $message->to($email->recipient_email)
                    ->subject($email->subject ?? '(No Subject)');

                if (!empty($email->attachments)) {
                    $attachments = is_array($email->attachments) ? $email->attachments : json_decode($email->attachments, true);
                    if (is_array($attachments)) {
                        foreach ($attachments as $att) {
                            $path = is_array($att) ? ($att['path'] ?? null) : $att;
                            if ($path && file_exists(public_path($path))) {
                                $message->attach(public_path($path));
                            }
                        }
                    }
                }
            });

            Log::info("ProcessQueuedEmail: Successfully processed outgoing email #{$email->id} to {$email->recipient_email}");
        } catch (\Throwable $e) {
            Log::error("ProcessQueuedEmail Failed for #{$email->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
