@extends('layouts.admin')

@section('title', 'Media Library - XivigAdmin PRO')

@section('content')
<div class="page-media">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Media Library &amp; Asset Storage</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">Media</span>
      </nav>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#uploadSection">
      <i class="bi bi-cloud-arrow-up me-1"></i> Upload New Asset
    </button>
  </div>

  <!-- Upload Section (Collapsible) -->
  <div class="collapse mb-4" id="uploadSection">
    <div class="card border-primary">
      <div class="card-header bg-primary-subtle py-3">
        <h5 class="card-title text-primary mb-0">Upload Image Asset</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
          @csrf
          <div class="col-md-5">
            <label class="form-label fw-semibold">Select File <span class="text-danger">*</span></label>
            <input type="file" name="media_file" class="form-control" accept="image/*" required>
          </div>
          <div class="col-md-5">
            <label class="form-label fw-semibold">Asset Label / Title</label>
            <input type="text" name="name" class="form-control" placeholder="E.g., Microservices architecture diagram">
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-upload me-1"></i> Upload
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Media Grid -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
      <h5 class="card-title mb-0">Media Assets Catalog</h5>
      <span class="badge bg-secondary-subtle text-secondary">{{ $pictures->total() }} Assets</span>
    </div>
    <div class="card-body">
      <div class="row g-3">
        @forelse ($pictures as $pic)
          @php
            $assetUrl = Str::startsWith($pic->link, ['http', 'https', 'uploads/']) ? asset($pic->link) : asset('xivig-blog/images/blog/' . $pic->link);
          @endphp
          <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 shadow-sm border">
              <div class="position-relative" style="height: 140px; overflow: hidden; background: #0000000a;">
                <img src="{{ $assetUrl }}" alt="{{ $pic->name }}" class="w-100 h-100" style="object-fit: cover;" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                <span class="position-absolute top-0 end-0 m-1 badge bg-dark bg-opacity-75">
                  #{{ $pic->id }}
                </span>
              </div>
              <div class="card-body p-2 d-flex flex-column justify-content-between">
                <div>
                  <div class="fw-semibold small text-truncate" title="{{ $pic->name }}">{{ $pic->name ?: 'Media asset' }}</div>
                  <div class="text-muted small" style="font-size: 11px;">
                    Used in {{ $pic->posts_count }} {{ Str::plural('post', $pic->posts_count) }}
                  </div>
                </div>
                <div class="btn-group btn-group-sm w-100 mt-2">
                  <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $assetUrl }}')" title="Copy image link">
                    <i class="bi bi-clipboard"></i> Copy
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteMedia('{{ $pic->id }}')" title="Delete asset">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>

            <form id="delete-media-{{ $pic->id }}" action="{{ route('admin.media.destroy', $pic->id) }}" method="POST" style="display: none;">
              @csrf
              @method('DELETE')
            </form>
          </div>
        @empty
          <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-images fs-1 d-block mb-2 text-secondary"></i>
            No media assets uploaded yet. Click "Upload New Asset" to add files.
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      <div class="mt-4 d-flex justify-content-center">
        {{ $pictures->links() }}
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
      alert('Asset link copied to clipboard:\n' + text);
    }).catch(function() {
      prompt('Copy asset URL:', text);
    });
  }

  function deleteMedia(id) {
    if (confirm('Are you sure you want to delete this media asset?')) {
      document.getElementById('delete-media-' + id).submit();
    }
  }
</script>
@endpush
@endsection
