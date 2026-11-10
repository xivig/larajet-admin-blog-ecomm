@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', 'Tag: #' . $tag->name . ' - Technical Articles & Insights')
@section('content')

<!-- Tag Hero Banner -->
<section class="page-title py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-3 small">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/blog-list') }}" class="text-muted">Blog</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">#{{ $tag->name }}</li>
                    </ol>
                </nav>

                <div class="d-flex align-items-center flex-wrap" style="gap: 16px;">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm" style="width: 58px; height: 58px;">
                        <i class="fas fa-hashtag" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <span class="badge-elite badge-elite-dark mb-1">
                            <span class="badge-dot pulse"></span> Topic Archive
                        </span>
                        <h1 class="font-weight-bold mb-1 display-5 text-body" style="letter-spacing: -0.02em;">
                            #{{ $tag->name }}
                        </h1>
                        <p class="text-muted mb-0 small">
                            Collection of <strong>{{ $posts->total() }}</strong> verified publications, architectural patterns, and guides indexed under #{{ $tag->name }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tag Articles Grid Section -->
<section class="section pt-4 pb-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left Main Column: Tag Articles -->
            <div class="col-lg-8">
                @if($posts->count() > 0)
                    <div class="row">
                        @foreach($posts as $post)
                            <div class="{{ $posts->count() === 1 ? 'col-12' : 'col-md-6' }} mb-4">
                                <article class="blog-item-elite h-100">
                                    <div class="blog-img-wrap-elite" style="height: 185px;">
                                        <a href="{{ route('single-post', $post->slug) }}">
                                            @php
                                                $postImg = $post->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                                                $imgSrc = Str::startsWith($postImg, ['http://', 'https://']) ? $postImg : asset($postImg);
                                            @endphp
                                            <img loading="lazy" src="{{ $imgSrc }}" alt="{{ $post->title }}" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                        </a>

                                        <div class="blog-card-badges">
                                            @if($post->is_sticky || $post->is_featured || in_array($post->visibility, ['password_protected', 'private']))
                                                <div class="badge-status-wrap">
                                                    @if($post->is_sticky)
                                                        <span class="badge-elite-pinned"><i class="fas fa-thumbtack mr-1"></i> Pinned</span>
                                                    @elseif($post->is_featured)
                                                        <span class="badge-elite-featured"><i class="fas fa-star mr-1"></i> Featured</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <span class="badge-elite-category" title="{{ $post->category->name ?? 'General' }}">
                                                {{ $post->category->name ?? 'General' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="blog-body-elite">
                                        <div class="blog-meta-row-elite">
                                            <span><i class="far fa-user"></i> {{ $post->author->nickname ?? ($post->user->name ?? 'Admin') }}</span>
                                            <span><i class="far fa-calendar-alt"></i> {{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span>
                                        </div>

                                        <h4 class="blog-title-wrap-elite" style="font-size: 1.05rem;">
                                            <a href="{{ route('single-post', $post->slug) }}" class="blog-title-elite" title="{{ $post->title }}">
                                                {{ $post->title }}
                                            </a>
                                        </h4>

                                        <p class="blog-excerpt-elite">
                                            {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 85) }}
                                        </p>

                                        <div class="blog-footer-elite">
                                            <a href="{{ route('single-post', $post->slug) }}" class="btn-read-elite">
                                                Read <i class="fas fa-arrow-right"></i>
                                            </a>
                                            <span class="blog-reading-time">
                                                <i class="far fa-clock"></i> {{ $post->reading_time }}m read
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="text-center py-5 bg-white rounded border p-4">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="font-weight-bold">No Articles for #{{ $tag->name }}</h5>
                        <p class="text-muted">There are currently no published articles associated with this tag.</p>
                        <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-primary btn-sm">Browse All Articles</a>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                @include('blog.partials.sidebar')
            </div>
        </div>
    </div>
</section>

@endsection
