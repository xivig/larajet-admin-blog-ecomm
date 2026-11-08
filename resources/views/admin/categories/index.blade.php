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
      <div class="card">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">Active Categories Directory</h5>
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
                      @if ($cat->status)
                        <span class="badge badge-published px-2 py-0.5 small">Active</span>
                      @else
                        <span class="badge badge-draft px-2 py-0.5 small">Inactive</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCatModal-{{ $cat->id }}" title="Edit Category">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteCategory('{{ $cat->id }}', '{{ addslashes($cat->name) }}', {{ $cat->posts_count }})" title="Delete Category">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>

                      <form id="delete-cat-{{ $cat->id }}" action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" style="display: none;">
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
  function deleteCategory(id, name, postCount) {
    if (postCount > 0) {
      alert('Cannot delete category "' + name + '" because it still contains ' + postCount + ' post(s). Please reassign them first.');
      return;
    }
    if (confirm('Are you sure you want to delete category "' + name + '"?')) {
      document.getElementById('delete-cat-' + id).submit();
    }
  }
</script>
@endpush
@endsection
