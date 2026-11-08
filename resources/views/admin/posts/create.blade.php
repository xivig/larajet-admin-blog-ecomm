@extends('layouts.admin')

@section('title', 'Compose Article - XivigAdmin PRO')

@section('content')
<div class="page-posts-create">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Compose New Article</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.posts.index') }}" class="breadcrumb-item">Articles</a>
        <span class="breadcrumb-item active">Compose</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Articles
      </a>
    </div>
  </div>

  <form id="postForm" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="content" id="hiddenContent">

    <div class="row g-4">
      <!-- Main Editorial Column (Title, Quill WYSIWYG, Excerpt) -->
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-header py-3">
            <h5 class="card-title mb-0">Article Content</h5>
            <small class="text-muted">Draft formatted text, code snippets, headers, and media</small>
          </div>
          <div class="card-body">

            <!-- Title -->
            <div class="mb-3">
              <label for="postTitle" class="form-label fw-semibold">Article Title <span class="text-danger">*</span></label>
              <input type="text" name="title" id="postTitle" class="form-control form-control-lg" placeholder="Enter a compelling article title..." value="{{ old('title') }}" required autofocus>
              <div class="form-text">
                Permalink slug: <code>/blog/<span id="slugPreview">article-title</span></code>
              </div>
            </div>

            <!-- Quill WYSIWYG Editor Container -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Body Content <span class="text-danger">*</span></label>
              <div id="postContentEditor" style="min-height: 380px;">
                {!! old('content', '<p>Start typing your professional article content here...</p>') !!}
              </div>
            </div>

            <!-- Excerpt -->
            <div class="mb-0">
              <label for="postExcerpt" class="form-label fw-semibold">Summary / Excerpt</label>
              <textarea name="excerpt" id="postExcerpt" class="form-control" rows="3" placeholder="Brief 1-2 sentence synopsis for social previews and search engines...">{{ old('excerpt') }}</textarea>
              <div class="form-text small">Leave blank to automatically extract from the opening paragraph.</div>
            </div>

          </div>
        </div>

        <!-- Featured Image Card -->
        <div class="card">
          <div class="card-header py-3">
            <h5 class="card-title mb-0">Featured Cover Image</h5>
            <small class="text-muted">Display image for article headers and card showcases</small>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Upload New Image</label>
                <input type="file" name="featured_image" id="featuredImageInput" class="form-control" accept="image/*">
                <div class="form-text small">Supported formats: JPG, PNG, WEBP, SVG (Max 5MB).</div>
              </div>
              <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label fw-semibold mb-0">Select From Media Library</label>
                  <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" data-bs-toggle="modal" data-bs-target="#mediaPickerModal">
                    <i class="bi bi-images me-1"></i> Visual Picker
                  </button>
                </div>
                <select name="image_id" id="imageIdSelect" class="form-select">
                  <option value="">-- Choose from existing media --</option>
                  @foreach ($pictures as $pic)
                    <option value="{{ $pic->id }}">
                      {{ $pic->name ?? 'Image #' . $pic->id }} ({{ $pic->link }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 mt-3" id="imagePreviewBox" style="display: none;">
                <label class="form-label small text-muted">Cover Preview:</label>
                <div class="border rounded p-2 text-center" style="max-width: 320px; max-height: 200px; overflow: hidden;">
                  <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 180px;">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Settings, Category, Tag, Author, Publish -->
      <div class="col-lg-4">

        <!-- Publish Box -->
        <div class="card mb-4">
          <div class="card-header py-3">
            <h5 class="card-title mb-0">Publishing Operations</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label fw-semibold d-block">Publication Status</label>
              <div class="form-check form-switch fs-5">
                <input class="form-check-input" type="checkbox" name="status" id="postStatus" value="1" checked>
                <label class="form-check-label fs-6 fw-normal" for="postStatus" id="statusLabel">
                  Published Immediately
                </label>
              </div>
              <small class="text-muted d-block mt-1">Uncheck to save as an unpublished internal draft.</small>
            </div>

            <hr>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                <i class="bi bi-send me-1"></i> Publish Article
              </button>
              <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()">
                <i class="bi bi-save me-1"></i> Save as Draft
              </button>
            </div>
          </div>
        </div>

        <!-- Taxonomy: Category & Tag -->
        <div class="card mb-4">
          <div class="card-header py-3">
            <h5 class="card-title mb-0">Taxonomy &amp; Categories</h5>
          </div>
          <div class="card-body">

            <!-- Category -->
            <div class="mb-3">
              <label for="categorySelect" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
              <select name="category_id" id="categorySelect" class="form-select" required>
                <option value="">-- Select Category --</option>
                @foreach ($categories as $cat)
                  <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
              <div class="mt-1 small">
                <a href="{{ route('admin.categories.index') }}" target="_blank" class="text-decoration-none">
                  <i class="bi bi-plus-circle me-1"></i> Add new category
                </a>
              </div>
            </div>

            <!-- Tag -->
            <div class="mb-3">
              <label for="tagSelect" class="form-label fw-semibold">Primary Tag</label>
              <select name="tag_id" id="tagSelect" class="form-select">
                <option value="">-- Select Primary Tag --</option>
                @foreach ($tags as $tag)
                  <option value="{{ $tag->id }}" {{ old('tag_id') == $tag->id ? 'selected' : '' }}>
                    #{{ $tag->name }}
                  </option>
                @endforeach
              </select>
              <div class="mt-1 small">
                <a href="{{ route('admin.tags.index') }}" target="_blank" class="text-decoration-none">
                  <i class="bi bi-plus-circle me-1"></i> Add new tag
                </a>
              </div>
            </div>

            <!-- Author -->
            <div class="mb-0">
              <label for="authorSelect" class="form-label fw-semibold">Assigned Author</label>
              <select name="author_id" id="authorSelect" class="form-select">
                @foreach ($authors as $author)
                  <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
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

</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill Snow Editor
    const quill = new Quill('#postContentEditor', {
      theme: 'snow',
      modules: {
        toolbar: [
          [{ 'header': [1, 2, 3, 4, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'color': [] }, { 'background': [] }],
          [{ 'list': 'ordered' }, { 'list': 'bullet' }],
          ['blockquote', 'code-block'],
          ['link', 'image', 'clean']
        ]
      },
      placeholder: 'Write your comprehensive article content here...'
    });

    // Auto-update slug preview on title input
    const titleInput = document.getElementById('postTitle');
    const slugPreview = document.getElementById('slugPreview');
    if (titleInput && slugPreview) {
      titleInput.addEventListener('input', function() {
        const slug = titleInput.value
          .toLowerCase()
          .trim()
          .replace(/[^a-z0-9 -]/g, '')
          .replace(/\s+/g, '-')
          .replace(/-+/g, '-');
        slugPreview.textContent = slug || 'article-title';
      });
    }

    // Status switch label update
    const postStatus = document.getElementById('postStatus');
    const statusLabel = document.getElementById('statusLabel');
    if (postStatus && statusLabel) {
      postStatus.addEventListener('change', function() {
        statusLabel.textContent = this.checked ? 'Published Immediately' : 'Saved as Draft';
      });
    }

    // Live preview for image upload
    const featuredImageInput = document.getElementById('featuredImageInput');
    const imagePreviewBox = document.getElementById('imagePreviewBox');
    const imagePreview = document.getElementById('imagePreview');
    if (featuredImageInput) {
      featuredImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreviewBox.style.display = 'block';
          };
          reader.readAsDataURL(file);
        }
      });
    }

    // Form submit handler: copy Quill HTML into hidden textarea
    const postForm = document.getElementById('postForm');
    postForm.addEventListener('submit', function(e) {
      const hiddenContent = document.getElementById('hiddenContent');
      hiddenContent.value = quill.root.innerHTML;
      if (quill.getText().trim().length === 0) {
        alert('Please enter article content before submitting.');
        e.preventDefault();
        return false;
      }
    });

    // Save as draft function
    window.saveAsDraft = function() {
      postStatus.checked = false;
      statusLabel.textContent = 'Saved as Draft';
      postForm.submit();
    };
  });
</script>
@endpush
@include('admin.partials.media-picker-modal')
@endsection
