@extends('layouts.blog')

@section('content')

@if(isset($isPreview) && $isPreview)
    <div class="alert alert-warning mb-0 border-0 rounded-0 d-flex justify-content-between align-items-center py-2 px-4 shadow-sm" style="position: sticky; top: 0; z-index: 1050;">
        <span class="small font-weight-bold">
            <i class="fas fa-eye mr-2 text-dark"></i> PREVIEW MODE: You are viewing a 
            <span class="badge badge-dark text-uppercase">{{ $post->post_status ?? ($post->status ? 'published' : 'draft') }}</span> 
            article. Not yet visible to public readers.
        </span>
        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-sm btn-dark font-weight-bold">
            <i class="fas fa-edit mr-1"></i> Edit Article
        </a>
    </div>
@endif

<!-- Header Breadcrumb & Article Hero Banner -->
<section class="page-title" style="padding: 90px 0 45px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto text-center">
                <!-- Navigational Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb justify-content-center bg-transparent p-0 small text-uppercase font-weight-bold letter-spacing-1">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted"><i class="fas fa-home mr-1"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/blog-list') }}" class="text-muted">Blog</a></li>
                        @if($post->category)
                            <li class="breadcrumb-item"><a href="{{ url('/blog-list?category=' . $post->category->id) }}" class="text-muted">{{ $post->category->name }}</a></li>
                        @endif
                        <li class="breadcrumb-item active text-primary" aria-current="page">{{ Str::limit($post->title, 35) }}</li>
                    </ol>
                </nav>

                <!-- Category & Badges -->
                <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 mb-3">
                    @if($post->is_sticky)
                        <span class="badge badge-danger p-2 font-weight-bold mr-2">
                            <i class="fas fa-thumbtack mr-1"></i> Pinned
                        </span>
                    @endif
                    @if($post->is_featured)
                        <span class="badge badge-warning text-dark p-2 font-weight-bold mr-2">
                            <i class="fas fa-star mr-1"></i> Featured
                        </span>
                    @endif
                    <span class="badge-elite badge-elite-dark">
                        <span class="badge-dot pulse"></span> {{ $post->category->name ?? 'Architecture' }}
                    </span>
                    @if($post->visibility === 'password_protected')
                        <span class="badge badge-info p-2 font-weight-bold ml-2">
                            <i class="fas fa-lock mr-1"></i> Protected
                        </span>
                    @elseif($post->visibility === 'private')
                        <span class="badge badge-secondary p-2 font-weight-bold ml-2">
                            <i class="fas fa-user-shield mr-1"></i> Private
                        </span>
                    @endif
                </div>

                <h1 class="font-weight-bold mb-4 display-4 text-body" style="line-height: 1.2; letter-spacing: -0.02em;">
                    {{ $post->title }}
                </h1>

                <div class="d-flex align-items-center justify-content-center text-muted small flex-wrap" style="gap: 18px;">
                    <span><i class="far fa-user mr-1 text-primary"></i> By <strong>{{ $post->author->nickname ?? ($post->user->name ?? 'Lead Architect') }}</strong></span>
                    <span>&bull;</span>
                    <span><i class="far fa-calendar-alt mr-1 text-primary"></i> {{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span>
                    <span>&bull;</span>
                    <span><i class="far fa-comments mr-1 text-primary"></i> {{ $post->comments ? $post->comments->count() : 0 }} Comments</span>
                    <span>&bull;</span>
                    <span><i class="far fa-clock mr-1 text-primary"></i> {{ $post->reading_time }} min read</span>
                    @if($post->views_count > 0)
                        <span>&bull;</span>
                        <span><i class="far fa-eye mr-1 text-primary"></i> {{ number_format($post->views_count) }} views</span>
                    @endif
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
                            $resolvedHero = Str::startsWith($coverLink, ['http://', 'https://']) ? $coverLink : asset($coverLink);
                        @endphp
                        <img loading="lazy" src="{{ $resolvedHero }}" alt="{{ $post->title }}" class="single-post-hero-img shadow-sm rounded-lg mb-4" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                    @endif

                    <div class="single-blog-content">
                        @if($post->excerpt)
                            <p class="lead mb-4 font-weight-normal text-muted">{{ $post->excerpt }}</p>
                        @endif

                        {{-- Password Protection / Private Lock Gate --}}
                        @if(!empty($isLocked) && $isLocked)
                            @if($post->visibility === 'password_protected')
                                <div class="card p-5 text-center my-4 shadow-sm border rounded-lg bg-light">
                                    <div class="mb-3">
                                        <i class="fas fa-lock text-primary" style="font-size: 3rem;"></i>
                                    </div>
                                    <h3 class="font-weight-bold text-body">Protected Content</h3>
                                    <p class="text-muted mb-4">This article requires an access passphrase to view.</p>

                                    @if(session('error'))
                                        <div class="alert alert-danger mx-auto mb-3" style="max-width: 420px;">
                                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                                        </div>
                                    @endif

                                    <form action="{{ route('blog.unlock', $post->slug) }}" method="POST" class="mx-auto w-100" style="max-width: 440px;">
                                        @csrf
                                        <div class="unlock-box-elite">
                                            <span class="unlock-icon-slot">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" name="password" class="unlock-field-elite" placeholder="Enter passphrase..." required autofocus>
                                            <button class="unlock-btn-elite" type="submit">
                                                <i class="fas fa-key mr-1"></i> Unlock
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @elseif($post->visibility === 'private')
                                <div class="card p-5 text-center my-4 shadow-sm border rounded-lg bg-light">
                                    <div class="mb-3">
                                        <i class="fas fa-user-shield text-warning" style="font-size: 3rem;"></i>
                                    </div>
                                    <h3 class="font-weight-bold text-body">Exclusive Member Article</h3>
                                    <p class="text-muted mb-4">This publication is private and restricted to verified registered readers.</p>
                                    <div>
                                        <a href="{{ route('login') }}" class="btn btn-primary font-weight-bold px-4 mr-2">
                                            <i class="fas fa-sign-in-alt mr-1"></i> Sign In
                                        </a>
                                        <a href="{{ route('register') }}" class="btn btn-outline-secondary font-weight-bold px-4">
                                            Create Account
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- Table of Contents (TOC) --}}
                            @if(!empty($tableOfContents) && count($tableOfContents) > 0)
                                <div class="article-toc-box p-4 mb-4 rounded-lg bg-light border shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 font-weight-bold text-uppercase text-muted" style="letter-spacing: 1px; font-size: 0.8125rem;">
                                            <i class="fas fa-list-ul text-primary mr-2"></i> Table of Contents
                                        </h6>
                                        <button class="btn btn-sm btn-link text-muted p-0 font-weight-bold text-decoration-none" type="button" data-toggle="collapse" data-target="#tocCollapse" aria-expanded="true">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="collapse show" id="tocCollapse">
                                        <ul class="list-unstyled mb-0 toc-list pt-2">
                                            @foreach($tableOfContents as $item)
                                                <li class="toc-item toc-level-{{ $item['level'] }} py-1 {{ $item['level'] === 3 ? 'pl-4' : '' }}">
                                                    <a href="#{{ $item['id'] }}" class="text-body text-decoration-none small">
                                                        <i class="fas fa-chevron-right text-primary mr-1" style="font-size: 10px;"></i>
                                                        {{ $item['text'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            {{-- Post Body with Heading Anchors --}}
                            <div class="post-body-text mb-5 text-body" style="font-size: 1.0625rem; line-height: 1.8;">
                                @if(!empty($contentWithAnchors))
                                    {!! $contentWithAnchors !!}
                                @elseif(strip_tags($post->content) !== $post->content)
                                    {!! $post->content !!}
                                @else
                                    {!! nl2br(e($post->content)) !!}
                                @endif
                            </div>

                            {{-- Unlimited Images / Photo Gallery --}}
                            @if(!empty($galleryImages) && $galleryImages->count() > 0)
                                <div class="post-gallery-section my-5 pt-4 border-top">
                                    <h5 class="font-weight-bold mb-3 text-body">
                                        <i class="fas fa-images text-primary mr-2"></i> Photo Gallery & Visual Architecture ({{ $galleryImages->count() }})
                                    </h5>
                                    <div class="row">
                                        @foreach($galleryImages as $img)
                                            <div class="col-md-6 mb-4">
                                                <div class="gallery-item-card rounded-lg overflow-hidden shadow-sm border h-100 bg-light">
                                                    <a href="{{ $img->url }}" target="_blank" class="d-block overflow-hidden">
                                                        <img src="{{ $img->url }}" alt="{{ $img->alt_text ?: $post->title }}" class="img-fluid w-100" style="height: 240px; object-fit: cover;" loading="lazy">
                                                    </a>
                                                    @if($img->caption)
                                                        <div class="p-3 bg-white small text-muted text-center border-top">
                                                            {{ $img->caption }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Tags and Share Buttons Suite -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center pt-4 pb-3 mt-5" style="border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center my-2 flex-wrap">
                                <span class="font-weight-bold mr-3 text-body"><i class="fas fa-tags text-primary mr-1"></i> Tags:</span>
                                @if($post->tag)
                                    <a href="{{ url('/blog-list?tag=' . $post->tag->id) }}" class="badge badge-light p-2 font-weight-bold mr-2 text-primary" style="font-size: 13px;">#{{ $post->tag->name }}</a>
                                @endif
                                @if($post->category)
                                    <a href="{{ url('/blog-list?category=' . $post->category->id) }}" class="badge badge-light p-2 font-weight-bold text-muted mr-2" style="font-size: 13px;">#{{ $post->category->name }}</a>
                                @endif
                            </div>

                            <div class="my-2">
                                @include('partials.share-buttons', ['shareUrl' => url()->current(), 'shareTitle' => $post->title])
                            </div>
                        </div>

                        <!-- Previous / Next Adjacent Article Navigation -->
                        @if(!empty($previousPost) || !empty($nextPost))
                            <div class="post-prev-next-nav my-5 pt-3">
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        @if($previousPost)
                                            <a href="{{ route('single-post', $previousPost->slug) }}" class="card p-3 h-100 text-decoration-none border shadow-sm rounded-lg bg-light text-left transition-all">
                                                <span class="text-primary small font-weight-bold mb-1"><i class="fas fa-arrow-left mr-1"></i> PREVIOUS ARTICLE</span>
                                                <h6 class="font-weight-bold text-body mb-0">{{ Str::limit($previousPost->title, 55) }}</h6>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        @if($nextPost)
                                            <a href="{{ route('single-post', $nextPost->slug) }}" class="card p-3 h-100 text-decoration-none border shadow-sm rounded-lg bg-light text-md-right transition-all">
                                                <span class="text-primary small font-weight-bold mb-1">NEXT ARTICLE <i class="fas fa-arrow-right ml-1"></i></span>
                                                <h6 class="font-weight-bold text-body mb-0">{{ Str::limit($nextPost->title, 55) }}</h6>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Related Articles Card Grid -->
                        @if(!empty($relatedPosts) && $relatedPosts->count() > 0)
                            <div class="related-posts-section my-5 pt-4 border-top">
                                <h4 class="font-weight-bold mb-4 text-body">
                                    <i class="fas fa-layer-group text-primary mr-2"></i> Related Articles
                                </h4>
                                <div class="row">
                                    @foreach($relatedPosts as $rel)
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 shadow-sm border rounded-lg overflow-hidden bg-white">
                                                @if($rel->image)
                                                    @php
                                                        $rLink = $rel->image->link;
                                                        $rHero = Str::startsWith($rLink, ['http://', 'https://']) ? $rLink : asset($rLink);
                                                    @endphp
                                                    <img src="{{ $rHero }}" alt="{{ $rel->title }}" class="card-img-top" style="height: 140px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                                @endif
                                                <div class="card-body p-3 d-flex flex-column">
                                                    <span class="badge badge-light text-primary small align-self-start mb-2">{{ $rel->category->name ?? 'Architecture' }}</span>
                                                    <h6 class="font-weight-bold mb-2">
                                                        <a href="{{ route('single-post', $rel->slug) }}" class="text-body text-decoration-none">{{ Str::limit($rel->title, 50) }}</a>
                                                    </h6>
                                                    <div class="mt-auto small text-muted pt-2 border-top">
                                                        <i class="far fa-clock mr-1"></i> {{ $rel->reading_time }} min read
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Author Bio Card -->
                        <div class="p-4 my-5 rounded-lg d-flex align-items-center author-box bg-light border shadow-sm" style="border-radius: 16px;">
                            <div class="mr-4">
                                <div class="comment-avatar" style="width: 64px; height: 64px; font-size: 24px;">
                                    {{ strtoupper(substr($post->author->nickname ?? ($post->user->name ?? 'A'), 0, 1)) }}
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1 font-weight-bold text-body">{{ $post->author->nickname ?? ($post->user->name ?? 'Author') }}</h5>
                                <span class="text-primary font-weight-bold small">Editorial Contributor</span>
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

                        @include('blog.partials.comments-section')


                    </div>
                </article>
            </div>

            <!-- Sidebar Column -->
            <div class="col-lg-4">
                @include('blog.partials.sidebar')
            </div>
        </div>
    </div>
</section>

@endsection
