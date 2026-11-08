@php
    $settings = \App\Models\Setting::getAll();

    $siteTitle = $settings->get('gazette_title', 'XIVIG Maison Gazette');
    $separator = $settings->get('seo_title_separator', '—');
    $defaultMetaTitle = $settings->get('seo_meta_title', $siteTitle . ' ' . $separator . ' ' . $settings->get('gazette_tagline', 'The Haute Couture & Horology Journal'));
    $defaultDescription = $settings->get('seo_meta_description', $settings->get('editorial_synopsis', 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics.'));
    $keywords = $settings->get('seo_meta_keywords', 'luxury, horology, haute couture, watches, bespoke, architecture, editorial');
    $author = $settings->get('seo_author', 'XIVIG Editorial Board');
    
    // Page-specific overrides
    if (isset($post) && $post instanceof \App\Models\Post) {
        $metaTitle = $post->title . ' ' . $separator . ' ' . $siteTitle;
        $description = $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 160);
        $canonicalUrl = url('/blog/' . $post->slug);
        $ogImage = $post->picture ? url($post->picture->path) : ($post->featured_image ? url($post->featured_image) : $settings->get('seo_og_image', url('/xivig-blog/images/blog/blog-post-1.jpg')));
        $ogType = 'article';
    } elseif (isset($category) && $category instanceof \App\Models\Category) {
        $metaTitle = $category->name . ' ' . $separator . ' ' . $siteTitle;
        $description = 'Explore curated editorial stories and articles filed under ' . $category->name . '.';
        $canonicalUrl = url('/blog?category=' . $category->slug);
        $ogImage = $settings->get('seo_og_image', url('/xivig-blog/images/blog/blog-post-1.jpg'));
        $ogType = 'website';
    } else {
        $metaTitle = $defaultMetaTitle;
        $description = $defaultDescription;
        $canonicalUrl = $settings->get('seo_canonical_url', url()->current());
        $ogImage = $settings->get('seo_og_image', url('/xivig-blog/images/blog/blog-post-1.jpg'));
        $ogType = $settings->get('seo_og_type', 'website');
    }

    // Robots meta directive
    $robotsIndex = $settings->get('seo_robots_index', 'index');
    $robotsFollow = $settings->get('seo_robots_follow', 'follow');
    $robotsAdvanced = $settings->get('seo_robots_advanced', 'max-snippet:-1, max-image-preview:large, max-video-preview:-1');
    $robotsCombined = trim($robotsIndex . ', ' . $robotsFollow . ($robotsAdvanced ? ', ' . $robotsAdvanced : ''), ', ');

    // Social & Twitter
    $ogLocale = $settings->get('seo_og_locale', 'en_US');
    $ogSiteName = $settings->get('seo_og_site_name', $siteTitle);
    $twitterCard = $settings->get('seo_twitter_card', 'summary_large_image');
    $twitterSite = $settings->get('seo_twitter_site', '@xivigmaison');
    $twitterCreator = $settings->get('seo_twitter_creator', '@xivigmaison');

    // Webmaster verifications
    $googleVerify = $settings->get('seo_google_verification');
    $bingVerify = $settings->get('seo_bing_verification');
    $yandexVerify = $settings->get('seo_yandex_verification');
    $pinterestVerify = $settings->get('seo_pinterest_verification');

    // Schema.org
    $schemaType = $settings->get('seo_schema_type', 'NewsMediaOrganization');
    $schemaName = $settings->get('seo_schema_name', $siteTitle);
    $schemaLogo = $settings->get('seo_schema_logo', url('/admin-dist/img/favicon-DgHNGhkV.ico'));
    $sameAsRaw = $settings->get('seo_schema_same_as', '');
    $sameAsList = array_values(array_filter(array_map('trim', explode("\n", $sameAsRaw))));
@endphp

<!-- Primary SEO Meta Tags -->
<title>{{ $metaTitle }}</title>
<meta name="title" content="{{ $metaTitle }}">
<meta name="description" content="{{ $description }}">
@if($keywords)
<meta name="keywords" content="{{ $keywords }}">
@endif
<meta name="author" content="{{ $author }}">
<meta name="robots" content="{{ $robotsCombined }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<!-- Open Graph / Facebook / LinkedIn -->
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $description }}">
@if($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:secure_url" content="{{ $ogImage }}">
@endif
<meta property="og:site_name" content="{{ $ogSiteName }}">
<meta property="og:locale" content="{{ $ogLocale }}">

<!-- Twitter / X Cards -->
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:url" content="{{ $canonicalUrl }}">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $description }}">
@if($ogImage)
<meta name="twitter:image" content="{{ $ogImage }}">
@endif
@if($twitterSite)
<meta name="twitter:site" content="{{ $twitterSite }}">
@endif
@if($twitterCreator)
<meta name="twitter:creator" content="{{ $twitterCreator }}">
@endif

<!-- Search Console & Webmaster Verification -->
@if($googleVerify)
  @if(str_contains($googleVerify, 'google-site-verification='))
    <meta name="google-site-verification" content="{{ str_replace('google-site-verification=', '', $googleVerify) }}">
  @else
    <meta name="google-site-verification" content="{{ $googleVerify }}">
  @endif
@endif

@if($bingVerify)
  @if(str_contains($bingVerify, 'msvalidate.01='))
    <meta name="msvalidate.01" content="{{ str_replace('msvalidate.01=', '', $bingVerify) }}">
  @else
    <meta name="msvalidate.01" content="{{ $bingVerify }}">
  @endif
@endif

@if($yandexVerify)
<meta name="yandex-verification" content="{{ $yandexVerify }}">
@endif

@if($pinterestVerify)
<meta name="p:domain_verify" content="{{ $pinterestVerify }}">
@endif

<!-- Structured Data (JSON-LD) -->
@if(isset($post) && $post instanceof \App\Models\Post)
@php
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post->title,
        'description' => $description,
        'image' => $ogImage,
        'datePublished' => $post->created_at ? $post->created_at->toIso8601String() : now()->toIso8601String(),
        'dateModified' => $post->updated_at ? $post->updated_at->toIso8601String() : now()->toIso8601String(),
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $canonicalUrl,
        ],
        'author' => [
            '@type' => 'Person',
            'name' => $post->user ? $post->user->name : $author,
        ],
        'publisher' => [
            '@type' => $schemaType,
            'name' => $schemaName,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $schemaLogo,
            ],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($articleSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
</script>
@else
@php
    $orgSchema = [
        '@context' => 'https://schema.org',
        '@type' => $schemaType,
        'name' => $schemaName,
        'url' => url('/'),
        'logo' => $schemaLogo,
        'sameAs' => $sameAsList,
    ];
    $websiteSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'url' => url('/'),
        'name' => $siteTitle,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => url('/blog-list?search={search_term_string}'),
            'query-input' => 'required name=search_term_string',
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($orgSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
</script>
<script type="application/ld+json">
{!! json_encode($websiteSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif
