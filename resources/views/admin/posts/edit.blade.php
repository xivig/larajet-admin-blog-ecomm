@extends('layouts.admin')

@section('title', 'Edit Article: ' . $post->title . ' - XivigAdmin PRO')

@section('content')
<div class="page-posts-edit">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Edit Article</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.posts.index') }}" class="breadcrumb-item">Articles</a>
        <span class="breadcrumb-item active">Edit #{{ $post->id }}</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <!-- Clone Action -->
      <form action="{{ route('admin.posts.clone', $post->id) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-success">
          <i class="bi bi-copy me-1"></i> Clone
        </button>
      </form>

      <!-- Live / Preview Action -->
      <a href="{{ route('admin.posts.preview', $post->id) }}" target="_blank" class="btn btn-outline-info">
        <i class="bi bi-eye me-1"></i> Preview Live
      </a>

      <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to List
      </a>
    </div>
  </div>

  <form id="editPostForm" action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="content" id="hiddenContent">

    <div class="row g-4">
      <!-- Main Editorial Column -->
      <div class="col-lg-8">
        <!-- Body Content Card -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h5 class="card-title mb-0 fw-bold">Article Body &amp; Content</h5>
          </div>
          <div class="card-body">

            <!-- Title -->
            <div class="mb-3">
              <label for="postTitle" class="form-label fw-semibold">Article Title <span class="text-danger">*</span></label>
              <input type="text" name="title" id="postTitle" class="form-control form-control-lg" value="{{ old('title', $post->title) }}" required>
              <div class="form-text">
                Current URL: <code>/blog/{{ $post->slug }}</code>
              </div>
            </div>

            <!-- Quill WYSIWYG Editor Container -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Article Content <span class="text-danger">*</span></label>
              <div id="postContentEditor" style="min-height: 400px;">
                {!! old('content', $post->content) !!}
              </div>
            </div>

            <!-- Excerpt -->
            <div class="mb-0">
              <label for="postExcerpt" class="form-label fw-semibold">Summary / Excerpt</label>
              <textarea name="excerpt" id="postExcerpt" class="form-control" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

          </div>
        </div>

        <!-- Featured Image Card -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h5 class="card-title mb-0 fw-bold">Cover Image</h5>
          </div>
          <div class="card-body">
            <div class="row g-3 align-items-center">
              <div class="col-md-4">
                <label class="form-label small text-muted d-block">Current Cover:</label>
                @if ($post->image && $post->image->link)
                  <img id="currentCoverImg" src="{{ Str::startsWith($post->image->link, ['http', 'https', 'uploads/']) ? asset($post->image->link) : asset('xivig-blog/images/blog/' . $post->image->link) }}" alt="Cover" class="img-fluid rounded border" style="max-height: 140px;" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                @else
                  <img id="currentCoverImg" src="{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}" alt="Cover" class="img-fluid rounded border" style="max-height: 140px;">
                @endif
              </div>
              <div class="col-md-8">
                <label class="form-label fw-semibold">Replace Cover Image</label>
                <input type="file" name="featured_image" id="featuredImageInput" class="form-control mb-2" accept="image/*">

                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label fw-semibold mb-0">Switch to Library Asset</label>
                  <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" data-bs-toggle="modal" data-bs-target="#mediaPickerModal">
                    <i class="bi bi-images me-1"></i> Visual Picker
                  </button>
                </div>
                <select name="image_id" id="imageIdSelect" class="form-select">
                  @foreach ($pictures as $pic)
                    <option value="{{ $pic->id }}" {{ $post->image_id == $pic->id ? 'selected' : '' }}>
                      {{ $pic->name ?? 'Image #' . $pic->id }} ({{ $pic->link }})
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Unlimited Images / Photo Gallery Component -->
        @include('admin.posts.partials.gallery-fields', ['post' => $post])

        <!-- Professional SEO Suite Component -->
        @include('admin.posts.partials.seo-fields', ['post' => $post])

      </div>

      <!-- Right Column: Publishing, Visibility, Taxonomy -->
      <div class="col-lg-4">

        <!-- Publishing Operations Box -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h5 class="card-title mb-0 fw-bold">Publishing Status</h5>
          </div>
          <div class="card-body">
            @php
              $currentPostStatus = old('post_status', $post->post_status ?? ($post->status ? 'published' : 'draft'));
            @endphp
            <!-- Publication Status -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Publication Status</label>
              <select name="post_status" id="postStatusSelect" class="form-select" onchange="handleStatusChange(this.value)">
                <option value="published" {{ $currentPostStatus === 'published' ? 'selected' : '' }}>Published Live</option>
                <option value="scheduled" {{ $currentPostStatus === 'scheduled' ? 'selected' : '' }}>Scheduled for Future</option>
                <option value="draft" {{ $currentPostStatus === 'draft' ? 'selected' : '' }}>Internal Draft</option>
              </select>
            </div>

            <!-- Scheduled Date/Time (Conditional) -->
            <div class="mb-3 {{ $currentPostStatus === 'scheduled' ? '' : 'd-none' }}" id="scheduledDateBox">
              <label class="form-label fw-semibold">
                <i class="bi bi-clock me-1 text-warning"></i> Scheduled Date &amp; Time
              </label>
              <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', $post->scheduled_at ? $post->scheduled_at->format('Y-m-d\TH:i') : '') }}">
            </div>

            <!-- Visibility -->
            @php
              $currentVisibility = old('visibility', $post->visibility ?? 'public');
            @endphp
            <div class="mb-3">
              <label class="form-label fw-semibold">Audience Visibility</label>
              <select name="visibility" id="visibilitySelect" class="form-select" onchange="handleVisibilityChange(this.value)">
                <option value="public" {{ $currentVisibility === 'public' ? 'selected' : '' }}>Public (Open to everyone)</option>
                <option value="password_protected" {{ $currentVisibility === 'password_protected' ? 'selected' : '' }}>Password Protected</option>
                <option value="private" {{ $currentVisibility === 'private' ? 'selected' : '' }}>Private (Members Only)</option>
              </select>
            </div>

            <!-- Password Protected Passphrase (Conditional) -->
            <div class="mb-3 {{ $currentVisibility === 'password_protected' ? '' : 'd-none' }}" id="passwordBox">
              <label class="form-label fw-semibold">
                <i class="bi bi-key me-1 text-info"></i> Access Passphrase
              </label>
              <input type="text" name="password" class="form-control" placeholder="Enter access passphrase..." value="{{ old('password', $post->password) }}">
            </div>

            <hr>

            <!-- Sticky & Featured Toggles -->
            <div class="mb-2">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_sticky" id="isSticky" value="1" {{ old('is_sticky', $post->is_sticky) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isSticky">
                  <i class="bi bi-pin-angle text-danger me-1"></i> Sticky Post (Pin to Top)
                </label>
              </div>
            </div>

            <div class="mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" value="1" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isFeatured">
                  <i class="bi bi-star text-warning me-1"></i> Featured Spotlight
                </label>
              </div>
            </div>

            <div class="d-flex justify-content-between text-muted small py-1.5 border-top border-bottom mb-3">
              <span>Reading Time</span>
              <span class="fw-semibold text-primary">{{ $post->reading_time }} min read</span>
            </div>

            <div class="d-flex justify-content-between text-muted small py-1.5 border-bottom mb-3">
              <span>Total Impressions</span>
              <span class="fw-semibold text-success">{{ number_format($post->views_count) }} views</span>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save me-1"></i> Update Article
              </button>
              <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                <i class="bi bi-trash me-1"></i> Delete Article
              </button>
            </div>
          </div>
        </div>

        <!-- Taxonomy: Category & Tag -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h5 class="card-title mb-0 fw-bold">Taxonomy &amp; Metadata</h5>
          </div>
          <div class="card-body">

            <!-- Category -->
            <div class="mb-3">
              <label for="categorySelect" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
              <select name="category_id" id="categorySelect" class="form-select" required>
                @foreach ($categories as $cat)
                  <option value="{{ $cat->id }}" {{ $post->category_id == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Tag -->
            <div class="mb-3">
              <label for="tagSelect" class="form-label fw-semibold">Primary Tag</label>
              <select name="tag_id" id="tagSelect" class="form-select">
                <option value="">-- None --</option>
                @foreach ($tags as $tag)
                  <option value="{{ $tag->id }}" {{ $post->tag_id == $tag->id ? 'selected' : '' }}>
                    #{{ $tag->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Author -->
            <div class="mb-0">
              <label for="authorSelect" class="form-label fw-semibold">Author</label>
              <select name="author_id" id="authorSelect" class="form-select">
                @foreach ($authors as $author)
                  <option value="{{ $author->id }}" {{ $post->author_id == $author->id ? 'selected' : '' }}>
                    {{ $author->nickname ?? ($author->user->name ?? 'Author #' . $author->id) }}
                  </option>
                @endforeach
              </select>
            </div>

          </div>
        </div>

      </div>
    </div>
  </form>

  <!-- Hidden Delete Form -->
  <form id="deleteForm" action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
  </form>

</div>

<!-- Media Picker Modal -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-labelledby="mediaPickerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="mediaPickerModalLabel">
          <i class="bi bi-images me-2 text-primary"></i> Media Library
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          @foreach ($pictures as $pic)
            <div class="col-md-3 col-sm-4 col-6">
              <div class="card h-100 border text-center p-2 cursor-pointer" data-id="{{ $pic->id }}" data-src="{{ Str::startsWith($pic->link, ['http', 'https', 'uploads/']) ? asset($pic->link) : asset('xivig-blog/images/blog/' . $pic->link) }}" onclick="selectMediaItem(this)">
                <div class="ratio ratio-1x1 mb-2 bg-light rounded overflow-hidden">
                  <img src="{{ Str::startsWith($pic->link, ['http', 'https', 'uploads/']) ? asset($pic->link) : asset('xivig-blog/images/blog/' . $pic->link) }}" alt="{{ $pic->name }}" class="object-fit-cover w-100 h-100" onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                </div>
                <div class="small text-truncate fw-semibold">{{ $pic->name ?? 'Image #' . $pic->id }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  let quill;

  document.addEventListener('DOMContentLoaded', function() {
    if (typeof Quill !== 'undefined') {
      quill = new Quill('#postContentEditor', {
        theme: 'snow',
        modules: {
          toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'align': [] }],
            ['blockquote', 'code-block'],
            ['link', 'image', 'video'],
            ['clean']
          ]
        }
      });
    }

    const form = document.getElementById('editPostForm');
    if (form) {
      form.addEventListener('submit', function() {
        if (quill) {
          document.getElementById('hiddenContent').value = quill.root.innerHTML;
        }
      });
    }
  });

  function handleStatusChange(status) {
    const scheduledBox = document.getElementById('scheduledDateBox');
    if (status === 'scheduled') {
      scheduledBox.classList.remove('d-none');
    } else {
      scheduledBox.classList.add('d-none');
    }
  }

  function handleVisibilityChange(vis) {
    const pwdBox = document.getElementById('passwordBox');
    if (vis === 'password_protected') {
      pwdBox.classList.remove('d-none');
    } else {
      pwdBox.classList.add('d-none');
    }
  }

  function selectMediaItem(item) {
    const id = item.dataset.id;
    const src = item.dataset.src;

    document.getElementById('imageIdSelect').value = id;
    document.getElementById('featuredImageInput').value = '';

    const currentImg = document.getElementById('currentCoverImg');
    if (currentImg) currentImg.src = src;

    const modal = bootstrap.Modal.getInstance(document.getElementById('mediaPickerModal'));
    if (modal) modal.hide();
  }

  function confirmDelete() {
    if (confirm('Are you sure you want to move this article to trash?')) {
      document.getElementById('deleteForm').submit();
    }
  }
</script>
@endpush
@endsection
