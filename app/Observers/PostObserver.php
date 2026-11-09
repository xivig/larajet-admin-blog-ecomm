<?php

namespace App\Observers;

use App\Events\PostPublished;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        Cache::forget('cms_sitemap_xml');
        Cache::forget('cms_rss_feed_xml');

        if ($post->status) {
            event(new PostPublished($post));
        }
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        Cache::forget('cms_sitemap_xml');
        Cache::forget('cms_rss_feed_xml');

        if ($post->wasChanged('status') && $post->status) {
            event(new PostPublished($post));
        }
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        Cache::forget('cms_sitemap_xml');
        Cache::forget('cms_rss_feed_xml');
    }
}
