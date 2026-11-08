@extends('layouts.admin')

@section('title', 'File Manager - XivigAdmin PRO')

@section('content')
<div class="page-file-manager">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">File Manager</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="#" class="breadcrumb-item">Apps</a>
        <span class="breadcrumb-item active">File Manager</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
        <i class="bi bi-folder2-open me-1"></i> {{ $totalFiles }} Files ({{ $totalSizeFormatted }})
      </span>
      <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
        <i class="bi bi-cloud-arrow-up"></i>
        <span>Upload Files</span>
      </button>
    </div>
  </div>

  <!-- File Manager Main Layout -->
  <div class="file-manager">
    <!-- Mobile Sidebar Overlay -->
    <div class="fm-sidebar-overlay" id="fmSidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="fm-sidebar" id="fmSidebar">
      <!-- Mobile Close Button -->
      <button class="fm-sidebar-close" id="fmSidebarClose" aria-label="Close sidebar">
        <i class="bi bi-x-lg"></i>
      </button>

      <div class="fm-sidebar-header">
        <button class="fm-upload-btn w-100" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
          <i class="bi bi-cloud-upload"></i>
          <span>Upload Files</span>
        </button>
      </div>

      <nav class="fm-nav">
        <div class="fm-nav-title">Storage Directories</div>
        @foreach ($allowedFolders as $folderKey => $folderLabel)
          <a href="{{ route('admin.file-manager.index', ['folder' => $folderKey]) }}" class="fm-nav-item {{ $currentFolder === $folderKey ? 'active' : '' }}">
            <i class="bi {{ $folderKey === 'uploads/trash' ? 'bi-trash text-danger' : ($currentFolder === $folderKey ? 'bi-folder2-open text-primary' : 'bi-folder') }}"></i>
            <span>{{ $folderLabel }}</span>
            @if ($folderKey === 'uploads/trash' && ($trashCount ?? 0) > 0)
              <span class="badge bg-danger ms-auto rounded-pill">{{ $trashCount }}</span>
            @endif
          </a>
        @endforeach
      </nav>

      <div class="fm-storage mt-auto">
        <div class="fm-storage-header">
          <span class="fm-storage-label">Storage Usage</span>
          <span class="fm-storage-value">{{ $totalSizeFormatted }} / 500 MB</span>
        </div>
        <div class="progress" style="height: 6px;">
          <div class="progress-bar bg-primary" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <span class="fm-storage-detail">Active: {{ $totalFiles }} files in this directory</span>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="fm-main">
      <!-- FM Header -->
      <div class="fm-header">
        <!-- Mobile Sidebar Toggle -->
        <button class="fm-sidebar-toggle" id="fmSidebarToggle" aria-label="Open folders">
          <i class="bi bi-folder2"></i>
        </button>

        <div class="fm-breadcrumb">
          <a href="{{ route('admin.dashboard') }}"><i class="bi bi-house"></i></a>
          <i class="bi bi-chevron-right"></i>
          <span>Storage</span>
          <i class="bi bi-chevron-right"></i>
          <span class="fw-semibold">{{ $allowedFolders[$currentFolder] ?? 'Files' }}</span>
          <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $totalFiles }} items</span>
          @if ($currentFolder === 'uploads/trash')
            <span class="badge bg-danger-subtle text-danger ms-1">Recycle Bin</span>
          @endif
        </div>

        <div class="fm-actions">
          <div class="fm-search">
            <i class="bi bi-search fm-search-icon"></i>
            <input type="text" id="fileFilterInput" class="form-control" placeholder="Search files...">
          </div>

          <div class="fm-view-toggle">
            <button class="fm-view-btn active" id="btnGridView" data-view="grid" title="Grid View">
              <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button class="fm-view-btn" id="btnListView" data-view="list" title="List View">
              <i class="bi bi-list-ul"></i>
            </button>
          </div>

          @if ($currentFolder === 'uploads/trash' && $totalFiles > 0)
            <form id="emptyBinForm" action="{{ route('admin.file-manager.empty-trash') }}" method="POST" class="d-inline">
              @csrf
              <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" onclick="confirmEmptyBin()">
                <i class="bi bi-trash3 me-1"></i> Empty Bin
              </button>
            </form>
          @endif
        </div>
      </div>

      <!-- FM Content -->
      <div class="fm-content">
        <!-- Multi-Selection Bulk Toolbar -->
        <form id="bulkFileManagerForm" action="{{ route('admin.file-manager.bulk') }}" method="POST">
          @csrf
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
              <input type="checkbox" id="selectAllFmFiles" class="form-check-input mt-0" title="Select All Files">
              <label for="selectAllFmFiles" class="small fw-semibold text-muted mb-0" style="cursor: pointer;">Select All Files</label>
              <span class="badge bg-primary-subtle text-primary rounded-pill d-none" id="selectedCountBadge">0 selected</span>
            </div>

            <div class="d-flex align-items-center gap-2">
              <select name="action" class="form-select form-select-sm w-auto rounded-pill" required>
                <option value="">Bulk Actions</option>
                @if ($currentFolder === 'uploads/trash')
                  <option value="restore">Restore Selected</option>
                  <option value="force_delete">Permanently Delete</option>
                @else
                  <option value="delete">Move to Trash</option>
                @endif
              </select>
              <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="confirmBulkFmAction()">Apply</button>
            </div>
          </div>

          <!-- Grid View Container -->
          <div class="fm-grid active" id="filesGrid">
            @forelse ($files as $file)
              <div class="fm-item" data-filename="{{ strtolower($file['name']) }}" data-type="{{ $file['is_image'] ? 'image' : 'document' }}">
                <input type="checkbox" name="paths[]" value="{{ $file['path'] }}" class="form-check-input fm-item-checkbox fm-file-check">

                @if ($file['is_image'])
                  <img src="{{ asset($file['path']) }}" alt="{{ $file['name'] }}" class="fm-item-thumbnail" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                @else
                  <div class="fm-item-icon {{ $file['extension'] === 'pdf' ? 'pdf' : ($file['extension'] === 'zip' ? 'archive' : 'document') }}">
                    <i class="bi {{ $file['extension'] === 'pdf' ? 'bi-file-earmark-pdf' : ($file['extension'] === 'zip' ? 'bi-file-earmark-zip' : 'bi-file-earmark-text') }}"></i>
                  </div>
                @endif

                <div class="fm-item-name" title="{{ $file['name'] }}">{{ $file['name'] }}</div>
                <div class="fm-item-meta">{{ $file['size'] }} &bull; {{ strtoupper($file['extension']) }}</div>

                <!-- Hover actions toolbar -->
                <div class="mt-2 pt-2 border-top d-flex justify-content-center gap-1">
                  @if ($currentFolder === 'uploads/trash')
                    <button type="submit" form="restore-file-{{ $loop->index }}" class="btn btn-sm btn-outline-success py-0.5 px-2" title="Restore to original location">
                      <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2" title="Delete Permanently" onclick="confirmForceDeleteFile(document.getElementById('force-del-file-{{ $loop->index }}'), '{{ addslashes($file['name']) }}')">
                      <i class="bi bi-x-octagon"></i>
                    </button>
                  @else
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2" onclick="copyAssetLink('{{ asset($file['path']) }}')" title="Copy Link">
                      <i class="bi bi-link-45deg"></i>
                    </button>
                    <a href="{{ asset($file['path']) }}" download class="btn btn-sm btn-outline-primary py-0.5 px-2" title="Download">
                      <i class="bi bi-download"></i>
                    </a>
                    <button type="submit" form="dup-file-form-{{ $loop->index }}" class="btn btn-sm btn-outline-success py-0.5 px-2" title="Duplicate">
                      <i class="bi bi-copy"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2" title="Move to Trash" onclick="confirmTrashFile(document.getElementById('del-file-form-{{ $loop->index }}'), '{{ addslashes($file['name']) }}')">
                      <i class="bi bi-trash"></i>
                    </button>
                  @endif
                </div>
              </div>
            @empty
              <div class="py-5 text-center text-muted" style="grid-column: 1 / -1;">
                <i class="bi bi-folder-x display-4 d-block mb-2 text-secondary"></i>
                <h6 class="fw-bold">{{ $currentFolder === 'uploads/trash' ? 'The Recycle Bin is currently empty.' : 'No files found in this directory.' }}</h6>
                <small class="text-muted">{{ $currentFolder === 'uploads/trash' ? 'Files deleted from other folders will appear here.' : 'Click "Upload Files" above to add new files.' }}</small>
              </div>
            @endforelse
          </div>

          <!-- List View Container (Hidden by default, toggled via JavaScript) -->
          <div class="fm-list" id="filesList">
            <table class="fm-list-table">
              <thead>
                <tr>
                  <th style="width: 40px;" class="text-center"></th>
                  <th class="sortable">Name</th>
                  <th class="sortable" style="width: 120px;">Size</th>
                  <th class="sortable" style="width: 180px;">Modified</th>
                  <th style="width: 160px;" class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($files as $file)
                  <tr class="fm-list-row" data-filename="{{ strtolower($file['name']) }}">
                    <td class="text-center">
                      <input type="checkbox" name="paths[]" value="{{ $file['path'] }}" class="form-check-input fm-file-check">
                    </td>
                    <td>
                      <div class="fm-list-item">
                        @if ($file['is_image'])
                          <img src="{{ asset($file['path']) }}" alt="" class="rounded" style="width: 32px; height: 32px; object-fit: cover;">
                        @else
                          <i class="bi {{ $file['extension'] === 'pdf' ? 'bi-file-earmark-pdf text-danger' : ($file['extension'] === 'zip' ? 'bi-file-earmark-zip text-warning' : 'bi-file-earmark-text text-primary') }} fm-list-icon"></i>
                        @endif
                        <span class="fm-list-name text-truncate" style="max-width: 360px;" title="{{ $file['name'] }}">{{ $file['name'] }}</span>
                      </div>
                    </td>
                    <td class="text-muted small">{{ $file['size'] }}</td>
                    <td class="text-muted small">{{ $file['modified'] }}</td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        @if ($currentFolder === 'uploads/trash')
                          <button type="submit" form="restore-file-{{ $loop->index }}" class="btn btn-outline-success" title="Restore">
                            <i class="bi bi-arrow-counterclockwise"></i>
                          </button>
                          <button type="button" class="btn btn-outline-danger" title="Permanently Delete" onclick="confirmForceDeleteFile(document.getElementById('force-del-file-{{ $loop->index }}'), '{{ addslashes($file['name']) }}')">
                            <i class="bi bi-x-octagon"></i>
                          </button>
                        @else
                          <button type="button" class="btn btn-outline-secondary" onclick="copyAssetLink('{{ asset($file['path']) }}')" title="Copy Link">
                            <i class="bi bi-link-45deg"></i>
                          </button>
                          <a href="{{ asset($file['path']) }}" download class="btn btn-outline-primary" title="Download">
                            <i class="bi bi-download"></i>
                          </a>
                          <button type="submit" form="dup-file-form-{{ $loop->index }}" class="btn btn-outline-success" title="Duplicate">
                            <i class="bi bi-copy"></i>
                          </button>
                          <button type="button" class="btn btn-outline-danger" title="Move to Trash" onclick="confirmTrashFile(document.getElementById('del-file-form-{{ $loop->index }}'), '{{ addslashes($file['name']) }}')">
                            <i class="bi bi-trash"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                      No files found in this directory.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </form>

        <!-- Hidden Standalone Single-action Forms -->
        @foreach ($files as $file)
          @if ($currentFolder === 'uploads/trash')
            <form id="restore-file-{{ $loop->index }}" action="{{ route('admin.file-manager.restore') }}" method="POST" class="d-none">
              @csrf
              <input type="hidden" name="path" value="{{ $file['path'] }}">
            </form>
            <form id="force-del-file-{{ $loop->index }}" action="{{ route('admin.file-manager.force-delete') }}" method="POST" class="d-none">
              @csrf
              @method('DELETE')
              <input type="hidden" name="path" value="{{ $file['path'] }}">
            </form>
          @else
            <form id="dup-file-form-{{ $loop->index }}" action="{{ route('admin.file-manager.duplicate') }}" method="POST" class="d-none">
              @csrf
              <input type="hidden" name="path" value="{{ $file['path'] }}">
            </form>
            <form id="del-file-form-{{ $loop->index }}" action="{{ route('admin.file-manager.destroy') }}" method="POST" class="d-none">
              @csrf
              @method('DELETE')
              <input type="hidden" name="path" value="{{ $file['path'] }}">
            </form>
          @endif
        @endforeach
      </div>
    </div>
  </div>

