@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', $category->name . ' News & Intelligence - Category Archive')
@section('content')

<!-- Category Hero Banner -->
<section class="page-title py-5" style="padding-top: 90px !important;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-3 small">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/blog-list') }}" class="text-muted">Blog</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">{{ $category->name }}</li>
                    </ol>
                </nav>

                <div class="d-flex align-items-center flex-wrap" style="gap: 16px;">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm" style="width: 58px; height: 58px;">
                        <i class="fas fa-folder-open" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <span class="badge-elite badge-elite-dark mb-1">
                            <span class="badge-dot pulse"></span> Topic Category
                        </span>
                        <h1 class="font-weight-bold mb-1 display-5 text-body" style="letter-spacing: -0.02em;">
                            {{ $category->name }} News & Articles
                        </h1>
                        <p class="text-muted mb-0 small">
                            Exploring comprehensive analyses, market shifts, and architectural updates under <strong>{{ $category->name }}</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Main Content Area -->
<section class="section pb-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left 8 Cols: Filtered Articles -->
            <div class="col-lg-8">
                @if($posts->count() > 0)
                    <div class="row g-4">
                        @foreach($posts as $post)
                            @php
                                $postImg = $post->image->link ?? 'dist-front/images/business-1.jpg';
                                $imgSrc = Str::startsWith($postImg, ['http://', 'https://']) ? $postImg : asset($postImg);
                            @endphp
                            <div class="col-md-6 mb-4">
                                <article class="mag-card shadow-sm border-0 rounded-4 overflow-hidden h-100 transition-hover">
                                    <div class="card-img-wrapper position-relative" style="height: 200px;">
                                        <a href="{{ route('single-post', $post->slug) }}">
                                            <img src="{{ $imgSrc }}" class="img-aspect w-100 h-100 object-fit-cover transition-transform" alt="{{ $post->title }}" onerror="this.onerror=null; this.src='{{ asset('dist-front/images/business-1.jpg') }}'">
                                        </a>
                                        @if($post->is_sticky)
                                            <span class="badge-elite-pinned position-absolute top-0 start-0 m-2 z-2"><i class="fas fa-thumbtack mr-1"></i> Pinned</span>
                                        @elseif($post->is_featured)
                                            <span class="badge-elite-featured position-absolute top-0 start-0 m-2 z-2"><i class="fas fa-star mr-1"></i> Featured</span>
                                        @endif
                                    </div>
                                    <div class="card-body-custom p-3 d-flex flex-column" style="min-height: 150px;">
                                        <div class="meta-text small text-muted mb-2 d-flex align-items-center justify-content-between">
                                            <span><i class="far fa-user"></i> {{ $post->author->nickname ?? ($post->user->name ?? 'Admin') }}</span>
                                            <span><i class="far fa-calendar-alt"></i> {{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span>
                                        </div>
                                        <h3 class="card-title-sm font-weight-bold mb-2" style="font-size: 1.1rem; line-height: 1.35;">
                                            <a href="{{ route('single-post', $post->slug) }}" class="text-body text-decoration-none">
                                                {{ $post->title }}
                                            </a>
                                        </h3>
                                        <p class="excerpt-text text-muted small mb-3">
                                            {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 85) }}
                                        </p>
                                        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top">
                                            <a href="{{ route('single-post', $post->slug) }}" class="small font-weight-bold text-primary text-decoration-none">
                                                Read Analysis <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                            <span class="small text-muted"><i class="far fa-clock"></i> {{ $post->reading_time }}m</span>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $posts->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="text-center py-5 p-4 rounded-4 bg-light-subtle border border-light-subtle">
                        <i class="fas fa-folder-open text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4 class="font-weight-bold text-body">No publications found in {{ $category->name }}</h4>
                        <p class="text-muted small">Check back soon or explore our other topic categories.</p>
                        <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-primary btn-sm px-4">Browse Catalog</a>
                    </div>
                @endif
            </div>

            <!-- Right 4 Cols: Shared Sidebar (categories <= 10, tags <= 25) -->
            <div class="col-lg-4">
                @include('blog.partials.sidebar')
            </div>
        </div>
    </div>
</section>

@endsection
