@extends('layouts.admin')

@section('title', 'System Backups & Disaster Recovery - XivigAdmin PRO')

@section('content')
<div class="page-backups">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">System Backups &amp; Disaster Recovery</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item">Administration</span>
        <span class="breadcrumb-item active">System Backups</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: #5551ff; border-color: #5551ff;" data-bs-toggle="modal" data-bs-target="#createBackupModal">
        <i class="bi bi-cloud-arrow-up-fill fs-5"></i>
        <span>Create New Backup</span>
      </button>
    </div>
  </div>

  <!-- Metric Badges -->
  <div class="row g-3 mb-4">
    <!-- Total Backups -->
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-primary-subtle text-primary rounded-3 p-3">
            <i class="bi bi-archive-fill fs-4"></i>
          </div>
          <div>
            <div class="fs-4 fw-bold mb-0 text-body">{{ $backups->count() }}</div>
            <small class="text-muted fw-semibold">Saved Archives</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Backup Size -->
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-info-subtle text-info rounded-3 p-3">
            <i class="bi bi-hdd-stack-fill fs-4"></i>
          </div>
          <div>
            <div class="fs-4 fw-bold mb-0 text-body">{{ $formattedTotalSize }}</div>
            <small class="text-muted fw-semibold">Storage Consumed</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Latest Backup -->
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-success-subtle text-success rounded-3 p-3">
            <i class="bi bi-shield-check fs-4"></i>
          </div>
          <div>
            <div class="fs-6 fw-bold mb-0 text-body">
              {{ $latestBackup ? $latestBackup->created_at->diffForHumans() : 'Never' }}
            </div>
            <small class="text-muted fw-semibold">Last Backup</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Disk Storage Telemetry -->
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-1">
          <span class="small fw-bold text-muted text-uppercase">Host Storage Free</span>
          <span class="badge bg-secondary-subtle text-secondary rounded-pill small">{{ $diskUsagePercent }}% used</span>
        </div>
        <div class="progress mb-2" style="height: 6px;">
          <div class="progress-bar {{ $diskUsagePercent > 85 ? 'bg-danger' : ($diskUsagePercent > 70 ? 'bg-warning' : 'bg-primary') }}" 
               role="progressbar" 
               style="width: {{ min(100, max(5, $diskUsagePercent)) }}%"></div>
        </div>
        <div class="small text-muted d-flex justify-content-between">
          <span>Free: <strong class="text-body">{{ $formattedDiskFree }}</strong></span>
          <span>Total: {{ $formattedDiskTotal }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Backups Table Card -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
      <div>
        <h5 class="fw-bold mb-1">Backup Archives &amp; Snapshots</h5>
        <p class="text-muted small mb-0">Snapshots contain complete SQL schemas, content records, and uploaded assets.</p>
      </div>
      <span class="badge bg-light text-muted border px-2.5 py-1.5 rounded-pill small">
        <i class="bi bi-hdd-network me-1"></i> storage/app/backups/
      </span>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-uppercase small text-muted" style="letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4 py-3">Archive Filename</th>
              <th class="py-3">Type</th>
              <th class="py-3">Size</th>
              <th class="py-3">Created</th>
              <th class="py-3">Status</th>
              <th class="py-3">Notes</th>
              <th class="text-end pe-4 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($backups as $b)
              <tr>
                <td class="ps-4">
                  <div class="d-flex align-items-center gap-2.5">
                    <div class="bg-body-secondary text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                      <i class="bi bi-file-earmark-zip fs-5"></i>
                    </div>
                    <div>
                      <div class="fw-bold text-body font-monospace small mb-0">{{ $b->filename }}</div>
                      <small class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $b->disk_path }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  @if ($b->type === 'full')
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1">Full System</span>
                  @elseif ($b->type === 'database')
                    <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1">Database Only</span>
                  @else
                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1">Media Only</span>
                  @endif
                </td>
                <td>
                  <span class="fw-semibold text-body small font-monospace">{{ $b->formatted_size }}</span>
                </td>
                <td>
                  <div>
                    <span class="small text-body fw-semibold d-block">{{ $b->created_at->format('M d, Y H:i:s') }}</span>
                    <small class="text-muted">{{ $b->created_at->diffForHumans() }}</small>
                  </div>
                </td>
                <td>
                  @if ($b->status === 'completed')
                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">
                      <i class="bi bi-check-circle-fill me-1"></i> Available
                    </span>
                  @elseif ($b->status === 'failed')
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">
                      <i class="bi bi-x-circle-fill me-1"></i> Failed
                    </span>
                  @else
                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2.5 py-1">
                      <i class="bi bi-hourglass-split me-1"></i> Pending
                    </span>
                  @endif
                </td>
                <td>
                  <span class="small text-muted">{{ $b->notes ?: '—' }}</span>
                </td>
                <td class="text-end pe-4">
                  <div class="d-inline-flex align-items-center gap-1.5">
                    <!-- Download -->
                    <a href="{{ route('admin.backups.download', $b->id) }}" 
                       class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5"
                       title="Download ZIP Archive">
                      <i class="bi bi-download"></i>
                      <span>Download</span>
                    </a>

                    <!-- Restore -->
                    <button type="button" 
                            class="btn btn-outline-warning btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5"
                            data-id="{{ $b->id }}"
                            data-filename="{{ $b->filename }}"
                            onclick="confirmRestoreBackup(this.dataset.id, this.dataset.filename)"
                            title="Restore Database and Files from Archive">
                      <i class="bi bi-arrow-counterclockwise"></i>
                      <span>Restore</span>
                    </button>

                    <!-- Delete -->
                    <button type="button" 
                            class="btn btn-outline-danger btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0"
                            style="width: 32px; height: 32px;"
                            data-id="{{ $b->id }}"
                            data-filename="{{ $b->filename }}"
                            onclick="confirmDeleteBackup(this.dataset.id, this.dataset.filename)"
                            title="Delete Archive">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <div class="py-4">
                    <div class="bg-body-secondary rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                      <i class="bi bi-archive fs-2 text-muted"></i>
                    </div>
                    <h5 class="fw-bold mb-1">No Backup Archives Generated Yet</h5>
                    <p class="text-muted small mb-3">Create automated snapshots of your database and media files to prevent data loss.</p>
                    <button type="button" class="btn btn-primary rounded-pill px-4" style="background-color: #5551ff; border-color: #5551ff;" data-bs-toggle="modal" data-bs-target="#createBackupModal">
                      <i class="bi bi-cloud-arrow-up-fill me-1"></i> Create First Backup
                    </button>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <form action="{{ route('admin.backups.store') }}" method="POST" id="createBackupForm">
        @csrf
        <div class="modal-header border-0 pb-0 pt-4 px-4">
          <div>
            <h5 class="modal-title fw-bold">Create System Snapshot</h5>
            <p class="text-muted small mb-0">Select snapshot scope and archive parameters.</p>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <!-- Backup Type Selection -->
          <div class="mb-3">
            <label class="form-label fw-semibold">Snapshot Scope <span class="text-danger">*</span></label>
            <div class="d-flex flex-column gap-2">
              <label class="form-check p-3 border rounded-3 cursor-pointer d-flex align-items-start gap-3 mb-0" style="cursor: pointer;">
                <input class="form-check-input mt-1" type="radio" name="type" value="full" checked>
                <div>
                  <div class="fw-bold text-body">Full System Snapshot <span class="badge bg-primary-subtle text-primary rounded-pill ms-1 small">Recommended</span></div>
                  <small class="text-muted">Includes full database tables, publication records, user accounts, and public/uploads media files.</small>
                </div>
              </label>

              <label class="form-check p-3 border rounded-3 cursor-pointer d-flex align-items-start gap-3 mb-0" style="cursor: pointer;">
                <input class="form-check-input mt-1" type="radio" name="type" value="database">
                <div>
                  <div class="fw-bold text-body">Database Snapshot Only</div>
                  <small class="text-muted">Captures all relational models, posts, categories, comments, and settings (fastest).</small>
                </div>
              </label>

              <label class="form-check p-3 border rounded-3 cursor-pointer d-flex align-items-start gap-3 mb-0" style="cursor: pointer;">
                <input class="form-check-input mt-1" type="radio" name="type" value="media">
                <div>
                  <div class="fw-bold text-body">Media &amp; Uploads Only</div>
                  <small class="text-muted">Archives all images, thumbnails, banners, and gallery assets inside public/uploads.</small>
                </div>
              </label>
            </div>
          </div>

          <!-- Notes / Description -->
          <div class="mb-2">
            <label for="backupNotes" class="form-label fw-semibold">Archive Notes (Optional)</label>
            <textarea name="notes" id="backupNotes" class="form-control rounded-3" rows="2" placeholder="e.g., Pre-deployment checkpoint or scheduled monthly backup..."></textarea>
          </div>
        </div>

        <div class="modal-footer border-0 pt-0 pb-4 px-4">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4" id="submitBackupBtn" style="background-color: #5551ff; border-color: #5551ff;">
            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Start Backup
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Add loading spinner on form submit
  const backupForm = document.getElementById('createBackupForm');
  if (backupForm) {
    backupForm.addEventListener('submit', function() {
      const btn = document.getElementById('submitBackupBtn');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Generating Snapshot...';
      }
    });
  }

  // Restore archive confirmation
  function confirmRestoreBackup(id, filename) {
    showConfirmDialog({
      title: 'Restore System Snapshot?',
      text: 'CRITICAL OPERATION: Restoring "' + (filename || 'this backup') + '" will overwrite current database records with the snapshot contents. A safety copy of your current database will be saved before restore begins. Do you wish to proceed?',
      icon: 'warning',
      confirmButtonText: 'Yes, Restore System'
    }, function() {
      const dynamicForm = document.createElement('form');
      dynamicForm.method = 'POST';
      dynamicForm.action = '{{ url('admin/backups') }}/' + id + '/restore';
      dynamicForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
      document.body.appendChild(dynamicForm);
      dynamicForm.submit();
    });
  }

  // Delete archive confirmation
  function confirmDeleteBackup(id, filename) {
    showConfirmDialog({
      title: 'Delete Backup Archive?',
      text: 'Are you sure you want to delete backup file "' + (filename || 'this backup') + '"? This file will be permanently deleted from disk storage.',
      icon: 'error',
      confirmButtonText: 'Delete Archive'
    }, function() {
      const dynamicForm = document.createElement('form');
      dynamicForm.method = 'POST';
      dynamicForm.action = '{{ url('admin/backups') }}/' + id;
      dynamicForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
      document.body.appendChild(dynamicForm);
      dynamicForm.submit();
    });
  }
</script>
@endpush

@endsection
