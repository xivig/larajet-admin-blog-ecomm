@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', 'Engineering Dispatch & Live Feed - Xivig')
@section('content')

<!-- Feed Hero Banner -->
<section class="page-title py-4">
    <div class="container-fluid px-xl-5 px-lg-4 px-3">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge-elite badge-elite-dark mb-3">
                    <span class="badge-dot pulse"></span> Continuous Architectural Telemetry
                </span>
                <h1 class="font-weight-bold mb-3 display-4" style="letter-spacing: -0.03em;">
                    Live Feed & Technical Stream
                </h1>
                <p class="text-muted lead mb-0">
                    Real-time index of engineering releases, distributed systems papers, and security advisories.
                </p>
            </div>
            <div class="col-lg-5 text-lg-right mt-3 mt-lg-0">
                <div class="d-inline-flex align-items-center gap-2">
                    <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-primary btn-sm mr-2">
                        <i class="fas fa-th-large mr-1"></i> 4-Column Catalog
                    </a>
                    <a href="{{ url('/blog/gallery') }}" class="btn-elite btn-elite-outline btn-sm">
                        <i class="fas fa-images mr-1"></i> Visual Gallery
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Feed Section -->
<section class="section py-4">
    <div class="container-fluid px-xl-5 px-lg-4 px-md-3 px-2">
        <div class="row g-4">
            <!-- Left Main Stream Column -->
            <div class="col-lg-8">
                <!-- 1. Recent Stories 4-Col Mini Grid -->
                <div class="feed-section-block mb-5">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <h3 class="font-weight-bold mb-0 text-body" style="font-size: 1.35rem;">
                            <i class="fas fa-bolt text-warning mr-2"></i> Spotlight Stories
                        </h3>
                        <span class="badge badge-light text-primary font-weight-bold">Live</span>
                    </div>

                    <div class="row">
                        @foreach($featuredPosts->take(4) as $fPost)
                            <div class="col-md-6 col-xl-3 mb-4">
                                <article class="blog-item-elite h-100">
                                    <div class="blog-img-wrap-elite" style="height: 155px;">
                                        <a href="{{ route('single-post', $fPost->slug) }}">
                                            @php
                                                $fImg = $fPost->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                                                $fSrc = Str::startsWith($fImg, ['http://', 'https://']) ? $fImg : asset($fImg);
                                            @endphp
                                            <img loading="lazy" src="{{ $fSrc }}" alt="{{ $fPost->title }}" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                        </a>
                                        <span class="badge-elite-category" style="position: absolute; top: 10px; right: 10px;">
                                            {{ $fPost->category->name ?? 'Architecture' }}
                                        </span>
                                    </div>
                                    <div class="blog-body-elite p-3">
                                        <h5 class="font-weight-bold mb-2" style="font-size: 0.95rem; line-height: 1.35;">
                                            <a href="{{ route('single-post', $fPost->slug) }}" class="text-body text-decoration-none">
                                                {{ Str::limit($fPost->title, 48) }}
                                            </a>
                                        </h5>
                                        <div class="mt-auto small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                                            <span><i class="far fa-clock mr-1"></i> {{ $fPost->reading_time }}m</span>
                                            <span>{{ ($fPost->published_at ?? $fPost->created_at)->format('M d') }}</span>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Block Post: 1 Large Feature + 2 Sub-Cards -->
                @if($blockHero)
                    <div class="feed-section-block mb-5">
                        <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                            <h3 class="font-weight-bold mb-0 text-body" style="font-size: 1.35rem;">
                                <i class="fas fa-layer-group text-primary mr-2"></i> Deep Dive Architecture
                            </h3>
                            <a href="{{ url('/blog-list') }}" class="small text-primary font-weight-bold text-decoration-none">View All <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <!-- Large Hero Feature Card -->
                        <div class="card p-0 mb-4 shadow-sm border rounded-xl overflow-hidden bg-white" style="border-radius: 20px;">
                            <div class="row g-0 align-items-center">
                                <div class="col-md-5">
                                    @php
                                        $bImg = $blockHero->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                                        $bSrc = Str::startsWith($bImg, ['http://', 'https://']) ? $bImg : asset($bImg);
                                    @endphp
                                    <a href="{{ route('single-post', $blockHero->slug) }}">
                                        <img src="{{ $bSrc }}" alt="{{ $blockHero->title }}" class="w-100 h-100 object-fit-cover" style="min-height: 250px; max-height: 320px;" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                    </a>
                                </div>
                                <div class="col-md-7 p-4 p-md-5">
                                    <span class="badge badge-light text-primary font-weight-bold py-1 px-3 mb-2" style="font-size: 12px;">
                                        {{ $blockHero->category->name ?? 'Cloud' }}
                                    </span>
                                    <h3 class="font-weight-bold mb-3">
                                        <a href="{{ route('single-post', $blockHero->slug) }}" class="text-body text-decoration-none">
                                            {{ $blockHero->title }}
                                        </a>
                                    </h3>
                                    <p class="text-muted mb-4" style="line-height: 1.6;">
                                        {{ $blockHero->excerpt ?: Str::limit(strip_tags($blockHero->content), 120) }}
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <a href="{{ route('single-post', $blockHero->slug) }}" class="btn-read-elite btn-sm">
                                            Read Blueprint <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                        <span class="small text-muted">
                                            <i class="far fa-eye mr-1"></i> {{ number_format($blockHero->views_count ?? 0) }} reads
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2 Sub Cards -->
                        <div class="row">
                            @foreach($blockSub->take(2) as $sPost)
                                <div class="col-md-6 mb-3">
                                    <div class="card p-3 h-100 shadow-sm border rounded-lg bg-white d-flex flex-row align-items-center" style="border-radius: 16px;">
                                        @php
                                            $sImg = $sPost->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                                            $sSrc = Str::startsWith($sImg, ['http://', 'https://']) ? $sImg : asset($sImg);
                                        @endphp
                                        <a href="{{ route('single-post', $sPost->slug) }}" class="flex-shrink-0 mr-3 me-3" style="width: 80px; height: 80px; border-radius: 12px; overflow: hidden; display: block;">
                                            <img src="{{ $sSrc }}" alt="{{ $sPost->title }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                        </a>
                                        <div>
                                            <span class="badge badge-light text-primary small py-0 px-2 font-weight-bold" style="font-size: 11px;">
                                                {{ $sPost->category->name ?? 'Systems' }}
                                            </span>
                                            <h6 class="font-weight-bold mb-1 mt-1" style="font-size: 14px; line-height: 1.35;">
                                                <a href="{{ route('single-post', $sPost->slug) }}" class="text-body text-decoration-none">
                                                    {{ Str::limit($sPost->title, 50) }}
                                                </a>
                                            </h6>
                                            <small class="text-muted"><i class="far fa-clock mr-1"></i> {{ $sPost->reading_time }}m read</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 3. Continuous Feed Stream -->
                <div class="feed-section-block mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <h3 class="font-weight-bold mb-0 text-body" style="font-size: 1.35rem;">
                            <i class="fas fa-stream text-primary mr-2"></i> All Stream Entries
                        </h3>
                    </div>

                    <div class="d-flex flex-column" style="gap: 16px;">
                        @foreach($feedPosts as $entry)
                            <div class="card p-3 shadow-sm border bg-white rounded-lg transition-all" style="border-radius: 16px;">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-3 col-sm-4">
                                        @php
                                            $eImg = $entry->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                                            $eSrc = Str::startsWith($eImg, ['http://', 'https://']) ? $eImg : asset($eImg);
                                        @endphp
                                        <a href="{{ route('single-post', $entry->slug) }}" class="d-block rounded overflow-hidden" style="height: 110px;">
                                            <img src="{{ $eSrc }}" alt="{{ $entry->title }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                        </a>
                                    </div>
                                    <div class="col-md-9 col-sm-8">
                                        <div class="d-flex align-items-center mb-1" style="gap: 10px;">
                                            <span class="badge badge-light text-primary font-weight-bold" style="font-size: 11px;">
                                                {{ $entry->category->name ?? 'General' }}
                                            </span>
                                            <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i> {{ ($entry->published_at ?? $entry->created_at)->format('M d, Y') }}</small>
                                            <small class="text-muted"><i class="far fa-user mr-1"></i> {{ $entry->author->nickname ?? ($entry->user->name ?? 'Admin') }}</small>
                                        </div>
                                        <h5 class="font-weight-bold mb-1" style="font-size: 1.05rem;">
                                            <a href="{{ route('single-post', $entry->slug) }}" class="text-body text-decoration-none">
                                                {{ $entry->title }}
                                            </a>
                                        </h5>
                                        <p class="text-muted small mb-0 d-none d-md-block" style="line-height: 1.45;">
                                            {{ Str::limit(strip_tags($entry->content), 110) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $feedPosts->links() }}
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                @include('blog.partials.sidebar')
            </div>
        </div>
    </div>
</section>

@endsection
