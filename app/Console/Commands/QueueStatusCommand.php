<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QueueStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:queue-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report current Database Queue and telemetry status';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $connection = config('queue.default');
        $this->info("Queue Connection: {$connection}");

        $pending = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
        $failed = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;

        $this->table(
            ['Metric', 'Value'],
            [
                ['Connection Driver', $connection],
                ['Pending Jobs', $pending],
                ['Failed Jobs', $failed],
                ['Database Table', config('queue.connections.database.table', 'jobs')],
            ]
        );

        return self::SUCCESS;
    }
}
