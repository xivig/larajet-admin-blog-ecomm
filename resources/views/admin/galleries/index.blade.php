@extends('layouts.admin')

@section('title', 'Gallery Management - XivigAdmin PRO')

@section('content')
<div class="page-galleries-index">

  <!-- Breadcrumbs & Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Visual Galleries &amp; Portfolios</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item">Assets &amp; Media</span>
        <span class="breadcrumb-item active">Galleries</span>
      </nav>
    </div>
  </div>

  <!-- Top Pill Navigation Bar (Matching Screenshot) -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <!-- Active / Trash Segmented Pill Tabs -->
    <div class="bg-body border rounded-pill p-1 d-inline-flex align-items-center shadow-sm">
      <a href="{{ route('admin.galleries.index', ['status' => 'active']) }}" 
         class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 {{ $status !== 'trash' ? 'btn-dark text-white' : 'btn-light text-muted' }}">
        <span>Active</span>
        <span class="badge {{ $status !== 'trash' ? 'bg-secondary text-white' : 'bg-secondary-subtle text-muted' }} rounded-pill ms-1" style="font-size: 11px;">
          {{ $counts['active'] }}
        </span>
      </a>
      <a href="{{ route('admin.galleries.index', ['status' => 'trash']) }}" 
         class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 {{ $status === 'trash' ? 'btn-dark text-white' : 'btn-light text-muted' }}">
        <i class="bi bi-trash"></i>
        <span>Trash</span>
        <span class="badge {{ $status === 'trash' ? 'bg-secondary text-white' : 'bg-secondary-subtle text-muted' }} rounded-pill ms-1" style="font-size: 11px;">
          {{ $counts['trash'] }}
        </span>
      </a>
    </div>

    <!-- Create New Gallery Button -->
    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createGalleryModal" style="background-color: #5551ff; border-color: #5551ff;">
      <i class="bi bi-plus-lg fs-6"></i>
      <span>Create New Gallery</span>
    </button>
  </div>

  <!-- Galleries Table Card -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="p-3 bg-light-subtle border-bottom">
      <form id="bulkGalleriesForm" action="{{ route('admin.galleries.bulk') }}" method="POST">
        @csrf
        <div class="d-flex align-items-center gap-2">
          <select name="action" class="form-select form-select-sm w-auto rounded-pill" required>
            <option value="">Bulk Actions</option>
            @if ($status === 'trash')
              <option value="restore">Restore Selected</option>
              <option value="force_delete">Permanently Delete Selected</option>
            @else
              <option value="delete">Move to Trash</option>
            @endif
          </select>
          <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="confirmBulkGalleryAction()">Apply</button>
        </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light-subtle text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.8px;">
          <tr>
            <th class="ps-4 py-3 text-center" style="width: 40px;" data-sortable="false">
              <input type="checkbox" id="selectAllGalleries" class="form-check-input">
            </th>
            <th class="py-3" style="width: 320px;">Gallery Title</th>
            <th class="py-3" style="width: 160px;">Assets</th>
            <th class="py-3" style="width: 140px;">Status</th>
            <th class="py-3" style="width: 180px;">Created At</th>
            <th class="py-3 pe-4 text-end" style="width: 220px;">Actions</th>
          </tr>
        </thead>
        <tbody class="border-top-0">
          @forelse ($galleries as $gallery)
            <tr>
              <td class="ps-4 py-3 text-center">
                <input type="checkbox" name="ids[]" value="{{ $gallery->id }}" class="form-check-input gallery-check">
              </td>
              <!-- Gallery Title & Thumbnail -->
              <td class="py-3">
                <div class="d-flex align-items-center gap-3">
                  <img src="{{ $gallery->cover_url }}" 
                       alt="{{ $gallery->title }}" 
                       class="rounded-3 border object-fit-cover shadow-xs" 
                       style="width: 48px; height: 48px; min-width: 48px;"
                       onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/1-DZLDOIIi.jpg') }}'">
                  <div>
                    <div class="fw-bold text-body fs-6 mb-0.5">{{ $gallery->title }}</div>
                    <div class="text-muted small" style="font-size: 12px;">
                      <code>/{{ $gallery->slug }}</code>
                    </div>
                  </div>
                </div>
              </td>

              <!-- Assets Count Pill -->
              <td class="py-3">
                <span class="badge bg-light text-dark border rounded-pill px-3 py-1.5 fw-medium d-inline-flex align-items-center gap-1.5 shadow-2xs" style="font-size: 12px;">
                  <i class="bi bi-images text-primary"></i>
                  <span>{{ $gallery->items_count }} Assets</span>
                </span>
              </td>

              <!-- Status Pill -->
              <td class="py-3">
                @if ($gallery->trashed())
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1.5 fw-medium" style="font-size: 12px;">
                    Trashed
                  </span>
                @elseif ($gallery->status === 'active')
                  <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-medium" style="font-size: 12px;">
                    Active
                  </span>
                @else
                  <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1.5 fw-medium" style="font-size: 12px;">
                    Inactive
                  </span>
                @endif
              </td>

              <!-- Created At -->
              <td class="py-3 text-muted small">
                <div class="d-inline-flex align-items-center gap-1.5">
                  <i class="bi bi-calendar3 text-muted"></i>
                  <span>{{ $gallery->created_at ? $gallery->created_at->format('M d, Y') : 'N/A' }}</span>
                </div>
              </td>

              <!-- Actions -->
              <td class="py-3 pe-4 text-end">
                @if ($gallery->trashed())
                  <div class="d-inline-flex align-items-center gap-1.5">
                    <form action="{{ route('admin.galleries.restore', $gallery->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3" title="Restore Gallery">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                      </button>
                    </form>
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" title="Permanently Delete" data-id="{{ $gallery->id }}" data-title="{{ $gallery->title }}" onclick="confirmForceDeleteGallery(this.dataset.id, this.dataset.title)">
                      <i class="bi bi-x-octagon me-1"></i> Purge
                    </button>
                  </div>
                @else
                  <div class="d-inline-flex align-items-center gap-1.5">
                    <!-- View Photos Pill Button -->
                    <a href="{{ route('admin.galleries.show', $gallery->id) }}" 
                       class="btn btn-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" 
                       style="background-color: #5551ff; border-color: #5551ff;"
                       title="View and manage photos">
                      <i class="bi bi-images"></i>
                      <span>View Photos</span>
                    </a>

                    <!-- Edit Pill Button -->
                    <button type="button" 
                            class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editGalleryModal-{{ $gallery->id }}"
                            title="Edit Gallery Details">
                      <i class="bi bi-pencil"></i>
                      <span>Edit</span>
                    </button>

                    <!-- Trash Button -->
                    <button type="button" 
                            class="btn btn-outline-danger btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0" 
                            style="width: 32px; height: 32px;"
                            data-id="{{ $gallery->id }}"
                            data-title="{{ $gallery->title }}"
                            onclick="confirmTrashGallery(this.dataset.id, this.dataset.title)"
                            title="Move to Trash">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">
                <div class="py-4">
                  <div class="bg-body-secondary rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-images fs-2 text-muted"></i>
                  </div>
                  <h5 class="fw-bold mb-1">No {{ $status === 'trash' ? 'trashed' : 'active' }} galleries found</h5>
                  <p class="text-muted small mb-3">Create high-resolution visual stories, seasonal lookbooks, and media catalogs.</p>
                  @if ($status !== 'trash')
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createGalleryModal" style="background-color: #5551ff; border-color: #5551ff;">
                      <i class="bi bi-plus-lg me-1"></i> Create First Gallery
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    </form>
  </div>

