<?php

namespace App\Console\Commands;

use App\Jobs\PublishScheduledPostsJob;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class PublishScheduledPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan and publish scheduled CMS posts whose published_at has arrived';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();
        $this->info("Checking for scheduled posts due before {$now->toDateTimeString()}...");

        $duePosts = Post::where('status', false)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->get();

        if ($duePosts->isEmpty()) {
            $this->line("No scheduled posts currently pending.");
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($duePosts as $post) {
            $post->update(['status' => true]);
            $count++;
            $this->line("Published: [ID #{$post->id}] {$post->title}");
        }

        Cache::forget('cms_sitemap_xml');
        Cache::forget('cms_rss_feed_xml');

        $this->info("Successfully published {$count} scheduled post(s). Cache cleared.");

        return self::SUCCESS;
    }
}
