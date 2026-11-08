@extends('layouts.admin')

@section('title', 'System Activity Logs & Audit Trail - XivigAdmin PRO')

@section('content')
<div class="page-activity-logs">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">System Activity Log &amp; Audit Trail</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item">Administration</span>
        <span class="breadcrumb-item active">Activity Log</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.activity-logs.export') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-2">
        <i class="bi bi-download"></i>
        <span>Export CSV</span>
      </a>
      <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
        <i class="bi bi-trash3"></i>
        <span>Clear Logs</span>
      </button>
    </div>
  </div>

  <!-- Metric Badges -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm p-3 rounded-4">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-primary-subtle text-primary rounded-3 p-3">
            <i class="bi bi-journal-text fs-4"></i>
          </div>
          <div>
            <div class="fs-5 fw-bold mb-0">{{ number_format($metrics['total']) }}</div>
            <small class="text-muted">Total Events Logged</small>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm p-3 rounded-4">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-success-subtle text-success rounded-3 p-3">
            <i class="bi bi-lightning-charge fs-4"></i>
          </div>
          <div>
            <div class="fs-5 fw-bold mb-0">{{ number_format($metrics['today']) }}</div>
            <small class="text-muted">Today's Operations</small>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm p-3 rounded-4">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-info-subtle text-info rounded-3 p-3">
            <i class="bi bi-person-badge fs-4"></i>
          </div>
          <div>
            <div class="fs-5 fw-bold mb-0">{{ number_format($metrics['operators']) }}</div>
            <small class="text-muted">Active Operators</small>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm p-3 rounded-4">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-3">
            <i class="bi bi-shield-check fs-4"></i>
          </div>
          <div>
            <div class="fs-5 fw-bold mb-0">{{ number_format($metrics['security']) }}</div>
            <small class="text-muted">Security &amp; System</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters & Search Toolbar -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
      <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="row g-2 align-items-center">
        <!-- Search -->
        <div class="col-md-5">
          <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0" placeholder="Search by description, operator, or IP..." value="{{ request('search') }}">
          </div>
        </div>

        <!-- Module Filter -->
        <div class="col-md-3">
          <select name="module" class="form-select" onchange="this.form.submit();">
            <option value="all">All Modules (All)</option>
            @foreach (['Publications', 'Taxonomies', 'Comments', 'Users', 'Roles', 'Media', 'Galleries', 'Settings', 'System'] as $m)
              <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
          </select>
        </div>

        <!-- Action Filter -->
        <div class="col-md-2">
          <select name="action" class="form-select" onchange="this.form.submit();">
            <option value="all">All Actions</option>
            <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Created</option>
            <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Updated</option>
            <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Deleted</option>
            <option value="restore" {{ request('action') === 'restore' ? 'selected' : '' }}>Restored</option>
            <option value="backup" {{ request('action') === 'backup' ? 'selected' : '' }}>Backup</option>
            <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
            <option value="reassign" {{ request('action') === 'reassign' ? 'selected' : '' }}>Reassigned</option>
          </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary rounded-3 w-100">Filter</button>
          @if (request()->hasAny(['search', 'module', 'action']))
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset Filters">
              <i class="bi bi-x-lg"></i>
            </a>
          @endif
        </div>
      </form>
    </div>
  </div>

  <!-- Activity Logs Table -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light-subtle text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.8px;">
          <tr>
            <th class="ps-4 py-3" style="width: 220px;">Operator</th>
            <th class="py-3" style="width: 130px;">Action</th>
            <th class="py-3" style="width: 140px;">Module</th>
            <th class="py-3">Event Description</th>
            <th class="py-3" style="width: 140px;">IP Address</th>
            <th class="py-3 pe-4 text-end" style="width: 180px;">Time</th>
          </tr>
        </thead>
        <tbody class="border-top-0">
          @forelse ($logs as $log)
            @php
              $actionBadge = match ($log->action) {
                'create' => 'bg-success-subtle text-success border border-success-subtle',
                'update' => 'bg-info-subtle text-info border border-info-subtle',
                'delete' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'restore' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                'backup' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'login' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                'reassign' => 'bg-purple-subtle text-purple border border-purple-subtle',
                default => 'bg-light text-dark border',
              };
            @endphp
            <tr>
              <!-- Operator -->
              <td class="ps-4 py-3">
                @if ($log->user)
                  <div class="d-flex align-items-center gap-2">
                    @if ($log->user->profile_photo_path)
                      <img src="{{ asset($log->user->profile_photo_path) }}" alt="{{ $log->user->name }}" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                    @else
                      <div class="avatar-initials bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                      </div>
                    @endif
                    <div>
                      <a href="{{ route('admin.users.show', $log->user->id) }}" class="fw-semibold text-body text-decoration-none small d-block text-truncate" style="max-width: 130px;">
                        {{ $log->user->name }}
                      </a>
                      <span class="text-muted small" style="font-size: 11px;">#{{ $log->user->id }}</span>
                    </div>
                  </div>
                @else
                  <div class="d-flex align-items-center gap-2">
                    <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                      <i class="bi bi-cpu"></i>
                    </div>
                    <div>
                      <span class="fw-semibold text-body small d-block">System Automation</span>
                      <span class="text-muted small" style="font-size: 11px;">Daemon Engine</span>
                    </div>
                  </div>
                @endif
              </td>

              <!-- Action Badge -->
              <td class="py-3">
                <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-semibold {{ $actionBadge }}" style="font-size: 10.5px; letter-spacing: 0.5px;">
                  {{ $log->action }}
                </span>
              </td>

              <!-- Module Badge -->
              <td class="py-3">
                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-medium" style="font-size: 11px;">
                  {{ $log->module }}
                </span>
              </td>

              <!-- Description -->
              <td class="py-3">
                <div class="fw-medium text-body small mb-0.5">{{ $log->description }}</div>
                @if ($log->properties)
                  <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none small text-muted" data-bs-toggle="modal" data-bs-target="#inspectLogModal-{{ $log->id }}" style="font-size: 11px;">
                    <i class="bi bi-braces me-0.5"></i> Inspect payload
                  </button>
                @endif
              </td>

              <!-- IP Address -->
              <td class="py-3 text-muted small font-monospace" style="font-size: 11.5px;">
                <i class="bi bi-globe2 me-1 text-muted"></i>{{ $log->ip_address ?: '127.0.0.1' }}
              </td>

              <!-- Time -->
              <td class="py-3 pe-4 text-end">
                <div class="text-muted small" title="{{ $log->created_at ? $log->created_at->toDateTimeString() : '' }}">
                  {{ $log->created_at ? $log->created_at->diffForHumans() : 'N/A' }}
                </div>
                <small class="text-muted font-monospace" style="font-size: 10px;">
                  {{ $log->created_at ? $log->created_at->format('H:i:s') : '' }}
                </small>
              </td>
            </tr>

            <!-- Inspect Payload Modal -->
            @if ($log->properties)
              <div class="modal fade" id="inspectLogModal-{{ $log->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-3">
                      <div>
                        <h6 class="modal-title fw-bold mb-0">Event Payload: Log #{{ $log->id }}</h6>
                        <small class="text-muted">{{ $log->module }} &bull; {{ $log->action }}</small>
                      </div>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                      <div class="p-3 bg-dark text-light rounded-3 font-monospace small" style="white-space: pre-wrap; font-size: 12px; max-height: 300px; overflow-y: auto;">
                        {{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <div class="py-4">
                  <i class="bi bi-clock-history fs-1 text-muted d-block mb-2"></i>
                  <h6 class="fw-bold mb-1">No activity log entries found</h6>
                  <p class="text-muted small mb-0">System operations, content edits, and auth transactions will appear here automatically.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($logs->hasPages())
      <div class="card-footer bg-transparent border-top p-3 d-flex justify-content-end">
        {{ $logs->links() }}
      </div>
    @endif
  </div>

</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-bottom py-3">
        <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <span>Clear Activity Logs</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.activity-logs.clear') }}" method="POST" id="clearLogsForm">
        @csrf
        <div class="modal-body p-4">
          <p class="text-muted mb-3">Choose the timeframe of activity log records you wish to purge:</p>
          <div class="form-check border rounded-3 p-3 mb-2">
            <input class="form-check-input" type="radio" name="period" id="p30" value="30" checked>
            <label class="form-check-label fw-semibold" for="p30">
              Clear logs older than 30 days
            </label>
            <small class="text-muted d-block">Retains recent month of audits while trimming historical load.</small>
          </div>
          <div class="form-check border rounded-3 p-3 mb-2">
            <input class="form-check-input" type="radio" name="period" id="p60" value="60">
            <label class="form-check-label fw-semibold" for="p60">
              Clear logs older than 60 days
            </label>
          </div>
          <div class="form-check border border-danger-subtle bg-danger-subtle rounded-3 p-3">
            <input class="form-check-input" type="radio" name="period" id="pall" value="all">
            <label class="form-check-label fw-semibold text-danger" for="pall">
              Purge all activity log records completely
            </label>
            <small class="text-danger-emphasis d-block">This action cannot be undone.</small>
          </div>
        </div>
        <div class="modal-footer border-top py-3">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger rounded-pill px-4" onclick="confirmClearLogs()">
            <i class="bi bi-trash3 me-1"></i> Purge Logs
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .bg-purple-subtle {
    background-color: rgba(147, 51, 234, 0.12) !important;
  }
  .text-purple {
    color: #9333ea !important;
  }
  .border-purple-subtle {
    border-color: rgba(147, 51, 234, 0.25) !important;
  }
</style>

@push('scripts')
<script>
  function confirmClearLogs() {
    const isAll = document.getElementById('pall').checked;
    showConfirmDialog({
      title: isAll ? 'Purge All Logs Completely?' : 'Purge Activity Logs?',
      text: isAll ? 'Are you sure you want to permanently wipe all system activity records? This action cannot be reversed.' : 'Are you sure you want to purge the selected activity records?',
      icon: 'error',
      confirmButtonText: 'Yes, Purge Now'
    }, function() {
      document.getElementById('clearLogsForm').submit();
    });
  }
</script>
@endpush

@endsection
