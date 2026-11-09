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
        <span>Compose Article</span>
      </a>
      <a href="{{ url('/blog-list') }}" target="_blank" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
        <i class="bi bi-box-arrow-up-right"></i>
        <span>View Live Blog</span>
      </a>
    </div>
  </div>

  <!-- Status Nav Pills -->
  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <a href="{{ route('admin.posts.index') }}" class="btn btn-sm rounded-pill {{ (!request('status') || request('status') === 'all') ? 'btn-primary' : 'btn-outline-secondary' }}">
      All Articles <span class="badge {{ (!request('status') || request('status') === 'all') ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $counts['all'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'published' ? 'btn-primary' : 'btn-outline-secondary' }}">
      <i class="bi bi-check-circle me-1"></i> Published <span class="badge {{ request('status') === 'published' ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $counts['published'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.posts.index', ['status' => 'scheduled']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'scheduled' ? 'btn-warning text-dark' : 'btn-outline-warning text-body' }}">
      <i class="bi bi-clock me-1"></i> Scheduled <span class="badge {{ request('status') === 'scheduled' ? 'bg-dark text-white' : 'bg-warning text-dark' }} ms-1">{{ $counts['scheduled'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'draft' ? 'btn-primary' : 'btn-outline-secondary' }}">
      <i class="bi bi-file-earmark me-1"></i> Drafts <span class="badge {{ request('status') === 'draft' ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $counts['draft'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.posts.index', ['status' => 'sticky']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'sticky' ? 'btn-danger' : 'btn-outline-danger' }}">
      <i class="bi bi-pin-angle me-1"></i> Sticky <span class="badge {{ request('status') === 'sticky' ? 'bg-white text-danger' : 'bg-danger' }} ms-1">{{ $counts['sticky'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.posts.index', ['status' => 'featured']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'featured' ? 'btn-info text-dark' : 'btn-outline-info text-body' }}">
      <i class="bi bi-star me-1"></i> Featured <span class="badge {{ request('status') === 'featured' ? 'bg-dark text-white' : 'bg-info text-dark' }} ms-1">{{ $counts['featured'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.posts.index', ['status' => 'trash']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'trash' ? 'btn-danger' : 'btn-outline-danger' }}">
      <i class="bi bi-trash me-1"></i> Trash <span class="badge {{ request('status') === 'trash' ? 'bg-white text-danger' : 'bg-danger' }} ms-1">{{ $counts['trash'] ?? 0 }}</span>
    </a>
  </div>

  <!-- Filter & Management Bar -->
  <div class="card mb-4 border-0 shadow-sm">
    <div class="card-body py-3">
      <form action="{{ route('admin.posts.index') }}" method="GET" class="row g-3 align-items-center">
        @if(request('status'))
          <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
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
        <div class="col-md-5">
          <label class="form-label small fw-semibold text-muted mb-1">Search Keywords</label>
          <div class="input-group input-group-sm">
            <input type="text" name="search" class="form-control" placeholder="Search title, slug, or content..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
          </div>
        </div>
        <div class="col-md-3 d-flex align-items-end justify-content-md-end gap-2">
          @if (request()->hasAny(['category_id', 'status', 'search']))
            <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-x-circle me-1"></i> Clear
            </a>
          @endif
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
            <strong>{{ $posts->total() }}</strong> Total Articles
          </span>
        </div>
      </form>
    </div>
  </div>

  <!-- Main Articles DataTable -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title mb-0 fw-bold">Articles Inventory</h5>
        <p class="card-subtitle text-muted mb-0 small">Real-time enterprise catalog with reading time, SEO, and visibility status</p>
      </div>
    </div>
    <div class="card-body p-0">
      <!-- Bulk Actions Form -->
      <form id="bulkPostsForm" action="{{ route('admin.posts.bulk') }}" method="POST">
        @csrf
        <div class="p-3 border-bottom d-flex align-items-center gap-2">
          <select name="action" class="form-select form-select-sm w-auto" required>
            <option value="">Bulk Actions</option>
            @if (request('status') === 'trash')
              <option value="restore">Restore Selected</option>
              <option value="force_delete">Permanently Delete Selected</option>
            @else
              <option value="publish">Publish Selected</option>
              <option value="draft">Move to Drafts</option>
              <option value="sticky">Mark as Sticky (Pin)</option>
              <option value="featured">Mark as Featured</option>
              <option value="delete">Move to Trash</option>
            @endif
          </select>
          <button type="button" class="btn btn-sm btn-primary" onclick="confirmBulkPostAction()">Apply</button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 40px;" class="ps-4 text-center">
                  <input type="checkbox" id="selectAllPosts" class="form-check-input">
                </th>
                <th style="width: 60px;">Cover</th>
                <th>Article Title &amp; Attributes</th>
                <th>Category</th>
                <th>Author</th>
                <th>Metrics</th>
                <th>Status</th>
                <th>Date</th>
                <th class="text-end pe-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($posts as $post)
                <tr>
                  <td class="ps-4 text-center">
                    <input type="checkbox" name="ids[]" value="{{ $post->id }}" class="form-check-input post-check">
                  </td>
                  <td>
                    @if ($post->image && $post->image->link)
                      @php
                        $cover = $post->image->link;
                        $src = Str::startsWith($cover, ['http', 'https', 'uploads/']) ? asset($cover) : asset('xivig-blog/images/blog/' . $cover);
                      @endphp
                      <img src="{{ $src }}" alt="" class="rounded object-fit-cover shadow-sm" style="width: 48px; height: 48px;" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                    @else
                      <img src="{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}" alt="" class="rounded object-fit-cover shadow-sm" style="width: 48px; height: 48px;">
                    @endif
                  </td>
                  <td>
                    <div class="fw-bold mb-1">
                      <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-decoration-none text-reset">
                        {{ $post->title }}
                      </a>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-wrap small">
                      <code class="text-muted">/blog/{{ $post->slug }}</code>
                      @if($post->is_sticky)
                        <span class="badge bg-danger-subtle text-danger px-1.5 py-0.5" style="font-size: 0.7rem;">
                          <i class="bi bi-pin-angle-fill"></i> Sticky
                        </span>
                      @endif
                      @if($post->is_featured)
                        <span class="badge bg-warning-subtle text-warning px-1.5 py-0.5" style="font-size: 0.7rem;">
                          <i class="bi bi-star-fill"></i> Featured
                        </span>
                      @endif
                      @if($post->visibility === 'password_protected')
                        <span class="badge bg-info-subtle text-info px-1.5 py-0.5" style="font-size: 0.7rem;">
                          <i class="bi bi-key-fill"></i> Protected
                        </span>
                      @elseif($post->visibility === 'private')
                        <span class="badge bg-secondary-subtle text-secondary px-1.5 py-0.5" style="font-size: 0.7rem;">
                          <i class="bi bi-shield-lock-fill"></i> Private
                        </span>
                      @endif
                      @if($post->images && $post->images->count() > 0)
                        <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 0.7rem;">
                          <i class="bi bi-images"></i> {{ $post->images->count() }}
                        </span>
                      @endif
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
                    <div class="small text-muted d-flex flex-column gap-1">
                      <span><i class="bi bi-clock me-1 text-primary"></i> {{ $post->reading_time }} min</span>
                      <span><i class="bi bi-eye me-1 text-success"></i> {{ number_format($post->views_count) }} views</span>
                    </div>
                  </td>
                  <td>
                    @if ($post->trashed())
                      <span class="badge bg-danger-subtle text-danger px-2.5 py-1">
                        <i class="bi bi-trash me-1"></i> Trashed
                      </span>
                    @elseif ($post->post_status === 'scheduled')
                      <span class="badge bg-warning-subtle text-warning px-2.5 py-1" title="Scheduled at {{ $post->scheduled_at }}">
                        <i class="bi bi-clock me-1"></i> Scheduled
                        @if($post->scheduled_at)
                          <small class="d-block" style="font-size: 0.65rem;">{{ $post->scheduled_at->format('M d, H:i') }}</small>
                        @endif
                      </span>
                    @elseif ($post->post_status === 'published' || $post->status)
                      <span class="badge bg-success-subtle text-success px-2.5 py-1">
                        <i class="bi bi-check-circle me-1"></i> Published
                      </span>
                    @else
                      <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">
                        <i class="bi bi-file-earmark me-1"></i> Draft
                      </span>
                    @endif
                  </td>
                  <td>
                    <span class="small text-muted">{{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span>
                  </td>
                  <td class="text-end pe-4">
                    @if ($post->trashed())
                      <div class="btn-group btn-group-sm">
                        <button type="submit" form="restore-form-{{ $post->id }}" class="btn btn-outline-success" title="Restore Article from Trash">
                          <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" title="Permanently Delete" onclick="confirmForceDelete({{ $post->id }}, '{{ addslashes($post->title) }}')">
                          <i class="bi bi-x-octagon"></i>
                        </button>
                      </div>

                      <form id="restore-form-{{ $post->id }}" action="{{ route('admin.posts.restore', $post->id) }}" method="POST" class="d-none">
                        @csrf
                      </form>
                      <form id="force-delete-form-{{ $post->id }}" action="{{ route('admin.posts.force-delete', $post->id) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                      </form>
                    @else
                      <div class="btn-group btn-group-sm">
                        <!-- Live Preview Action -->
                        <a href="{{ route('admin.posts.preview', $post->id) }}" target="_blank" class="btn btn-outline-info" title="Preview Live Article">
                          <i class="bi bi-eye"></i>
                        </a>
                        <!-- Edit Action -->
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-outline-primary" title="Edit Article">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <!-- Clone Action -->
                        <button type="submit" form="clone-post-{{ $post->id }}" class="btn btn-outline-success" title="Clone Article as Draft">
                          <i class="bi bi-copy"></i>
                        </button>
                        <!-- Delete / Trash Action -->
                        <button type="button" class="btn btn-outline-danger" title="Move to Trash" onclick="confirmDelete({{ $post->id }}, '{{ addslashes($post->title) }}')">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>

                      <form id="clone-post-{{ $post->id }}" action="{{ route('admin.posts.clone', $post->id) }}" method="POST" class="d-none">
                        @csrf
                      </form>
                      <form id="delete-form-{{ $post->id }}" action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                    No articles found matching the selected filter criteria.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-between align-items-center">
          <small class="text-muted">Showing {{ $posts->firstItem() ?? 0 }} to {{ $posts->lastItem() ?? 0 }} of {{ $posts->total() }} entries</small>
          <div>{{ $posts->links() }}</div>
        </div>
      </form>
    </div>
  </div>

</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllPosts');
    if (selectAll) {
      selectAll.addEventListener('change', function() {
        document.querySelectorAll('.post-check').forEach(cb => cb.checked = selectAll.checked);
      });
    }
  });

  function confirmBulkPostAction() {
    const form = document.getElementById('bulkPostsForm');
    const action = form.querySelector('select[name="action"]').value;
    const checked = form.querySelectorAll('.post-check:checked');

    if (!action) {
      alert('Please select an action from the dropdown.');
      return;
    }
    if (checked.length === 0) {
      alert('Please select at least one article to apply this action.');
      return;
    }

    if (confirm(`Are you sure you want to apply "${action}" to ${checked.length} selected article(s)?`)) {
      form.submit();
    }
  }

  function confirmDelete(id, title) {
    if (confirm(`Move "${title}" to trash?`)) {
      document.getElementById(`delete-form-${id}`).submit();
    }
  }

  function confirmForceDelete(id, title) {
    if (confirm(`Permanently delete "${title}"? This cannot be undone.`)) {
      document.getElementById(`force-delete-form-${id}`).submit();
    }
  }
</script>
@endpush
@endsection
