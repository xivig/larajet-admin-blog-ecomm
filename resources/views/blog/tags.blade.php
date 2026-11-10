@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', 'Tags Directory - Browse Topics & Architectural Blueprints')
@section('content')

<!-- Tags Directory Banner -->
<section class="page-title py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-3 small">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/blog-list') }}" class="text-muted">Blog</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tags Directory</li>
                    </ol>
                </nav>

                <div class="d-flex align-items-center flex-wrap" style="gap: 16px;">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm" style="width: 58px; height: 58px;">
                        <i class="fas fa-tags" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <span class="badge-elite badge-elite-dark mb-1">
                            <span class="badge-dot pulse"></span> Knowledge Index
                        </span>
                        <h1 class="font-weight-bold mb-1 display-5 text-body" style="letter-spacing: -0.02em;">
                            Tags & Topics Directory
                        </h1>
                        <p class="text-muted mb-0 small">
                            Browse over <strong>{{ $totalTagsCount }}</strong> engineering and editorial tags categorized alphabetically.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tags Index Section -->
<section class="section pt-4 pb-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left 8 Cols: Tags Directory -->
            <div class="col-lg-8">
                <!-- Trending Tags Cloud -->
                <div class="p-4 rounded-4 shadow-sm bg-body border border-light-subtle mb-4">
                    <h5 class="font-weight-bold text-body mb-3 d-flex align-items-center" style="font-size: 16px;">
                        <i class="fas fa-fire mr-2 text-danger"></i> Trending & High-Volume Topics
                    </h5>
                    <div class="d-flex flex-wrap" style="gap: 10px;">
                        @foreach($topTags as $tTag)
                            <a href="{{ url('/blog/tag/' . $tTag->id) }}" class="badge badge-light border text-decoration-none py-2 px-3 rounded-pill text-body font-weight-bold shadow-sm d-inline-flex align-items-center" style="font-size: 13px; gap: 6px;">
                                <span class="text-primary">#{{ $tTag->name }}</span>
                                <span class="badge bg-primary text-white rounded-pill small" style="font-size: 10px;">{{ $tTag->posts_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Alphabetical Index -->
                <div class="p-4 rounded-4 shadow-sm bg-body border border-light-subtle">
                    <h5 class="font-weight-bold text-body mb-4 d-flex align-items-center pb-2 border-bottom" style="font-size: 16px;">
                        <i class="fas fa-sort-alpha-down mr-2 text-primary"></i> A to Z Topic Index
                    </h5>

                    <!-- Quick Jump Links -->
                    <div class="d-flex flex-wrap mb-4 pb-3 border-bottom" style="gap: 6px;">
                        @foreach($groupedTags as $letter => $tagsList)
                            <a href="#letter-{{ $letter }}" class="btn btn-outline-secondary btn-sm py-1 px-2 font-weight-bold" style="font-size: 12px; min-width: 32px;">
                                {{ $letter }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Groups List -->
                    <div class="d-flex flex-column" style="gap: 24px;">
                        @foreach($groupedTags as $letter => $tagsList)
                            <div id="letter-{{ $letter }}" class="tag-letter-group pt-2">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle font-weight-bold mr-2 me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                        {{ $letter }}
                                    </span>
                                    <h6 class="font-weight-bold mb-0 text-body" style="font-size: 15px;">Section {{ $letter }}</h6>
                                </div>

                                <div class="row g-2">
                                    @foreach($tagsList as $tagItem)
                                        <div class="col-md-6 col-lg-4">
                                            <a href="{{ url('/blog/tag/' . $tagItem->id) }}" class="p-2 rounded bg-light-subtle text-decoration-none text-body d-flex align-items-center justify-content-between border border-light-subtle hover-primary">
                                                <span class="small font-weight-bold text-truncate" style="max-width: 170px;" title="{{ $tagItem->name }}">
                                                    #{{ $tagItem->name }}
                                                </span>
                                                <span class="badge bg-secondary-subtle text-secondary small rounded-pill">{{ $tagItem->posts_count }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right 4 Cols: Shared Sidebar (categories <= 10, tags <= 25) -->
            <div class="col-lg-4">
                @include('blog.partials.sidebar')
            </div>
        </div>
    </div>
</section>

@endsection
