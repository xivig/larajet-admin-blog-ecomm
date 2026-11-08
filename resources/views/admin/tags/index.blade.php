@extends('layouts.admin')

@section('title', 'Tags Management - XivigAdmin PRO')

@section('content')
<div class="page-tags">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Tags Taxonomy</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.posts.index') }}" class="breadcrumb-item">Editorial</a>
        <span class="breadcrumb-item active">Tags</span>
      </nav>
    </div>
  </div>

  <div class="row g-4">
    <!-- Add Tag Form -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">Add New Tag</h5>
          <small class="text-muted">Keywords for cross-referencing articles</small>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.tags.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="tagName" class="form-label fw-semibold">Tag Name <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">#</span>
                <input type="text" name="name" id="tagName" class="form-control" placeholder="kubernetes" required>
              </div>
              <div class="form-text small">Enter single keywords or hyphenated phrases.</div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-tag me-1"></i> Save Tag
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Tags List DataTable -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">Active Tags Directory</h5>
          <small class="text-muted">All metadata tags used across published articles</small>
        </div>
        <div class="card-body">
          <!-- Bulk Actions Form -->
          <form id="bulkTagsForm" action="{{ route('admin.tags.bulk') }}" method="POST">
            @csrf
            <div class="d-flex align-items-center gap-2 mb-3">
              <select name="action" class="form-select form-select-sm w-auto" required>
                <option value="">Bulk Actions</option>
                <option value="delete">Move to Trash</option>
                <option value="restore">Restore Selected</option>
                <option value="force_delete">Permanently Delete Selected</option>
              </select>
              <button type="button" class="btn btn-sm btn-primary" onclick="confirmBulkTagAction()">Apply</button>
            </div>

            <div class="table-responsive">
              <table id="tagsTable" class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th style="width: 40px;" class="text-center" data-sortable="false">
                      <input type="checkbox" id="selectAllTags" class="form-check-input">
                    </th>
                    <th style="width: 50px;">#</th>
                    <th>Tag Name</th>
                    <th>Articles Tagged</th>
                    <th>Created</th>
                    <th data-sortable="false" class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($tags as $tag)
                    <tr>
                      <td class="text-center">
                        <input type="checkbox" name="ids[]" value="{{ $tag->id }}" class="form-check-input tag-check">
                      </td>
                      <td>{{ $tag->id }}</td>
                    <td>
                      <span class="badge bg-secondary-subtle text-secondary fs-6 px-2.5 py-1">
                        #{{ $tag->name }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-info-subtle text-info rounded-pill px-2.5">
                        {{ $tag->posts_count }} {{ Str::plural('article', $tag->posts_count) }}
                      </span>
                    </td>
                    <td>
                      <span class="small text-muted">{{ $tag->created_at ? $tag->created_at->format('M d, Y') : 'N/A' }}</span>
                    </td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTagModal-{{ $tag->id }}" title="Edit Tag">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-id="{{ $tag->id }}" data-name="{{ $tag->name }}" onclick="deleteTag(this.dataset.id, this.dataset.name)" title="Delete Tag">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>

                      <form id="delete-tag-{{ $tag->id }}" action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                      </form>
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
  </div>

  {{-- Edit Modals rendered outside of table for DOM integrity --}}
  @foreach ($tags as $tag)
    <div class="modal fade" id="editTagModal-{{ $tag->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
              <h5 class="modal-title">Edit Tag</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">Tag Name</label>
                <div class="input-group">
                  <span class="input-group-text">#</span>
                  <input type="text" name="name" class="form-control" value="{{ $tag->name }}" required>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Update Tag</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach

</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllTags');
    if (selectAll) {
      selectAll.addEventListener('change', function() {
        document.querySelectorAll('.tag-check').forEach(cb => cb.checked = selectAll.checked);
      });
    }
  });

  function confirmBulkTagAction() {
    const form = document.getElementById('bulkTagsForm');
    const action = form.querySelector('select[name="action"]').value;
    const checked = form.querySelectorAll('.tag-check:checked');

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
        Swal.fire({ icon: 'warning', title: 'Selection Required', text: 'Please select at least one tag.' });
      } else {
        alert('Please select at least one tag.');
      }
      return;
    }

    const labels = {
      'delete': 'Move ' + checked.length + ' tag(s) to trash?',
      'restore': 'Restore ' + checked.length + ' tag(s) from trash?',
      'force_delete': 'Permanently delete ' + checked.length + ' tag(s)?'
    };

    showConfirmDialog({
      title: 'Confirm Bulk Action',
      text: labels[action] || 'Proceed with bulk action for ' + checked.length + ' tag(s)?',
      icon: (action === 'force_delete' || action === 'delete') ? 'warning' : 'question',
      confirmButtonText: 'Yes, Proceed'
    }, function() {
      form.submit();
    });
  }

  function deleteTag(id, name) {
    showConfirmDialog({
      title: 'Delete Tag?',
      text: 'Are you sure you want to delete tag "#' + (name || 'this tag') + '"?',
      icon: 'warning',
      confirmButtonText: 'Yes, delete tag'
    }, function() {
      const form = document.getElementById('delete-tag-' + id);
      if (form) {
        form.submit();
      } else {
        const dynamicForm = document.createElement('form');
        dynamicForm.method = 'POST';
        dynamicForm.action = '{{ url('admin/tags') }}/' + id;
        dynamicForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(dynamicForm);
        dynamicForm.submit();
      }
    });
  }
</script>
@endpush
@endsection
