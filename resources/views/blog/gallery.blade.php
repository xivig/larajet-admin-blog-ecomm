@use('Illuminate\Support\Str')
@extends('layouts.blog')
@section('title', 'Living Image Gallery - Architectural Visions & Visual Assets')
@section('content')

<!-- Gallery Hero Banner -->
<section class="page-title py-4">
    <div class="container text-center">
        <span class="badge-elite badge-elite-dark mb-3">
            <span class="badge-dot pulse"></span> Visual Intelligence & Architectural Imagery
        </span>
        <h1 class="font-weight-bold mb-3 display-4 text-body" style="letter-spacing: -0.03em;">
            Living Image Gallery
        </h1>
        <p class="text-muted lead mx-auto mb-4" style="max-width: 680px;">
            A responsive, living exploration of architectural blueprints, UI design systems, and contemporary engineering aesthetics.
        </p>

        <!-- Category/Filter Pill Row -->
        <div class="d-flex align-items-center justify-content-center flex-wrap" style="gap: 8px;">
            <a href="{{ url('/blog/gallery') }}" class="cat-pill active">
                <i class="fas fa-th-large mr-1"></i> All Visuals ({{ count($galleryItems) }})
            </a>
            @foreach($categories->take(6) as $cat)
                <a href="{{ url('/blog-list?category=' . $cat->id) }}" class="cat-pill">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Gallery Grid Section -->
<section class="section pt-4 pb-5">
    <div class="container-fluid px-xl-5 px-lg-4 px-3">
        @if(count($galleryItems) > 0)
            <div class="living-gallery mb-5">
                @foreach($galleryItems as $index => $item)
                    @php
                        // Cycle masonry classes: large, tall, wide, normal
                        $sizeClass = match($index % 6) {
                            0 => 'large',
                            1 => 'tall',
                            2 => 'wide',
                            3 => '',
                            4 => 'tall',
                            5 => 'wide',
                            default => ''
                        };
                        $imgUrl = $item['url'];
                        $imgCaption = $item['caption'] ?: 'Architectural Visual ' . ($index + 1);
                        $postSlug = $item['post_slug'] ?? null;
                        $postTitle = $item['post_title'] ?? 'Visual Detail';
                    @endphp

                    <div class="gallery-item {{ $sizeClass }}" onclick="openGalleryLightbox('{{ $imgUrl }}', '{{ addslashes($imgCaption) }}', '{{ addslashes($postTitle) }}', '{{ $postSlug ? route('single-post', $postSlug) : '' }}')">
                        <img loading="lazy" src="{{ $imgUrl }}" class="lazy-img" alt="{{ $imgCaption }}" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                        <div class="gallery-caption">
                            <h4>{{ $imgCaption }}</h4>
                            @if($postTitle)
                                <p><i class="fas fa-link mr-1"></i> {{ Str::limit($postTitle, 40) }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded border p-4 my-4">
                <i class="far fa-images fa-3x text-muted mb-3"></i>
                <h4 class="font-weight-bold">No Gallery Images Available</h4>
                <p class="text-muted">Images will automatically populate as multi-image posts and visual assets are added.</p>
                <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-primary btn-sm">Explore Articles</a>
            </div>
        @endif
    </div>
</section>

<!-- Lightbox Modal -->
<div class="modal fade" id="galleryLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark text-white border-0" style="border-radius: 20px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.7);">
            <div class="modal-header border-0 pb-0 position-absolute top-0 end-0 z-3 p-3" style="right: 15px; top: 15px;">
                <button type="button" class="btn btn-light rounded-circle shadow d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; padding: 0;" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-0 text-center position-relative">
                <img id="lightboxImg" src="" alt="Fullscreen Visual" class="img-fluid w-100" style="max-height: 75vh; object-fit: contain; background: #0b0f19;">
                <div class="p-4 text-left text-start d-flex flex-wrap align-items-center justify-content-between" style="background: rgba(15, 23, 42, 0.95);">
                    <div>
                        <h4 id="lightboxCaption" class="font-weight-bold mb-1 text-white">Visual Details</h4>
                        <p id="lightboxSubtitle" class="text-muted small mb-0">High-Resolution Architectural Asset</p>
                    </div>
                    <div class="mt-2 mt-sm-0">
                        <a id="lightboxPostLink" href="#" class="btn-read-elite btn-sm" style="display: none;">
                            Read Linked Article <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openGalleryLightbox(url, caption, title, postLink) {
    document.getElementById('lightboxImg').src = url;
    document.getElementById('lightboxCaption').innerText = caption;
    document.getElementById('lightboxSubtitle').innerText = title;
    
    const postLinkBtn = document.getElementById('lightboxPostLink');
    if (postLink) {
        postLinkBtn.href = postLink;
        postLinkBtn.style.display = 'inline-flex';
    } else {
        postLinkBtn.style.display = 'none';
    }

    if (window.bootstrap && bootstrap.Modal) {
        const modal = new bootstrap.Modal(document.getElementById('galleryLightboxModal'));
        modal.show();
    } else if (window.$) {
        $('#galleryLightboxModal').modal('show');
    }
}
</script>

@endsection
