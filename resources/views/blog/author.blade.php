@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', 'Author: ' . ($author->nickname ?? $author->user->name ?? 'Author') . ' - Articles & Contributions')
@section('content')

<!-- Author Hero Banner -->
<section class="page-title py-5" style="padding-top: 90px !important;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-3 small">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/blog-list') }}" class="text-muted">Blog</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/blog/authors') }}" class="text-muted">Authors</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">{{ $author->nickname ?? $author->user->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Author Content Section -->
<section class="section pb-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left Main Column -->
            <div class="col-lg-8">
                <!-- Author Bio Box -->
                <div class="card p-4 p-md-5 mb-5 shadow-sm border rounded-xl text-center bg-white" style="border-radius: 20px;">
                    <div class="mb-3 position-relative d-inline-block mx-auto">
                        @php
                            $userPhoto = $author->user->profile_photo_path ?? null;
                            $avatarSrc = $userPhoto ? (Str::startsWith($userPhoto, ['http://', 'https://']) ? $userPhoto : asset($userPhoto)) : asset('admin-dist/img/avatar.png');
                        @endphp
                        <img src="{{ $avatarSrc }}" alt="{{ $author->nickname ?? $author->user->name }}" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #ffffff; box-shadow: 0 8px 24px rgba(99, 102, 241, 0.2);" onerror="this.src='{{ asset('admin-dist/img/avatar.png') }}'">
                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle" style="width: 18px; height: 18px; border: 3px solid #ffffff; right: 8px; bottom: 8px;" title="Verified Author"></span>
                    </div>

                    <h2 class="font-weight-bold mb-1 text-body">{{ $author->nickname ?? $author->user->name }}</h2>
                    <p class="text-primary font-weight-bold small mb-2 text-uppercase letter-spacing-1">Verified Technical Author & Systems Architect</p>
                    <p class="text-muted mx-auto mb-4" style="max-width: 580px; font-size: 15px; line-height: 1.6;">
                        {{ $author->user->bio ?? 'Senior engineering contributor and technical researcher specializing in distributed cloud infrastructure, microservices orchestration, and modern reactive full-stack web platforms.' }}
                    </p>

                    <!-- Stats Badges -->
                    <div class="d-flex align-items-center justify-content-center flex-wrap mb-4" style="gap: 16px;">
                        <span class="badge badge-light p-2 px-3 font-weight-bold text-muted border" style="font-size: 13px;">
                            <i class="fas fa-file-alt text-primary mr-1"></i> {{ $posts->total() }} Published Articles
                        </span>
                        <span class="badge badge-light p-2 px-3 font-weight-bold text-muted border" style="font-size: 13px;">
                            <i class="fas fa-eye text-primary mr-1"></i> {{ number_format($totalViews ?? 0) }} Total Reads
                        </span>
                        <span class="badge badge-light p-2 px-3 font-weight-bold text-muted border" style="font-size: 13px;">
                            <i class="fas fa-certificate text-warning mr-1"></i> Core Contributor
                        </span>
                    </div>

                    <!-- Author Social Links -->
                    <div class="d-flex justify-content-center" style="gap: 10px;">
                        <a href="https://twitter.com/xivig" target="_blank" class="share-circle-btn share-twitter" title="Follow on X">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://linkedin.com/company/xivig" target="_blank" class="share-circle-btn share-linkedin" title="Connect on LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://github.com/xivig" target="_blank" class="share-circle-btn share-twitter" title="GitHub Code">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="mailto:{{ $author->user->email ?? 'author@xivig.com' }}" class="share-circle-btn share-facebook" title="Direct Email">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>

                <!-- Latest from Author Header -->
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                    <h3 class="font-weight-bold mb-0 text-body" style="font-size: 1.45rem;">
                        <i class="fas fa-feather-alt text-primary mr-2"></i> Publications by {{ $author->nickname ?? $author->user->name }}
                    </h3>
                    <span class="text-muted small">Showing {{ $posts->count() }} of {{ $posts->total() }}</span>
                </div>

                @if($posts->count() > 0)
                    <div class="row">
                        @foreach($posts as $post)
                            <div class="col-md-6 mb-4">
                                <article class="blog-item-elite h-100">
                                    <div class="blog-img-wrap-elite" style="height: 180px;">
                                        <a href="{{ route('single-post', $post->slug) }}">
                                            @php
                                                $postImg = $post->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                                                $imgSrc = Str::startsWith($postImg, ['http://', 'https://']) ? $postImg : asset($postImg);
                                            @endphp
                                            <img loading="lazy" src="{{ $imgSrc }}" alt="{{ $post->title }}" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                        </a>

                                        <div class="blog-card-badges">
                                            <span>
                                                @if($post->is_sticky)
                                                    <span class="badge-elite-pinned"><i class="fas fa-thumbtack mr-1"></i> Pinned</span>
                                                @elseif($post->is_featured)
                                                    <span class="badge-elite-featured"><i class="fas fa-star mr-1"></i> Featured</span>
                                                @endif
                                            </span>
                                            <span class="badge-elite-category">
                                                {{ $post->category->name ?? 'Architecture' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="blog-body-elite">
                                        <div class="blog-meta-row-elite">
                                            <span><i class="far fa-calendar-alt"></i> {{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span>
                                            <span><i class="far fa-clock"></i> {{ $post->reading_time }}m read</span>
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
                                                <i class="far fa-eye"></i> {{ number_format($post->views_count ?? 0) }} views
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
                        <i class="far fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5 class="font-weight-bold">No Publications Yet</h5>
                        <p class="text-muted">This author hasn't published any articles yet. Check back soon!</p>
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
