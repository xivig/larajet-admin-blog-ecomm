@extends('layouts.blog')
@section('title', 'Magazine Portal - Breaking News, Technology & Architecture Insights')
@section('content')
@php
    $fallbackThumb = asset('admin-dist/img/blog-1-PTDVKJDt.webp');
    $resolveImg = fn ($post) => $post?->image?->url ?? $fallbackThumb;
    $resolveCat = fn ($post) => $post?->category?->name ?? 'General';
    $resolveCatId = fn ($post) => $post?->category?->id ?? 1;
    $resolveAuthor = fn ($post) => $post?->author?->user?->name ?? ($post?->user?->name ?? 'Editorial Desk');
    $resolveDate = fn ($post) => $post?->published_at?->format('M d, Y') ?? ($post?->created_at?->format('M d, Y') ?? now()->format('M d, Y'));
    $resolveExcerpt = fn ($post, $limit = 115) => $post?->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) ($post?->content ?? '')), $limit);
@endphp

<!-- Top Breaking News Ticker -->
@if($tickerPosts->isNotEmpty())
<div class="ticker-wrap">
    <div class="container d-flex align-items-center min-w-0">
        <span class="ticker-title bg-danger text-white shadow-sm">
            <i class="fas fa-bolt me-1"></i> Breaking
        </span>
        <div class="ticker-scroll-container">
            <div class="ticker">
                @foreach($tickerPosts->concat($tickerPosts) as $tPost)
                    <a href="{{ route('blog.single-magazine', $tPost->slug) }}" class="ticker-item text-decoration-none d-inline-flex align-items-center gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1" style="font-size: 10px;">
                            {{ $resolveCat($tPost) }}
                        </span>
                        <span class="fw-semibold">{{ \Illuminate\Support\Str::limit($tPost->title, 64) }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<div class="container my-4 my-lg-5">
    <!-- Magazine Edition Masthead & Telemetry Band -->
    <div class="mag-masthead-band mb-4 mb-lg-5">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">
                        <i class="fas fa-newspaper me-1"></i> MAGAZINE EDITION
                    </span>
                    <span class="text-muted small">
                        <i class="far fa-calendar-alt me-1"></i> {{ now()->format('l, M d, Y') }}
                    </span>
                </div>
                <h1 class="h3 fw-bold mb-1 text-body">Xivig Global Dispatch &amp; Editorial Portal</h1>
                <p class="text-muted small mb-0">
                    Curated reporting across engineering, enterprise strategy, visual essays, and deep-dive research.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                    <span class="badge bg-body-secondary text-body border px-3 py-2 rounded-pill small">
                        <i class="fas fa-file-alt text-primary me-1"></i> {{ $magazineStats['published_posts'] ?? 0 }} Stories
                    </span>
                    <span class="badge bg-body-secondary text-body border px-3 py-2 rounded-pill small">
                        <i class="fas fa-layer-group text-success me-1"></i> {{ $magazineStats['active_categories'] ?? 0 }} Channels
                    </span>
                    <a href="{{ route('blog.gallery') }}" class="badge bg-body-secondary text-body border px-3 py-2 rounded-pill small text-decoration-none">
                        <i class="fas fa-images text-info me-1"></i> {{ $magazineStats['gallery_assets'] ?? 0 }} Visuals
                    </a>
                    <a href="{{ route('blog.videos') }}" class="badge bg-body-secondary text-body border px-3 py-2 rounded-pill small text-decoration-none">
                        <i class="fas fa-play-circle text-danger me-1"></i> {{ $magazineStats['video_dispatches'] ?? 0 }} Videos
                    </a>
                    <a href="{{ route('blog.list') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                        All Stories <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Hero / Leading Showcase (3-Column: Left 2 + Center Carousel + Right 2) -->
    <section class="mb-5 fade-in-section" data-mag-section="hero">
        <div class="row g-4 align-items-stretch">
            <!-- Left Stack -->
            <div class="col-lg-3 d-flex flex-column gap-4">
                @foreach($heroLeft as $post)
                    <article class="mag-card flex-fill d-flex flex-column">
                        <div class="card-img-wrapper" style="height: 175px;">
                            @if($post->is_featured || $post->is_sticky)
                                <span class="badge bg-warning text-dark position-absolute shadow-sm" style="top: 10px; right: 10px; z-index: 4; font-size: 10px; font-weight: 700;">
                                    <i class="fas fa-star me-1"></i> Featured
                                </span>
                            @endif
                            <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="badge-category text-decoration-none">
                                {{ \Illuminate\Support\Str::limit($resolveCat($post), 18) }}
                            </a>
                            <img src="{{ $resolveImg($post) }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
                            <h3 class="h6 fw-bold mb-2 lh-sm mag-line-clamp-2">
                                <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 11.5px;">
                                <span><i class="far fa-clock me-1"></i> {{ $resolveDate($post) }}</span>
                                <span><i class="far fa-eye me-1"></i> {{ number_format($post->views_count ?? 0) }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Center Hero Carousel -->
            <div class="col-lg-6">
                @if($heroCenter->isNotEmpty())
                <div id="heroMagCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="5500">
                    @if($heroCenter->count() > 1)
                    <div class="carousel-indicators mb-2">
                        @foreach($heroCenter as $idx => $cPost)
                            <button type="button" data-bs-target="#heroMagCarousel" data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}" aria-label="Slide {{ $idx + 1 }}"></button>
                        @endforeach
                    </div>
                    @endif
                    <div class="carousel-inner h-100 rounded-4 overflow-hidden shadow-sm">
                        @foreach($heroCenter as $idx => $cPost)
                            <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}">
                                <article class="mag-card h-100 d-flex flex-column border-0">
                                    <div class="card-img-wrapper" style="height: 315px;">
                                        <span class="badge bg-dark bg-opacity-75 text-white position-absolute" style="top: 12px; right: 12px; z-index: 4; font-size: 11px;">
                                            <i class="fas fa-book-open me-1"></i> {{ $cPost->reading_time ?? 3 }}m read
                                        </span>
                                        <a href="{{ route('blog.category', $resolveCatId($cPost)) }}" class="badge-category text-decoration-none">
                                            {{ \Illuminate\Support\Str::limit($resolveCat($cPost), 22) }}
                                        </a>
                                        <img src="{{ $resolveImg($cPost) }}" alt="{{ $cPost->title }}" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div class="p-4 flex-grow-1 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="text-muted small mb-2 d-flex flex-wrap gap-3">
                                                <span><i class="far fa-user me-1 text-primary"></i> {{ $resolveAuthor($cPost) }}</span>
                                                <span><i class="far fa-calendar-alt me-1 text-primary"></i> {{ $resolveDate($cPost) }}</span>
                                                <span><i class="far fa-eye me-1 text-primary"></i> {{ number_format($cPost->views_count ?? 0) }} views</span>
                                            </div>
                                            <h2 class="h4 fw-bold mb-2 lh-sm">
                                                <a href="{{ route('blog.single-magazine', $cPost->slug) }}" class="text-body text-decoration-none">
                                                    {{ $cPost->title }}
                                                </a>
                                            </h2>
                                            <p class="text-muted small mb-0 mag-line-clamp-2">{{ $resolveExcerpt($cPost, 145) }}</p>
                                        </div>
                                        <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                                            <a href="{{ route('blog.single-magazine', $cPost->slug) }}" class="text-primary fw-semibold small text-decoration-none">
                                                Read Full Story <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                            @if($cPost->video_url)
                                                <a href="{{ route('blog.video', $cPost->slug) }}" class="badge bg-danger bg-opacity-10 text-danger text-decoration-none">
                                                    <i class="fas fa-play me-1"></i> Video Inside
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                    @if($heroCenter->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroMagCarousel" data-bs-slide="prev" style="width: 40px; height: 40px; background: rgba(15,23,42,0.55); border-radius: 50%; top: 145px; left: 14px;">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="width: 18px;"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroMagCarousel" data-bs-slide="next" style="width: 40px; height: 40px; background: rgba(15,23,42,0.55); border-radius: 50%; top: 145px; right: 14px;">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="width: 18px;"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    @endif
                </div>
                @endif
            </div>

            <!-- Right Stack -->
            <div class="col-lg-3 d-flex flex-column gap-4">
                @foreach($heroRight as $post)
                    <article class="mag-card flex-fill d-flex flex-column">
                        <div class="card-img-wrapper" style="height: 175px;">
                            <span class="badge bg-danger bg-opacity-90 text-white position-absolute shadow-sm" style="top: 10px; right: 10px; z-index: 4; font-size: 10px; font-weight: 700;">
                                <i class="fas fa-fire me-1"></i> Trending
                            </span>
                            <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="badge-category text-decoration-none">
                                {{ \Illuminate\Support\Str::limit($resolveCat($post), 18) }}
                            </a>
                            <img src="{{ $resolveImg($post) }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
                            <h3 class="h6 fw-bold mb-2 lh-sm mag-line-clamp-2">
                                <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 11.5px;">
                                <span><i class="far fa-clock me-1"></i> {{ $resolveDate($post) }}</span>
                                <span><i class="far fa-eye me-1"></i> {{ number_format($post->views_count ?? 0) }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 2. Popular Stories (4-Column Grid) -->
    <section class="mb-5 fade-in-section" data-mag-section="popular">
        <div class="mag-section-heading">
            <h4 class="mag-section-title text-body">
                <i class="fas fa-fire text-danger"></i> Popular Stories
            </h4>
            <a href="{{ route('blog.list') }}" class="text-primary small fw-semibold text-decoration-none">
                View Archive <i class="fas fa-angle-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3">
            @foreach($popularStories as $post)
                <div class="col-sm-6 col-lg-3">
                    <article class="mag-card h-100 d-flex flex-column">
                        <div class="card-img-wrapper" style="height: 170px;">
                            <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="badge-category text-decoration-none">
                                {{ \Illuminate\Support\Str::limit($resolveCat($post), 18) }}
                            </a>
                            <img src="{{ $resolveImg($post) }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                            <h3 class="h6 fw-bold mb-2 lh-sm mag-line-clamp-2">
                                <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <div class="text-muted d-flex justify-content-between align-items-center" style="font-size: 12px;">
                                <span><i class="far fa-calendar-alt me-1"></i> {{ $resolveDate($post) }}</span>
                                <span><i class="far fa-eye me-1"></i> {{ number_format($post->views_count ?? 0) }}</span>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 3. Recent Dispatches (4-Column Grid) -->
    <section class="mb-5 fade-in-section" data-mag-section="recent">
        <div class="mag-section-heading">
            <h4 class="mag-section-title text-body">
                <i class="fas fa-clock text-primary"></i> Recent Dispatches
            </h4>
            <a href="{{ route('blog.feed') }}" class="text-primary small fw-semibold text-decoration-none">
                Live Feed <i class="fas fa-angle-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3">
            @foreach($recentStories as $post)
                <div class="col-sm-6 col-lg-3">
                    <article class="mag-card h-100 d-flex flex-column">
                        <div class="card-img-wrapper" style="height: 170px;">
                            <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="badge-category text-decoration-none">
                                {{ \Illuminate\Support\Str::limit($resolveCat($post), 18) }}
                            </a>
                            <img src="{{ $resolveImg($post) }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                            <h3 class="h6 fw-bold mb-2 lh-sm mag-line-clamp-2">
                                <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <div class="text-muted d-flex justify-content-between align-items-center" style="font-size: 12px;">
                                <span><i class="far fa-user me-1"></i> {{ \Illuminate\Support\Str::limit($resolveAuthor($post), 14) }}</span>
                                <span><i class="far fa-clock me-1"></i> {{ $post->reading_time ?? 3 }}m read</span>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Main Split Column (8 cols Editorial Sections + 4 cols Sticky Sidebar) -->
    <div class="row g-4">
        <div class="col-lg-8 min-w-0">

            <!-- 4. Featured Block Posts (2x2 = 4 Cards) -->
            <section class="mb-5 fade-in-section" data-mag-section="block-posts">
                <div class="mag-section-heading">
                    <h4 class="mag-section-title text-body">
                        <i class="fas fa-th-large text-primary"></i> Featured Block Posts
                    </h4>
                    <span class="text-muted small">2x2 Editorial Grid</span>
                </div>
                <div class="row g-4">
                    @foreach($blockPosts as $post)
                        <div class="col-md-6">
                            <article class="mag-card h-100 d-flex flex-column">
                                <div class="card-img-wrapper" style="height: 210px;">
                                    <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="badge-category text-decoration-none">
                                        {{ \Illuminate\Support\Str::limit($resolveCat($post), 20) }}
                                    </a>
                                    <img src="{{ $resolveImg($post) }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="p-4 flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="text-muted small mb-2 d-flex flex-wrap gap-3">
                                            <span><i class="far fa-clock me-1 text-primary"></i> {{ $resolveDate($post) }}</span>
                                            <span><i class="fas fa-book-reader me-1 text-primary"></i> {{ $post->reading_time ?? 3 }} min</span>
                                        </div>
                                        <h3 class="h5 fw-bold mb-2 lh-sm">
                                            <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                                {{ $post->title }}
                                            </a>
                                        </h3>
                                        <p class="text-muted small mb-0 mag-line-clamp-2">{{ $resolveExcerpt($post, 120) }}</p>
                                    </div>
                                    <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small">
                                        <span class="text-muted"><i class="far fa-user me-1"></i> {{ $resolveAuthor($post) }}</span>
                                        <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-primary fw-semibold text-decoration-none">
                                            Explore <i class="fas fa-chevron-right ms-1" style="font-size: 10px;"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- 5. Analysis Matrix & Dynamic Visual Briefs (6+6 Split) -->
            <section class="mb-5 fade-in-section" data-mag-section="matrix-visuals">
                <div class="row g-4">
                    <!-- Analysis Matrix -->
                    <div class="col-md-6 min-w-0">
                        <div class="mag-section-heading">
                            <h4 class="mag-section-title text-body">
                                <i class="fas fa-chart-line text-success"></i> Analysis Matrix
                            </h4>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            @foreach($matrixPosts as $post)
                                <article class="mag-list-row">
                                    <img src="{{ $resolveImg($post) }}" class="mag-list-thumb" alt="{{ $post->title }}">
                                    <div class="mag-list-body">
                                        <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none mb-1" style="font-size: 10px;">
                                            {{ \Illuminate\Support\Str::limit($resolveCat($post), 20) }}
                                        </a>
                                        <h5 class="h6 fw-bold mb-1 mag-line-clamp-2" style="font-size: 13.5px;">
                                            <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                                {{ $post->title }}
                                            </a>
                                        </h5>
                                        <span class="text-muted" style="font-size: 11.5px;">
                                            <i class="far fa-calendar-alt me-1"></i> {{ $resolveDate($post) }}
                                        </span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dynamic Visual Briefs Gallery -->
                    <div class="col-md-6 min-w-0">
                        <div class="mag-section-heading">
                            <h4 class="mag-section-title text-body">
                                <i class="fas fa-camera text-info"></i> Visual Briefs
                            </h4>
                            <a href="{{ route('blog.gallery') }}" class="text-primary small fw-semibold text-decoration-none">
                                Gallery <i class="fas fa-external-link-alt ms-1" style="font-size: 10px;"></i>
                            </a>
                        </div>
                        <div class="row g-2">
                            @foreach($visualBriefs as $brief)
                                <div class="col-6">
                                    <a href="{{ $brief['link'] }}" class="mag-visual-tile text-decoration-none">
                                        <img src="{{ $brief['url'] }}" alt="{{ $brief['title'] }}">
                                        <div class="mag-visual-scrim">
                                            <span class="text-info fw-semibold" style="font-size: 10px; letter-spacing: 0.4px; text-transform: uppercase;">
                                                {{ \Illuminate\Support\Str::limit($brief['subtitle'], 20) }}
                                            </span>
                                            <span class="text-white fw-bold mag-line-clamp-2" style="font-size: 12px; line-height: 1.25;">
                                                {{ \Illuminate\Support\Str::limit($brief['title'], 36) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <!-- 6. Special Feature Block Post (1 Flagship 21:9 + 2 Sub Cards) -->
            @if($spotlightMain)
            <section class="mb-5 fade-in-section" data-mag-section="spotlight">
                <div class="mag-section-heading">
                    <h4 class="mag-section-title text-body">
                        <i class="fas fa-award text-warning"></i> Editorial Spotlight
                    </h4>
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1" style="font-size: 11px;">Flagship Feature</span>
                </div>
                <div class="row g-4">
                    <div class="col-12">
                        <article class="mag-card">
                            <div class="mag-spotlight-media card-img-wrapper">
                                <a href="{{ route('blog.category', $resolveCatId($spotlightMain)) }}" class="badge-category text-decoration-none">
                                    {{ $resolveCat($spotlightMain) }}
                                </a>
                                <img src="{{ $resolveImg($spotlightMain) }}" alt="{{ $spotlightMain->title }}">
                            </div>
                            <div class="p-4">
                                <div class="text-muted small mb-2 d-flex flex-wrap gap-3">
                                    <span><i class="far fa-user me-1 text-primary"></i> {{ $resolveAuthor($spotlightMain) }}</span>
                                    <span><i class="far fa-clock me-1 text-primary"></i> {{ $spotlightMain->reading_time ?? 4 }} min read</span>
                                    <span><i class="far fa-eye me-1 text-primary"></i> {{ number_format($spotlightMain->views_count ?? 0) }} views</span>
                                </div>
                                <h3 class="h3 fw-bold mb-2">
                                    <a href="{{ route('blog.single-magazine', $spotlightMain->slug) }}" class="text-body text-decoration-none">
                                        {{ $spotlightMain->title }}
                                    </a>
                                </h3>
                                <p class="text-muted mb-0">{{ $resolveExcerpt($spotlightMain, 190) }}</p>
                            </div>
                        </article>
                    </div>
                    @foreach($spotlightSub as $subPost)
                        <div class="col-md-6">
                            <article class="mag-card h-100 d-flex flex-column">
                                <div class="card-img-wrapper" style="height: 175px;">
                                    <a href="{{ route('blog.category', $resolveCatId($subPost)) }}" class="badge-category text-decoration-none">
                                        {{ \Illuminate\Support\Str::limit($resolveCat($subPost), 18) }}
                                    </a>
                                    <img src="{{ $resolveImg($subPost) }}" alt="{{ $subPost->title }}" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                                    <h4 class="h6 fw-bold mb-2 mag-line-clamp-2">
                                        <a href="{{ route('blog.single-magazine', $subPost->slug) }}" class="text-body text-decoration-none">
                                            {{ $subPost->title }}
                                        </a>
                                    </h4>
                                    <span class="text-muted" style="font-size: 12px;">
                                        <i class="far fa-calendar-alt me-1"></i> {{ $resolveDate($subPost) }}
                                    </span>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- 7. Two-Column Dispatches (Column Post Layout) -->
            <section class="mb-5 fade-in-section" data-mag-section="column-posts">
                <div class="mag-section-heading">
                    <h4 class="mag-section-title text-body">
                        <i class="fas fa-columns text-primary"></i> Column Dispatches
                    </h4>
                    <span class="text-muted small">Opinion &amp; Briefings</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 d-flex flex-column gap-3 min-w-0">
                        @foreach($columnLeft as $post)
                            <article class="mag-list-row">
                                <img src="{{ $resolveImg($post) }}" class="mag-list-thumb" alt="{{ $post->title }}">
                                <div class="mag-list-body">
                                    <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="text-primary fw-semibold text-decoration-none d-block mb-1" style="font-size: 11px;">
                                        {{ \Illuminate\Support\Str::limit($resolveCat($post), 22) }}
                                    </a>
                                    <h5 class="h6 fw-bold mb-1 mag-line-clamp-2" style="font-size: 13.5px;">
                                        <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    <span class="text-muted" style="font-size: 11.5px;">{{ $resolveDate($post) }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="col-md-6 d-flex flex-column gap-3 min-w-0">
                        @foreach($columnRight as $post)
                            <article class="mag-list-row">
                                <img src="{{ $resolveImg($post) }}" class="mag-list-thumb" alt="{{ $post->title }}">
                                <div class="mag-list-body">
                                    <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="text-primary fw-semibold text-decoration-none d-block mb-1" style="font-size: 11px;">
                                        {{ \Illuminate\Support\Str::limit($resolveCat($post), 22) }}
                                    </a>
                                    <h5 class="h6 fw-bold mb-1 mag-line-clamp-2" style="font-size: 13.5px;">
                                        <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    <span class="text-muted" style="font-size: 11.5px;">{{ $resolveDate($post) }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- 8. Deep-Dive Dispatch (Grid Post: 1 Large Left + 4 Stacked Right) -->
            @if($gridHero)
            <section class="mb-5 fade-in-section" data-mag-section="deep-dive">
                <div class="mag-section-heading">
                    <h4 class="mag-section-title text-body">
                        <i class="fas fa-newspaper text-warning"></i> Deep-Dive Dispatch
                    </h4>
                    <span class="text-muted small">Lead Brief &amp; Follow-Ups</span>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <article class="mag-card h-100 d-flex flex-column">
                            <div class="card-img-wrapper" style="height: 240px;">
                                <a href="{{ route('blog.category', $resolveCatId($gridHero)) }}" class="badge-category text-decoration-none">
                                    {{ \Illuminate\Support\Str::limit($resolveCat($gridHero), 20) }}
                                </a>
                                <img src="{{ $resolveImg($gridHero) }}" alt="{{ $gridHero->title }}" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="p-4 flex-grow-1 d-flex flex-column justify-content-between">
                                <div>
                                    <h3 class="h5 fw-bold mb-2 lh-sm">
                                        <a href="{{ route('blog.single-magazine', $gridHero->slug) }}" class="text-body text-decoration-none">
                                            {{ $gridHero->title }}
                                        </a>
                                    </h3>
                                    <p class="text-muted small mb-0">{{ $resolveExcerpt($gridHero, 140) }}</p>
                                </div>
                                <div class="mt-3 pt-2 border-top d-flex justify-content-between text-muted small">
                                    <span><i class="far fa-user me-1"></i> {{ $resolveAuthor($gridHero) }}</span>
                                    <span><i class="far fa-calendar-alt me-1"></i> {{ $resolveDate($gridHero) }}</span>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div class="col-lg-6 d-flex flex-column gap-3 min-w-0">
                        @foreach($gridStack as $post)
                            <article class="mag-list-row">
                                <img src="{{ $resolveImg($post) }}" class="mag-list-thumb" alt="{{ $post->title }}">
                                <div class="mag-list-body">
                                    <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="text-primary fw-semibold text-decoration-none d-block mb-1" style="font-size: 11px;">
                                        {{ \Illuminate\Support\Str::limit($resolveCat($post), 22) }}
                                    </a>
                                    <h5 class="h6 fw-bold mb-1 mag-line-clamp-2" style="font-size: 13.5px;">
                                        <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    <span class="text-muted" style="font-size: 11.5px;">{{ $resolveDate($post) }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            <!-- 9. Business & Commerce Showcase (Layout A: Full-Width Block + 3-Col List Grid) -->
            @if($businessHero)
            <section class="mb-5 fade-in-section" data-mag-section="business">
                <div class="mag-section-heading">
                    <h4 class="mag-section-title text-body">
                        <i class="fas fa-briefcase text-success"></i> Business &amp; Markets Showcase
                    </h4>
                    <a href="{{ route('blog.category', $resolveCatId($businessHero)) }}" class="text-primary small fw-semibold text-decoration-none">
                        Browse Sector <i class="fas fa-angle-right ms-1"></i>
                    </a>
                </div>
                <article class="mag-card mb-4">
                    <div class="mag-spotlight-media card-img-wrapper">
                        <a href="{{ route('blog.category', $resolveCatId($businessHero)) }}" class="badge-category text-decoration-none">
                            {{ $resolveCat($businessHero) }}
                        </a>
                        <img src="{{ $resolveImg($businessHero) }}" alt="{{ $businessHero->title }}">
                    </div>
                    <div class="p-4">
                        <div class="text-muted small mb-2 d-flex flex-wrap gap-3">
                            <span><i class="far fa-clock me-1 text-success"></i> {{ $resolveDate($businessHero) }}</span>
                            <span><i class="far fa-user me-1 text-success"></i> {{ $resolveAuthor($businessHero) }}</span>
                            <span><i class="far fa-comment-alt me-1 text-success"></i> {{ $businessHero->comments()->count() }} Comments</span>
                        </div>
                        <h3 class="h4 fw-bold mb-2">
                            <a href="{{ route('blog.single-magazine', $businessHero->slug) }}" class="text-body text-decoration-none">
                                {{ $businessHero->title }}
                            </a>
                        </h3>
                        <p class="text-muted small mb-0">{{ $resolveExcerpt($businessHero, 170) }}</p>
                    </div>
                </article>

                <div class="row g-3">
                    @foreach($businessPosts as $post)
                        <div class="col-md-4 min-w-0">
                            <article class="mag-list-row h-100 align-items-start">
                                <img src="{{ $resolveImg($post) }}" class="mag-list-thumb" style="width: 68px; height: 68px;" alt="{{ $post->title }}">
                                <div class="mag-list-body">
                                    <h5 class="h6 fw-bold mb-1 mag-line-clamp-2" style="font-size: 13px;">
                                        <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    <span class="text-muted d-block" style="font-size: 11px;">
                                        <i class="far fa-calendar-alt me-1"></i> {{ $resolveDate($post) }}
                                    </span>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- 10. Technology Intelligence (Layout B: 2 Block Posts + 2-Column 4-Item List) -->
            <section class="mb-5 fade-in-section" data-mag-section="technology">
                <div class="mag-section-heading">
                    <h4 class="mag-section-title text-body">
                        <i class="fas fa-microchip text-primary"></i> Technology Intelligence
                    </h4>
                    <a href="{{ route('blog.list') }}" class="text-primary small fw-semibold text-decoration-none">
                        All Tech Stories <i class="fas fa-angle-right ms-1"></i>
                    </a>
                </div>
                <div class="row g-4 mb-4">
                    @foreach($techPosts->take(2) as $post)
                        <div class="col-md-6">
                            <article class="mag-card h-100 d-flex flex-column">
                                <div class="card-img-wrapper" style="height: 195px;">
                                    <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="badge-category text-decoration-none">
                                        {{ \Illuminate\Support\Str::limit($resolveCat($post), 18) }}
                                    </a>
                                    <img src="{{ $resolveImg($post) }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="p-3 p-lg-4 flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="text-muted small mb-1">
                                            <i class="far fa-clock me-1 text-primary"></i> {{ $resolveDate($post) }}
                                        </div>
                                        <h4 class="h5 fw-bold mb-2 lh-sm">
                                            <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                                {{ $post->title }}
                                            </a>
                                        </h4>
                                        <p class="text-muted small mb-0 mag-line-clamp-2">{{ $resolveExcerpt($post, 105) }}</p>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
                @if($techPosts->count() > 2)
                <div class="row g-3">
                    @foreach($techPosts->slice(2, 4) as $post)
                        <div class="col-md-6 min-w-0">
                            <article class="mag-list-row">
                                <img src="{{ $resolveImg($post) }}" class="mag-list-thumb" alt="{{ $post->title }}">
                                <div class="mag-list-body">
                                    <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="text-primary fw-semibold text-decoration-none d-block mb-1" style="font-size: 11px;">
                                        {{ \Illuminate\Support\Str::limit($resolveCat($post), 22) }}
                                    </a>
                                    <h5 class="h6 fw-bold mb-1 mag-line-clamp-2" style="font-size: 13.5px;">
                                        <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    <span class="text-muted" style="font-size: 11.5px;">{{ $resolveDate($post) }}</span>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
                @endif
            </section>

            <!-- 11. Video & Multimedia Dispatches -->
            <section class="mb-5 fade-in-section" data-mag-section="videos">
                <div class="mag-section-heading">
                    <h4 class="mag-section-title text-body">
                        <i class="fas fa-video text-danger"></i> Video &amp; Multimedia Briefings
                    </h4>
                    <a href="{{ route('blog.videos') }}" class="text-primary small fw-semibold text-decoration-none">
                        Video Studio <i class="fas fa-angle-right ms-1"></i>
                    </a>
                </div>
                <div class="row g-3">
                    @foreach($videoPosts as $post)
                        <div class="col-md-4">
                            <article class="mag-card h-100 d-flex flex-column">
                                <a href="{{ $post->video_url ? route('blog.video', $post->slug) : route('blog.single-magazine', $post->slug) }}" class="card-img-wrapper d-block" style="height: 165px;">
                                    <span class="badge-category">{{ \Illuminate\Support\Str::limit($resolveCat($post), 16) }}</span>
                                    <span class="mag-video-play"><i class="fas fa-play"></i></span>
                                    <img src="{{ $resolveImg($post) }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                                </a>
                                <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                                    <h5 class="h6 fw-bold mb-2 mag-line-clamp-2" style="font-size: 13.5px;">
                                        <a href="{{ $post->video_url ? route('blog.video', $post->slug) : route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    <div class="d-flex justify-content-between text-muted" style="font-size: 11.5px;">
                                        <span><i class="far fa-clock me-1"></i> {{ $post->reading_time ?? 3 }}m watch</span>
                                        <span class="text-danger fw-semibold"><i class="fas fa-play-circle me-1"></i> Watch</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- 12. Editor's Picks ("You Missed") -->
            <section class="mb-5 fade-in-section" data-mag-section="editors-picks">
                <div class="mag-section-heading">
                    <h4 class="mag-section-title text-body">
                        <i class="fas fa-bookmark text-info"></i> Editor's Picks (In Case You Missed)
                    </h4>
                    <a href="{{ route('blog.tags') }}" class="text-primary small fw-semibold text-decoration-none">
                        Browse Tags <i class="fas fa-angle-right ms-1"></i>
                    </a>
                </div>
                <div class="row g-3">
                    @foreach($editorsPicks as $post)
                        <div class="col-sm-6 col-md-3">
                            <article class="mag-card h-100 d-flex flex-column">
                                <div class="card-img-wrapper" style="height: 145px;">
                                    <a href="{{ route('blog.category', $resolveCatId($post)) }}" class="badge-category text-decoration-none">
                                        {{ \Illuminate\Support\Str::limit($resolveCat($post), 14) }}
                                    </a>
                                    <img src="{{ $resolveImg($post) }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                                    <h5 class="h6 fw-bold mb-2 mag-line-clamp-2" style="font-size: 13px;">
                                        <a href="{{ route('blog.single-magazine', $post->slug) }}" class="text-body text-decoration-none">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    <span class="text-muted" style="font-size: 11px;">
                                        <i class="far fa-calendar-alt me-1"></i> {{ $resolveDate($post) }}
                                    </span>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- 13. Explore Topics & Channels Directory -->
            @if($featuredCategories->isNotEmpty())
            <section class="mb-4 fade-in-section" data-mag-section="channels">
                <div class="mag-section-heading">
                    <h4 class="mag-section-title text-body">
                        <i class="fas fa-compass text-primary"></i> Explore Topic Channels
                    </h4>
                    <span class="text-muted small">Top Active Desks</span>
                </div>
                <div class="row g-3">
                    @foreach($featuredCategories as $cat)
                        <div class="col-sm-6 col-md-4 col-xl-3 min-w-0">
                            <a href="{{ route('blog.category', $cat->id) }}" class="mag-channel-card text-body">
                                <div class="min-w-0">
                                    <div class="fw-bold text-truncate" style="font-size: 13px;">{{ $cat->name }}</div>
                                    <div class="text-muted" style="font-size: 11px;">View Desk Archive</div>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill flex-shrink-0">
                                    {{ $cat->posts_count }}
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

        </div>

        <!-- Sticky Magazine Sidebar (Categories strictly <= 10, Tags strictly <= 25) -->
        <div class="col-lg-4">
            <div class="position-sticky" style="top: 90px;">
                @include('blog.partials.sidebar')
            </div>
        </div>
    </div>
</div>
@endsection
