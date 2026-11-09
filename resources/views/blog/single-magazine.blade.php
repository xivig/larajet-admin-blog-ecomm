@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', $post->title . ' - Magazine Plus')
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

<main class="container my-5" style="padding-top: 15px;">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('blog.magazine') }}" class="text-muted">Magazine</a></li>
            @if($post->category)
                <li class="breadcrumb-item"><a href="{{ url('/blog-list?category=' . $post->category->name) }}" class="text-muted">{{ $post->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active text-primary text-truncate" style="max-width: 320px;" aria-current="page">{{ $post->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Main Article Column (8 cols) -->
        <div class="col-lg-8">
            <article class="fade-in-section mb-5">
                <!-- Category Badge -->
                <div class="mb-3">
                    <span class="badge bg-danger text-uppercase px-3 py-2 rounded-pill font-weight-bold tracking-wide" style="letter-spacing: 1px; font-size: 11px;">
                        {{ $post->category->name ?? 'Intelligence' }}
                    </span>
                    @if($post->is_sticky)
                        <span class="badge-elite-pinned ml-2 ms-2"><i class="fas fa-thumbtack mr-1"></i> Pinned</span>
                    @elseif($post->is_featured)
                        <span class="badge-elite-featured ml-2 ms-2"><i class="fas fa-star mr-1"></i> Featured</span>
                    @endif
                </div>

                <!-- Headline -->
                <h1 class="card-title-hero mb-3 font-serif fw-bold text-body" style="font-size: 2.35rem; line-height: 1.25; letter-spacing: -0.02em;">
                    {{ $post->title }}
                </h1>

                <!-- Meta row -->
                <div class="meta-text text-muted mb-4 d-flex align-items-center flex-wrap pb-3 border-bottom" style="gap: 16px; font-size: 13.5px;">
                    <span>
                        <i class="far fa-user mr-1 text-primary"></i>
                        <a href="{{ url('/blog/author/' . ($post->author->id ?? $post->author_id ?? 1)) }}" class="text-body font-weight-bold text-decoration-none">
                            {{ $post->author->nickname ?? ($post->user->name ?? 'Admin') }}
                        </a>
                    </span>
                    <span><i class="far fa-calendar-alt mr-1"></i> {{ ($post->published_at ?? $post->created_at)->format('F d, Y') }}</span>
                    @if($post->tag)
                        <span><i class="fas fa-hashtag mr-1 text-muted"></i> <a href="{{ url('/blog/tag/' . $post->tag->id) }}" class="text-muted text-decoration-none">#{{ $post->tag->name }}</a></span>
                    @endif
                    <span><i class="far fa-clock mr-1"></i> {{ $post->reading_time ?? 3 }} min read</span>
                    <span><i class="far fa-eye mr-1"></i> {{ number_format($post->views_count ?? 0) }} views</span>
                </div>

                <!-- Hero Image Wrapper -->
                @php
                    $mainImg = $post->image->link ?? 'dist-front/images/hero-image-1.jpg';
                    $mainSrc = Str::startsWith($mainImg, ['http://', 'https://']) ? $mainImg : asset($mainImg);
                @endphp
                <div class="card-img-wrapper rounded-4 mb-5 shadow-sm overflow-hidden position-relative" style="max-height: 480px;">
                    <img src="{{ $mainSrc }}" class="w-100 h-100 object-fit-cover" alt="{{ $post->title }}" onerror="this.src='{{ asset('dist-front/images/hero-image-1.jpg') }}'">
                </div>

                <!-- Table of Contents -->
                @if(!empty($tableOfContents) && count($tableOfContents) > 0 && !$isLocked)
                    <div class="toc-container-elite mb-5 p-4 rounded-4 shadow-sm bg-body border border-light-subtle">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-list-ul mr-2 text-primary"></i>
                            <h5 class="mb-0 font-weight-bold text-body" style="font-size: 16px;">Article Index & Highlights</h5>
                        </div>
                        <ul class="list-unstyled mb-0 d-flex flex-column" style="gap: 8px;">
                            @foreach($tableOfContents as $toc)
                                <li class="toc-level-{{ $toc['level'] }}" style="padding-left: {{ ($toc['level'] - 2) * 16 }}px;">
                                    <a href="#{{ $toc['id'] }}" class="text-decoration-none text-muted font-weight-500 hover-primary">
                                        <i class="fas fa-chevron-right mr-2 text-primary" style="font-size: 10px;"></i> {{ $toc['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Article Content or Locked State -->
                @if($isLocked)
                    <div class="protected-content-box p-5 rounded-4 shadow-sm bg-body text-center border my-5">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center bg-danger text-white rounded-circle" style="width: 64px; height: 64px;">
                                <i class="fas fa-lock" style="font-size: 1.5rem;"></i>
                            </span>
                        </div>
                        <h3 class="font-weight-bold mb-2 text-body">Protected Content</h3>
                        <p class="text-muted mb-4">This article requires an access passphrase to view full specifications.</p>

                        @if(session('error'))
                            <div class="alert alert-danger py-2 px-3 mb-3 d-inline-block small">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('blog.unlock', $post->slug) }}" method="POST" class="d-flex justify-content-center">
                            @csrf
                            <div class="unlock-box-elite d-inline-flex align-items-center">
                                <input type="password" name="password" class="unlock-input" placeholder="Enter passphrase..." required autofocus>
                                <button type="submit" class="unlock-btn">
                                    <i class="fas fa-key mr-1"></i> UNLOCK
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="entry-content-elite fs-5 lh-lg text-body-secondary mb-5">
                        @if($post->excerpt)
                            <div class="lead fs-4 mb-4 fw-medium text-body fst-italic p-3 rounded-3 bg-light-subtle border-start border-4 border-primary">
                                {{ $post->excerpt }}
                            </div>
                        @endif

                        <div class="article-body-text">
                            {!! $contentWithAnchors ?? $post->content !!}
                        </div>
                    </div>
                @endif

                <!-- Post Tags & Circular Share Bar -->
                <div class="tags-share-bar d-flex flex-wrap align-items-center justify-content-between p-3 rounded-4 bg-light-subtle border border-light-subtle my-5" style="gap: 16px;">
                    <!-- Left: Tags -->
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <span class="font-weight-bold text-body small d-flex align-items-center mr-2">
                            <i class="fas fa-tag text-primary mr-1"></i> Tags:
                        </span>
                        @if($post->tag)
                            <a href="{{ url('/blog/tag/' . $post->tag->id) }}" class="badge badge-light border text-decoration-none py-1 px-3 rounded-pill text-body font-weight-bold">
                                #{{ $post->tag->name }}
                            </a>
                        @endif
                        @if($post->category)
                            <a href="{{ url('/blog-list?category=' . $post->category->name) }}" class="badge badge-light border text-decoration-none py-1 px-3 rounded-pill text-body font-weight-bold">
                                #{{ $post->category->name }}
                            </a>
                        @endif
                    </div>

                    <!-- Right: Share Buttons (Strictly Circular) -->
                    <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                        <span class="font-weight-bold text-body small text-uppercase mr-1" style="letter-spacing: 0.5px;">
                            <i class="fas fa-share-alt text-primary mr-1"></i> Share:
                        </span>

                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-circle-btn share-facebook" title="Share on Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>

                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" class="share-circle-btn share-twitter" title="Share on X">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>

                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-circle-btn share-linkedin" title="Share on LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>

                        <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}" target="_blank" class="share-circle-btn share-whatsapp" title="Share on WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>

                        <a href="https://t.me/share/url?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" class="share-circle-btn share-telegram" title="Share on Telegram">
                            <i class="fab fa-telegram-plane"></i>
                        </a>

                        <button type="button" class="share-circle-btn share-copylink" onclick="navigator.clipboard.writeText(window.location.href); alert('Article URL copied to clipboard!');" title="Copy Link">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>

                <!-- Related Stories: 8 Cards Grid (from dist-front single.html) -->
                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                <section class="related-stories mt-5 pt-5 border-top">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="font-serif fw-bold text-body m-0">
                            <i class="fas fa-newspaper mr-2 text-primary"></i> Related Stories
                        </h3>
                        <a href="{{ url('/blog-list') }}" class="small font-weight-bold text-primary text-decoration-none">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="row g-4">
                        @foreach($relatedPosts as $rStory)
                            @php
                                $rImg = $rStory->image->link ?? 'dist-front/images/highres-new-1.jpg';
                                $rSrc = Str::startsWith($rImg, ['http://', 'https://']) ? $rImg : asset($rImg);
                            @endphp
                            <div class="col-lg-3 col-md-6 mb-4">
                                <article class="mag-card elite-card h-100 shadow-sm rounded-4 overflow-hidden bg-body border-0 transition-hover">
                                    <div class="card-img-wrapper position-relative overflow-hidden" style="height: 160px;">
                                        <a href="{{ route('single-post', $rStory->slug) }}">
                                            <img src="{{ $rSrc }}" class="w-100 h-100 object-fit-cover transition-transform" alt="{{ $rStory->title }}" onerror="this.src='{{ asset('dist-front/images/highres-new-1.jpg') }}'">
                                        </a>
                                        <span class="badge bg-dark position-absolute top-0 start-0 m-2 z-2" style="font-size: 10px;">
                                            {{ $rStory->category->name ?? 'Intel' }}
                                        </span>
                                    </div>
                                    <div class="card-body-custom p-3 d-flex flex-column" style="min-height: 110px;">
                                        <h5 class="card-title-sm font-serif fw-bold mb-2" style="font-size: 13.5px; line-height: 1.35;">
                                            <a href="{{ route('single-post', $rStory->slug) }}" class="text-decoration-none text-body">
                                                {{ Str::limit($rStory->title, 48) }}
                                            </a>
                                        </h5>
                                        <div class="mt-auto text-muted small d-flex align-items-center justify-content-between" style="font-size: 11px;">
                                            <span><i class="far fa-user"></i> {{ $rStory->author->nickname ?? 'Admin' }}</span>
                                            <span>{{ ($rStory->published_at ?? $rStory->created_at)->format('M d') }}</span>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- Comments Section -->
                @if(!$isLocked)
                    <section class="comments-section mt-5 pt-4 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h4 class="font-weight-bold text-body m-0">
                                <i class="far fa-comments mr-2 text-primary"></i> Discussion ({{ $post->comments->count() }})
                            </h4>
                        </div>

                        <!-- Add Comment Form -->
                        <div class="comment-form-card p-4 rounded-4 shadow-sm bg-body border border-light-subtle mb-4">
                            <h5 class="font-weight-bold mb-3 text-body" style="font-size: 15px;">Leave a Comment</h5>
                            <form action="{{ route('comments.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $post->id }}">
                                <div class="form-group mb-3">
                                    <textarea name="comment" rows="3" class="form-control form-control-elite" placeholder="Share your perspective or analysis..." required></textarea>
                                </div>
                                <button type="submit" class="btn-elite btn-elite-primary btn-sm px-4">
                                    <i class="fas fa-paper-plane mr-1"></i> Post Comment
                                </button>
                            </form>
                        </div>

                        <!-- Existing Comments List -->
                        <div class="comments-list d-flex flex-column" style="gap: 16px;">
                            @forelse($post->comments as $comment)
                                <div class="comment-item p-3 rounded-4 bg-light-subtle border border-light-subtle">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mr-2 me-2" style="width: 32px; height: 32px; font-size: 12px; font-weight: bold;">
                                                {{ strtoupper(substr($comment->user->name ?? 'User', 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0 font-weight-bold text-body" style="font-size: 13.5px;">{{ $comment->user->name ?? 'Community Member' }}</h6>
                                                <small class="text-muted" style="font-size: 11px;">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mb-0 text-body small" style="line-height: 1.5;">{{ $comment->comment }}</p>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Be the first to share thoughts on this publication.</p>
                            @endforelse
                        </div>
                    </section>
                @endif
            </article>
        </div>

        <!-- Sidebar Column (4 cols) -->
        <div class="col-lg-4">
            @include('blog.partials.sidebar')
        </div>
    </div>
</main>

@endsection