</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.file-manager.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Upload File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Target Storage Directory</label>
            <select name="folder" class="form-select" required>
              @foreach ($allowedFolders as $folderKey => $folderLabel)
                @if ($folderKey !== 'uploads/trash')
                  <option value="{{ $folderKey }}" {{ $currentFolder === $folderKey ? 'selected' : '' }}>
                    {{ $folderLabel }} ({{ $folderKey }})
                  </option>
                @endif
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Select File</label>
            <input type="file" name="file" class="form-control" required>
            <small class="text-muted">Maximum allowed file size: 20 MB</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-cloud-arrow-up me-1"></i> Upload Now
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Copy link helper
  function copyAssetLink(url) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(() => {
        showSuccessAlert('Copied!', 'Direct asset link copied to clipboard.');
      });
    } else {
      const temp = document.createElement('input');
      temp.value = url;
      document.body.appendChild(temp);
      temp.select();
      document.execCommand('copy');
      document.body.removeChild(temp);
      showSuccessAlert('Copied!', 'Direct asset link copied to clipboard.');
    }
  }

  function showSuccessAlert(title, text) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        timer: 2000,
        showConfirmButton: false
      });
    } else {
      alert(text);
    }
  }

  // Trash single file
  function confirmTrashFile(form, name) {
    showConfirmDialog({
      title: 'Move File to Trash?',
      text: 'Move file "' + (name || 'this file') + '" to the Recycle Bin?',
      icon: 'warning',
      confirmButtonText: 'Yes, Move to Trash'
    }, function() {
      form.submit();
    });
  }

  // Force delete single file
  function confirmForceDeleteFile(form, name) {
    showConfirmDialog({
      title: 'Permanently Delete File?',
      text: 'Permanently delete file "' + (name || 'this file') + '"? This action is irreversible.',
      icon: 'error',
      confirmButtonText: 'Yes, Delete Permanently'
    }, function() {
      form.submit();
    });
  }

  // Empty Trash Bin
  function confirmEmptyBin() {
    showConfirmDialog({
      title: 'Empty Entire Recycle Bin?',
      text: 'All files currently in the Recycle Bin will be permanently purged from the server!',
      icon: 'error',
      confirmButtonText: 'Yes, Empty Bin'
    }, function() {
      document.getElementById('emptyBinForm').submit();
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    // 1. Grid vs List View Toggle
    const btnGridView = document.getElementById('btnGridView');
    const btnListView = document.getElementById('btnListView');
    const filesGrid = document.getElementById('filesGrid');
    const filesList = document.getElementById('filesList');

    if (btnGridView && btnListView && filesGrid && filesList) {
      btnGridView.addEventListener('click', function() {
        btnGridView.classList.add('active');
        btnListView.classList.remove('active');
        filesGrid.classList.add('active');
        filesList.classList.remove('active');
      });

      btnListView.addEventListener('click', function() {
        btnListView.classList.add('active');
        btnGridView.classList.remove('active');
        filesGrid.classList.remove('active');
        filesList.classList.add('active');
      });
    }

    // 2. Real-time Search Filter
    const filterInput = document.getElementById('fileFilterInput');
    if (filterInput) {
      filterInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('.fm-item').forEach(el => {
          const name = el.getAttribute('data-filename') || '';
          el.style.display = name.includes(query) ? '' : 'none';
        });
        document.querySelectorAll('.fm-list-row').forEach(el => {
          const name = el.getAttribute('data-filename') || '';
          el.style.display = name.includes(query) ? '' : 'none';
        });
      });
    }

    // 3. Multi-Selection Checkbox Logic
    const selectAllFmFiles = document.getElementById('selectAllFmFiles');
    const selectedBadge = document.getElementById('selectedCountBadge');

    function updateSelectedCount() {
      const checkedCount = document.querySelectorAll('.fm-file-check:checked').length;
      if (selectedBadge) {
        if (checkedCount > 0) {
          selectedBadge.textContent = checkedCount + ' selected';
          selectedBadge.classList.remove('d-none');
        } else {
          selectedBadge.classList.add('d-none');
        }
      }
      document.querySelectorAll('.fm-item').forEach(item => {
        const cb = item.querySelector('.fm-file-check');
        if (cb && cb.checked) {
          item.classList.add('selected');
        } else {
          item.classList.remove('selected');
        }
      });
    }

    if (selectAllFmFiles) {
      selectAllFmFiles.addEventListener('change', function() {
        document.querySelectorAll('.fm-file-check').forEach(cb => {
          cb.checked = selectAllFmFiles.checked;
        });
        updateSelectedCount();
      });
    }

    document.querySelectorAll('.fm-file-check').forEach(cb => {
      cb.addEventListener('change', updateSelectedCount);
    });

    // Mobile sidebar toggles
    const fmSidebarToggle = document.getElementById('fmSidebarToggle');
    const fmSidebar = document.getElementById('fmSidebar');
    const fmSidebarClose = document.getElementById('fmSidebarClose');
    const fmSidebarOverlay = document.getElementById('fmSidebarOverlay');

    if (fmSidebarToggle && fmSidebar && fmSidebarOverlay) {
      fmSidebarToggle.addEventListener('click', function() {
        fmSidebar.classList.add('show');
        fmSidebarOverlay.classList.add('show');
      });

      const closeSidebar = function() {
        fmSidebar.classList.remove('show');
        fmSidebarOverlay.classList.remove('show');
      };

      if (fmSidebarClose) fmSidebarClose.addEventListener('click', closeSidebar);
      fmSidebarOverlay.addEventListener('click', closeSidebar);
    }
  });

  // Bulk action confirmation
  function confirmBulkFmAction() {
    const form = document.getElementById('bulkFileManagerForm');
    const action = form.querySelector('select[name="action"]').value;
    const checked = form.querySelectorAll('.fm-file-check:checked');

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
        Swal.fire({ icon: 'warning', title: 'Selection Required', text: 'Please select at least one file.' });
      } else {
        alert('Please select at least one file.');
      }
      return;
    }

    const labels = {
      'delete': 'Move ' + checked.length + ' file(s) to trash?',
      'restore': 'Restore ' + checked.length + ' file(s) from trash?',
      'force_delete': 'Permanently delete ' + checked.length + ' file(s)? This cannot be undone!'
    };

    showConfirmDialog({
      title: 'Confirm Bulk Action',
      text: labels[action] || 'Proceed with bulk action for ' + checked.length + ' file(s)?',
      icon: (action === 'force_delete' || action === 'delete') ? 'warning' : 'question',
      confirmButtonText: 'Yes, Proceed'
    }, function() {
      form.submit();
    });
  }
</script>
@endpush

@endsection
