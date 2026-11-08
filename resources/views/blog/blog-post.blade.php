@extends('blog')
@section('title', $post->title . ' - Xivig Editorial')
@section('content')

<!-- Header Breadcrumb Banner -->
<section class="page-title" style="padding: 100px 0 50px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto text-center">
                <span class="badge-elite badge-elite-dark mb-3">
                    <span class="badge-dot pulse"></span> {{ $post->category->name ?? 'Architecture' }}
                </span>
                <h1 class="font-weight-bold mb-4 display-4" style="line-height: 1.2; letter-spacing: -0.02em;">
                    {{ $post->title }}
                </h1>
                <div class="d-flex align-items-center justify-content-center text-muted small flex-wrap" style="gap: 18px;">
                    <span><i class="far fa-user mr-1 text-primary"></i> By <strong>{{ $post->author->nickname ?? ($post->user->name ?? 'Lead Architect') }}</strong></span>
                    <span>&bull;</span>
                    <span><i class="far fa-calendar-alt mr-1 text-primary"></i> {{ $post->created_at ? $post->created_at->format('M d, Y') : 'Recent' }}</span>
                    <span>&bull;</span>
                    <span><i class="far fa-comments mr-1 text-primary"></i> {{ $post->comments ? $post->comments->count() : 0 }} Comments</span>
                    <span>&bull;</span>
                    <span><i class="far fa-clock mr-1 text-primary"></i> {{ max(3, round(str_word_count(strip_tags($post->content ?? $post->excerpt)) / 180)) }} min read</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Single Post Content Area -->
