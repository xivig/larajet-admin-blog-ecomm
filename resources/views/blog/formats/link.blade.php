@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', $post->title . ' - Curated Resource')
@section('content')

@php
    $linkImg = $post->image->link ?? 'dist-front/images/highres-new-2.jpg';
    $linkSrc = Str::startsWith($linkImg, ['http://', 'https://']) ? $linkImg : asset($linkImg);
    $targetUrl = 'https://xivig.com';
@endphp

<main class="container my-5" style="padding-top: 15px;">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/blog-list') }}" class="text-muted">Blog</a></li>
            <li class="breadcrumb-item active text-danger" aria-current="page">External Dispatch</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <article class="fade-in-section">
                <!-- Header Context -->
                <header class="mb-4">
                    <span class="badge bg-danger text-uppercase tracking-wide mb-3 px-3 py-2 rounded-pill shadow-sm" style="letter-spacing: 1px; font-size: 11px;">
                        <i class="fas fa-external-link-alt mr-1"></i> External Link
                    </span>
                    <h1 class="display-4 fw-bold font-serif mb-2 text-body" style="font-size: 2.25rem; letter-spacing: -0.02em;">
                        {{ $post->title }}
                    </h1>
                    <div class="d-flex align-items-center gap-2 text-muted small mt-3">
                        <i class="far fa-user"></i> By <a href="{{ url('/blog/author/' . ($post->author->id ?? 1)) }}" class="text-body fw-medium text-decoration-none">{{ $post->author->nickname ?? 'Alex Mercer' }}</a>
                        <span class="mx-2">&bull;</span>
                        <i class="far fa-calendar-alt"></i> {{ ($post->published_at ?? $post->created_at)->format('F d, Y') }}
                    </div>
                </header>

                <!-- Premium Bookmark Card -->
                <a href="{{ $targetUrl }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                    <div class="mag-card elite-card bookmark-card shadow-sm rounded-4 overflow-hidden bg-body border transition-hover mb-5 mt-4">
                        <!-- Image Area -->
                        <div class="card-img-wrapper position-relative overflow-hidden" style="height: 340px;">
                            <img src="{{ $linkSrc }}" class="w-100 h-100 object-fit-cover transition-transform" alt="{{ $post->title }}" onerror="this.src='{{ asset('dist-front/images/highres-new-2.jpg') }}'">
                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-overlay opacity-50 z-1" style="background: rgba(0,0,0,0.4);"></div>

                            <!-- Overlay Icon -->
                            <div class="position-absolute top-50 start-50 translate-middle text-white z-3 bookmark-icon">
                                <i class="fas fa-external-link-alt" style="font-size: 3rem; text-shadow: 0 4px 15px rgba(0,0,0,0.6);"></i>
                            </div>
                        </div>

                        <!-- Link Metadata Area -->
                        <div class="card-body-custom p-4 bg-light-subtle d-flex justify-content-between align-items-center border-top">
                            <div>
                                <h3 class="card-title-sm font-serif fw-bold mb-1 text-body" style="font-size: 1.15rem;">
                                    {{ $post->title }}
                                </h3>
                                <p class="text-muted small mb-0"><i class="fas fa-link mr-1"></i> xivig.com</p>
                            </div>
                            <div class="btn btn-outline-danger rounded-pill px-4 ms-3 ml-3 flex-shrink-0 bookmark-btn fw-bold">
                                Read Article <i class="fas fa-arrow-right ml-1"></i>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Editorial Context -->
                <div class="elite-content fs-5 lh-lg text-body-secondary mt-5">
                    @if($post->excerpt)
                        <p class="drop-cap lead fs-4 mb-4 fw-medium text-body p-3 bg-light-subtle rounded-3 border-start border-4 border-danger">
                            {{ $post->excerpt }}
                        </p>
                    @endif

                    <div class="article-text-body">
                        {!! $post->content !!}
                    </div>

                    <blockquote class="elite-quote my-5 p-4 border-start border-4 border-danger bg-light-subtle rounded-end shadow-sm">
                        <p class="fs-4 font-serif fw-bold text-body fst-italic mb-2">
                            "We highly recommend giving this piece a full read if you want to understand the future of digital scarcity."
                        </p>
                        <footer class="blockquote-footer fs-6 mt-0">Research Curation Desk, <cite title="Source Title">Xivig Intelligence</cite></footer>
                    </blockquote>
                </div>

                <!-- Circular Share Bar -->
                <div class="tags-share-bar d-flex flex-wrap align-items-center justify-content-between p-3 rounded-4 bg-light-subtle border border-light-subtle my-5" style="gap: 16px;">
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <span class="font-weight-bold text-body small mr-2"><i class="fas fa-tag text-primary mr-1"></i> Source:</span>
                        <span class="badge badge-light border py-1 px-3 rounded-pill text-body font-weight-bold">External Resource</span>
                    </div>

                    <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                        <span class="font-weight-bold text-body small text-uppercase mr-1"><i class="fas fa-share-alt text-primary mr-1"></i> Share:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-circle-btn share-facebook" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" class="share-circle-btn share-twitter" title="Share on X"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-circle-btn share-linkedin" title="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <button type="button" class="share-circle-btn share-copylink" onclick="navigator.clipboard.writeText(window.location.href); alert('URL copied!');" title="Copy Link"><i class="fas fa-link"></i></button>
                    </div>
                </div>
            </article>
        </div>

        <!-- Right 4 Cols: Shared Sidebar -->
        <div class="col-lg-4">
            @include('blog.partials.sidebar')
        </div>
    </div>
</main>

@endsection
