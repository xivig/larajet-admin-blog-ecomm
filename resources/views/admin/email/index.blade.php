@extends('layouts.admin')

@section('title', 'Inbox & Communications - XivigAdmin PRO')

@section('content')
<div class="page-email">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Communications &amp; Inbox</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">Email Dispatch</span>
      </nav>
    </div>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#composeEmailModal">
      <i class="bi bi-pencil-fill me-1"></i> Compose Email
    </button>
  </div>

  <div class="row g-4">

    <!-- Email Folders Sidebar -->
    <div class="col-lg-3">
      <div class="d-grid mb-3">
        <button type="button" class="btn btn-primary py-2.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#composeEmailModal">
          <i class="bi bi-plus-lg me-1"></i> New Message
        </button>
      </div>

      <div class="card mb-4">
        <div class="list-group list-group-flush">
          <a href="{{ route('admin.email.index', ['folder' => 'inbox']) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between {{ $folder === 'inbox' && !request('starred') ? 'active' : '' }}">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-inbox fs-5"></i>
              <span>Inbox</span>
            </div>
            @if ($counts['unread'] > 0)
              <span class="badge {{ $folder === 'inbox' && !request('starred') ? 'bg-white text-primary' : 'bg-primary' }} rounded-pill">{{ $counts['unread'] }}</span>
            @endif
          </a>

          <a href="{{ route('admin.email.index', ['starred' => '1']) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between {{ request('starred') ? 'active' : '' }}">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-star-fill text-warning fs-5"></i>
              <span>Starred</span>
            </div>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $counts['starred'] }}</span>
          </a>

          <a href="{{ route('admin.email.index', ['folder' => 'sent']) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between {{ $folder === 'sent' && !request('starred') ? 'active' : '' }}">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-send fs-5"></i>
              <span>Sent Dispatches</span>
            </div>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $counts['sent'] }}</span>
          </a>

          <a href="{{ route('admin.email.index', ['folder' => 'draft']) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between {{ $folder === 'draft' && !request('starred') ? 'active' : '' }}">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-file-earmark-text fs-5"></i>
              <span>Drafts</span>
            </div>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $counts['draft'] }}</span>
          </a>

          <a href="{{ route('admin.email.index', ['folder' => 'trash']) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between {{ $folder === 'trash' && !request('starred') ? 'active' : '' }}">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-trash3 fs-5"></i>
              <span>Trash</span>
            </div>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $counts['trash'] }}</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Email List Card -->
    <div class="col-lg-9">
      <div class="card">
        <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div class="d-flex align-items-center gap-2">
            <h5 class="card-title mb-0 text-capitalize">{{ request('starred') ? 'Starred Messages' : $folder }}</h5>
            <span class="badge bg-secondary-subtle text-secondary small">{{ $emails->total() }} conversations</span>
          </div>

          <form action="{{ route('admin.email.index') }}" method="GET" class="d-flex gap-2">
            <input type="hidden" name="folder" value="{{ $folder }}">
            @if(request('starred'))
              <input type="hidden" name="starred" value="1">
            @endif
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search emails..." value="{{ request('search') }}" style="max-width: 220px;">
            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
          </form>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <tbody>
                @forelse ($emails as $email)
                  <tr class="{{ !$email->is_read ? 'table-light fw-bold' : '' }}">
                    <td style="width: 40px;" class="text-center">
                      <form action="{{ route('admin.email.star', $email->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="{{ $email->is_starred ? 'Unstar' : 'Star' }}">
                          <i class="bi {{ $email->is_starred ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}"></i>
                        </button>
                      </form>
                    </td>

                    <td style="width: 200px;">
                      <a href="{{ route('admin.email.show', $email->id) }}" class="text-reset text-decoration-none d-block text-truncate" style="max-width: 190px;">
                        {{ $email->folder === 'sent' ? 'To: ' . $email->recipient_email : ($email->sender_name ?: $email->sender_email) }}
                      </a>
                    </td>

                    <td>
                      <a href="{{ route('admin.email.show', $email->id) }}" class="text-reset text-decoration-none d-block text-truncate" style="max-width: 450px;">
                        <span class="fw-semibold">{{ $email->subject }}</span>
                        <span class="text-muted fw-normal ms-1">— {{ Str::limit(strip_tags($email->body), 80) }}</span>
                      </a>
                    </td>

                    <td style="width: 110px;" class="text-end text-muted small">
                      {{ $email->created_at ? $email->created_at->format('M d') : '' }}
                    </td>

                    <td style="width: 90px;" class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewEmailModal-{{ $email->id }}" title="Read Email">
                          <i class="bi bi-envelope-open"></i>
                        </button>

                        @if ($email->folder !== 'trash')
                          <form action="{{ route('admin.email.move', $email->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="folder" value="trash">
                            <button type="submit" class="btn btn-outline-danger" title="Move to Trash">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        @else
                          <form action="{{ route('admin.email.destroy', $email->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-danger" title="Purge Permanently" onclick="confirmPurgeEmail(event, this)">
                              <i class="bi bi-x-circle"></i>
                            </button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                      <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                      No emails found in {{ $folder }}.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if ($emails->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
              {{ $emails->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>

</div>

<!-- Compose Email Modal -->
<div class="modal fade" id="composeEmailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('admin.email.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-pencil-square me-2 text-primary"></i> Compose Message</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Recipient Email <span class="text-danger">*</span></label>
            <input type="email" name="recipient_email" class="form-control" placeholder="user@company.com" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
            <input type="text" name="subject" class="form-control" placeholder="Brief subject line..." required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Message Body</label>
            <textarea name="body" class="form-control" rows="8" placeholder="Type your message here..." required></textarea>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button type="submit" name="is_draft" value="1" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark me-1"></i> Save Draft
          </button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-send-fill me-1"></i> Send Dispatch
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Email Modals -->
@foreach ($emails as $email)
  <div class="modal fade" id="viewEmailModal-{{ $email->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title fw-bold mb-1">{{ $email->subject }}</h5>
            <small class="text-muted">From: <strong>{{ $email->sender_name ?: $email->sender_email }}</strong> &lt;{{ $email->sender_email }}&gt;</small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom text-muted small">
            <span>To: {{ $email->recipient_email }}</span>
            <span>{{ $email->created_at ? $email->created_at->format('M d, Y h:i A') : '' }}</span>
          </div>
          <div class="p-3 bg-body-tertiary rounded" style="white-space: pre-wrap; line-height: 1.7;">{{ $email->body }}</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endforeach

@push('scripts')
<script>
  function confirmPurgeEmail(e, btn) {
    e.preventDefault();
    const form = btn.closest('form');
    if (window.showConfirmDialog) {
      window.showConfirmDialog({
        title: 'Purge Email Permanently?',
        text: 'This email will be removed permanently from your trash mailbox.',
        icon: 'error',
        confirmButtonText: 'Yes, purge email'
      }, function() {
        form.submit();
      });
    } else if (confirm('Purge email permanently?')) {
      form.submit();
    }
  }
</script>
@endpush

@endsection
