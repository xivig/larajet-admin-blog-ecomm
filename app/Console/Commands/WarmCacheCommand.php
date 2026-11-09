<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class WarmCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:warm-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-warm high-frequency CMS Database Cache items';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Flushing and pre-warming Database Cache...');

        Cache::forget('site_settings_all');
        Cache::forget('cms_sitemap_xml');
        Cache::forget('cms_rss_feed_xml');

        Setting::getAll();
        Category::all();
        Tag::all();

        $this->info('Database Cache pre-warmed successfully: site settings, categories, and tags.');

        return self::SUCCESS;
    }
}
