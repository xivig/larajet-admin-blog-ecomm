@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', 'Xivig Authors & Technical Contributors')
@section('content')

<!-- Authors Directory Hero -->
<section class="page-title py-5" style="padding-top: 90px !important;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge-elite badge-elite-dark mb-3">
                    <span class="badge-dot pulse"></span> Editorial Board & Research Fellows
                </span>
                <h1 class="font-weight-bold mb-3 display-4" style="letter-spacing: -0.03em;">
                    Authors & Engineering Minds
                </h1>
                <p class="text-muted lead mb-0">
                    Meet the architects, security researchers, and systems engineers behind Xivig technical publications.
                </p>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-outline btn-sm">
                    <i class="fas fa-th-large mr-1"></i> View All Articles
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Authors Grid Section -->
<section class="section py-5">
    <div class="container">
        <div class="row">
            @forelse($authors as $author)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card p-4 h-100 shadow-sm border text-center bg-white transition-all hover-translate" style="border-radius: 20px;">
                        <div class="mb-3 position-relative d-inline-block mx-auto">
                            @php
                                $userPhoto = $author->user->profile_photo_path ?? null;
                                $avatarSrc = $userPhoto ? (Str::startsWith($userPhoto, ['http://', 'https://']) ? $userPhoto : asset($userPhoto)) : asset('admin-dist/img/avatar.png');
                            @endphp
                            <img src="{{ $avatarSrc }}" alt="{{ $author->nickname ?? $author->user->name }}" class="rounded-circle shadow-sm" style="width: 96px; height: 96px; object-fit: cover; border: 3px solid #6366f1;" onerror="this.src='{{ asset('admin-dist/img/avatar.png') }}'">
                        </div>

                        <h4 class="font-weight-bold mb-1 text-body">
                            <a href="{{ route('blog.author', $author->id) }}" class="text-body text-decoration-none">
                                {{ $author->nickname ?? $author->user->name }}
                            </a>
                        </h4>
                        <span class="badge badge-light text-primary font-weight-bold small py-1 px-3 mb-3 d-inline-block">
                            {{ $author->posts_count ?? 0 }} Publications
                        </span>

                        <p class="text-muted small mb-4" style="line-height: 1.5; min-height: 3em;">
                            {{ Str::limit($author->user->bio ?? 'Technical architect and core system researcher contributing high-impact cloud blueprints.', 90) }}
                        </p>

                        <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                            <a href="{{ route('blog.author', $author->id) }}" class="btn-read-elite btn-sm" style="padding: 6px 16px; font-size: 12.5px;">
                                View Articles <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                            <div class="d-flex" style="gap: 6px;">
                                <a href="https://twitter.com/xivig" target="_blank" class="share-circle-btn share-twitter" style="width: 32px; height: 32px;" title="X">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                                <a href="https://github.com/xivig" target="_blank" class="share-circle-btn share-twitter" style="width: 32px; height: 32px;" title="GitHub">
                                    <i class="fab fa-github" style="font-size: 13px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No authors registered yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

@endsection
