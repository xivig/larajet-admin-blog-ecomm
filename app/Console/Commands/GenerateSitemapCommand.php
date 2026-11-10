<?php

namespace App\Console\Commands;

use App\Http\Controllers\Frontend\SitemapController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     */
    protected $description = 'Generate full XML sitemap file with posts, pages, categories, tags, authors, and images';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating automated XML sitemap...');

        $controller = new SitemapController();
        $xmlContent = $controller->buildSitemapXml();

        $destination = public_path('sitemap.xml');
        File::put($destination, $xmlContent);

        $this->info("XML sitemap successfully generated and saved to: {$destination}");

        return 0;
    }
}
