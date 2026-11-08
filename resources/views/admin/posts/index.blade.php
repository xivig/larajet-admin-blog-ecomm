@extends('layouts.admin')

@section('title', 'All Articles - XivigAdmin PRO')

@section('content')
<div class="page-posts-index">

  <!-- Page Header & Actions -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Blog Articles &amp; Editorial Catalog</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item">Editorial</span>
        <span class="breadcrumb-item active">All Articles</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.posts.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Create New Article</span>
      </a>
      <a href="{{ url('/blog') }}" target="_blank" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
        <i class="bi bi-box-arrow-up-right"></i>
        <span>View Live Site</span>
      </a>
    </div>
  </div>

  <!-- Filter & Management Bar -->
  <div class="card mb-4">
    <div class="card-body py-3">
      <form action="{{ route('admin.posts.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-4">
          <label class="form-label small fw-semibold text-muted mb-1">Filter by Category</label>
          <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-semibold text-muted mb-1">Filter by Status</label>
          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published Only</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Drafts Only</option>
          </select>
        </div>
        <div class="col-md-5 d-flex align-items-end justify-content-md-end gap-2">
          @if (request()->hasAny(['category_id', 'status', 'q']))
            <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-x-circle me-1"></i> Clear Filters
            </a>
          @endif
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
            <strong>{{ count($posts) }}</strong> Total Records
          </span>
        </div>
      </form>
    </div>
  </div>

  <!-- Main Articles DataTable -->
  <div class="card">
    <div class="card-header py-3">
      <h5 class="card-title mb-0">Articles Inventory</h5>
      <p class="card-subtitle text-muted mb-0 small">Real-time searchable datatable with sorting and instant pagination</p>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="postsDataTable" class="table table-hover align-middle">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th style="width: 60px;">Cover</th>
              <th>Article Title &amp; Slug</th>
              <th>Category</th>
              <th>Author</th>
              <th>Comments</th>
              <th>Status</th>
              <th>Date</th>
              <th data-sortable="false" class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($posts as $post)
              <tr>
                <td>{{ $post->id }}</td>
                <td>
                  @if ($post->image && $post->image->link)
                    <img src="{{ Str::startsWith($post->image->link, ['http', 'https', 'uploads/']) ? asset($post->image->link) : asset('xivig-blog/images/blog/' . $post->image->link) }}" alt="" class="post-thumb-sm" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                  @else
                    <img src="{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}" alt="" class="post-thumb-sm">
                  @endif
                </td>
                <td>
                  <div class="fw-bold">
                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-decoration-none text-reset">
                      {{ $post->title }}
                    </a>
                  </div>
                  <div class="text-muted small text-truncate" style="max-width: 260px;">
                    <code>/blog/{{ $post->slug }}</code>
                  </div>
                </td>
                <td>
                  <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">
                    {{ $post->category->name ?? 'Uncategorized' }}
                  </span>
                </td>
                <td>
                  <span class="small fw-semibold">{{ $post->author->nickname ?? ($post->author->user->name ?? 'Admin') }}</span>
                </td>
                <td>
                  <span class="badge bg-info-subtle text-info px-2 py-0.5">
                    <i class="bi bi-chat-left-dots me-1"></i> {{ $post->comments ? $post->comments->count() : 0 }}
                  </span>
                </td>
                <td>
                  @if ($post->status)
                    <span class="badge badge-published px-2.5 py-1">Published</span>
                  @else
                    <span class="badge badge-draft px-2.5 py-1">Draft</span>
                  @endif
                </td>
                <td>
                  <span class="small text-muted">{{ $post->created_at ? $post->created_at->format('M d, Y') : 'N/A' }}</span>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('single-post', $post->slug) }}" target="_blank" class="btn btn-outline-secondary" title="View live article">
                      <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-outline-primary" title="Edit Article with Quill">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <button type="button" class="btn btn-outline-danger" title="Delete Article" onclick="confirmDelete('{{ $post->id }}', '{{ addslashes($post->title) }}')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>

                  <form id="delete-form-{{ $post->id }}" action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  function confirmDelete(id, title) {
    if (confirm('Are you sure you want to delete article "' + title + '"? This will remove all associated comments as well.')) {
      document.getElementById('delete-form-' + id).submit();
    }
  }
</script>
@endpush
@endsection
