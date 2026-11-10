@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', $post->title . ' — Visual Feature Essay')
@section('content')
@php
    $authorName = $post->author?->user?->name ?? ($post->author?->nickname ?? ($post->user?->name ?? 'Editorial Desk'));
    $authorId = $post->author?->id ?? $post->author_id ?? 1;
    $authorInitial = strtoupper(substr($authorName, 0, 1));
    $pubDate = ($post->published_at ?? $post->created_at ?? now())->format('M d, Y');
    $catId = $post->category?->id ?? 1;
    $catName = $post->category?->name ?? 'Visual Feature';
    $fallbackThumb = asset('admin-dist/img/blog-1-PTDVKJDt.webp');
@endphp

<!-- Fixed Reading Progress Bar -->
<div class="vf-progress-track" aria-hidden="true">
    <div class="vf-progress-fill" id="vfProgressBar"></div>
</div>

<article id="vfArticleRoot" class="elite-post">
    <!-- 1. Cinematic Full-Bleed Hero Stage -->
    <header class="vf-hero">
        <img
            id="vfHeroBg"
            src="{{ $visualTelemetry['src'] }}"
            alt="{{ $visualTelemetry['alt'] }}"
            class="vf-hero-bg"
        >
        <div class="vf-hero-scrim" aria-hidden="true"></div>

        <!-- Top Frosted Controls & Breadcrumbs -->
        <div class="container position-relative z-2 pt-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 vf-hero-breadcrumbs px-3 py-2 rounded-pill small">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('blog.magazine') }}" class="text-white-50 text-decoration-none">Magazine</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('blog.category', $catId) }}" class="text-white-50 text-decoration-none">{{ Str::limit($catName, 22) }}</a></li>
                        <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Visual Format</li>
                    </ol>
                </nav>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="vf-glass-btn" id="vfZenToggle" title="Toggle distraction-free reading">
                        <i class="fas fa-book-open"></i> <span id="vfZenLabel">Zen Mode</span>
                    </button>
                    <button type="button" class="vf-glass-btn" id="vfOpenLightboxBtn" data-frame-index="0">
                        <i class="fas fa-expand-arrows-alt"></i> <span>Inspect Full-Res</span>
                    </button>
                    <a href="{{ $visualTelemetry['src'] }}" download class="vf-glass-btn d-none d-sm-inline-flex" title="Download high-resolution plate">
                        <i class="fas fa-download"></i> <span>Save Plate</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Hero Headline & Editorial Credits -->
        <div class="container position-relative z-2 pb-5 mb-4 mt-5">
            <div class="row">
                <div class="col-xl-10 mx-auto text-center text-white">
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 mb-3">
                        <a href="{{ route('blog.category', $catId) }}" class="badge bg-danger text-white text-uppercase text-decoration-none px-3 py-2 rounded-pill shadow-sm" style="letter-spacing: 1.5px; font-size: 11px;">
                            {{ $catName }}
                        </a>
                        <span class="badge bg-dark bg-opacity-75 text-info border border-info border-opacity-50 px-3 py-2 rounded-pill" style="font-size: 11px; letter-spacing: 1px;">
                            <i class="fas fa-camera-retro me-1"></i> VISUAL ESSAY · {{ $visualFrames->count() }} PLATES
                        </span>
                        @if($post->is_featured || $post->is_sticky)
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 11px;">
                                <i class="fas fa-star me-1"></i> {{ $post->is_sticky ? 'Pinned' : 'Featured' }}
                            </span>
                        @endif
                    </div>

                    <h1 class="display-4 fw-bold font-serif mb-3 text-white" style="letter-spacing: -0.025em; line-height: 1.15; text-shadow: 0 6px 24px rgba(0,0,0,0.45);">
                        {{ $post->title }}
                    </h1>

                    @if($post->excerpt)
                        <p class="lead text-white-50 mx-auto mb-4" style="max-width: 760px; font-size: 1.12rem;">
                            {{ Str::limit($post->excerpt, 175) }}
                        </p>
                    @endif

                    <div class="d-flex justify-content-center align-items-center gap-3 text-white-50 flex-wrap" style="font-size: 14px;">
                        <a href="{{ route('blog.author', $authorId) }}" class="d-inline-flex align-items-center gap-2 text-white text-decoration-none">
                            <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle border border-2 border-white fw-bold" style="width: 38px; height: 38px; font-size: 14px;">
                                {{ $authorInitial }}
                            </span>
                            <span class="fw-semibold">{{ $authorName }}</span>
                        </a>
                        <span>&bull;</span>
                        <span><i class="far fa-calendar-alt me-1 text-info"></i> {{ $pubDate }}</span>
                        <span>&bull;</span>
                        <span><i class="far fa-clock me-1 text-info"></i> {{ $post->reading_time ?? 4 }} min read</span>
                        <span>&bull;</span>
                        <span><i class="far fa-eye me-1 text-info"></i> {{ number_format($post->views_count ?? 0) }} views</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. Floating Darkroom EXIF Telemetry & Plate Filmstrip Dock -->
    <div class="container">
        <section class="vf-telemetry-dock mb-4" data-vf-section="telemetry">
            <div class="row g-3 align-items-center">
                <div class="col-6 col-lg-3">
                    <div class="vf-spec-item">
                        <span class="vf-spec-icon"><i class="fas fa-vector-square"></i></span>
                        <div class="min-w-0">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 10.5px; letter-spacing: 0.6px;">Resolution</div>
                            <div class="fw-bold text-body text-truncate" style="font-size: 14px;" id="vfActiveDim">{{ $visualTelemetry['dimensions'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="vf-spec-item">
                        <span class="vf-spec-icon"><i class="fas fa-file-image"></i></span>
                        <div class="min-w-0">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 10.5px; letter-spacing: 0.6px;">Master Format</div>
                            <div class="fw-bold text-body text-truncate" style="font-size: 14px;">{{ $visualTelemetry['format_badge'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="vf-spec-item">
                        <span class="vf-spec-icon"><i class="fas fa-weight-hanging"></i></span>
                        <div class="min-w-0">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 10.5px; letter-spacing: 0.6px;">Asset Size</div>
                            <div class="fw-bold text-body text-truncate" style="font-size: 14px;">{{ $visualTelemetry['formatted_size'] }} · {{ $visualTelemetry['mime_type'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 d-flex justify-content-lg-end gap-2">
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold" id="vfDockLightboxBtn">
                        <i class="fas fa-search-plus me-1"></i> Studio Viewer
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold" onclick="vfCopyShareLink()">
                        <i class="fas fa-link me-1"></i> Share
                    </button>
                </div>
            </div>

            <!-- Interactive Multi-Plate Filmstrip -->
            @if($visualFrames->isNotEmpty())
            <div class="mt-3 pt-3 border-top">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <span class="small fw-bold text-body">
                        <i class="fas fa-film me-1 text-primary"></i>
                        Visual Filmstrip &mdash; <span id="vfActivePlateCaption" class="text-muted fw-normal">{{ $visualTelemetry['caption'] }}</span>
                    </span>
                    <a href="{{ route('blog.gallery') }}" class="small fw-semibold text-primary text-decoration-none">
                        Full Visual Archive <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="row g-2">
                    @foreach($visualFrames as $idx => $frame)
                        <div class="col-4 col-sm-3 col-md-2">
                            <button
                                type="button"
                                class="vf-filmstrip-btn {{ $idx === 0 ? 'active' : '' }}"
                                data-frame-idx="{{ $idx }}"
                                data-frame-url="{{ $frame['url'] }}"
                                data-frame-title="{{ $frame['title'] }}"
                                title="{{ $frame['title'] }}"
                            >
                                <img src="{{ $frame['url'] }}" alt="{{ $frame['alt'] }}">
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </section>

        <!-- 3. Visual Editions Quick Switcher -->
        @if($visualSwitcherPosts->isNotEmpty())
        <section class="mb-5" data-vf-section="editions">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.7px;">
                    <i class="fas fa-layer-group me-1 text-primary"></i> Switch Visual Edition
                </span>
                <a href="{{ route('blog.magazine') }}" class="small text-primary fw-semibold text-decoration-none">
                    Magazine Portal <i class="fas fa-external-link-alt ms-1" style="font-size: 10px;"></i>
                </a>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($visualSwitcherPosts as $vp)
                    <a href="{{ route('blog.format.image', $vp->slug) }}" class="vf-switcher-pill">
                        <img
                            src="{{ $vp->image?->url ?? $fallbackThumb }}"
                            alt="{{ $vp->title }}"
                            class="rounded-circle object-fit-cover flex-shrink-0"
                            style="width: 34px; height: 34px;"
                        >
                        <div class="min-w-0">
                            <div class="fw-semibold text-body text-truncate" style="font-size: 12.5px;">{{ $vp->title }}</div>
                            <div class="text-muted text-truncate" style="font-size: 10.5px;">{{ $vp->category?->name ?? 'Visual' }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 4. Main Reading Canvas (8 Cols Article + 4 Cols Sticky Sidebar) -->
        <div class="row g-4 mb-5" id="vfLayoutRow">
            <div class="col-lg-8 vf-article-col min-w-0">
                <div class="vf-surface-card p-4 p-lg-5 mb-4">
                    @if($isLocked)
                        <div class="text-center py-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-danger text-white rounded-circle mb-3" style="width: 64px; height: 64px;">
                                <i class="fas fa-lock fs-4"></i>
                            </div>
                            <h3 class="fw-bold text-body mb-2">Protected Visual Feature</h3>
                            <p class="text-muted mb-4">Enter the access passphrase to inspect the complete longform essay and commentary.</p>
                            <form action="{{ route('blog.unlock', $post->slug) }}" method="POST" class="mx-auto" style="max-width: 420px;">
                                @csrf
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" placeholder="Passphrase..." required>
                                    <button type="submit" class="btn btn-primary fw-semibold px-4">Unlock Essay</button>
                                </div>
                            </form>
                        </div>
                    @else
                        @if(!empty($tableOfContents) && count($tableOfContents) > 0)
                            <div class="p-3 p-md-4 rounded-4 vf-subpanel mb-4" data-vf-section="toc">
                                <div class="fw-bold text-body mb-2 d-flex align-items-center gap-2">
                                    <i class="fas fa-list-ul text-primary"></i> Essay Sections
                                </div>
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-1">
                                    @foreach($tableOfContents as $toc)
                                        <li style="padding-left: {{ max(0, ($toc['level'] - 2) * 14) }}px;">
                                            <a href="#{{ $toc['id'] }}" class="text-decoration-none text-muted small fw-medium">
                                                <i class="fas fa-chevron-right text-primary me-1" style="font-size: 9px;"></i> {{ $toc['title'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($post->excerpt)
                            <p class="vf-drop-cap lead fs-4 fw-medium text-body mb-4 lh-base">
                                {{ $post->excerpt }}
                            </p>
                        @endif

                        <div class="vf-prose text-body">
                            {!! $contentWithAnchors ?: $post->content !!}
                        </div>

                        <!-- Dynamic Context Callout -->
                        <blockquote class="vf-callout-quote">
                            <div class="d-flex align-items-center gap-2 text-primary fw-bold small text-uppercase mb-1" style="letter-spacing: 0.7px;">
                                <i class="fas fa-compass"></i> Edition Brief &bull; {{ $catName }}
                            </div>
                            <p class="mb-0 text-body fw-medium">
                                Filed under <strong>{{ $catName }}</strong> ({{ $post->reading_time ?? 4 }}-minute visual feature) in high-resolution {{ $visualTelemetry['dimensions'] }} presentation by {{ $authorName }}.
                            </p>
                        </blockquote>

                        <!-- Tags & Toast Share Bar -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-4 mt-4 border-top">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="fw-bold text-body small"><i class="fas fa-tags text-primary me-1"></i> Desk:</span>
                                <a href="{{ route('blog.category', $catId) }}" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none px-3 py-2 rounded-pill">
                                    #{{ $catName }}
                                </a>
                                @if($post->tag)
                                    <a href="{{ route('blog.tag', $post->tag->id) }}" class="badge bg-body-secondary text-body border text-decoration-none px-3 py-2 rounded-pill">
                                        #{{ $post->tag->name }}
                                    </a>
                                @endif
                            </div>

                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="fw-bold text-body small text-uppercase me-1"><i class="fas fa-share-alt text-primary me-1"></i> Share:</span>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-circle-btn share-facebook" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" class="share-circle-btn share-twitter" title="Share on X"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-circle-btn share-linkedin" title="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}" target="_blank" class="share-circle-btn share-whatsapp" title="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                <button type="button" class="share-circle-btn share-copylink" onclick="vfCopyShareLink()" title="Copy Visual Essay Link"><i class="fas fa-link"></i></button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Author Dossier Card -->
                <div class="vf-surface-card p-4 mb-4" data-vf-section="author">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle fw-bold flex-shrink-0" style="width: 56px; height: 56px; font-size: 20px;">
                                {{ $authorInitial }}
                            </span>
                            <div class="min-w-0">
                                <span class="text-primary small fw-bold text-uppercase" style="letter-spacing: 0.6px;">Essay Author</span>
                                <h5 class="fw-bold mb-1 text-body text-truncate">{{ $authorName }}</h5>
                                <p class="text-muted small mb-0">
                                    {{ $post->author?->bio ?? 'Contributing visual journalist & systems editor covering design architecture and global dispatches.' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('blog.author', $authorId) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold flex-shrink-0">
                            Author Profile <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Previous / Next Visual Navigation -->
                @if($previousPost || $nextPost)
                <div class="row g-3 mb-4" data-vf-section="prev-next">
                    <div class="col-md-6">
                        @if($previousPost)
                            <a href="{{ route('blog.format.image', $previousPost->slug) }}" class="vf-surface-card p-3 d-block text-decoration-none h-100">
                                <div class="text-muted small mb-1"><i class="fas fa-arrow-left me-1 text-primary"></i> Previous Plate</div>
                                <div class="fw-bold text-body mag-line-clamp-2" style="font-size: 14px;">{{ $previousPost->title }}</div>
                            </a>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($nextPost)
                            <a href="{{ route('blog.format.image', $nextPost->slug) }}" class="vf-surface-card p-3 d-block text-decoration-none text-md-end h-100">
                                <div class="text-muted small mb-1">Next Plate <i class="fas fa-arrow-right ms-1 text-primary"></i></div>
                                <div class="fw-bold text-body mag-line-clamp-2" style="font-size: 14px;">{{ $nextPost->title }}</div>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Community Discussion & Comments -->
                @if(!$isLocked)
                <section class="vf-surface-card p-4" data-vf-section="comments">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="fw-bold text-body m-0">
                            <i class="far fa-comments me-2 text-primary"></i> Reader Critique ({{ $post->comments->count() }})
                        </h4>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">Open Thread</span>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success small">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('comments.store') }}" method="POST" class="mb-4 pb-4 border-bottom">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        <div class="mb-3">
                            <textarea name="comment" rows="3" class="form-control" placeholder="Share a perspective or visual critique on this essay..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold">
                            <i class="fas fa-paper-plane me-1"></i> Post Critique
                        </button>
                    </form>

                    <div class="d-flex flex-column gap-3">
                        @forelse($post->comments as $comment)
                            <div class="p-3 rounded-4 vf-subpanel">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($comment->user->name ?? 'R', 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="fw-bold text-body" style="font-size: 13.5px;">{{ $comment->user->name ?? 'Reader' }}</div>
                                        <div class="text-muted" style="font-size: 11px;">{{ $comment->created_at?->diffForHumans() ?? 'Recently' }}</div>
                                    </div>
                                </div>
                                <p class="mb-0 text-body small lh-base">{{ $comment->comment }}</p>
                            </div>
                        @empty
                            <p class="text-muted small mb-0 text-center py-2">
                                Be the first reader to leave a visual critique on this story.
                            </p>
                        @endforelse
                    </div>
                </section>
                @endif
            </div>

            <!-- Sticky Sidebar (Categories strictly <= 10, Tags strictly <= 25) -->
            <div class="col-lg-4 vf-sidebar-col">
                <div class="position-sticky" style="top: 90px;">
                    @include('blog.partials.sidebar')
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Related Visual & Editorial Stories Showcase -->
    @if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
    <section class="py-5 border-top" data-vf-section="related">
        <div class="container">
            <div class="mag-section-heading">
                <h3 class="mag-section-title text-body">
                    <i class="fas fa-images text-primary"></i> More Visual &amp; Editorial Features
                </h3>
                <a href="{{ route('blog.gallery') }}" class="text-primary small fw-semibold text-decoration-none">
                    Explore All Visuals <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="row g-4">
                @foreach($relatedPosts as $rPost)
                    @php
                        $rSrc = $rPost->image?->url ?? $fallbackThumb;
                        $rCatId = $rPost->category?->id ?? 1;
                    @endphp
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <article class="mag-card h-100 d-flex flex-column">
                            <div class="card-img-wrapper" style="height: 185px;">
                                <a href="{{ route('blog.category', $rCatId) }}" class="badge-category text-decoration-none">
                                    {{ Str::limit($rPost->category?->name ?? 'Visual', 18) }}
                                </a>
                                <a href="{{ route('blog.format.image', $rPost->slug) }}" class="d-block w-100 h-100">
                                    <img src="{{ $rSrc }}" class="w-100 h-100 object-fit-cover" alt="{{ $rPost->title }}">
                                </a>
                            </div>
                            <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                                <h4 class="h6 fw-bold mb-2 mag-line-clamp-2 lh-sm">
                                    <a href="{{ route('blog.format.image', $rPost->slug) }}" class="text-decoration-none text-body">
                                        {{ $rPost->title }}
                                    </a>
                                </h4>
                                <div class="pt-2 border-top d-flex align-items-center justify-content-between text-muted" style="font-size: 11.5px;">
                                    <span><i class="far fa-user me-1"></i> {{ Str::limit($rPost->author?->user?->name ?? ($rPost->author?->nickname ?? 'Editor'), 14) }}</span>
                                    <span><i class="far fa-clock me-1"></i> {{ $rPost->reading_time ?? 3 }}m</span>
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

<!-- Full-Screen Cinema Lightbox Modal -->
<div class="vf-lightbox" id="vfLightbox" role="dialog" aria-modal="true" aria-label="Full Resolution Visual Studio">
    <div class="d-flex align-items-center justify-content-between text-white">
        <div class="d-flex align-items-center gap-3 min-w-0">
            <span class="badge bg-primary rounded-pill px-3 py-2" id="vfLightboxCounter">1 / {{ $visualFrames->count() }}</span>
            <span class="fw-semibold text-truncate" id="vfLightboxTitle">{{ $visualTelemetry['caption'] }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="vf-glass-btn" id="vfLightboxZoomBtn" title="Toggle Optical Zoom (1.45x)">
                <i class="fas fa-search-plus"></i> <span>Zoom</span>
            </button>
            <button type="button" class="vf-glass-btn" id="vfCloseLightboxBtn" title="Close Lightbox (Esc)">
                <i class="fas fa-times"></i> <span>Close</span>
            </button>
        </div>
    </div>

    <div class="vf-lightbox-stage">
        <button type="button" class="vf-glass-btn position-absolute start-0 ms-2" id="vfPrevFrameBtn" aria-label="Previous Plate">
            <i class="fas fa-chevron-left"></i>
        </button>
        <img id="vfLightboxImg" src="{{ $visualTelemetry['src'] }}" alt="{{ $visualTelemetry['alt'] }}">
        <button type="button" class="vf-glass-btn position-absolute end-0 me-2" id="vfNextFrameBtn" aria-label="Next Plate">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <div class="text-center text-white-50 small">
        Click image or press <kbd class="bg-dark border border-secondary px-2 py-1 rounded">Esc</kbd> to exit &bull; Use <kbd class="bg-dark border border-secondary px-2 py-1 rounded">&larr;</kbd> <kbd class="bg-dark border border-secondary px-2 py-1 rounded">&rarr;</kbd> keys to cycle plates
    </div>
</div>

<!-- Non-Blocking Share Toast -->
<div class="vf-toast" id="vfToast" role="status" aria-live="polite">
    <i class="fas fa-check-circle text-success me-2"></i> Visual essay link copied to clipboard
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const frames = @json($visualFrames->values());
    let currentFrame = 0;

    // 1. Scroll reading progress bar
    const progressBar = document.getElementById('vfProgressBar');
    function updateProgress() {
        if (!progressBar) return;
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const pct = docHeight > 0 ? Math.min(100, Math.max(0, (scrollTop / docHeight) * 100)) : 0;
        progressBar.style.width = pct + '%';
    }
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();

    // 2. Zen Mode toggle
    const zenToggle = document.getElementById('vfZenToggle');
    const zenLabel = document.getElementById('vfZenLabel');
    const articleRoot = document.getElementById('vfArticleRoot');
    if (zenToggle && articleRoot) {
        zenToggle.addEventListener('click', function () {
            const active = articleRoot.classList.toggle('vf-zen-mode');
            if (zenLabel) {
                zenLabel.textContent = active ? 'Exit Zen' : 'Zen Mode';
            }
        });
    }

    // 3. Filmstrip stage switching + Cinema Lightbox
    const heroBg = document.getElementById('vfHeroBg');
    const activeCaption = document.getElementById('vfActivePlateCaption');
    const filmBtns = document.querySelectorAll('.vf-filmstrip-btn');
    const lightbox = document.getElementById('vfLightbox');
    const lightboxImg = document.getElementById('vfLightboxImg');
    const lightboxTitle = document.getElementById('vfLightboxTitle');
    const lightboxCounter = document.getElementById('vfLightboxCounter');

    function setActiveFrame(idx) {
        if (!frames.length) return;
        currentFrame = (idx + frames.length) % frames.length;
        const f = frames[currentFrame];
        if (heroBg) heroBg.src = f.url;
        if (activeCaption) activeCaption.textContent = f.title || '';
        filmBtns.forEach((btn, i) => btn.classList.toggle('active', i === currentFrame));
        if (lightboxImg) {
            lightboxImg.src = f.url;
            lightboxImg.alt = f.alt || f.title || '';
            lightboxImg.classList.remove('zoomed');
        }
        if (lightboxTitle) lightboxTitle.textContent = f.title || '';
        if (lightboxCounter) lightboxCounter.textContent = (currentFrame + 1) + ' / ' + frames.length;
    }

    filmBtns.forEach((btn) => {
        btn.addEventListener('click', function () {
            const idx = parseInt(this.dataset.frameIdx || '0', 10);
            setActiveFrame(idx);
        });
        btn.addEventListener('dblclick', function () {
            openLightbox(parseInt(this.dataset.frameIdx || '0', 10));
        });
    });

    function openLightbox(idx) {
        if (!lightbox) return;
        if (typeof idx === 'number') setActiveFrame(idx);
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.classList.remove('open');
        document.body.style.overflow = '';
        if (lightboxImg) lightboxImg.classList.remove('zoomed');
    }

    document.getElementById('vfOpenLightboxBtn')?.addEventListener('click', () => openLightbox(currentFrame));
    document.getElementById('vfDockLightboxBtn')?.addEventListener('click', () => openLightbox(currentFrame));
    document.getElementById('vfCloseLightboxBtn')?.addEventListener('click', closeLightbox);
    document.getElementById('vfPrevFrameBtn')?.addEventListener('click', () => setActiveFrame(currentFrame - 1));
    document.getElementById('vfNextFrameBtn')?.addEventListener('click', () => setActiveFrame(currentFrame + 1));

    const toggleZoom = () => lightboxImg?.classList.toggle('zoomed');
    document.getElementById('vfLightboxZoomBtn')?.addEventListener('click', toggleZoom);
    lightboxImg?.addEventListener('click', toggleZoom);

    document.addEventListener('keydown', function (e) {
        if (!lightbox || !lightbox.classList.contains('open')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') setActiveFrame(currentFrame - 1);
        if (e.key === 'ArrowRight') setActiveFrame(currentFrame + 1);
    });
});

function vfCopyShareLink() {
    const showToast = () => {
        const toast = document.getElementById('vfToast');
        if (!toast) return;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2600);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(window.location.href).then(showToast).catch(showToast);
    } else {
        showToast();
    }
}
</script>
@endsection
