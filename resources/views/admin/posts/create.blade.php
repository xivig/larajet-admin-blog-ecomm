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
      <!-- Main Editorial Column (Title, Quill WYSIWYG, Excerpt, Gallery, SEO) -->
      <div class="col-lg-8">
        <!-- Content Card -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h5 class="card-title mb-0 fw-bold">Article Content</h5>
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
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h5 class="card-title mb-0 fw-bold">Featured Cover Image</h5>
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

        <!-- Unlimited Images / Photo Gallery Component -->
        @include('admin.posts.partials.gallery-fields')

        <!-- Professional SEO Suite Component -->
        @include('admin.posts.partials.seo-fields')

      </div>

      <!-- Right Column: Publishing, Visibility, Taxonomy, Author -->
      <div class="col-lg-4">

        <!-- Publishing Operations Box -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h5 class="card-title mb-0 fw-bold">Publishing Operations</h5>
          </div>
          <div class="card-body">
            <!-- Publication Status -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Publication Status</label>
              <select name="post_status" id="postStatusSelect" class="form-select" onchange="handleStatusChange(this.value)">
                <option value="published" {{ old('post_status', 'published') === 'published' ? 'selected' : '' }}>Published Immediately</option>
                <option value="scheduled" {{ old('post_status') === 'scheduled' ? 'selected' : '' }}>Scheduled for Future</option>
                <option value="draft" {{ old('post_status') === 'draft' ? 'selected' : '' }}>Internal Draft</option>
              </select>
            </div>

            <!-- Scheduled Date/Time (Conditional) -->
            <div class="mb-3 {{ old('post_status') === 'scheduled' ? '' : 'd-none' }}" id="scheduledDateBox">
              <label class="form-label fw-semibold">
                <i class="bi bi-clock me-1 text-warning"></i> Scheduled Publication Date
              </label>
              <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
              <div class="form-text small">Article will be published automatically by the scheduler.</div>
            </div>

            <!-- Visibility -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Audience Visibility</label>
              <select name="visibility" id="visibilitySelect" class="form-select" onchange="handleVisibilityChange(this.value)">
                <option value="public" {{ old('visibility', 'public') === 'public' ? 'selected' : '' }}>Public (Open to everyone)</option>
                <option value="password_protected" {{ old('visibility') === 'password_protected' ? 'selected' : '' }}>Password Protected</option>
                <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>Private (Members Only)</option>
              </select>
            </div>

            <!-- Password Protected Passphrase (Conditional) -->
            <div class="mb-3 {{ old('visibility') === 'password_protected' ? '' : 'd-none' }}" id="passwordBox">
              <label class="form-label fw-semibold">
                <i class="bi bi-key me-1 text-info"></i> Access Passphrase
              </label>
              <input type="text" name="password" class="form-control" placeholder="Enter access passphrase..." value="{{ old('password') }}">
              <div class="form-text small">Readers must enter this password to read the article.</div>
            </div>

            <hr>

            <!-- Sticky & Featured Toggles -->
            <div class="mb-2">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_sticky" id="isSticky" value="1" {{ old('is_sticky') ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isSticky">
                  <i class="bi bi-pin-angle text-danger me-1"></i> Sticky Post (Pin to Top)
                </label>
              </div>
              <small class="text-muted ps-4 d-block">Pinned articles float to the top of category &amp; blog lists.</small>
            </div>

            <div class="mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isFeatured">
                  <i class="bi bi-star text-warning me-1"></i> Featured Spotlight
                </label>
              </div>
              <small class="text-muted ps-4 d-block">Spotlighted in hero banners and featured widgets.</small>
            </div>

            <hr>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                <i class="bi bi-send me-1"></i> Save &amp; Publish
              </button>
              <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()">
                <i class="bi bi-save me-1"></i> Save as Draft
              </button>
            </div>
          </div>
        </div>

        <!-- Taxonomy: Category, Tag, Author -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h5 class="card-title mb-0 fw-bold">Taxonomy &amp; Editorial</h5>
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

