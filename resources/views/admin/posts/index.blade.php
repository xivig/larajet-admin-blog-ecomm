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

  <!-- Status Nav Pills -->
  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <a href="{{ route('admin.posts.index') }}" class="btn btn-sm rounded-pill {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
      All Articles <span class="badge {{ !request('status') ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $counts['all'] ?? count($posts) }}</span>
    </a>
    <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'published' ? 'btn-primary' : 'btn-outline-secondary' }}">
      <i class="bi bi-check-circle me-1"></i> Published <span class="badge {{ request('status') === 'published' ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $counts['published'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'draft' ? 'btn-primary' : 'btn-outline-secondary' }}">
      <i class="bi bi-file-earmark me-1"></i> Drafts <span class="badge {{ request('status') === 'draft' ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $counts['draft'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.posts.index', ['status' => 'trash']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'trash' ? 'btn-danger' : 'btn-outline-danger' }}">
      <i class="bi bi-trash me-1"></i> Trash <span class="badge {{ request('status') === 'trash' ? 'bg-white text-danger' : 'bg-danger' }} ms-1">{{ $counts['trash'] ?? 0 }}</span>
    </a>
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
            <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>Trash (Deleted)</option>
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
      <!-- Bulk Actions Form -->
      <form id="bulkPostsForm" action="{{ route('admin.posts.bulk') }}" method="POST">
        @csrf
        <div class="d-flex align-items-center gap-2 mb-3">
          <select name="action" class="form-select form-select-sm w-auto" required>
            <option value="">Bulk Actions</option>
            @if (request('status') === 'trash')
              <option value="restore">Restore Selected</option>
              <option value="force_delete">Permanently Delete Selected</option>
            @else
              <option value="publish">Publish Selected</option>
              <option value="draft">Draft Selected</option>
              <option value="delete">Move to Trash</option>
            @endif
          </select>
          <button type="button" class="btn btn-sm btn-primary" onclick="confirmBulkPostAction()">Apply</button>
        </div>

        <div class="table-responsive">
          <table id="postsDataTable" class="table table-hover align-middle">
            <thead>
              <tr>
                <th style="width: 40px;" class="text-center" data-sortable="false">
                  <input type="checkbox" id="selectAllPosts" class="form-check-input">
                </th>
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
                  <td class="text-center">
                    <input type="checkbox" name="ids[]" value="{{ $post->id }}" class="form-check-input post-check">
                  </td>
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
                  @if ($post->trashed())
                    <span class="badge bg-danger-subtle text-danger px-2.5 py-1">
                      <i class="bi bi-trash me-1"></i> Trashed
                    </span>
                  @elseif ($post->status)
                    <span class="badge badge-published px-2.5 py-1">Published</span>
                  @else
                    <span class="badge badge-draft px-2.5 py-1">Draft</span>
                  @endif
                </td>
                <td>
                  <span class="small text-muted">{{ $post->created_at ? $post->created_at->format('M d, Y') : 'N/A' }}</span>
                </td>
                <td class="text-end">
                  @if ($post->trashed())
                    <div class="btn-group btn-group-sm">
                      <button type="submit" form="restore-form-{{ $post->id }}" class="btn btn-outline-success" title="Restore Article from Trash">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                      </button>
                      <button type="button" class="btn btn-outline-danger" title="Permanently Delete" data-id="{{ $post->id }}" data-title="{{ $post->title }}" onclick="confirmForceDelete(this.dataset.id, this.dataset.title)">
                        <i class="bi bi-x-octagon me-1"></i> Permanent Delete
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
                      <a href="{{ route('single-post', $post->slug) }}" target="_blank" class="btn btn-outline-secondary" title="View live article">
                        <i class="bi bi-box-arrow-up-right"></i>
                      </a>
                      <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-outline-primary" title="Edit Article with Quill">
                        <i class="bi bi-pencil-square"></i>
                      </a>
                      <button type="submit" form="duplicate-post-{{ $post->id }}" class="btn btn-outline-success" title="Duplicate Article as Draft">
                        <i class="bi bi-copy"></i>
                      </button>
                      <button type="button" class="btn btn-outline-danger" title="Move to Trash" data-id="{{ $post->id }}" data-title="{{ $post->title }}" onclick="confirmDelete(this.dataset.id, this.dataset.title)">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>

                    <form id="duplicate-post-{{ $post->id }}" action="{{ route('admin.posts.duplicate', $post->id) }}" method="POST" class="d-none">
                      @csrf
                    </form>
                    <form id="delete-form-{{ $post->id }}" action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-none">
                      @csrf
                      @method('DELETE')
                    </form>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
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
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'warning', title: 'Action Required', text: 'Please select a bulk action from the dropdown.' });
      } else {
        alert('Please select a bulk action.');
      }
      return;
    }

    if (checked.length === 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'warning', title: 'Selection Required', text: 'Please select at least one article.' });
      } else {
        alert('Please select at least one article.');
      }
      return;
    }

    const labels = {
      'publish': 'Publish ' + checked.length + ' article(s)?',
      'draft': 'Move ' + checked.length + ' article(s) to draft status?',
      'delete': 'Move ' + checked.length + ' article(s) to trash?',
      'restore': 'Restore ' + checked.length + ' article(s) from trash?',
      'force_delete': 'Permanently delete ' + checked.length + ' article(s)? This cannot be undone!'
    };

    showConfirmDialog({
      title: 'Confirm Bulk Action',
      text: labels[action] || 'Proceed with bulk action for ' + checked.length + ' article(s)?',
      icon: (action === 'force_delete' || action === 'delete') ? 'warning' : 'question',
      confirmButtonText: 'Yes, Proceed'
    }, function() {
      form.submit();
    });
  }

  function confirmDelete(id, title) {
    showConfirmDialog({
      title: 'Move to Trash?',
      text: 'Move article "' + (title || 'this item') + '" to Trash?',
      icon: 'warning',
      confirmButtonText: 'Yes, Move to Trash'
    }, function() {
      const form = document.getElementById('delete-form-' + id);
      if (form) {
        form.submit();
      } else {
        const dynamicForm = document.createElement('form');
        dynamicForm.method = 'POST';
        dynamicForm.action = '{{ url('admin/posts') }}/' + id;
        dynamicForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(dynamicForm);
        dynamicForm.submit();
      }
    });
  }

  function confirmForceDelete(id, title) {
    showConfirmDialog({
      title: 'Permanent Deletion',
      text: 'Are you sure you want to permanently delete article "' + (title || 'this item') + '"? This will remove all associated comments as well and cannot be undone.',
      icon: 'error',
      confirmButtonText: 'Delete Permanently'
    }, function() {
      const form = document.getElementById('force-delete-form-' + id);
      if (form) {
        form.submit();
      } else {
        const dynamicForm = document.createElement('form');
        dynamicForm.method = 'POST';
        dynamicForm.action = '{{ url('admin/posts') }}/' + id + '/force-delete';
        dynamicForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(dynamicForm);
        dynamicForm.submit();
      }
    });
  }
</script>
@endpush
@endsection
