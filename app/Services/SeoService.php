<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * Generate complete HTML metadata tags for the given Post, Page, or Site default.
     */
    public static function renderMeta(Post|Page|null $model = null): string
    {
        $data = self::resolveMetadata($model);
        $html = [];

        // 1. Core Meta Tags
        $html[] = '<title>' . e($data['title']) . '</title>';
        $html[] = '<meta name="description" content="' . e($data['description']) . '">';
        if (!empty($data['keywords'])) {
            $html[] = '<meta name="keywords" content="' . e($data['keywords']) . '">';
        }
        $html[] = '<link rel="canonical" href="' . e($data['canonical']) . '">';
        if (!empty($data['author'])) {
            $html[] = '<meta name="author" content="' . e($data['author']) . '">';
        }
        $html[] = '<meta name="robots" content="' . e($data['robots']) . '">';
        $html[] = '<meta name="language" content="' . e($data['language']) . '">';
        $html[] = '<meta http-equiv="content-language" content="' . e($data['language']) . '">';

        // 2. Geographic Metadata
        if (!empty($data['geo_region'])) {
            $html[] = '<meta name="geo.region" content="' . e($data['geo_region']) . '">';
        }
        if (!empty($data['geo_placename'])) {
            $html[] = '<meta name="geo.placename" content="' . e($data['geo_placename']) . '">';
        }
        if (!empty($data['geo_position'])) {
            $html[] = '<meta name="geo.position" content="' . e($data['geo_position']) . '">';
            $html[] = '<meta name="ICBM" content="' . e(str_replace(';', ', ', $data['geo_position'])) . '">';
        }

        // 3. Rating & Copyright
        $html[] = '<meta name="rating" content="' . e($data['rating']) . '">';
        if (!empty($data['copyright'])) {
            $html[] = '<meta name="copyright" content="' . e($data['copyright']) . '">';
        }

        return implode("\n  ", $html);
    }

    /**
     * Generate Open Graph & Social sharing tags (Facebook, Twitter/X, LinkedIn, WhatsApp).
     */
    public static function renderOpenGraph(Post|Page|null $model = null): string
    {
        $data = self::resolveMetadata($model);
        $html = [];

        // Facebook & Core Open Graph
        $html[] = '<meta property="og:type" content="' . e($data['og_type']) . '">';
        $html[] = '<meta property="og:site_name" content="' . e($data['site_name']) . '">';
        $html[] = '<meta property="og:url" content="' . e($data['canonical']) . '">';
        $html[] = '<meta property="og:title" content="' . e($data['og_title']) . '">';
        $html[] = '<meta property="og:description" content="' . e($data['og_description']) . '">';
        if (!empty($data['image'])) {
            $html[] = '<meta property="og:image" content="' . e($data['image']) . '">';
            $html[] = '<meta property="og:image:secure_url" content="' . e($data['image']) . '">';
            $html[] = '<meta property="og:image:width" content="1200">';
            $html[] = '<meta property="og:image:height" content="630">';
            $html[] = '<meta property="og:image:alt" content="' . e($data['title']) . '">';
        }

        // Article Specific Meta (For Facebook, LinkedIn, WhatsApp)
        if ($model instanceof Post) {
            if ($model->published_at) {
                $html[] = '<meta property="article:published_time" content="' . $model->published_at->toIso8601String() . '">';
            }
            if ($model->updated_at) {
                $html[] = '<meta property="article:modified_time" content="' . $model->updated_at->toIso8601String() . '">';
            }
            if (!empty($data['author'])) {
                $html[] = '<meta property="article:author" content="' . e($data['author']) . '">';
            }
            if ($model->category) {
                $html[] = '<meta property="article:section" content="' . e($model->category->name) . '">';
            }
            if ($model->tag) {
                $html[] = '<meta property="article:tag" content="' . e($model->tag->name) . '">';
            }
        }

        // Twitter / X Meta
        $html[] = '<meta name="twitter:card" content="' . e($data['twitter_card']) . '">';
        $html[] = '<meta name="twitter:title" content="' . e($data['og_title']) . '">';
        $html[] = '<meta name="twitter:description" content="' . e($data['og_description']) . '">';
        if (!empty($data['image'])) {
            $html[] = '<meta name="twitter:image" content="' . e($data['image']) . '">';
        }
        if (!empty($data['twitter_site'])) {
            $html[] = '<meta name="twitter:site" content="' . e($data['twitter_site']) . '">';
        }
        if (!empty($data['twitter_creator'])) {
            $html[] = '<meta name="twitter:creator" content="' . e($data['twitter_creator']) . '">';
        }

        return implode("\n  ", $html);
    }

    /**
     * Generate Automatic Schema.org JSON-LD Structured Data script.
     */
    public static function generateJsonLd(Post|Page|null $model = null): string
    {
        $data = self::resolveMetadata($model);
        $siteUrl = url('/');
        $currentUrl = $data['canonical'];

        $graph = [];

        // 1. Organization Schema
        $graph[] = [
            '@type' => 'Organization',
            '@id' => $siteUrl . '/#organization',
            'name' => $data['site_name'],
            'url' => $siteUrl,
            'logo' => [
                '@type' => 'ImageObject',
                '@id' => $siteUrl . '/#logo',
                'url' => $data['logo_url'],
                'caption' => $data['site_name'],
            ],
            'sameAs' => $data['same_as'],
        ];

        // 2. WebSite Schema
        $graph[] = [
            '@type' => 'WebSite',
            '@id' => $siteUrl . '/#website',
            'url' => $siteUrl,
            'name' => $data['site_name'],
            'description' => $data['site_description'],
            'publisher' => ['@id' => $siteUrl . '/#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $siteUrl . '/blog-list?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
            'inLanguage' => $data['language'],
        ];

        // 3. BreadcrumbList Schema
        $breadcrumbs = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => $siteUrl,
            ],
        ];

        if ($model instanceof Post) {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Blog',
                'item' => url('/blog-list'),
            ];
            if ($model->category) {
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $model->category->name,
                    'item' => url('/blog-list?category=' . $model->category->id),
                ];
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => 4,
                    'name' => $model->title,
                    'item' => $currentUrl,
                ];
            } else {
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $model->title,
                    'item' => $currentUrl,
                ];
            }
        } elseif ($model instanceof Page) {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $model->title,
                'item' => $currentUrl,
            ];
        }

        $graph[] = [
            '@type' => 'BreadcrumbList',
            '@id' => $currentUrl . '/#breadcrumb',
            'itemListElement' => $breadcrumbs,
        ];

        // 4. Author (Person) Schema
        if (!empty($data['author'])) {
            $graph[] = [
                '@type' => 'Person',
                '@id' => $siteUrl . '/#/schema/person/' . Str::slug($data['author']),
                'name' => $data['author'],
                'url' => $siteUrl,
            ];
        }

        // 5. ImageObject Schema
        if (!empty($data['image'])) {
            $graph[] = [
                '@type' => 'ImageObject',
                '@id' => $data['image'] . '#primaryimage',
                'url' => $data['image'],
                'contentUrl' => $data['image'],
                'caption' => $data['title'],
            ];
        }

        // 6. Article / NewsArticle Schema (For Blog Posts)
        if ($model instanceof Post) {
            $schemaType = in_array($model->schema_type, ['NewsArticle', 'BlogPosting', 'Article']) 
                ? $model->schema_type 
                : 'Article';

            $articleSchema = [
                '@type' => $schemaType,
                '@id' => $currentUrl . '/#article',
                'isPartOf' => ['@id' => $siteUrl . '/#website'],
                'headline' => Str::limit($model->title, 110),
                'description' => $data['description'],
                'datePublished' => ($model->published_at ?? $model->created_at)?->toIso8601String(),
                'dateModified' => ($model->updated_at ?? $model->created_at)?->toIso8601String(),
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $currentUrl,
                ],
                'wordCount' => str_word_count(strip_tags($model->content ?? '')),
                'inLanguage' => $data['language'],
                'publisher' => ['@id' => $siteUrl . '/#organization'],
            ];

            if (!empty($data['author'])) {
                $articleSchema['author'] = ['@id' => $siteUrl . '/#/schema/person/' . Str::slug($data['author'])];
            }

            if (!empty($data['image'])) {
                $articleSchema['image'] = ['@id' => $data['image'] . '#primaryimage'];
            }

            if ($model->category) {
                $articleSchema['articleSection'] = [$model->category->name];
            }

            if ($model->tag) {
                $articleSchema['keywords'] = $model->tag->name;
            }

            $graph[] = $articleSchema;

            // 7. FAQPage Schema (if faq_schema array present on post)
            if (!empty($model->faq_schema) && is_array($model->faq_schema)) {
                $faqEntities = [];
                foreach ($model->faq_schema as $faq) {
                    if (!empty($faq['question']) && !empty($faq['answer'])) {
                        $faqEntities[] = [
                            '@type' => 'Question',
                            'name' => $faq['question'],
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => $faq['answer'],
                            ],
                        ];
                    }
                }
                if (!empty($faqEntities)) {
                    $graph[] = [
                        '@type' => 'FAQPage',
                        '@id' => $currentUrl . '/#faq',
                        'mainEntity' => $faqEntities,
                    ];
                }
            }

            // 8. VideoObject Schema (if video_schema present on post)
            if (!empty($model->video_schema) && is_array($model->video_schema) && !empty($model->video_schema['name'])) {
                $graph[] = [
                    '@type' => 'VideoObject',
                    '@id' => $currentUrl . '/#video',
                    'name' => $model->video_schema['name'],
                    'description' => $model->video_schema['description'] ?? $model->title,
                    'thumbnailUrl' => $model->video_schema['thumbnail_url'] ?? $data['image'],
                    'uploadDate' => $model->video_schema['upload_date'] ?? ($model->published_at ?? Carbon::now())->toIso8601String(),
                    'contentUrl' => $model->video_schema['content_url'] ?? null,
                ];
            }
        }

        $json = [
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ];

        return '<script type="application/ld+json">' . "\n" . json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';
    }

    /**
     * Render the combined meta tags, OpenGraph, and JSON-LD schema suite.
     */
    public static function renderAll(Post|Page|null $model = null): string
    {
        return self::renderMeta($model) . "\n  " . self::renderOpenGraph($model) . "\n  " . self::generateJsonLd($model);
    }

    /**
     * Resolve metadata with intelligent cascade from Model -> Site Settings -> Defaults.
     */
    protected static function resolveMetadata(Post|Page|null $model = null): array
    {
        $settings = Setting::getAll();

        $siteName = $settings->get('site_title', $settings->get('gazette_title', config('app.name', 'Xivig Editorial')));
        $siteDesc = $settings->get('seo_meta_description', 'High-fidelity software engineering architectural insights, developer ergonomics, and modern framework patterns.');
        $defaultImage = $settings->get('seo_og_image', asset('admin-dist/img/blog-1-PTDVKJDt.webp'));
        $defaultRobots = $settings->get('seo_robots_default', 'index, follow');
        $defaultLang = $settings->get('site_locale', 'en');
        $defaultRating = $settings->get('seo_rating_default', 'general');
        $defaultCopyright = $settings->get('seo_copyright', 'Copyright © ' . date('Y') . ' ' . $siteName . '. All rights reserved.');
        $logoUrl = $settings->get('seo_schema_logo', asset('admin-dist/img/favicon-DgHNGhkV.ico'));

        // Social handles
        $twitterSite = $settings->get('seo_twitter_site', '@xivigmaison');
        $twitterCreator = $twitterSite;

        $sameAsRaw = $settings->get('seo_schema_same_as', "https://twitter.com/xivigmaison\nhttps://linkedin.com/company/xivigmaison\nhttps://instagram.com/xivigmaison");
        $sameAs = array_values(array_filter(array_map('trim', explode("\n", $sameAsRaw))));

        // Model-specific overrides
        if ($model instanceof Post) {
            $title = !empty($model->meta_title) ? $model->meta_title : ($model->title . ' | ' . $siteName);
            $desc = !empty($model->meta_description) ? $model->meta_description : ($model->excerpt ?: Str::limit(strip_tags($model->content), 160));
            $keywords = !empty($model->meta_keywords) ? $model->meta_keywords : ($model->tag ? $model->tag->name . ', ' . $model->category?->name : 'architecture, cloud, engineering');
            $canonical = !empty($model->canonical_url) ? $model->canonical_url : url('/blog/' . $model->slug);
            $author = !empty($model->meta_author) ? $model->meta_author : ($model->author?->nickname ?: ($model->user?->name ?: 'Xivig Contributor'));
            $robots = !empty($model->meta_robots) ? $model->meta_robots : $defaultRobots;
            $language = !empty($model->meta_language) ? $model->meta_language : $defaultLang;
            $rating = !empty($model->meta_rating) ? $model->meta_rating : $defaultRating;
            $copyright = !empty($model->meta_copyright) ? $model->meta_copyright : $defaultCopyright;

            $image = null;
            if (!empty($model->og_image)) {
                $image = Str::startsWith($model->og_image, ['http://', 'https://']) ? $model->og_image : asset($model->og_image);
            } elseif ($model->image) {
                $link = $model->image->link;
                $image = Str::startsWith($link, ['http://', 'https://']) ? $link : asset($link);
            } else {
                $image = $defaultImage;
            }

            $ogTitle = !empty($model->og_title) ? $model->og_title : $model->title;
            $ogDesc = !empty($model->og_description) ? $model->og_description : $desc;
            $twitterCard = !empty($model->twitter_card) ? $model->twitter_card : 'summary_large_image';

            return [
                'title' => $title,
                'description' => $desc,
                'keywords' => $keywords,
                'canonical' => $canonical,
                'author' => $author,
                'robots' => $robots,
                'language' => $language,
                'geo_region' => $model->geo_region ?? $settings->get('seo_geo_region'),
                'geo_placename' => $model->geo_placename ?? $settings->get('seo_geo_placename'),
                'geo_position' => $model->geo_position ?? $settings->get('seo_geo_position'),
                'rating' => $rating,
                'copyright' => $copyright,
                'og_type' => 'article',
                'og_title' => $ogTitle,
                'og_description' => $ogDesc,
                'image' => $image,
                'twitter_card' => $twitterCard,
                'twitter_site' => $twitterSite,
                'twitter_creator' => $twitterCreator,
                'site_name' => $siteName,
                'site_description' => $siteDesc,
                'logo_url' => $logoUrl,
                'same_as' => $sameAs,
            ];
        }

        if ($model instanceof Page) {
            $title = !empty($model->meta_title) ? $model->meta_title : ($model->title . ' | ' . $siteName);
            $desc = !empty($model->meta_description) ? $model->meta_description : ($model->excerpt ?: Str::limit(strip_tags($model->content), 160));
            $canonical = url('/page/' . $model->slug);

            return [
                'title' => $title,
                'description' => $desc,
                'keywords' => $settings->get('seo_meta_keywords'),
                'canonical' => $canonical,
                'author' => $siteName,
                'robots' => $defaultRobots,
                'language' => $defaultLang,
                'geo_region' => $settings->get('seo_geo_region'),
                'geo_placename' => $settings->get('seo_geo_placename'),
                'geo_position' => $settings->get('seo_geo_position'),
                'rating' => $defaultRating,
                'copyright' => $defaultCopyright,
                'og_type' => 'website',
                'og_title' => $title,
                'og_description' => $desc,
                'image' => $defaultImage,
                'twitter_card' => 'summary_large_image',
                'twitter_site' => $twitterSite,
                'twitter_creator' => $twitterSite,
                'site_name' => $siteName,
                'site_description' => $siteDesc,
                'logo_url' => $logoUrl,
                'same_as' => $sameAs,
            ];
        }

        // Respect per-page Blade @section('title') on non-home static routes (e.g. /project, /about, /services)
        $sectionTitle = trim(app('view')->getSection('title', ''));
        $isNamedNonHome = request()->route()?->getName() !== 'home';
        $resolvedGlobalTitle = ($sectionTitle !== '' && $isNamedNonHome)
            ? $sectionTitle
            : $settings->get('seo_meta_title', $siteName . ' - Elite Editorial Command Center');

        // Global site fallback
        return [
            'title' => $resolvedGlobalTitle,
            'description' => $siteDesc,
            'keywords' => $settings->get('seo_meta_keywords', 'laravel, architecture, engineering, cms'),
            'canonical' => url()->current(),
            'author' => $siteName,
            'robots' => $defaultRobots,
            'language' => $defaultLang,
            'geo_region' => $settings->get('seo_geo_region'),
            'geo_placename' => $settings->get('seo_geo_placename'),
            'geo_position' => $settings->get('seo_geo_position'),
            'rating' => $defaultRating,
            'copyright' => $defaultCopyright,
            'og_type' => 'website',
            'og_title' => $resolvedGlobalTitle,
            'og_description' => $siteDesc,
            'image' => $defaultImage,
            'twitter_card' => 'summary_large_image',
            'twitter_site' => $twitterSite,
            'twitter_creator' => $twitterSite,
            'site_name' => $siteName,
            'site_description' => $siteDesc,
            'logo_url' => $logoUrl,
            'same_as' => $sameAs,
        ];
    }
}