<!-- Media Picker Modal -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-labelledby="mediaPickerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="mediaPickerModalLabel">
          <i class="bi bi-images me-2 text-primary"></i> Media Library Picker
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          @forelse ($pictures as $pic)
            <div class="col-md-3 col-sm-4 col-6">
              <div class="card h-100 border text-center p-2 cursor-pointer media-picker-item" data-id="{{ $pic->id }}" data-name="{{ $pic->name ?? 'Image' }}" data-src="{{ Str::startsWith($pic->link, ['http', 'https', 'uploads/']) ? asset($pic->link) : asset('xivig-blog/images/blog/' . $pic->link) }}" onclick="selectMediaItem(this)">
                <div class="ratio ratio-1x1 mb-2 bg-light rounded overflow-hidden">
                  <img src="{{ Str::startsWith($pic->link, ['http', 'https', 'uploads/']) ? asset($pic->link) : asset('xivig-blog/images/blog/' . $pic->link) }}" alt="{{ $pic->name }}" class="object-fit-cover w-100 h-100" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                </div>
                <div class="small text-truncate fw-semibold">{{ $pic->name ?? 'ID: ' . $pic->id }}</div>
              </div>
            </div>
          @empty
            <div class="col-12 text-center py-4 text-muted">
              No media items available in gallery.
            </div>
          @endforelse
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
    // 1. Initialize Quill WYSIWYG
    if (typeof Quill !== 'undefined') {
      quill = new Quill('#postContentEditor', {
        theme: 'snow',
        placeholder: 'Write your comprehensive article insight...',
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

    // 2. Slug Auto-generate
    const titleInput = document.getElementById('postTitle');
    const slugPreview = document.getElementById('slugPreview');
    if (titleInput && slugPreview) {
      titleInput.addEventListener('input', function() {
        const slug = this.value
          .toLowerCase()
          .trim()
          .replace(/[^\w\s-]/g, '')
          .replace(/[\s_-]+/g, '-')
          .replace(/^-+|-+$/g, '');
        slugPreview.textContent = slug || 'article-title';
      });
    }

    // 3. Featured Image Upload Preview
    const featuredInput = document.getElementById('featuredImageInput');
    const previewBox = document.getElementById('imagePreviewBox');
    const previewImg = document.getElementById('imagePreview');

    if (featuredInput) {
      featuredInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewBox.style.display = 'block';
            document.getElementById('imageIdSelect').value = '';
          };
          reader.readAsDataURL(this.files[0]);
        }
      });
    }

    // 4. Form Submit Handler (Sync Quill HTML to Hidden Input)
    const form = document.getElementById('postForm');
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
    const submitBtn = document.getElementById('submitBtn');

    if (status === 'scheduled') {
      scheduledBox.classList.remove('d-none');
      submitBtn.innerHTML = '<i class="bi bi-clock me-1"></i> Schedule Article';
    } else if (status === 'draft') {
      scheduledBox.classList.add('d-none');
      submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Save Draft';
    } else {
      scheduledBox.classList.add('d-none');
      submitBtn.innerHTML = '<i class="bi bi-send me-1"></i> Publish Article';
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

    const previewBox = document.getElementById('imagePreviewBox');
    const previewImg = document.getElementById('imagePreview');
    previewImg.src = src;
    previewBox.style.display = 'block';

    const modal = bootstrap.Modal.getInstance(document.getElementById('mediaPickerModal'));
    if (modal) modal.hide();
  }

  function saveAsDraft() {
    document.getElementById('postStatusSelect').value = 'draft';
    handleStatusChange('draft');
    if (quill) {
      document.getElementById('hiddenContent').value = quill.root.innerHTML;
    }
    document.getElementById('postForm').submit();
  }
</script>
@endpush
@endsection
