@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', 'Magazine Portal - Breaking News, Technology & Architecture Insights')
@section('content')

<!-- Breaking News Ticker -->
@if(isset($tickerPosts) && $tickerPosts->count() > 0)
<div class="ticker-wrap shadow-sm">
    <div class="container d-flex align-items-center h-100">
        <div class="ticker-title bg-danger text-white font-weight-bold">
            <i class="fas fa-bolt mr-1"></i> News Flash
        </div>
        <div class="ticker-scroll-container">
            <div class="ticker" id="breakingTicker">
                @foreach($tickerPosts as $tPost)
                    @php
                        $tImg = $tPost->image->link ?? 'dist-front/images/block-sub.jpg';
                        $tSrc = Str::startsWith($tImg, ['http://', 'https://']) ? $tImg : asset($tImg);
                    @endphp
                    <span class="ticker-item d-inline-flex align-items-center" style="gap: 8px;">
                        <img src="{{ $tSrc }}" alt="Thumbnail" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;" onerror="this.src='{{ asset('dist-front/images/block-sub.jpg') }}'">
                        <a href="{{ route('single-post', $tPost->slug) }}" class="text-reset text-decoration-none font-weight-bold">
                            {{ $tPost->title }}
                        </a>
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<main class="container my-5" style="padding-top: 10px;">
    <!-- 1. Hero / Leading Section (Left Stack + Center Carousel + Right Stack) -->
    <section class="mb-5 fade-in-section">
        <div class="row g-4">
            <!-- Left Stack (2 vertical cards) -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="d-flex flex-column h-100" style="gap: 1.5rem;">
                    @foreach($heroLeft as $hlPost)
                        @php
                            $hlImg = $hlPost->image->link ?? 'dist-front/images/block-sub.jpg';
                            $hlSrc = Str::startsWith($hlImg, ['http://', 'https://']) ? $hlImg : asset($hlImg);
                        @endphp
                        <article class="mag-card flex-fill shadow-sm border-0 rounded-4 overflow-hidden h-100">
                            <div class="card-img-wrapper position-relative" style="height: 180px;">
                                <span class="badge-category">{{ $hlPost->category->name ?? 'Featured' }}</span>
                                <a href="{{ route('single-post', $hlPost->slug) }}">
                                    <img src="{{ $hlSrc }}" alt="{{ $hlPost->title }}" class="img-aspect w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('dist-front/images/block-sub.jpg') }}'">
                                </a>
                            </div>
                            <div class="card-body-custom p-3">
                                <div class="meta-text small text-muted mb-1">
                                    <span><i class="far fa-calendar-alt"></i> {{ ($hlPost->published_at ?? $hlPost->created_at)->format('M d, Y') }}</span>
                                </div>
                                <h3 class="card-title-sm font-weight-bold mb-0" style="font-size: 1.05rem;">
                                    <a href="{{ route('single-post', $hlPost->slug) }}" class="text-body text-decoration-none">
                                        {{ $hlPost->title }}
                                    </a>
                                </h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <!-- Center Carousel / Feature (Large Highlight) -->
            <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
                <div id="heroCarousel" class="carousel slide h-100 shadow-sm rounded-4 overflow-hidden position-relative" data-bs-ride="carousel" data-ride="carousel">
                    <div class="carousel-inner h-100">
                        @foreach($heroCenter as $idx => $hcPost)
                            @php
                                $hcImg = $hcPost->image->link ?? 'dist-front/images/entrepreneurship.jpg';
                                $hcSrc = Str::startsWith($hcImg, ['http://', 'https://']) ? $hcImg : asset($hcImg);
                            @endphp
                            <div class="carousel-item {{ $idx === 0 ? 'active' : '' }} h-100">
                                <article class="mag-card h-100 position-relative border-0" style="min-height: 440px;">
                                    <div class="card-img-wrapper w-100 h-100 position-absolute top-0 start-0">
                                        <img src="{{ $hcSrc }}" alt="{{ $hcPost->title }}" class="w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('dist-front/images/entrepreneurship.jpg') }}'">
                                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-overlay" style="background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.85) 100%);"></div>
                                        <span class="badge-category position-absolute top-0 start-0 m-3 z-2">
                                            {{ $hcPost->category->name ?? 'Spotlight' }}
                                        </span>
                                    </div>
                                    <div class="card-body-custom position-absolute bottom-0 start-0 w-100 p-4 p-md-5 text-white z-3">
                                        <div class="meta-text text-white-50 mb-2 small d-flex align-items-center" style="gap: 14px;">
                                            <span><i class="far fa-user mr-1"></i> {{ $hcPost->author->nickname ?? ($hcPost->user->name ?? 'Editor') }}</span>
                                            <span><i class="far fa-calendar-alt mr-1"></i> {{ ($hcPost->published_at ?? $hcPost->created_at)->format('M d, Y') }}</span>
                                            <span><i class="far fa-clock mr-1"></i> {{ $hcPost->reading_time }}m read</span>
                                        </div>
                                        <h2 class="card-title-hero font-weight-bold mb-2">
                                            <a href="{{ route('single-post', $hcPost->slug) }}" class="text-white text-decoration-none">
                                                {{ $hcPost->title }}
                                            </a>
                                        </h2>
                                        <p class="excerpt-text text-white-50 mb-0 d-none d-md-block" style="font-size: 0.95rem;">
                                            {{ $hcPost->excerpt ?: Str::limit(strip_tags($hcPost->content), 120) }}
                                        </p>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-target="#heroCarousel" data-bs-slide="prev" data-slide="prev" style="width: 44px; height: 44px; background: rgba(0,0,0,0.45); border-radius: 50%; top: 50%; transform: translateY(-50%); left: 16px; border: none;">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="width: 18px;"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-target="#heroCarousel" data-bs-slide="next" data-slide="next" style="width: 44px; height: 44px; background: rgba(0,0,0,0.45); border-radius: 50%; top: 50%; transform: translateY(-50%); right: 16px; border: none;">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="width: 18px;"></span>
                    </button>
                </div>
            </div>

            <!-- Right Stack (2 vertical cards) -->
            <div class="col-lg-3 col-md-6">
                <div class="d-flex flex-column h-100" style="gap: 1.5rem;">
                    @foreach($heroRight as $hrPost)
                        @php
                            $hrImg = $hrPost->image->link ?? 'dist-front/images/lifestyle.jpg';
                            $hrSrc = Str::startsWith($hrImg, ['http://', 'https://']) ? $hrImg : asset($hrImg);
                        @endphp
                        <article class="mag-card flex-fill shadow-sm border-0 rounded-4 overflow-hidden h-100">
                            <div class="card-img-wrapper position-relative" style="height: 180px;">
                                <span class="badge-category bg-dark">{{ $hrPost->category->name ?? 'Trending' }}</span>
                                <a href="{{ route('single-post', $hrPost->slug) }}">
                                    <img src="{{ $hrSrc }}" alt="{{ $hrPost->title }}" class="img-aspect w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('dist-front/images/lifestyle.jpg') }}'">
                                </a>
                            </div>
                            <div class="card-body-custom p-3">
                                <div class="meta-text small text-muted mb-1">
                                    <span><i class="far fa-calendar-alt"></i> {{ ($hrPost->published_at ?? $hrPost->created_at)->format('M d, Y') }}</span>
                                </div>
                                <h3 class="card-title-sm font-weight-bold mb-0" style="font-size: 1.05rem;">
                                    <a href="{{ route('single-post', $hrPost->slug) }}" class="text-body text-decoration-none">
                                        {{ $hrPost->title }}
                                    </a>
                                </h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Popular Stories (4-Column Elite Grid) -->
    <section class="mb-5 fade-in-section">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
            <h4 class="font-weight-bold m-0 text-body d-flex align-items-center" style="letter-spacing: -0.02em;">
                <i class="fas fa-fire mr-2 text-danger"></i> Popular Stories
            </h4>
            <a href="{{ url('/blog-list') }}" class="small font-weight-bold text-primary text-decoration-none">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($popularStories as $popPost)
                @php
                    $popImg = $popPost->image->link ?? 'dist-front/images/image.jpg';
                    $popSrc = Str::startsWith($popImg, ['http://', 'https://']) ? $popImg : asset($popImg);
                @endphp
                <div class="col-lg-3 col-md-6 mb-4">
                    <article class="mag-card shadow-sm border-0 rounded-4 overflow-hidden h-100 transition-hover">
                        <div class="card-img-wrapper position-relative" style="height: 190px;">
                            <span class="badge-category">{{ $popPost->category->name ?? 'General' }}</span>
                            <a href="{{ route('single-post', $popPost->slug) }}">
                                <img src="{{ $popSrc }}" alt="{{ $popPost->title }}" class="img-aspect w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('dist-front/images/image.jpg') }}'">
                            </a>
                        </div>
                        <div class="card-body-custom p-3 d-flex flex-column" style="min-height: 140px;">
                            <h3 class="card-title-sm font-weight-bold mb-2" style="font-size: 1.05rem; line-height: 1.35;">
                                <a href="{{ route('single-post', $popPost->slug) }}" class="text-body text-decoration-none">
                                    {{ $popPost->title }}
                                </a>
                            </h3>
                            <div class="mt-auto meta-text small text-muted d-flex align-items-center justify-content-between">
                                <span><i class="far fa-calendar-alt"></i> {{ ($popPost->published_at ?? $popPost->created_at)->format('M d, Y') }}</span>
                                <span><i class="far fa-eye"></i> {{ number_format($popPost->views_count ?? 0) }}</span>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Main Content Row (Left 8 Cols Main Content, Right 4 Cols Sidebar) -->
    <div class="row g-4">
        <div class="col-lg-8">
            <!-- 3. Block Posts (2x2 Grid) -->
            <section class="mb-5 fade-in-section">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                    <h4 class="font-weight-bold m-0 text-body">
                        <i class="fas fa-layer-group mr-2 text-primary"></i> Featured Block Posts
                    </h4>
                </div>
                <div class="row g-4">
                    @foreach($blockPosts as $bPost)
                        @php
                            $bImg = $bPost->image->link ?? 'dist-front/images/architecture.jpg';
                            $bSrc = Str::startsWith($bImg, ['http://', 'https://']) ? $bImg : asset($bImg);
                        @endphp
                        <div class="col-md-6 mb-4">
                            <article class="mag-card shadow-sm border-0 rounded-4 overflow-hidden h-100 transition-hover">
                                <div class="card-img-wrapper" style="height: 220px;">
                                    <a href="{{ route('single-post', $bPost->slug) }}">
                                        <img src="{{ $bSrc }}" alt="{{ $bPost->title }}" class="img-aspect w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('dist-front/images/architecture.jpg') }}'">
                                    </a>
                                </div>
                                <div class="card-body-custom p-3">
                                    <div class="meta-text small text-muted mb-2 d-flex align-items-center" style="gap: 12px;">
                                        <span><i class="fas fa-tag text-primary"></i> {{ $bPost->category->name ?? 'Architecture' }}</span>
                                        <span><i class="far fa-clock"></i> {{ $bPost->reading_time }}m read</span>
                                    </div>
                                    <h3 class="card-title-md font-weight-bold mb-2" style="font-size: 1.15rem;">
                                        <a href="{{ route('single-post', $bPost->slug) }}" class="text-body text-decoration-none">
                                            {{ $bPost->title }}
                                        </a>
                                    </h3>
                                    <p class="excerpt-text text-muted small mb-0">
                                        {{ $bPost->excerpt ?: Str::limit(strip_tags($bPost->content), 95) }}
                                    </p>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- 4. Matrix & Visual Briefs Section -->
            <section class="mb-5 fade-in-section">
                <div class="row g-4">
                    <!-- Matrix List -->
                    <div class="col-md-6 mb-4 mb-md-0">
                        <h4 class="font-weight-bold mb-3 pb-2 border-bottom text-body">
                            <i class="fas fa-chart-line mr-2 text-warning"></i> Analysis Matrix
                        </h4>
                        <div class="d-flex flex-column" style="gap: 16px;">
                            @foreach($matrixPosts as $mPost)
                                @php
                                    $mImg = $mPost->image->link ?? 'dist-front/images/image-2.jpg';
                                    $mSrc = Str::startsWith($mImg, ['http://', 'https://']) ? $mImg : asset($mImg);
                                @endphp
                                <div class="list-widget-item d-flex align-items-center p-2 rounded-3 bg-light-subtle border border-light-subtle">
                                    <img src="{{ $mSrc }}" class="img-thumb-aspect rounded-3 mr-3 me-3" style="width: 80px; height: 70px; object-fit: cover;" alt="" onerror="this.src='{{ asset('dist-front/images/image-2.jpg') }}'">
                                    <div>
                                        <h5 class="card-title-sm mb-1 font-weight-bold" style="font-size: 14px; line-height: 1.35;">
                                            <a href="{{ route('single-post', $mPost->slug) }}" class="text-body text-decoration-none">
                                                {{ $mPost->title }}
                                            </a>
                                        </h5>
                                        <span class="meta-text small text-muted">{{ ($mPost->published_at ?? $mPost->created_at)->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Visual Briefs Grid -->
                    <div class="col-md-6">
                        <h4 class="font-weight-bold mb-3 pb-2 border-bottom text-body">
                            <i class="fas fa-camera mr-2 text-info"></i> Visual Briefs
                        </h4>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('blog.gallery') }}" class="mag-card d-block rounded-3 overflow-hidden shadow-sm" style="height: 150px;">
                                    <img src="{{ asset('dist-front/images/image-4.jpg') }}" class="w-100 h-100 object-fit-cover transition-transform" alt="Visual Brief">
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('blog.gallery') }}" class="mag-card d-block rounded-3 overflow-hidden shadow-sm" style="height: 150px;">
                                    <img src="{{ asset('dist-front/images/image-5.jpg') }}" class="w-100 h-100 object-fit-cover transition-transform" alt="Visual Brief">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. Grid Post (1 Large Left Hero, 4 Small Stacked Items) -->
            @if($gridHero)
            <section class="mb-5 fade-in-section">
                <h4 class="font-weight-bold mb-3 pb-2 border-bottom text-body">
                    <i class="fas fa-th mr-2 text-danger"></i> Deep-Dive Dispatch
                </h4>
                <div class="row g-4">
                    <!-- Large Left Post -->
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        @php
                            $ghImg = $gridHero->image->link ?? 'dist-front/images/grid-large.jpg';
                            $ghSrc = Str::startsWith($ghImg, ['http://', 'https://']) ? $ghImg : asset($ghImg);
                        @endphp
                        <article class="mag-card shadow-sm border-0 rounded-4 overflow-hidden h-100 transition-hover">
                            <div class="card-img-wrapper position-relative" style="height: 240px;">
                                <span class="badge-category">{{ $gridHero->category->name ?? 'Technology' }}</span>
                                <a href="{{ route('single-post', $gridHero->slug) }}">
                                    <img src="{{ $ghSrc }}" alt="{{ $gridHero->title }}" class="img-aspect w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('dist-front/images/grid-large.jpg') }}'">
                                </a>
                            </div>
                            <div class="card-body-custom p-3">
                                <h3 class="card-title-md font-weight-bold mb-2" style="font-size: 1.2rem;">
                                    <a href="{{ route('single-post', $gridHero->slug) }}" class="text-body text-decoration-none">
                                        {{ $gridHero->title }}
                                    </a>
                                </h3>
                                <p class="excerpt-text text-muted small mb-0">
                                    {{ $gridHero->excerpt ?: Str::limit(strip_tags($gridHero->content), 120) }}
                                </p>
                            </div>
                        </article>
                    </div>

                    <!-- 4 Small Stacked Posts -->
                    <div class="col-lg-6 d-flex flex-column" style="gap: 14px;">
                        @foreach($gridStack as $gsPost)
                            @php
                                $gsImg = $gsPost->image->link ?? 'dist-front/images/grid-small.jpg';
                                $gsSrc = Str::startsWith($gsImg, ['http://', 'https://']) ? $gsImg : asset($gsImg);
                            @endphp
                            <div class="list-widget-item d-flex align-items-center p-2 rounded-3 bg-light-subtle border border-light-subtle">
                                <img src="{{ $gsSrc }}" class="img-thumb-aspect rounded-3 mr-3 me-3" style="width: 72px; height: 60px; object-fit: cover;" alt="" onerror="this.src='{{ asset('dist-front/images/grid-small.jpg') }}'">
                                <div class="flex-grow-1 min-w-0">
                                    <h5 class="card-title-sm mb-1 font-weight-bold" style="font-size: 13.5px; line-height: 1.35;">
                                        <a href="{{ route('single-post', $gsPost->slug) }}" class="text-body text-decoration-none d-block text-truncate">
                                            {{ $gsPost->title }}
                                        </a>
                                    </h5>
                                    <span class="meta-text small text-muted">{{ ($gsPost->published_at ?? $gsPost->created_at)->format('M d, Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            <!-- 6. Technology & Business Showcases -->
            <section class="mb-5 fade-in-section">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h4 class="font-weight-bold m-0 text-body">
                        <i class="fas fa-microchip mr-2 text-primary"></i> Technology Intelligence
                    </h4>
                    <a href="{{ url('/blog/category/technology') }}" class="small font-weight-bold text-primary text-decoration-none">
                        Category Archive <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @foreach($techPosts->take(2) as $tPost)
                        @php
                            $tImg = $tPost->image->link ?? 'dist-front/images/tech-news.jpg';
                            $tSrc = Str::startsWith($tImg, ['http://', 'https://']) ? $tImg : asset($tImg);
                        @endphp
                        <div class="col-md-6 mb-3">
                            <article class="mag-card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                                <div class="card-img-wrapper" style="height: 190px;">
                                    <a href="{{ route('single-post', $tPost->slug) }}">
                                        <img src="{{ $tSrc }}" alt="{{ $tPost->title }}" class="img-aspect w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('dist-front/images/tech-news.jpg') }}'">
                                    </a>
                                </div>
                                <div class="card-body-custom p-3">
                                    <h3 class="card-title-sm font-weight-bold mb-1" style="font-size: 1.05rem;">
                                        <a href="{{ route('single-post', $tPost->slug) }}" class="text-body text-decoration-none">
                                            {{ $tPost->title }}
                                        </a>
                                    </h3>
                                    <p class="excerpt-text text-muted small mb-0">
                                        {{ $tPost->excerpt ?: Str::limit(strip_tags($tPost->content), 80) }}
                                    </p>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Right 4 Cols: Shared Magazine Sidebar (strictly category <= 10, tags <= 25) -->
        <div class="col-lg-4">
            @include('blog.partials.sidebar')
        </div>
    </div>
</main>

@endsection
