@extends('layouts.admin')

@section('title', 'File Manager - XivigAdmin PRO')

@section('content')
<div class="page-file-manager">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Media &amp; File Manager</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">File Manager</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
        <i class="bi bi-folder2-open me-1"></i> {{ $totalFiles }} Files ({{ $totalSizeFormatted }})
      </span>
      <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
        <i class="bi bi-cloud-arrow-up me-1"></i> Upload File
      </button>
    </div>
  </div>

  <div class="row g-4">

    <!-- Folders Sidebar -->
    <div class="col-lg-3">
      <div class="card mb-4">
        <div class="card-header py-3">
          <h6 class="card-title mb-0">Storage Directories</h6>
        </div>
        <div class="list-group list-group-flush">
          @foreach ($allowedFolders as $folderKey => $folderLabel)
            <a href="{{ route('admin.file-manager.index', ['folder' => $folderKey]) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between {{ $currentFolder === $folderKey ? 'active' : '' }}">
              <div class="d-flex align-items-center gap-2">
                <i class="bi {{ $folderKey === 'uploads/trash' ? 'bi-trash' : ($currentFolder === $folderKey ? 'bi-folder2-open' : 'bi-folder') }} fs-5"></i>
                <span class="fw-semibold">{{ $folderLabel }}</span>
              </div>
              <div class="d-flex align-items-center gap-1">
                @if ($folderKey === 'uploads/trash' && ($trashCount ?? 0) > 0)
                  <span class="badge {{ $currentFolder === $folderKey ? 'bg-white text-danger' : 'bg-danger-subtle text-danger' }} rounded-pill">{{ $trashCount }}</span>
                @endif
                <code class="small {{ $currentFolder === $folderKey ? 'text-white' : 'text-muted' }}" style="font-size: 11px;">/{{ $folderKey }}</code>
              </div>
            </a>
          @endforeach
        </div>
      </div>

      <div class="card p-3 bg-body-tertiary border-0">
        <h6 class="fw-bold mb-2 small text-uppercase text-muted" style="letter-spacing: 0.05em;">Storage Usage</h6>
        <div class="progress mb-2" style="height: 6px;">
          <div class="progress-bar bg-primary" role="progressbar" style="width: 28%;" aria-valuenow="28" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <small class="text-muted d-flex justify-content-between">
          <span>Active: {{ $totalSizeFormatted }}</span>
          <span>Quota: 500 MB</span>
        </small>
      </div>
    </div>

    <!-- Files Grid View -->
    <div class="col-lg-9">
      <div class="card">
        <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div class="d-flex align-items-center gap-2">
            <h5 class="card-title mb-0">{{ $allowedFolders[$currentFolder] ?? 'Files' }}</h5>
            <span class="badge bg-secondary-subtle text-secondary small">{{ count($files) }} items</span>
            @if ($currentFolder === 'uploads/trash')
              <span class="badge bg-danger-subtle text-danger small">Recycle Bin Mode</span>
            @endif
          </div>
          <div class="d-flex align-items-center gap-2">
            @if ($currentFolder === 'uploads/trash' && count($files) > 0)
              <form id="emptyBinForm" action="{{ route('admin.file-manager.empty-trash') }}" method="POST" class="d-inline">
                @csrf
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmEmptyBin()">
                  <i class="bi bi-trash3 me-1"></i> Empty Bin
                </button>
              </form>
            @endif
            <input type="text" id="fileFilterInput" class="form-control form-control-sm" placeholder="Filter files in directory..." style="max-width: 220px;">
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3" id="filesGrid">
            @forelse ($files as $file)
              <div class="col-xl-3 col-lg-4 col-md-6 file-card-item" data-filename="{{ strtolower($file['name']) }}">
                <div class="card h-100 border shadow-none hover-shadow position-relative overflow-hidden">
                  <div class="p-2 text-center bg-body-tertiary d-flex align-items-center justify-content-center" style="height: 130px;">
                    @if ($file['is_image'])
                      <img src="{{ asset($file['path']) }}" alt="{{ $file['name'] }}" class="img-fluid rounded" style="max-height: 120px; object-fit: contain;">
                    @else
                      <div class="text-secondary fs-1">
                        <i class="bi bi-file-earmark-text"></i>
                      </div>
                    @endif
                  </div>
                  <div class="card-body p-2.5">
                    <div class="fw-bold text-truncate small mb-1" title="{{ $file['name'] }}">
                      {{ $file['name'] }}
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 11px;">
                      <span>{{ $file['size'] }}</span>
                      <span class="text-uppercase">{{ $file['extension'] }}</span>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 10px;">{{ $file['modified'] }}</small>
                  </div>
                  <div class="card-footer p-2 bg-transparent border-top d-flex justify-content-between align-items-center">
                    @if ($currentFolder === 'uploads/trash')
                      <form action="{{ route('admin.file-manager.restore') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="path" value="{{ $file['path'] }}">
                        <button type="submit" class="btn btn-sm btn-outline-success py-0.5 px-2" title="Restore File to Original Location">
                          <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                        </button>
                      </form>
                      <form action="{{ route('admin.file-manager.force-delete') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="path" value="{{ $file['path'] }}">
                        <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2" title="Delete Permanently" onclick="confirmForceDeleteFile(this, '{{ addslashes($file['name']) }}')">
                          <i class="bi bi-x-octagon"></i>
                        </button>
                      </form>
                    @else
                      <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2" onclick="copyAssetLink('{{ asset($file['path']) }}')" title="Copy Link">
                        <i class="bi bi-link-45deg"></i>
                      </button>
                      <a href="{{ asset($file['path']) }}" download class="btn btn-sm btn-outline-primary py-0.5 px-2" title="Download">
                        <i class="bi bi-download"></i>
                      </a>
                      <button type="submit" form="dup-file-form-{{ $loop->index }}" class="btn btn-sm btn-outline-success py-0.5 px-2" title="Duplicate File">
                        <i class="bi bi-copy"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2" title="Move to Trash" onclick="confirmTrashFile(document.getElementById('del-file-form-{{ $loop->index }}'), '{{ addslashes($file['name']) }}')">
                        <i class="bi bi-trash"></i>
                      </button>
                    @endif
                  </div>

                  @if(empty($file['is_dir']))
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
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12 py-5 text-center text-muted">
                <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                @if ($currentFolder === 'uploads/trash')
                  The Recycle Bin is currently empty.
                @else
                  No files found in this directory. Click "Upload File" to add assets.
                @endif
              </div>
            @endforelse
          </div>
        </div>
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
          <h5 class="modal-title">Upload Asset to Storage</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Destination Folder</label>
            <select name="folder" class="form-select">
              @foreach ($allowedFolders as $folderKey => $folderLabel)
                <option value="{{ $folderKey }}" {{ $currentFolder === $folderKey ? 'selected' : '' }}>
                  {{ $folderLabel }} (/{{ $folderKey }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Select File</label>
            <input type="file" name="file" class="form-control" required>
            <small class="text-muted">Maximum file size: 20 MB. Supports JPG, PNG, WebP, GIF, SVG, PDF, ZIP.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Upload Now</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Filter files in real time
  const filterInput = document.getElementById('fileFilterInput');
  if (filterInput) {
    filterInput.addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      document.querySelectorAll('.file-card-item').forEach(card => {
        const name = card.getAttribute('data-filename');
        card.style.display = name.includes(q) ? '' : 'none';
      });
    });
  }

  function copyAssetLink(url) {
    if (!url) return;
    navigator.clipboard.writeText(url).then(() => {
      showToast('Asset link copied to clipboard!', 'success');
    }).catch(() => {
      prompt('Copy asset URL:', url);
    });
  }

  function confirmEmptyBin() {
    showConfirmDialog({
      title: 'Empty Recycle Bin?',
      text: 'PERMANENT DELETION: Are you sure you want to empty the entire Recycle Bin? All trashed files will be deleted forever.',
      icon: 'error',
      confirmButtonText: 'Yes, Empty Bin'
    }, function() {
      document.getElementById('emptyBinForm').submit();
    });
  }

  function confirmForceDeleteFile(btn, name) {
    const form = btn.closest('form');
    showConfirmDialog({
      title: 'Permanent Deletion',
      text: 'Are you sure you want to permanently delete "' + name + '"? This file cannot be recovered.',
      icon: 'error',
      confirmButtonText: 'Delete Permanently'
    }, function() {
      if (form) form.submit();
    });
  }

  function confirmTrashFile(btn, name) {
    const form = btn.closest('form');
    showConfirmDialog({
      title: 'Move to Recycle Bin?',
      text: 'Move file "' + name + '" to Recycle Bin?',
      icon: 'warning',
      confirmButtonText: 'Move to Bin'
    }, function() {
      if (form) form.submit();
    });
  }
</script>
@endpush
@endsection
