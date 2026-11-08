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
      <a href="{{ route('single-post', $post->slug) }}" target="_blank" class="btn btn-outline-secondary">
        <i class="bi bi-box-arrow-up-right me-1"></i> View Live
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
        <div class="card mb-4">
          <div class="card-header py-3">
            <h5 class="card-title mb-0">Article Body &amp; Metadata</h5>
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
        <div class="card">
          <div class="card-header py-3">
            <h5 class="card-title mb-0">Cover Image</h5>
          </div>
          <div class="card-body">
            <div class="row g-3 align-items-center">
              <div class="col-md-4">
                <label class="form-label small text-muted d-block">Current Cover:</label>
                @if ($post->image && $post->image->link)
                  <img src="{{ Str::startsWith($post->image->link, ['http', 'https', 'uploads/']) ? asset($post->image->link) : asset('xivig-blog/images/blog/' . $post->image->link) }}" alt="Cover" class="img-fluid rounded border" style="max-height: 140px;" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                @else
                  <div class="text-muted small">No cover image attached.</div>
                @endif
              </div>
              <div class="col-md-8">
                <label class="form-label fw-semibold">Replace Cover Image</label>
                <input type="file" name="featured_image" id="featuredImageInput" class="form-control mb-2" accept="image/*">

                <label class="form-label fw-semibold">Or Switch to Library Asset</label>
                <select name="image_id" class="form-select">
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
      </div>

      <!-- Right Column: Settings & Publish -->
      <div class="col-lg-4">

        <!-- Publish Operations -->
        <div class="card mb-4">
          <div class="card-header py-3">
            <h5 class="card-title mb-0">Publishing Status</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label fw-semibold d-block">Publication Status</label>
              <div class="form-check form-switch fs-5">
                <input class="form-check-input" type="checkbox" name="status" id="postStatus" value="1" {{ $post->status ? 'checked' : '' }}>
                <label class="form-check-label fs-6 fw-normal" for="postStatus" id="statusLabel">
                  {{ $post->status ? 'Published Live' : 'Unpublished Draft' }}
                </label>
              </div>
            </div>

            <div class="d-flex justify-content-between text-muted small py-1.5 border-top border-bottom mb-3">
              <span>Created</span>
              <span>{{ $post->created_at ? $post->created_at->format('M d, Y H:i') : 'N/A' }}</span>
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
        <div class="card mb-4">
          <div class="card-header py-3">
            <h5 class="card-title mb-0">Taxonomy &amp; Metadata</h5>
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

  <form id="deleteForm" action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
  </form>

</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill Editor
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
      }
    });

    // Status switch label update
    const postStatus = document.getElementById('postStatus');
    const statusLabel = document.getElementById('statusLabel');
    if (postStatus && statusLabel) {
      postStatus.addEventListener('change', function() {
        statusLabel.textContent = this.checked ? 'Published Live' : 'Unpublished Draft';
      });
    }

    // Form submit handler
    const form = document.getElementById('editPostForm');
    form.addEventListener('submit', function(e) {
      const hiddenContent = document.getElementById('hiddenContent');
      hiddenContent.value = quill.root.innerHTML;
    });

    window.confirmDelete = function() {
      if (confirm('Are you sure you want to delete this article? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
      }
    };
  });
</script>
@endpush
@endsection
