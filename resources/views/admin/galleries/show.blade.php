@extends('layouts.admin')

@section('title', 'Gallery Photos: ' . $gallery->title . ' - XivigAdmin PRO')

@section('content')
<div class="page-galleries-show">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <h1 class="page-title mb-0">{{ $gallery->title }}</h1>
        <span class="badge {{ $gallery->status === 'active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} rounded-pill px-2.5 py-1">
          {{ ucfirst($gallery->status) }}
        </span>
      </div>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.galleries.index') }}" class="breadcrumb-item">Galleries</a>
        <span class="breadcrumb-item active">{{ $gallery->title }} ({{ $gallery->items->count() }} Assets)</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#attachMediaModal">
        <i class="bi bi-collection"></i>
        <span>Attach from Media Library</span>
      </button>
      <button type="button" class="btn btn-primary rounded-pill px-4 py-1.5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadPhotosModal" style="background-color: #5551ff; border-color: #5551ff;">
        <i class="bi bi-cloud-arrow-up fs-6"></i>
        <span>Upload Photos</span>
      </button>
      <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1.5">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
    </div>
  </div>

  <!-- Gallery Overview Banner -->
  <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div class="d-flex align-items-center gap-3">
        <img src="{{ $gallery->cover_url }}" alt="Cover" class="rounded-3 border object-fit-cover shadow-sm" style="width: 64px; height: 64px;">
        <div>
          <h5 class="fw-bold mb-1">{{ $gallery->title }}</h5>
          <p class="text-muted small mb-0">{{ $gallery->description ?: 'No synopsis provided for this gallery.' }}</p>
          <div class="text-muted small mt-1">
            <span>Slug: <code>/{{ $gallery->slug }}</code></span> &bull; 
            <span>Created: {{ $gallery->created_at ? $gallery->created_at->format('M d, Y') : 'N/A' }}</span>
          </div>
        </div>
      </div>
      <div class="d-flex gap-2">
        <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-medium fs-6 d-inline-flex align-items-center gap-2 shadow-2xs">
          <i class="bi bi-images text-primary"></i>
          <span>{{ $gallery->items->count() }} Total Photos</span>
        </span>
      </div>
    </div>
  </div>

  <!-- Photos Grid -->
  <div class="row g-3" id="galleryPhotoGrid">
    @forelse ($gallery->items as $item)
      <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative group-asset">
          <div class="position-relative overflow-hidden bg-light" style="padding-top: 100%;">
            <img src="{{ $item->url }}" 
                 alt="{{ $item->caption }}" 
                 class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover transition-transform"
                 onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/1-DZLDOIIi.jpg') }}'">
            
            @if ($gallery->cover_image === $item->image_path)
              <span class="position-absolute top-0 start-0 m-2 badge bg-primary shadow-sm rounded-pill px-2 py-1" style="font-size: 10px;">
                <i class="bi bi-star-fill me-0.5"></i> Cover
              </span>
            @endif

            <!-- Overlay Actions -->
            <div class="asset-overlay position-absolute bottom-0 start-0 w-100 p-2 d-flex justify-content-between align-items-center bg-dark bg-opacity-75">
              @if ($gallery->cover_image !== $item->image_path)
                <form action="{{ route('admin.galleries.cover', [$gallery->id, $item->id]) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-light py-0.5 px-1.5 rounded-pill" title="Set as Gallery Cover" style="font-size: 11px;">
                    <i class="bi bi-star"></i> Cover
                  </button>
                </form>
              @else
                <span class="text-white small" style="font-size: 11px;">Active Cover</span>
              @endif

              <form action="{{ route('admin.galleries.photos.delete', $item->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-sm btn-danger py-0.5 px-1.5 rounded-pill" title="Delete Photo" onclick="confirmDeletePhoto(this)">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </div>
          @if ($item->caption)
            <div class="p-2 border-top bg-body">
              <small class="text-truncate d-block fw-semibold" style="font-size: 11px;">{{ $item->caption }}</small>
            </div>
          @endif
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <div class="py-5">
          <div class="bg-body-secondary rounded-circle p-4 d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
            <i class="bi bi-camera fs-1 text-muted"></i>
          </div>
          <h4 class="fw-bold mb-1">No photos in this gallery yet</h4>
          <p class="text-muted small mb-4">Upload new high-resolution photos or attach assets from your media catalog.</p>
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadPhotosModal" style="background-color: #5551ff; border-color: #5551ff;">
              <i class="bi bi-cloud-arrow-up me-1"></i> Upload Photos
            </button>
            <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#attachMediaModal">
              <i class="bi bi-collection me-1"></i> Choose from Media
            </button>
          </div>
        </div>
      </div>
    @endforelse
  </div>

</div>

<!-- Upload Photos Modal -->
<div class="modal fade" id="uploadPhotosModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-bottom py-3">
        <h5 class="modal-title fw-bold">Upload Photos to Gallery</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.galleries.photos.upload', $gallery->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body p-4 text-center">
          <div class="border border-2 border-dashed rounded-4 p-5 mb-3 bg-light-subtle">
            <i class="bi bi-images fs-1 text-primary d-block mb-2"></i>
            <h6 class="fw-bold mb-1">Select Images to Upload</h6>
            <p class="text-muted small mb-3">Supports JPG, PNG, WEBP, GIF (Max 10MB per file)</p>
            <input type="file" name="photos[]" class="form-control" multiple accept="image/*" required>
          </div>
          <small class="text-muted">You can select multiple photos at once.</small>
        </div>
        <div class="modal-footer border-top py-3">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #5551ff; border-color: #5551ff;">
            <i class="bi bi-cloud-arrow-up me-1"></i> Start Upload
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Attach from Media Library Modal -->
<div class="modal fade" id="attachMediaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-bottom py-3">
        <div>
          <h5 class="modal-title fw-bold mb-0">Attach from Media Library</h5>
          <small class="text-muted">Select catalog pictures to link into this gallery</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.galleries.media.attach', $gallery->id) }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="row g-2">
            @forelse ($availablePictures as $pic)
              <div class="col-4 col-md-3 col-lg-2">
                <label class="d-block position-relative border rounded-3 p-1 cursor-pointer media-select-card h-100">
                  <input type="checkbox" name="picture_ids[]" value="{{ $pic->id }}" class="position-absolute top-0 start-0 m-2 form-check-input">
                  <img src="{{ asset($pic->link) }}" alt="{{ $pic->name }}" class="w-100 rounded-2 object-fit-cover" style="height: 80px;" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/1-DZLDOIIi.jpg') }}'">
                  <div class="text-truncate small mt-1 text-center" style="font-size: 10px;">{{ $pic->name }}</div>
                </label>
              </div>
            @empty
              <div class="col-12 text-center py-4 text-muted">
                No pictures available in Media Library.
              </div>
            @endforelse
          </div>
        </div>
        <div class="modal-footer border-top py-3">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #5551ff; border-color: #5551ff;">
            <i class="bi bi-plus-lg me-1"></i> Attach Selected
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .group-asset:hover .asset-overlay {
    opacity: 1;
  }
  .asset-overlay {
    transition: opacity 0.2s ease-in-out;
  }
</style>

@push('scripts')
<script>
  function confirmDeletePhoto(btn) {
    const form = btn.closest('form');
    showConfirmDialog({
      title: 'Remove Photo?',
      text: 'Are you sure you want to remove this photo from the gallery?',
      icon: 'warning',
      confirmButtonText: 'Yes, Remove'
    }, function() {
      if (form) form.submit();
    });
  }
</script>
@endpush

@endsection
