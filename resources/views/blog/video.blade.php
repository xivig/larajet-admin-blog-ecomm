@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', ($mainVideo->title ?? 'Technical Video Walkthroughs') . ' - Xivig Videos')
@section('content')

<!-- Video Hero Banner -->
<section class="page-title page-title-compact py-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-0 small">
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
<section class="section pt-4 pb-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left Main Column: Video Player & Details -->
            <div class="col-lg-8">
                @if($mainVideo)
                    <article class="card vf-surface-card p-0 mb-4 shadow-sm border rounded-xl overflow-hidden" style="border-radius: 20px;">
                        <!-- 16:9 Video Embed Player -->
                        @php
                            $vidUrl = trim($mainVideo->video_url ?? '');
                            $postHeroImg = $mainVideo->image->link ?? 'xivig-public/images/blog/video-feature-fallback.jpg';
                            $posterSrc = Str::startsWith($postHeroImg, ['http://', 'https://']) ? $postHeroImg : asset($postHeroImg);

                            $isIframe = Str::startsWith($vidUrl, '<iframe');
                            $isDirectVideo = preg_match('/\.(mp4|webm|ogg|m4v)(\?.*)?$/i', $vidUrl);
                            $embedSrc = null;

                            if (!$isIframe && !$isDirectVideo) {
                                if ($vidUrl && (str_contains($vidUrl, 'youtube.com') || str_contains($vidUrl, 'youtu.be'))) {
                                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $vidUrl, $match);
                                    $youtubeId = $match[1] ?? 'tgbNymZ7vqY';
                                    $embedSrc = 'https://www.youtube-nocookie.com/embed/' . $youtubeId . '?rel=0&modestbranding=1&enablejsapi=1';
                                } elseif ($vidUrl && str_contains($vidUrl, 'vimeo.com')) {
                                    preg_match('/vimeo\.com\/([0-9]{1,10})/', $vidUrl, $match);
                                    $vimeoId = $match[1] ?? '76979871';
                                    $embedSrc = 'https://player.vimeo.com/video/' . $vimeoId . '?dnt=1';
                                } elseif (!empty($vidUrl) && Str::startsWith($vidUrl, ['http://', 'https://'])) {
                                    $embedSrc = $vidUrl;
                                } else {
                                    // Default embeddable demo video from dist-front/video.html
                                    $embedSrc = 'https://www.youtube-nocookie.com/embed/tgbNymZ7vqY?rel=0&modestbranding=1&enablejsapi=1';
                                }
                            }
                        @endphp

                        <div class="ratio ratio-16x9 position-relative overflow-hidden rounded-top-4" style="background: #000; min-height: 400px; border-radius: 20px 20px 0 0;">
                            @if($isIframe)
                                {!! $vidUrl !!}
                            @elseif($isDirectVideo)
                                <video controls playsinline preload="metadata" class="w-100 h-100 object-fit-cover" poster="{{ $posterSrc }}">
                                    <source src="{{ $vidUrl }}" type="video/mp4">
                                    Your browser does not support HTML5 video streaming.
                                </video>
                            @else
                                <iframe 
                                    src="{{ $embedSrc }}" 
                                    title="{{ $mainVideo->title }}" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                    allowfullscreen 
                                    loading="lazy"
                                    style="width: 100%; height: 100%; border: none;">
                                </iframe>
                            @endif
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
                                    $defaultAvatar = asset('xivig-admin/img/avatars/avatar-' . ((($mainVideo->author_id ?? 1) % 9) + 1) . '.webp');
                                    $authorPhoto = $mainVideo->author->user->profile_photo_path ?? null;
                                    $authorSrc = $authorPhoto ? (Str::startsWith($authorPhoto, ['http://', 'https://']) ? $authorPhoto : asset($authorPhoto)) : $defaultAvatar;
                                @endphp
                                <img src="{{ $authorSrc }}" alt="{{ $mainVideo->author->nickname ?? ($mainVideo->user->name ?? 'Admin') }}" class="rounded-circle mr-3 me-3" style="width: 44px; height: 44px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ $defaultAvatar }}'">
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
                                            $vImg = $vid->image->link ?? 'xivig-admin/img/blog-1-PTDVKJDt.webp';
                                            $vSrc = Str::startsWith($vImg, ['http://', 'https://']) ? $vImg : asset($vImg);
                                        @endphp
                                        <img src="{{ $vSrc }}" alt="{{ $vid->title }}" class="w-100 h-100 object-fit-cover opacity-75" onerror="this.onerror=null; this.src='{{ asset('xivig-admin/img/blog-1-PTDVKJDt.webp') }}'">
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
