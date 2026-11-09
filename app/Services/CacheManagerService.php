<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class CacheManagerService
{
    /**
     * Purge all CMS application caches.
     */
    public function purgeAll(): void
    {
        Cache::forget('site_settings_all');
        Cache::forget('cms_sitemap_xml');
        Cache::forget('cms_rss_feed_xml');
    }

    /**
     * Pre-warm high-priority database cache records.
     */
    public function warmUp(): array
    {
        $this->purgeAll();

        $settings = Setting::getAll();
        $categories = Category::withCount('posts')->get();
        $tags = Tag::all();

        return [
            'settings_count' => count($settings),
            'categories_count' => $categories->count(),
            'tags_count' => $tags->count(),
        ];
    }
}
