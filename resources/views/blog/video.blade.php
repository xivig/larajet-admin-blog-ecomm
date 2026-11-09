@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', ($mainVideo->title ?? 'Technical Video Walkthroughs') . ' - Xivig Videos')
@section('content')

<!-- Video Hero Banner -->
<section class="page-title py-5" style="padding-top: 90px !important;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-3 small">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/blog-list') }}" class="text-muted">Blog</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Video Insights</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Main Video Section -->
<section class="section pb-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left Main Column: Video Player & Details -->
            <div class="col-lg-8">
                @if($mainVideo)
                    <article class="card p-0 mb-5 shadow-sm border rounded-xl overflow-hidden bg-white" style="border-radius: 20px;">
                        <!-- 16:9 Video Embed Player -->
                        <div class="ratio ratio-16x9 position-relative overflow-hidden" style="background: #000; min-height: 380px;">
                            @php
                                $vidUrl = $mainVideo->video_url ?? null;
                                // Parse YouTube embed ID if youtube URL
                                if ($vidUrl && (str_contains($vidUrl, 'youtube.com') || str_contains($vidUrl, 'youtu.be'))) {
                                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $vidUrl, $match);
                                    $embedSrc = isset($match[1]) ? 'https://www.youtube.com/embed/' . $match[1] . '?autoplay=0' : $vidUrl;
                                } elseif ($vidUrl && str_contains($vidUrl, 'vimeo.com')) {
                                    preg_match('/vimeo\.com\/([0-9]{1,10})/', $vidUrl, $match);
                                    $embedSrc = isset($match[1]) ? 'https://player.vimeo.com/video/' . $match[1] : $vidUrl;
                                } else {
                                    // Default featured architectural demo video
                                    $embedSrc = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0';
                                }
                            @endphp
                            <iframe src="{{ $embedSrc }}" title="{{ $mainVideo->title }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width: 100%; height: 100%; border: none;"></iframe>
                        </div>

                        <!-- Video Metadata & Article Body -->
                        <div class="p-4 p-md-5">
                            <div class="d-flex align-items-center flex-wrap mb-3" style="gap: 12px;">
                                <span class="badge badge-light text-primary font-weight-bold py-1 px-3" style="font-size: 12px;">
                                    <i class="fas fa-play-circle mr-1"></i> Video Insight
                                </span>
                                <span class="badge badge-light text-muted font-weight-bold py-1 px-3" style="font-size: 12px;">
                                    {{ $mainVideo->category->name ?? 'Architecture' }}
                                </span>
                                <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i> {{ ($mainVideo->published_at ?? $mainVideo->created_at)->format('M d, Y') }}</small>
                                <small class="text-muted"><i class="far fa-eye mr-1"></i> {{ number_format($mainVideo->views_count ?? 0) }} views</small>
                            </div>

                            <h1 class="font-weight-bold mb-3 text-body" style="font-size: 1.85rem; line-height: 1.35;">
                                {{ $mainVideo->title }}
                            </h1>

                            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                                @php
                                    $authorPhoto = $mainVideo->author->user->profile_photo_path ?? null;
                                    $authorSrc = $authorPhoto ? (Str::startsWith($authorPhoto, ['http://', 'https://']) ? $authorPhoto : asset($authorPhoto)) : asset('admin-dist/img/avatar.png');
                                @endphp
                                <img src="{{ $authorSrc }}" alt="{{ $mainVideo->author->nickname ?? ($mainVideo->user->name ?? 'Admin') }}" class="rounded-circle mr-3 me-3" style="width: 44px; height: 44px; object-fit: cover;" onerror="this.src='{{ asset('admin-dist/img/avatar.png') }}'">
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-body">{{ $mainVideo->author->nickname ?? ($mainVideo->user->name ?? 'Admin') }}</h6>
                                    <small class="text-muted">Technical Presenter & Systems Architect</small>
                                </div>
                            </div>

                            <div class="video-content-body text-muted mb-4" style="line-height: 1.7; font-size: 15.5px;">
                                {!! $mainVideo->content !!}
                            </div>

                            <!-- Share Bar -->
                            <div class="pt-3 border-top">
                                @include('partials.share-buttons', ['shareUrl' => url()->current(), 'shareTitle' => $mainVideo->title])
                            </div>
                        </div>
                    </article>
                @endif

                <!-- More Video Insights Grid -->
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <h3 class="font-weight-bold mb-0 text-body" style="font-size: 1.35rem;">
                            <i class="fas fa-video text-primary mr-2"></i> More Video Publications
                        </h3>
                    </div>

                    <div class="row">
                        @forelse($moreVideos as $vid)
                            <div class="col-md-6 mb-4">
                                <div class="card p-0 h-100 shadow-sm border rounded-xl overflow-hidden bg-white" style="border-radius: 16px;">
                                    <div class="position-relative" style="height: 170px; background: #000;">
                                        @php
                                            $vImg = $vid->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                                            $vSrc = Str::startsWith($vImg, ['http://', 'https://']) ? $vImg : asset($vImg);
                                        @endphp
                                        <img src="{{ $vSrc }}" alt="{{ $vid->title }}" class="w-100 h-100 object-fit-cover opacity-75" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                        <a href="{{ route('blog.video', $vid->slug) }}" class="position-absolute top-50 start-50 translate-middle d-flex align-items-center justify-content-center bg-danger text-white rounded-circle shadow" style="width: 50px; height: 50px; transform: translate(-50%, -50%);" title="Watch Video">
                                            <i class="fas fa-play" style="font-size: 1.1rem; margin-left: 3px;"></i>
                                        </a>
                                        <span class="badge badge-dark position-absolute bottom-0 end-0 m-2" style="background: rgba(0,0,0,0.75);">
                                            <i class="far fa-clock mr-1"></i> {{ $vid->reading_time ?? 5 }}m
                                        </span>
                                    </div>
                                    <div class="p-3 d-flex flex-column flex-grow-1">
                                        <span class="badge badge-light text-primary align-self-start mb-2" style="font-size: 11px;">
                                            {{ $vid->category->name ?? 'Technology' }}
                                        </span>
                                        <h5 class="font-weight-bold mb-2" style="font-size: 1rem; line-height: 1.35;">
                                            <a href="{{ route('blog.video', $vid->slug) }}" class="text-body text-decoration-none">
                                                {{ Str::limit($vid->title, 55) }}
                                            </a>
                                        </h5>
                                        <div class="mt-auto pt-2 border-top small text-muted d-flex align-items-center justify-content-between">
                                            <span>{{ ($vid->published_at ?? $vid->created_at)->format('M d, Y') }}</span>
                                            <span><i class="far fa-eye mr-1"></i> {{ number_format($vid->views_count ?? 0) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">
                                More video tutorials coming soon!
                            </div>
                        @endforelse
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
