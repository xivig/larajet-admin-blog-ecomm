<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QueueMonitorService
{
    /**
     * Get real-time queue pending and failure metrics.
     */
    public function getMetrics(): array
    {
        $connection = config('queue.default');
        $hasJobsTable = Schema::hasTable('jobs');
        $hasFailedJobsTable = Schema::hasTable('failed_jobs');

        $pending = $hasJobsTable ? DB::table('jobs')->count() : 0;
        $failed = $hasFailedJobsTable ? DB::table('failed_jobs')->count() : 0;

        return [
            'connection' => $connection,
            'driver' => config("queue.connections.{$connection}.driver", 'sync'),
            'pending_jobs' => $pending,
            'failed_jobs' => $failed,
            'status' => $pending > 0 ? 'processing' : 'idle',
        ];
    }
}
