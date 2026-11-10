<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SitemapController extends Controller
{
    /**
     * Generate or serve XML Sitemap.
     */
    public function index(): Response
    {
        $staticPath = public_path('sitemap.xml');
        if (File::exists($staticPath)) {
            $content = File::get($staticPath);
            return response($content, 200)->header('Content-Type', 'text/xml');
        }

        $xml = Cache::remember('cms_sitemap_xml', 3600, fn () => $this->buildSitemapXml());

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    /**
     * Build the full standard XML sitemap string.
     */
    public function buildSitemapXml(): string
    {
        $posts = Post::published()->with(['image', 'author'])->latest('published_at')->get();
        $pages = Page::where('status', true)->get();
        $categories = Category::all();
        $tags = Tag::all();
        $authors = Author::with('user')->get();

        $output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $output .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // 1. Homepage & Essential Portal Hubs
        $hubs = [
            ['url' => url('/'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => url('/blog'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => url('/magazine'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => url('/authors'), 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => url('/videos'), 'priority' => '0.7', 'changefreq' => 'weekly'],
        ];

        foreach ($hubs as $hub) {
            $output .= "  <url>\n";
            $output .= '    <loc>' . htmlspecialchars($hub['url']) . "</loc>\n";
            $output .= '    <changefreq>' . $hub['changefreq'] . "</changefreq>\n";
            $output .= '    <priority>' . $hub['priority'] . "</priority>\n";
            $output .= "  </url>\n";
        }

        // 2. Published Posts with Embedded Google Image Sitemaps
        foreach ($posts as $post) {
            $output .= "  <url>\n";
            $output .= '    <loc>' . htmlspecialchars(url('/blog/' . $post->slug)) . "</loc>\n";
            $output .= '    <lastmod>' . ($post->updated_at ? $post->updated_at->toAtomString() : Carbon::now()->toAtomString()) . "</lastmod>\n";
            $output .= "    <changefreq>weekly</changefreq>\n";
            $output .= "    <priority>0.8</priority>\n";

            if ($post->image) {
                $imgUrl = $post->image->webp_url ?: $post->image->url;
                $output .= "    <image:image>\n";
                $output .= '      <image:loc>' . htmlspecialchars($imgUrl) . "</image:loc>\n";
                $output .= '      <image:title>' . htmlspecialchars($post->title) . "</image:title>\n";
                if (!empty($post->excerpt)) {
                    $output .= '      <image:caption>' . htmlspecialchars(strip_tags($post->excerpt)) . "</image:caption>\n";
                }
                $output .= "    </image:image>\n";
            }

            $output .= "  </url>\n";
        }

        // 3. Static & Custom Pages
        foreach ($pages as $page) {
            $output .= "  <url>\n";
            $output .= '    <loc>' . htmlspecialchars(url('/page/' . $page->slug)) . "</loc>\n";
            $output .= '    <lastmod>' . ($page->updated_at ? $page->updated_at->toAtomString() : Carbon::now()->toAtomString()) . "</lastmod>\n";
            $output .= "    <changefreq>monthly</changefreq>\n";
            $output .= "    <priority>0.7</priority>\n";
            $output .= "  </url>\n";
        }

        // 4. Categories
        foreach ($categories as $cat) {
            $output .= "  <url>\n";
            $output .= '    <loc>' . htmlspecialchars(url('/category/' . $cat->slug)) . "</loc>\n";
            $output .= "    <changefreq>weekly</changefreq>\n";
            $output .= "    <priority>0.6</priority>\n";
            $output .= "  </url>\n";
        }

        // 5. Tags
        foreach ($tags as $tag) {
            $output .= "  <url>\n";
            $output .= '    <loc>' . htmlspecialchars(url('/tag/' . $tag->id)) . "</loc>\n";
            $output .= "    <changefreq>weekly</changefreq>\n";
            $output .= "    <priority>0.5</priority>\n";
            $output .= "  </url>\n";
        }

        // 6. Authors
        foreach ($authors as $author) {
            $output .= "  <url>\n";
            $output .= '    <loc>' . htmlspecialchars(url('/authors')) . "</loc>\n";
            $output .= "    <changefreq>weekly</changefreq>\n";
            $output .= "    <priority>0.5</priority>\n";
            $output .= "  </url>\n";
        }

        $output .= "</urlset>";

        return $output;
    }
}
