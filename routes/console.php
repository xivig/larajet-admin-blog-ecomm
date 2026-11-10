<?php

use App\Jobs\ProcessQueuedEmail;
use App\Jobs\PruneSystemAuditLogsJob;
use App\Jobs\PublishScheduledPostsJob;
use App\Models\Category;
use App\Models\Picture;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// --------------------------------------------------------------------------
// CMS Automated Artisan Maintenance Commands
// --------------------------------------------------------------------------

/**
 * Automatically publish scheduled posts whose published_at time has arrived.
 */
Artisan::command('cms:publish-scheduled', function () {
    $now = Carbon::now();
    $duePosts = Post::where(function ($q) use ($now) {
        $q->where('post_status', 'scheduled')
          ->where(function ($sub) use ($now) {
              $sub->where('scheduled_at', '<=', $now)
                  ->orWhere('published_at', '<=', $now);
          });
    })->orWhere(function ($q) use ($now) {
        $q->where('status', false)
          ->whereNotNull('published_at')
          ->where('published_at', '<=', $now);
    })->get();

    if ($duePosts->isEmpty()) {
        $this->info("No scheduled posts currently due for publication (at {$now->toDateTimeString()}).");
        return;
    }

    $count = 0;
    foreach ($duePosts as $post) {
        $post->update([
            'status' => true,
            'post_status' => 'published',
            'published_at' => $post->scheduled_at ?? $now,
        ]);
        $count++;
    }

    Cache::forget('cms_sitemap_xml');
    Cache::forget('cms_rss_feed_xml');

    $this->info("Successfully published {$count} scheduled blog post(s).");
})->purpose('Scan and publish scheduled CMS posts');

/**
 * Pre-warm high-frequency Database Cache entries.
 */
Artisan::command('cms:warm-cache', function () {
    $this->info('Flushing and pre-warming Database Cache...');

    Cache::forget('site_settings_all');
    Cache::forget('cms_sitemap_xml');
    Cache::forget('cms_rss_feed_xml');

    Setting::getAll();
    Category::all();
    Tag::all();

    $this->info('Database Cache pre-warmed with site settings, categories, and tags.');
})->purpose('Pre-warm high-frequency CMS Database Cache items');

/**
 * Check and report database queue statistics.
 */
Artisan::command('cms:queue-status', function () {
    $pending = DB::table('jobs')->count();
    $failed = DB::table('failed_jobs')->count();
    $connection = config('queue.default');

    $this->info("Queue Connection: {$connection}");
    $this->info("Pending Jobs in Database Queue: {$pending}");
    $this->info("Failed Jobs: {$failed}");
})->purpose('Report current Database Queue telemetry');

/**
 * Verify local storage health and permissions.
 */
Artisan::command('cms:storage-check', function () {
    $storagePublic = storage_path('app/public');
    $publicLink = public_path('storage');
    $uploadsDir = public_path('uploads');

    $isWritable = function ($dir) {
        if (!File::exists($dir)) return false;
        $testFile = rtrim($dir, '/\\') . '/.write_test_' . uniqid();
        $written = @file_put_contents($testFile, 'test');
        if ($written !== false) {
            @unlink($testFile);
            return true;
        }
        return false;
    };

    $this->info('--- Local Storage Health Diagnostic ---');
    $this->info("storage/app/public directory: " . (File::exists($storagePublic) ? 'EXISTS' : 'MISSING'));
    $this->info("storage/app/public writable: " . ($isWritable($storagePublic) ? 'YES' : 'NO'));
    $this->info("public/storage link: " . (File::exists($publicLink) || is_dir($publicLink) ? 'CONNECTED' : 'STANDALONE (Direct /public/uploads active)'));
    $this->info("public/uploads writable: " . ($isWritable($uploadsDir) ? 'YES' : 'NO'));
    $this->info("Default Filesystem Disk: " . config('filesystems.default'));
})->purpose('Verify local storage health and permissions');

Artisan::command('blog:populate-images', function () {
    $images = [
        'xivig-admin/img/blog-1-PTDVKJDt.webp',
        'xivig-admin/img/blog-2-DbDLCISq.webp',
        'xivig-admin/img/blog-3-BCh7ZU5G.webp',
        'xivig-admin/img/card-1-DVsHfMAh.webp',
        'xivig-admin/img/card-2-BChm1jqb.webp',
        'xivig-admin/img/card-3-GvyRdp0l.webp',
        'xivig-admin/img/card-horizontal-1-B59MnoQO.webp',
        'xivig-admin/img/card-horizontal-2-SY-zgtGt.webp',
        'xivig-admin/img/slide-hero-1-DvTkwF07.webp',
        'xivig-admin/img/slide-hero-2-CrXQlEOl.webp',
        'xivig-admin/img/slide-hero-3-Ckz1jIOd.webp',
        'xivig-admin/img/album-1-r1zfpAlg.webp',
        'xivig-admin/img/album-2-Cnpqltfz.webp',
        'xivig-admin/img/album-3-BvLZBV-Z.webp',
    ];

    // Ensure Picture records exist with these beautiful images
    $pictures = Picture::all();
    if ($pictures->isEmpty()) {
        foreach ($images as $idx => $img) {
            Picture::create([
                'name' => 'High-Performance Architecture ' . ($idx + 1),
                'link' => $img,
            ]);
        }
        $pictures = Picture::all();
    } else {
        foreach ($pictures as $idx => $pic) {
            $img = $images[$idx % count($images)];
            $pic->update(['link' => $img]);
        }
    }

    // Now populate each post with one of these pictures
    $posts = Post::all();
    $picCount = $pictures->count();
    foreach ($posts as $idx => $post) {
        $assignedPic = $pictures[$idx % $picCount];
        $post->update(['image_id' => $assignedPic->id]);
    }

    $this->info("Populated {$posts->count()} posts with high-fidelity images across {$picCount} pictures.");
})->purpose('Populate blog post images with elite assets');

// --------------------------------------------------------------------------
// CMS Automated Production Cron Job Scheduler
// --------------------------------------------------------------------------

// 1. Every Minute: Automatically publish scheduled blog posts
Schedule::command('cms:publish-scheduled')
    ->everyMinute()
    ->name('publish-scheduled-posts')
    ->withoutOverlapping();

// 2. Every Minute: Process pending jobs from database queue (fallback for cron-only environments)
Schedule::command('queue:work --stop-when-empty --tries=3')
    ->everyMinute()
    ->name('process-database-queue')
    ->withoutOverlapping();

// 3. Daily: Prune expired failed jobs from database queue
Schedule::command('queue:prune-failed --hours=48')
    ->daily()
    ->name('prune-failed-jobs');

// 4. Daily: Prune expired session records from database
Schedule::command('session:prune')
    ->daily()
    ->name('prune-expired-sessions');

// 5. Daily: Prune soft-deleted records older than retention
Schedule::command('model:prune')
    ->daily()
    ->name('prune-models');

// 6. Weekly: Clean old activity logs older than 30 days
Schedule::job(new PruneSystemAuditLogsJob(30))
    ->weekly()
    ->name('prune-audit-logs');

// 7. Daily: Re-warm and synchronize dynamic XML Sitemap and RSS cache in Database Cache
Schedule::command('cms:warm-cache')
    ->daily()
    ->name('warm-cms-cache');

// 8. Daily: Generate and write comprehensive XML Sitemap file (posts, pages, categories, tags, authors, images)
Schedule::command('sitemap:generate')
    ->daily()
    ->name('generate-xml-sitemap');