</div>

<!-- Create New Gallery Modal -->
<div class="modal fade" id="createGalleryModal" tabindex="-1" aria-labelledby="createGalleryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-bottom py-3">
        <h5 class="modal-title fw-bold" id="createGalleryModalLabel">Create New Gallery</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">Gallery Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. abc or Autumn Lookbook" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Custom Slug / Permalink <small class="text-muted">(Optional)</small></label>
            <div class="input-group">
              <span class="input-group-text">/</span>
              <input type="text" name="slug" class="form-control" placeholder="abc-lpeiY (Auto-generated if blank)">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Cover Thumbnail Image</label>
            <input type="file" name="cover_image" class="form-control" accept="image/*">
            <small class="text-muted">Will use the first uploaded photo if left empty.</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Editorial Synopsis / Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Brief visual overview and context..."></textarea>
          </div>

          <div class="mb-2">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select rounded-3">
              <option value="active" selected>Active (Visible)</option>
              <option value="inactive">Inactive (Hidden)</option>
            </select>
          </div>
        </div>
        <div class="modal-footer border-top py-3">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #5551ff; border-color: #5551ff;">
            <i class="bi bi-check-lg me-1"></i> Create Gallery
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Gallery Modals -->
@foreach ($galleries as $gallery)
  @if (!$gallery->trashed())
    <div class="modal fade" id="editGalleryModal-{{ $gallery->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
          <div class="modal-header border-bottom py-3">
            <h5 class="modal-title fw-bold">Edit Gallery: {{ $gallery->title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('admin.galleries.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body p-4">
              <div class="mb-3">
                <label class="form-label fw-semibold">Gallery Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control rounded-3" value="{{ $gallery->title }}" required>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Slug / Permalink</label>
                <div class="input-group">
                  <span class="input-group-text">/</span>
                  <input type="text" name="slug" class="form-control" value="{{ $gallery->slug }}" required>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Update Cover Image</label>
                <div class="d-flex align-items-center gap-3 mb-2">
                  <img src="{{ $gallery->cover_url }}" alt="Cover" class="rounded-3 border" style="width: 48px; height: 48px; object-fit: cover;">
                  <span class="small text-muted">Current cover thumbnail</span>
                </div>
                <input type="file" name="cover_image" class="form-control" accept="image/*">
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Editorial Synopsis / Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $gallery->description }}</textarea>
              </div>

              <div class="mb-2">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select rounded-3">
                  <option value="active" {{ $gallery->status === 'active' ? 'selected' : '' }}>Active (Visible)</option>
                  <option value="inactive" {{ $gallery->status === 'inactive' ? 'selected' : '' }}>Inactive (Hidden)</option>
                </select>
              </div>
            </div>
            <div class="modal-footer border-top py-3">
              <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #5551ff; border-color: #5551ff;">
                <i class="bi bi-save me-1"></i> Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@endforeach

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllGalleries');
    if (selectAll) {
      selectAll.addEventListener('change', function() {
        document.querySelectorAll('.gallery-check').forEach(cb => cb.checked = selectAll.checked);
      });
    }
  });

  function confirmBulkGalleryAction() {
    const form = document.getElementById('bulkGalleriesForm');
    const action = form.querySelector('select[name="action"]').value;
    const checked = form.querySelectorAll('.gallery-check:checked');

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
        Swal.fire({ icon: 'warning', title: 'Selection Required', text: 'Please select at least one gallery.' });
      } else {
        alert('Please select at least one gallery.');
      }
      return;
    }

    const labels = {
      'delete': 'Move ' + checked.length + ' gallery(s) to trash?',
      'restore': 'Restore ' + checked.length + ' gallery(s) from trash?',
      'force_delete': 'Permanently delete ' + checked.length + ' gallery(s)?'
    };

    showConfirmDialog({
      title: 'Confirm Bulk Action',
      text: labels[action] || 'Proceed with bulk action for ' + checked.length + ' gallery(s)?',
      icon: (action === 'force_delete' || action === 'delete') ? 'warning' : 'question',
      confirmButtonText: 'Yes, Proceed'
    }, function() {
      form.submit();
    });
  }

  function confirmTrashGallery(id, title) {
    showConfirmDialog({
      title: 'Move Gallery to Trash?',
      text: 'Move gallery "' + (title || 'this gallery') + '" and its assets to Trash?',
      icon: 'warning',
      confirmButtonText: 'Yes, Move to Trash'
    }, function() {
      const dynamicForm = document.createElement('form');
      dynamicForm.method = 'POST';
      dynamicForm.action = '{{ url('admin/galleries') }}/' + id;
      dynamicForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
      document.body.appendChild(dynamicForm);
      dynamicForm.submit();
    });
  }

  function confirmForceDeleteGallery(id, title) {
    showConfirmDialog({
      title: 'Permanently Purge Gallery?',
      text: 'Permanently purge gallery "' + (title || 'this gallery') + '" and all attached photos? This action cannot be recovered.',
      icon: 'error',
      confirmButtonText: 'Delete Permanently'
    }, function() {
      const dynamicForm = document.createElement('form');
      dynamicForm.method = 'POST';
      dynamicForm.action = '{{ url('admin/galleries') }}/' + id + '/force-delete';
      dynamicForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
      document.body.appendChild(dynamicForm);
      dynamicForm.submit();
    });
  }
</script>
@endpush

@endsection
