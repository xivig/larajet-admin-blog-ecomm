<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PublishScheduledPostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct()
    {
        $this->onQueue('default');
    }

    /**
     * Execute the scheduled post publication job.
     */
    public function handle(): void
    {
        $now = Carbon::now();
        $duePosts = Post::where('status', false)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->get();

        if ($duePosts->isEmpty()) {
            return;
        }

        $count = 0;
        foreach ($duePosts as $post) {
            $post->update(['status' => true]);
            $count++;

            try {
                ActivityLog::create([
                    'log_name' => 'scheduler',
                    'description' => "Automated publication of scheduled post #{$post->id}: '{$post->title}'",
                    'subject_type' => Post::class,
                    'subject_id' => $post->id,
                    'causer_type' => null,
                    'causer_id' => null,
                    'properties' => [
                        'scheduled_for' => $post->published_at?->toIso8601String(),
                        'published_at' => $now->toIso8601String(),
                    ],
                ]);
            } catch (\Throwable $e) {}
        }

        // Invalidate sitemap & RSS feeds in Database Cache
        try {
            Cache::forget('cms_rss_feed_xml');
            Cache::forget('cms_sitemap_xml');
        } catch (\Throwable $e) {}

        Log::info("PublishScheduledPostsJob: Automatically published {$count} scheduled post(s).");
    }
}
