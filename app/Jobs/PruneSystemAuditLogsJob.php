<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PruneSystemAuditLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public int $retentionDays = 30)
    {
        $this->onQueue(config('queue.connections.database.queue', 'default'));
    }

    /**
     * Execute the log and queue cleanup.
     */
    public function handle(): void
    {
        $cutoff = Carbon::now()->subDays($this->retentionDays);

        try {
            $deletedLogs = ActivityLog::where('created_at', '<', $cutoff)->delete();
            Log::info("PruneSystemAuditLogsJob: Cleaned {$deletedLogs} activity log records older than {$this->retentionDays} days.");
        } catch (\Throwable $e) {
            Log::warning("PruneSystemAuditLogsJob: Activity log pruning error: " . $e->getMessage());
        }

        try {
            $failedCutoff = Carbon::now()->subHours(48);
            $deletedFailed = DB::table('failed_jobs')->where('failed_at', '<', $failedCutoff)->delete();
            Log::info("PruneSystemAuditLogsJob: Purged {$deletedFailed} expired failed jobs.");
        } catch (\Throwable $e) {}
    }
}
