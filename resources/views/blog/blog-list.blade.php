@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', 'Xivig - Editorial & Engineering Blog')
@section('content')

<!-- Blog Hero Banner -->
<section class="page-title" style="padding: 90px 0 50px 0;">
    <div class="container-fluid px-xl-5 px-lg-4 px-3">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge-elite badge-elite-dark mb-3">
                    <span class="badge-dot pulse"></span> Engineering & Architecture Magazine
                </span>
                <h1 class="font-weight-bold mb-3 display-4" style="letter-spacing: -0.03em;">
                    Articles & Engineering Insights
                </h1>
                <p class="text-muted lead mb-0" style="max-width: 680px;">
                    Explore verified deep-dive technical articles, architectural blueprints, and full-stack best practices.
                </p>
            </div>
            <div class="col-lg-5 mt-4 mt-lg-0 text-lg-right">
                <form action="{{ request()->url() }}" method="GET" class="position-relative w-100" style="max-width: 380px; margin-left: auto;">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('cols'))
                        <input type="hidden" name="cols" value="{{ request('cols') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-elite pr-5" style="border-radius: 999px; padding-right: 52px; height: 48px;" placeholder="Search articles, guides, tech...">
                    <button type="submit" class="btn-elite btn-elite-primary btn-sm position-absolute" style="top: 50%; right: 6px; transform: translateY(-50%); border-radius: 999px; height: 36px; width: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center; z-index: 5;" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Main Blog Section -->
<section class="section py-4">
    <div class="container-fluid px-xl-5 px-lg-4 px-md-3 px-2">

        <!-- Elite Category Filter & View Controls Bar -->
        <div class="blog-filter-bar mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 12px;">
                <div class="category-pill-group">
                    <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" 
                       class="cat-pill {{ !request('category') ? 'active' : '' }}">
                        <i class="fas fa-layer-group mr-1"></i> All Articles <span class="cat-count">{{ $totalPostsCount ?? $posts->total() }}</span>
                    </a>
                    @foreach($categories->take(7) as $category)
                        <a href="{{ request()->fullUrlWithQuery(['category' => $category->name, 'page' => null]) }}" 
                           class="cat-pill {{ request('category') == $category->name || request('category') == $category->id ? 'active' : '' }}">
                            {{ $category->name }} <span class="cat-count">{{ $category->posts_count }}</span>
                        </a>
                    @endforeach
                </div>

                <div class="d-flex align-items-center ml-auto" style="gap: 16px;">
                    <span class="text-muted small d-none d-md-inline">
                        Showing <strong>{{ $posts->firstItem() ?? 0 }}-{{ $posts->lastItem() ?? 0 }}</strong> of <strong>{{ $posts->total() }}</strong>
                    </span>
                    <div class="btn-group btn-group-sm layout-switcher" role="group">
                        <a href="{{ request()->fullUrlWithQuery(['cols' => 4]) }}" class="btn {{ ($columns ?? 4) == 4 ? 'btn-primary' : 'btn-outline-secondary' }}" title="4 Columns Grid">
                            <i class="fas fa-th"></i>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['cols' => 3]) }}" class="btn {{ ($columns ?? 4) == 3 ? 'btn-primary' : 'btn-outline-secondary' }}" title="3 Columns Grid">
                            <i class="fas fa-th-large"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(count($posts) > 0)
            <div class="row">
                @foreach($posts as $post)
                    <div class="{{ ($columns ?? 4) == 3 ? 'col-lg-4 col-md-6 col-12 mb-4' : 'col-xl-3 col-lg-3 col-md-6 col-12 mb-4' }}">
                        <article class="blog-item-elite h-100">
                            <!-- Image Wrap with Badges -->
                            <div class="blog-img-wrap-elite">
                                <a href="{{ route('single-post', $post->slug) }}">
                                    @php
                                        $postImg = $post->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                                        $imgSrc = Str::startsWith($postImg, ['http://', 'https://']) ? $postImg : asset($postImg);
                                    @endphp
                                    <img loading="lazy" src="{{ $imgSrc }}" alt="{{ $post->title }}" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                </a>

                                <!-- Badges Overlay: Pinned/Featured Left, Category Right -->
                                <div class="blog-card-badges">
                                    @if($post->is_sticky || $post->is_featured || in_array($post->visibility, ['password_protected', 'private']))
                                        <div class="badge-status-wrap">
                                            @if($post->is_sticky)
                                                <span class="badge-elite-pinned">
                                                    <i class="fas fa-thumbtack mr-1"></i> Pinned
                                                </span>
                                            @elseif($post->is_featured)
                                                <span class="badge-elite-featured">
                                                    <i class="fas fa-star mr-1"></i> Featured
                                                </span>
                                            @endif

                                            @if($post->visibility === 'password_protected')
                                                <span class="badge-elite-locked" title="Password Protected">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @elseif($post->visibility === 'private')
                                                <span class="badge-elite-locked" title="Members Only">
                                                    <i class="fas fa-user-lock"></i>
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    <span class="badge-elite-category" title="{{ $post->category->name ?? 'General' }}">
                                        {{ $post->category->name ?? 'General' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="blog-body-elite">
                                <!-- Meta Row 1: Author & Published Date -->
                                <div class="blog-meta-row-elite">
                                    <span><i class="far fa-user"></i> {{ $post->author->nickname ?? ($post->user->name ?? 'Admin') }}</span>
                                    <span><i class="far fa-calendar-alt"></i> {{ $post->published_at ? $post->published_at->format('M d, Y') : ($post->created_at ? $post->created_at->format('M d, Y') : 'Recent') }}</span>
                                </div>

                                <!-- Meta Row 2: Tag / Category Pill -->
                                @if($post->tag || $post->category)
                                    <div class="blog-tag-row-elite">
                                        <span class="blog-tag-pill" title="{{ $post->tag->name ?? $post->category->name }}">
                                            <i class="fas fa-tag"></i> {{ $post->tag->name ?? $post->category->name }}
                                        </span>
                                    </div>
                                @endif

                                <!-- Title (Clamped to 2 lines with uniform height) -->
                                <h3 class="blog-title-wrap-elite">
                                    <a href="{{ route('single-post', $post->slug) }}" class="blog-title-elite" title="{{ $post->title }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                <!-- Excerpt (Clamped to 2 lines with uniform height) -->
                                <p class="blog-excerpt-elite">
                                    {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 85) }}
                                </p>

                                <!-- Footer: Gradient Read Button + Reading Time -->
                                <div class="blog-footer-elite">
                                    <a href="{{ route('single-post', $post->slug) }}" class="btn-read-elite">
                                        Read <i class="fas fa-arrow-right"></i>
                                    </a>
                                    <span class="blog-reading-time">
                                        <i class="far fa-clock"></i> {{ $post->reading_time }} min read
                                    </span>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <!-- Modern Pagination -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8 text-center">
                    <div class="d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="p-5 card-elite rounded shadow-sm d-inline-block border" style="max-width: 500px;">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="font-weight-bold">No Articles Found</h4>
                    <p class="text-muted">We couldn't find any articles matching your query. Try resetting your search or filter.</p>
                    <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-primary btn-sm mt-2">View All Articles</a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