<section class="section py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content Column -->
            <div class="col-lg-8 mb-5 mb-lg-0">
                <article class="single-blog-item">
                    @if($post->image)
                        @php
                            $coverLink = $post->image->link;
                            if (!Str::startsWith($coverLink, ['http://', 'https://', 'uploads/'])) {
                                $coverLink = asset('xivig-blog/images/blog/' . $coverLink);
                            } else {
                                $coverLink = asset($coverLink);
                            }
                        @endphp
                        <img loading="lazy" src="{{ $coverLink }}" alt="{{ $post->title }}" class="single-post-hero-img" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                    @endif

                    <div class="single-blog-content">
                        @if($post->excerpt)
                            <p class="lead mb-4 font-weight-normal">{{ $post->excerpt }}</p>
                        @endif

                        <div class="post-body-text mb-5">
                            @if(strip_tags($post->content) !== $post->content)
                                {!! $post->content !!}
                            @else
                                {!! nl2br(e($post->content)) !!}
                            @endif
                        </div>

                        <blockquote class="quote">
                            "Modern software architectures demand simplicity, deterministic behavior, and ruthless elegance at the boundary of code and infrastructure."
                        </blockquote>

                        <p>Continuous evolution across modern frameworks like Laravel 13 empowers engineering teams to build reactive, robust, and horizontally scalable cloud platforms with unmatched developer velocity.</p>

                        <!-- Tags and Social Share -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center pt-4 pb-3 mt-5" style="border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center my-2">
                                <span class="font-weight-bold mr-3 text-dark"><i class="fas fa-tags text-primary mr-1"></i> Tags:</span>
                                @if($post->tag)
                                    <span class="badge badge-light p-2 font-weight-bold mr-2 text-primary" style="font-size: 13px;">#{{ $post->tag->name }}</span>
                                @endif
                                <span class="badge badge-light p-2 font-weight-bold text-muted" style="font-size: 13px;">#Laravel13</span>
                                <span class="badge badge-light p-2 font-weight-bold text-muted ml-2" style="font-size: 13px;">#Architecture</span>
                            </div>

                            <div class="d-flex align-items-center my-2">
                                <span class="font-weight-bold mr-3 text-dark">Share:</span>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" class="footer-social-btn mr-2" aria-label="Share on X"><i class="bi bi-twitter-x"></i></a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" class="footer-social-btn mr-2" aria-label="Share on LinkedIn"><i class="bi bi-linkedin"></i></a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="footer-social-btn" aria-label="Share on Facebook"><i class="bi bi-facebook"></i></a>
                            </div>
                        </div>

                        <!-- Author Bio Card -->
                        <div class="p-4 my-5 rounded-lg d-flex align-items-center author-box" style="border-radius: 16px;">
                            <div class="mr-4">
                                <div class="comment-avatar" style="width: 64px; height: 64px; font-size: 24px;">
                                    {{ strtoupper(substr($post->author->nickname ?? ($post->user->name ?? 'A'), 0, 1)) }}
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1 font-weight-bold text-dark">{{ $post->author->nickname ?? ($post->user->name ?? 'Author') }}</h5>
                                <span class="text-primary font-weight-bold small">Senior Systems Contributor</span>
                                <p class="text-muted small mb-0 mt-2">Writing on distributed computing, modern full-stack workflows, and enterprise application architectures.</p>
                            </div>
                        </div>

                        <!-- Flash Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Comments Section -->
                        <div class="comment-area pt-4">
                            <h4 class="mb-4 font-weight-bold text-dark">
                                <i class="far fa-comments text-primary mr-2"></i> Discussion ({{ $post->comments ? $post->comments->count() : 0 }})
                            </h4>

                            @if($post->comments && $post->comments->count() > 0)
                                @foreach($post->comments as $comment)
                                    <div class="comment-area-box">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="comment-avatar mr-3" style="width: 40px; height: 40px; font-size: 16px;">
                                                    {{ strtoupper(substr($comment->user->name ?? 'G', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold text-dark">{{ $comment->user->name ?? 'Reader' }}</h6>
                                                    <span class="text-muted small">{{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Just now' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="comment-content mt-2 text-dark" style="font-size: 15px;">
                                            <p class="mb-0">{{ $comment->comment }}</p>
                                        </div>

                                        <!-- Render Replies if present -->
                                        @if($comment->replies && $comment->replies->count() > 0)
                                            <div class="pl-4 mt-3 pt-3" style="border-left: 2px solid #e2e8f0;">
                                                @foreach($comment->replies as $reply)
                                                    <div class="mb-3">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <strong class="text-dark small mr-2">{{ $reply->user->name ?? 'Team Member' }}</strong>
                                                            <span class="text-muted" style="font-size: 12px;">{{ $reply->created_at ? $reply->created_at->diffForHumans() : 'Recent' }}</span>
                                                        </div>
                                                        <p class="mb-0 small text-dark">{{ $reply->reply }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 bg-light rounded mb-4">
                                    <p class="text-muted mb-0">No comments yet. Start the conversation below!</p>
                                </div>
                            @endif
                        </div>

                        <!-- Comment Submission Form -->
                        <div class="mt-5 p-4 bg-white rounded shadow-sm border">
                            <h4 class="mb-3 font-weight-bold text-dark">Leave a Comment</h4>
                            <p class="text-muted small mb-4">Share your perspectives or ask a question regarding this article.</p>

                            <form action="{{ route('comments.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $post->id }}">
                                
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-dark">Your Comment</label>
                                    <textarea class="form-control form-control-elite" name="comment" rows="4" placeholder="Write your thoughts..." required></textarea>
                                </div>

                                <button type="submit" class="btn-elite btn-elite-primary btn-sm">
                                    <i class="fas fa-paper-plane mr-2"></i> Submit Comment
                                </button>
                            </form>
                        </div>

                    </div>
                </article>
            </div>

            <!-- Sidebar Column -->
            <div class="col-lg-4">
                <!-- Search Widget -->
                <div class="sidebar-widget">
                    <h5 class="sidebar-widget-title"><i class="bi bi-search text-primary me-2 mr-2"></i> Search Insights</h5>
                    <form action="{{ url('/blog-list') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-elite me-2 mr-2" placeholder="Search architecture, 2FA, APIs...">
                        <button type="submit" class="btn-elite btn-elite-primary btn-sm"><i class="bi bi-search"></i></button>
                    </form>
                </div>

                <!-- About Publisher Widget -->
                <div class="sidebar-widget">
                    <div class="d-flex align-items-center mb-3">
                        <span class="brand-text-gradient font-weight-bold h4 mb-0">Xivig</span>
                        <span class="badge-v13 ms-auto ml-auto"><span class="badge-dot pulse"></span> v13 Engine</span>
                    </div>
                    <p class="text-muted small mb-3">
                        Xivig publishes high-fidelity software engineering architectural insights, developer ergonomics, and modern framework patterns built with Laravel 13, Fortify, and Jetstream.
                    </p>
                    <a href="{{ url('/about') }}" class="btn-elite btn-elite-outline btn-sm w-100 text-center">
                        <i class="bi bi-info-circle me-1 mr-1"></i> About Our Mission
                    </a>
                </div>

                <!-- Trending / Recent Articles Widget -->
                @if(isset($recentPosts) && count($recentPosts) > 0)
                    <div class="sidebar-widget">
                        <h5 class="sidebar-widget-title"><i class="bi bi-fire text-danger me-2 mr-2"></i> Trending Articles</h5>
                        <div class="sidebar-recent-posts">
                            @foreach($recentPosts as $recent)
                                <div class="recent-post-item">
                                    <a href="{{ route('single-post', $recent->slug) }}" class="recent-post-thumb-link">
                                        <img src="{{ $recent->image->link ?? 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=160&auto=format&fit=crop&q=60' }}" 
                                             alt="{{ $recent->title }}" class="recent-post-thumb">
                                    </a>
                                    <div class="recent-post-body">
                                        <span class="badge badge-light text-primary font-weight-bold recent-post-badge">
                                            {{ $recent->category->name ?? 'General' }}
                                        </span>
                                        <h6 class="recent-post-title">
                                            <a href="{{ route('single-post', $recent->slug) }}" class="blog-title-link">
                                                {{ $recent->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted"><i class="bi bi-clock me-1 mr-1"></i> {{ $recent->created_at ? $recent->created_at->format('M d, Y') : 'Recent' }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Categories Widget -->
                <div class="sidebar-widget">
                    <h5 class="sidebar-widget-title"><i class="bi bi-folder2-open text-primary me-2 mr-2"></i> Topic Categories</h5>
                    <ul class="list-unstyled mb-0 sidebar-cat-list">
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories->take(8) as $cat)
                                <li class="py-2 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(226, 232, 240, 0.6);">
                                    <a href="{{ url('/blog-list?category=' . urlencode($cat->name)) }}" class="sidebar-cat-link">
                                        <i class="bi bi-chevron-right text-primary me-2 mr-2" style="font-size: 11px;"></i> {{ $cat->name }}
                                    </a>
                                    <span class="badge badge-light text-muted font-weight-bold">{{ $cat->posts_count ?? ($cat->posts ? $cat->posts->count() : 0) }}</span>
                                </li>
                            @endforeach
                        @else
                            <li class="text-muted small">No categories registered.</li>
                        @endif
                    </ul>
                </div>

                <!-- Technical Tags Cloud (Limited to 25) -->
                <div class="sidebar-widget">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="sidebar-widget-title mb-0"><i class="bi bi-tags text-primary me-2 mr-2"></i> Technical Tags</h5>
                        <span class="badge badge-light text-muted font-weight-bold small">{{ min(25, count($tags ?? [])) }}</span>
                    </div>
                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        @if(isset($tags) && count($tags) > 0)
                            @foreach($tags->take(25) as $t)
                                <a href="{{ url('/blog-list?tag=' . urlencode($t->name)) }}" class="tag-pill">#{{ $t->name }}</a>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Newsletter Dispatch -->
                <div class="sidebar-widget sidebar-newsletter-widget">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-envelope-paper text-primary me-2 mr-2 h5 mb-0"></i>
                        <h5 class="mb-0 font-weight-bold">Architecture Dispatch</h5>
                    </div>
                    <p class="text-muted small mb-3">
                        Curated enterprise engineering patterns, zero-trust protocols, and framework deep-dives delivered every Tuesday.
                    </p>
                    <form onsubmit="event.preventDefault(); alert('Subscribed to Architecture Dispatch!');">
                        <div class="mb-2">
                            <input type="email" class="form-control form-control-elite" placeholder="architect@company.com" required>
                        </div>
                        <button type="submit" class="btn-elite btn-elite-primary btn-sm w-100">
                            <i class="bi bi-send me-1 mr-1"></i> Subscribe Free
                        </button>
                    </form>
                    <small class="text-muted d-block text-center mt-2" style="font-size: 11px;">Zero spam. One-click unsubscribe.</small>
                </div>

                <!-- Developer Guild & Community -->
                <div class="sidebar-widget text-center">
                    <h6 class="font-weight-bold mb-2">Join Our Developer Guild</h6>
                    <p class="text-muted small mb-3">Collaborate with fellow systems engineers and contributors across our open-source repositories.</p>
                    <div class="d-flex justify-content-center" style="gap: 10px;">
                        <a href="https://github.com/xivig/" target="_blank" class="footer-social-btn" title="GitHub"><i class="bi bi-github"></i></a>
                        <a href="https://twitter.com/xivig" target="_blank" class="footer-social-btn" title="X / Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://www.linkedin.com/company/xivig" target="_blank" class="footer-social-btn" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="https://www.facebook.com/xivig" target="_blank" class="footer-social-btn" title="Facebook"><i class="bi bi-facebook"></i></a>
                    </div>
                </div>

                <!-- Browse Full Archive Navigation -->
                <div class="sidebar-widget text-center">
                    <h6 class="font-weight-bold mb-2">Explore the Full Archive</h6>
                    <p class="text-muted small mb-3">Access our 3-column magazine or 4-column master catalog.</p>
                    <div>
                        <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-primary btn-sm w-100 mb-2">
                            <i class="bi bi-grid-3x3-gap me-2 mr-2"></i> 3-Column Magazine
                        </a>
                        <a href="{{ url('/blog') }}" class="btn-elite btn-elite-outline btn-sm w-100">
                            <i class="bi bi-grid me-2 mr-2"></i> 4-Column All Articles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
