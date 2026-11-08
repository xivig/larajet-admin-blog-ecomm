@extends('layouts.admin')

@section('title', 'Categories Management - XivigAdmin PRO')

@section('content')
<div class="page-categories">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Categories Management</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.posts.index') }}" class="breadcrumb-item">Editorial</a>
        <span class="breadcrumb-item active">Categories</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.posts.create') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-pencil-square me-1"></i> Compose Post
      </a>
    </div>
  </div>

  <div class="row g-4">
    <!-- Add Category Form -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">Create New Category</h5>
          <small class="text-muted">Organize editorial content under distinct subject headings</small>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="catName" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="catName" class="form-control" placeholder="E.g., Cloud Architecture" required>
            </div>

            <div class="mb-3">
              <label for="catDesc" class="form-label fw-semibold">Description</label>
              <textarea name="description" id="catDesc" class="form-control" rows="3" placeholder="Brief summary of what this category covers..."></textarea>
            </div>

            <div class="mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="status" id="catStatus" value="1" checked>
                <label class="form-check-label fw-semibold" for="catStatus">Active in Navigation</label>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-plus-circle me-1"></i> Save Category
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Categories List DataTable -->
    <div class="col-lg-8">
      <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm rounded-pill {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
          Active Categories <span class="badge {{ !request('status') ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $counts['active'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.categories.index', ['status' => 'trash']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'trash' ? 'btn-danger' : 'btn-outline-danger' }}">
          <i class="bi bi-trash me-1"></i> Trash <span class="badge {{ request('status') === 'trash' ? 'bg-white text-danger' : 'bg-danger' }} ms-1">{{ $counts['trash'] ?? 0 }}</span>
        </a>
      </div>

      <div class="card">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">{{ request('status') === 'trash' ? 'Trashed Categories' : 'Active Categories Directory' }}</h5>
          <small class="text-muted">Listing of all registered topic taxonomies and related post volumes</small>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="categoriesTable" class="table table-hover align-middle">
              <thead>
                <tr>
                  <th style="width: 50px;">#</th>
                  <th>Category Name</th>
                  <th>Description</th>
                  <th>Articles</th>
                  <th>Status</th>
                  <th data-sortable="false" class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($categories as $cat)
                  <tr>
                    <td>{{ $cat->id }}</td>
                    <td>
                      <strong class="text-primary">{{ $cat->name }}</strong>
                    </td>
                    <td>
                      <span class="small text-muted">{{ Str::limit($cat->description, 50) ?: '—' }}</span>
                    </td>
                    <td>
                      <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5">
                        {{ $cat->posts_count }} {{ Str::plural('post', $cat->posts_count) }}
                      </span>
                    </td>
                    <td>
                      @if ($cat->trashed())
                        <span class="badge bg-danger-subtle text-danger px-2 py-0.5 small"><i class="bi bi-trash me-1"></i> Trashed</span>
                      @elseif ($cat->status)
                        <span class="badge badge-published px-2 py-0.5 small">Active</span>
                      @else
                        <span class="badge badge-draft px-2 py-0.5 small">Inactive</span>
                      @endif
                    </td>
                    <td class="text-end">
                      @if ($cat->trashed())
                        <div class="btn-group btn-group-sm">
                          <form action="{{ route('admin.categories.restore', $cat->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-success" title="Restore Category">
                              <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                            </button>
                          </form>
                          <button type="button" class="btn btn-outline-danger" onclick="confirmForceDeleteCat('{{ $cat->id }}', '{{ addslashes($cat->name) }}', {{ $cat->posts_count }})" title="Permanently Delete">
                            <i class="bi bi-x-octagon me-1"></i> Delete
                          </button>
                        </div>

                        <form id="force-delete-cat-{{ $cat->id }}" action="{{ route('admin.categories.force-delete', $cat->id) }}" method="POST" style="display: none;">
                          @csrf
                          @method('DELETE')
                        </form>
                      @else
                        <div class="btn-group btn-group-sm">
                          <form action="{{ route('admin.categories.duplicate', $cat->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-success" title="Duplicate Category">
                              <i class="bi bi-copy"></i>
                            </button>
                          </form>
                          <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCatModal-{{ $cat->id }}" title="Edit Category">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button type="button" class="btn btn-outline-danger" onclick="deleteCategory('{{ $cat->id }}', '{{ addslashes($cat->name) }}')" title="Move to Trash">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>

                        <form id="delete-cat-{{ $cat->id }}" action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" style="display: none;">
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
        </div>
      </div>
    </div>
  </div>

  {{-- Edit Modals rendered outside of table for DOM integrity --}}
  @foreach ($categories as $cat)
    <div class="modal fade" id="editCatModal-{{ $cat->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('admin.categories.update', $cat->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
              <h5 class="modal-title">Edit Category: {{ $cat->name }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $cat->description }}</textarea>
              </div>
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="status" id="editStatus-{{ $cat->id }}" value="1" {{ $cat->status ? 'checked' : '' }}>
                  <label class="form-check-label" for="editStatus-{{ $cat->id }}">Active</label>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Update Category</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  function deleteCategory(id, name) {
    showConfirmDialog({
      title: 'Move to Trash?',
      text: 'Move category "' + name + '" to Trash?',
      icon: 'warning',
      confirmButtonText: 'Yes, Move to Trash'
    }, function() {
      document.getElementById('delete-cat-' + id).submit();
    });
  }

  function confirmForceDeleteCat(id, name, postCount) {
    if (postCount > 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Action Restricted',
        text: 'Cannot delete category "' + name + '" because it still contains ' + postCount + ' post(s). Please reassign them first.',
        customClass: { confirmButton: 'btn btn-primary px-4 rounded-pill' },
        buttonsStyling: false
      });
      return;
    }

    showConfirmDialog({
      title: 'Permanent Deletion',
      text: 'Are you sure you want to permanently delete category "' + name + '"? This action cannot be undone.',
      icon: 'error',
      confirmButtonText: 'Delete Permanently'
    }, function() {
      document.getElementById('force-delete-cat-' + id).submit();
    });
  }
</script>
@endpush
@endsection
