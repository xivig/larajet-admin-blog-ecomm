@extends('layouts.admin')

@section('title', 'Article Preview: ' . $post->title . ' - XivigAdmin PRO')

@section('content')
<div class="page-post-show">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Article Preview &amp; Audit</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.posts.index') }}" class="breadcrumb-item">Articles</a>
        <span class="breadcrumb-item active">Preview</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('single-post', $post->slug) }}" target="_blank" class="btn btn-outline-secondary">
        <i class="bi bi-box-arrow-up-right me-1"></i> View Live
      </a>
      <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i> Edit Article
      </a>
      <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card mb-4">
        @if ($post->image && $post->image->link)
          <div style="max-height: 360px; overflow: hidden;">
            <img src="{{ Str::startsWith($post->image->link, ['http', 'https', 'uploads/']) ? asset($post->image->link) : asset('xivig-blog/images/blog/' . $post->image->link) }}" alt="{{ $post->title }}" class="card-img-top" style="object-fit: cover; max-height: 360px;" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
          </div>
        @endif
        <div class="card-body p-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-primary-subtle text-primary">{{ $post->category->name ?? 'General' }}</span>
            <span class="text-muted small">&bull;</span>
            <span class="text-muted small">{{ $post->created_at ? $post->created_at->format('F d, Y') : '' }}</span>
            <span class="text-muted small">&bull;</span>
            <span class="badge {{ $post->status ? 'badge-published' : 'badge-draft' }}">
              {{ $post->status ? 'Published' : 'Draft' }}
            </span>
          </div>

          <h2 class="card-title fw-bold mb-3">{{ $post->title }}</h2>

          @if ($post->excerpt)
            <div class="lead text-muted mb-4 pb-3 border-bottom font-italic">
              {{ $post->excerpt }}
            </div>
          @endif

          <div class="article-content lh-lg">
            {!! $post->content !!}
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <!-- Article Metadata Card -->
      <div class="card mb-4">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">Article Metadata</h5>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted small">ID</span>
            <span class="fw-semibold small">#{{ $post->id }}</span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted small">Slug</span>
            <span class="small"><code>{{ $post->slug }}</code></span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted small">Author</span>
            <span class="fw-semibold small">{{ $post->author->nickname ?? ($post->author->user->name ?? 'Admin') }}</span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted small">Tag</span>
            <span class="badge bg-secondary-subtle text-secondary small">#{{ $post->tag->name ?? 'None' }}</span>
          </div>
          <div class="d-flex justify-content-between py-2">
            <span class="text-muted small">Comments</span>
            <span class="fw-semibold small">{{ $post->comments ? $post->comments->count() : 0 }}</span>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">Management Actions</h5>
        </div>
        <div class="card-body d-grid gap-2">
          <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil-square me-1"></i> Edit Article
          </a>
          <a href="{{ route('single-post', $post->slug) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-box-arrow-up-right me-1"></i> View Live Article
          </a>
          <form id="showDeletePostForm" action="{{ route('admin.posts.destroy', $post->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-outline-danger w-100" onclick="showConfirmDialog({ title: 'Move to Trash?', text: 'Are you sure you want to move this article to Trash?' }, () => document.getElementById('showDeletePostForm').submit())">
              <i class="bi bi-trash me-1"></i> Delete Article
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
