@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', $post->title . ' - Immersive Visual Post')
@section('content')

@php
    $heroImg = $post->image->link ?? 'dist-front/images/hero-image.jpg';
    $heroSrc = Str::startsWith($heroImg, ['http://', 'https://']) ? $heroImg : asset($heroImg);
@endphp

<article class="elite-post">
    <!-- Massive 100vh Hero Section -->
    <header class="hero-section position-relative vh-100 d-flex align-items-end mb-5" style="background: url('{{ $heroSrc }}') center/cover no-repeat; min-height: 80vh;">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-overlay" style="background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.85) 100%);"></div>
        <div class="container position-relative z-3 pb-5 fade-in-up">
            <div class="row">
                <div class="col-lg-10 mx-auto text-center text-white">
                    <span class="badge bg-danger text-uppercase tracking-wide mb-3 px-3 py-2 rounded-pill shadow" style="letter-spacing: 2px;">
                        {{ $post->category->name ?? 'Visual Feature' }}
                    </span>
                    <h1 class="display-3 fw-bold font-serif mb-4 text-shadow-lg elite-title" style="letter-spacing: -0.02em;">
                        {{ $post->title }}
                    </h1>
                    <div class="d-flex justify-content-center align-items-center gap-3 text-white-50 fs-5 mt-4 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle border border-2 border-white mr-2 me-2" style="width: 44px; height: 44px; font-weight: bold; font-size: 14px;">
                                {{ strtoupper(substr($post->author->nickname ?? ($post->user->name ?? 'A'), 0, 1)) }}
                            </span>
                            <span class="fw-medium text-white">By {{ $post->author->nickname ?? ($post->user->name ?? 'Alex Mercer') }}</span>
                        </div>
                        <span>&bull;</span>
                        <span><i class="far fa-calendar-alt"></i> {{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span>
                        <span>&bull;</span>
                        <span><i class="far fa-clock"></i> {{ $post->reading_time ?? 5 }} min read</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Elite Content Area -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="elite-content fs-5 lh-lg text-body-secondary">
                    @if($post->excerpt)
                        <p class="drop-cap lead fs-4 mb-4 fw-medium text-body">
                            {{ $post->excerpt }}
                        </p>
                    @endif

                    <div class="article-text-body">
                        {!! $post->content !!}
                    </div>

                    <blockquote class="elite-quote my-5 p-4 border-start border-4 border-primary bg-light-subtle rounded-end shadow-sm">
                        <p class="fs-4 font-serif fw-bold text-body fst-italic mb-2">
                            "Visual architectures are not merely decorative—they communicate structural integrity and human intuition in systems."
                        </p>
                        <footer class="blockquote-footer fs-6 mt-0">Elite Design Collective, <cite title="Source Title">System Specifications</cite></footer>
                    </blockquote>
                </div>

                <!-- Circular Share Bar -->
                <div class="tags-share-bar d-flex flex-wrap align-items-center justify-content-between p-3 rounded-4 bg-light-subtle border border-light-subtle my-5" style="gap: 16px;">
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <span class="font-weight-bold text-body small mr-2"><i class="fas fa-tag text-primary mr-1"></i> Topic:</span>
                        <span class="badge badge-light border py-1 px-3 rounded-pill text-body font-weight-bold">#{{ $post->category->name ?? 'Visual' }}</span>
                    </div>

                    <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                        <span class="font-weight-bold text-body small text-uppercase mr-1"><i class="fas fa-share-alt text-primary mr-1"></i> Share:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-circle-btn share-facebook" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" class="share-circle-btn share-twitter" title="Share on X"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-circle-btn share-linkedin" title="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <button type="button" class="share-circle-btn share-copylink" onclick="navigator.clipboard.writeText(window.location.href); alert('URL copied!');" title="Copy Link"><i class="fas fa-link"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Stories: 8 Cards Grid -->
    @if(isset($relatedPosts) && $relatedPosts->count() > 0)
    <section class="related-stories bg-light-subtle py-5 border-top">
        <div class="container">
            <h3 class="font-serif fw-bold text-body mb-4"><i class="fas fa-images mr-2 text-primary"></i> More Visual & Editorial Stories</h3>
            <div class="row g-4">
                @foreach($relatedPosts as $rPost)
                    @php
                        $rImg = $rPost->image->link ?? 'dist-front/images/highres-new-2.jpg';
                        $rSrc = Str::startsWith($rImg, ['http://', 'https://']) ? $rImg : asset($rImg);
                    @endphp
                    <div class="col-lg-3 col-md-6 mb-4">
                        <article class="mag-card elite-card h-100 shadow-sm rounded-4 overflow-hidden bg-body border-0 transition-hover">
                            <div class="card-img-wrapper position-relative overflow-hidden" style="height: 180px;">
                                <a href="{{ route('single-post', $rPost->slug) }}">
                                    <img src="{{ $rSrc }}" class="w-100 h-100 object-fit-cover transition-transform" alt="{{ $rPost->title }}" onerror="this.src='{{ asset('dist-front/images/highres-new-2.jpg') }}'">
                                </a>
                                <span class="badge bg-dark position-absolute top-0 start-0 m-2 z-2" style="font-size: 10px;">{{ $rPost->category->name ?? 'Feature' }}</span>
                            </div>
                            <div class="card-body-custom p-3 d-flex flex-column" style="min-height: 120px;">
                                <h5 class="card-title-sm font-serif fw-bold mb-2" style="font-size: 13.5px; line-height: 1.35;">
                                    <a href="{{ route('single-post', $rPost->slug) }}" class="text-decoration-none text-body">
                                        {{ Str::limit($rPost->title, 50) }}
                                    </a>
                                </h5>
                                <div class="mt-auto text-muted small d-flex align-items-center justify-content-between" style="font-size: 11px;">
                                    <span><i class="far fa-user"></i> {{ $rPost->author->nickname ?? 'Admin' }}</span>
                                    <span>{{ ($rPost->published_at ?? $rPost->created_at)->format('M d') }}</span>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</article>

@endsection
