@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', ($columns ?? 3) == 4 ? 'Xivig - All Articles & Publications' : 'Xivig - Editorial & Engineering Blog')
@section('content')

<!-- Blog Hero Banner -->
<section class="page-title" style="padding: 100px 0 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge-elite badge-elite-dark mb-3">
                    <span class="badge-dot pulse"></span> {{ ($columns ?? 3) == 4 ? 'Complete Technical Archive' : 'Engineering & Architecture Magazine' }}
                </span>
                <h1 class="font-weight-bold mb-3 display-4" style="letter-spacing: -0.03em;">
                    {{ ($columns ?? 3) == 4 ? 'All Articles & Blueprints' : 'Insights & Knowledge' }}
                </h1>
                <p class="text-muted lead mb-0">
                    {{ ($columns ?? 3) == 4 ? 'Browse our complete multi-column catalog of engineering blueprints, tutorials, and system releases.' : 'Explore deep-dive technical articles, architectural blueprints, and full-stack best practices.' }}
                </p>
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0 text-lg-right">
                <form action="{{ request()->url() }}" method="GET" class="d-flex">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-elite mr-2" placeholder="Search insights...">
                    <button type="submit" class="btn-elite btn-elite-primary btn-sm"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Main Blog Section -->
<section class="section py-5">
    <div class="{{ ($columns ?? 3) == 4 ? 'container-fluid px-lg-5' : 'container' }}">
        @if(count($posts) > 0)
            <div class="row">
                @foreach($posts as $post)
                    <div class="{{ ($columns ?? 3) == 4 ? 'col-xl-3 col-lg-4 col-md-6 mb-4' : 'col-lg-4 col-md-6 mb-4' }}">
                        <article class="blog-item h-100 d-flex flex-column">
                            <div class="blog-img-wrap">
                                <a href="{{ route('single-post', $post->slug) }}">
                                    @php
                                        $postImg = $post->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                                        $imgSrc = Str::startsWith($postImg, ['http://', 'https://']) ? $postImg : asset($postImg);
                                    @endphp
                                    <img loading="lazy" src="{{ $imgSrc }}" alt="{{ $post->title }}" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                </a>
                                <span class="blog-category-tag">
                                    {{ $post->category->name ?? 'General' }}
                                </span>
                            </div>

                            <div class="blog-item-content d-flex flex-column flex-grow-1">
                                <div class="blog-meta-pills">
                                    <span><i class="far fa-user"></i> {{ $post->author->nickname ?? ($post->user->name ?? 'Admin') }}</span>
                                    <span><i class="far fa-calendar-alt"></i> {{ $post->created_at ? $post->created_at->format('M d, Y') : 'Recent' }}</span>
                                    @if($post->tag)
                                        <span><i class="fas fa-tag"></i> {{ $post->tag->name }}</span>
                                    @endif
                                </div>

                                <h3 style="font-size: {{ ($columns ?? 3) == 4 ? '1.15rem' : '1.35rem' }};">
                                    <a href="{{ route('single-post', $post->slug) }}" class="blog-title-link">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                <p class="blog-excerpt">
                                    {{ $post->excerpt ?: Str::limit(strip_tags($post->content), ($columns ?? 3) == 4 ? 85 : 120) }}
                                </p>

                                <div class="mt-auto pt-3 d-flex align-items-center justify-content-between" style="border-top: 1px solid #f1f5f9;">
                                    <a href="{{ route('single-post', $post->slug) }}" class="btn-elite btn-elite-primary btn-sm">
                                        Read <i class="fas fa-arrow-right ml-1" style="font-size: 11px;"></i>
                                    </a>
                                    <span class="text-muted small">
                                        <i class="far fa-clock mr-1"></i> {{ max(2, round(str_word_count(strip_tags($post->content ?? $post->excerpt)) / 180)) }} min
                                    </span>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <!-- Modern Pagination -->
            <div class="row justify-content-center mt-5">
                <div class="col-lg-8 text-center">
                    <div class="d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="p-5 bg-white rounded shadow-sm d-inline-block" style="max-width: 500px;">
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

