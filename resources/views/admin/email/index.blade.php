@extends('layouts.admin')

@section('title', 'Mail Center & Communications - XivigAdmin PRO')

@section('content')
<div class="page-email">

  <!-- Page Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Mail Center &amp; Communications</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Home</a>
        <a href="#" class="breadcrumb-item">Apps</a>
        <span class="breadcrumb-item active">Email</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#composeEmailModal">
        <i class="bi bi-pencil-square"></i>
        <span>Compose Message</span>
      </button>
    </div>
  </div>

  <!-- Email App Container -->
  <div class="email-container">
    <!-- Mobile Sidebar Overlay -->
    <div class="email-sidebar-overlay" id="emailSidebarOverlay"></div>

    <!-- Email Sidebar -->
    <div class="email-sidebar" id="emailSidebar">
      <!-- Mobile Close Button -->
      <button class="email-sidebar-close" id="emailSidebarClose" aria-label="Close sidebar">
        <i class="bi bi-x-lg"></i>
      </button>

      <div class="email-sidebar-header">
        <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#composeEmailModal">
          <i class="bi bi-pencil-square"></i>
          <span>Compose</span>
        </button>
      </div>

      <!-- Navigation Folders -->
      <nav class="email-nav">
        <ul class="email-nav-list list-unstyled mb-0">
          <li class="email-nav-item {{ ($folder === 'inbox' && !request('starred')) ? 'active' : '' }}">
            <a href="{{ route('admin.email.index', ['folder' => 'inbox']) }}" class="d-flex align-items-center justify-content-between text-decoration-none text-reset w-100">
              <span class="d-flex align-items-center gap-2">
                <i class="bi bi-inbox"></i>
                <span>Inbox</span>
              </span>
              @if ($counts['unread'] > 0)
                <span class="email-nav-badge">{{ $counts['unread'] }}</span>
              @endif
            </a>
          </li>

          <li class="email-nav-item {{ request('starred') ? 'active' : '' }}">
            <a href="{{ route('admin.email.index', ['starred' => '1']) }}" class="d-flex align-items-center justify-content-between text-decoration-none text-reset w-100">
              <span class="d-flex align-items-center gap-2">
                <i class="bi bi-star"></i>
                <span>Starred</span>
              </span>
              @if ($counts['starred'] > 0)
                <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $counts['starred'] }}</span>
              @endif
            </a>
          </li>

          <li class="email-nav-item {{ ($folder === 'sent' && !request('starred')) ? 'active' : '' }}">
            <a href="{{ route('admin.email.index', ['folder' => 'sent']) }}" class="d-flex align-items-center justify-content-between text-decoration-none text-reset w-100">
              <span class="d-flex align-items-center gap-2">
                <i class="bi bi-send"></i>
                <span>Sent</span>
              </span>
              @if ($counts['sent'] > 0)
                <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $counts['sent'] }}</span>
              @endif
            </a>
          </li>

          <li class="email-nav-item {{ ($folder === 'draft' && !request('starred')) ? 'active' : '' }}">
            <a href="{{ route('admin.email.index', ['folder' => 'draft']) }}" class="d-flex align-items-center justify-content-between text-decoration-none text-reset w-100">
              <span class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark"></i>
                <span>Drafts</span>
              </span>
              @if ($counts['draft'] > 0)
                <span class="email-nav-badge">{{ $counts['draft'] }}</span>
              @endif
            </a>
          </li>

          <li class="email-nav-item {{ ($folder === 'trash' && !request('starred')) ? 'active' : '' }}">
            <a href="{{ route('admin.email.index', ['folder' => 'trash']) }}" class="d-flex align-items-center justify-content-between text-decoration-none text-reset w-100">
              <span class="d-flex align-items-center gap-2">
                <i class="bi bi-trash"></i>
                <span>Trash</span>
              </span>
              @if ($counts['trash'] > 0)
                <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $counts['trash'] }}</span>
              @endif
            </a>
          </li>
        </ul>
      </nav>

      <!-- Labels Section -->
      <div class="email-labels mt-4">
        <div class="email-labels-header d-flex align-items-center justify-content-between px-3 py-2 text-muted small text-uppercase fw-semibold">
          <span>Categories</span>
        </div>
        <ul class="email-labels-list list-unstyled mb-0">
          <li class="email-label-item px-3 py-1.5 d-flex align-items-center gap-2 small">
            <span class="email-label-dot" style="background: var(--bs-success, #198754);"></span>
            <span>Editorial &amp; Content</span>
          </li>
          <li class="email-label-item px-3 py-1.5 d-flex align-items-center gap-2 small">
            <span class="email-label-dot" style="background: var(--bs-danger, #dc3545);"></span>
            <span>Security &amp; Alerts</span>
          </li>
          <li class="email-label-item px-3 py-1.5 d-flex align-items-center gap-2 small">
            <span class="email-label-dot" style="background: var(--bs-info, #0dcaf0);"></span>
            <span>Community Discussions</span>
          </li>
          <li class="email-label-item px-3 py-1.5 d-flex align-items-center gap-2 small">
            <span class="email-label-dot" style="background: var(--bs-warning, #ffc107);"></span>
            <span>Operations &amp; System</span>
          </li>
        </ul>
      </div>

      <!-- Storage Widget -->
      <div class="email-storage mt-auto p-3">
        <div class="email-storage-info d-flex justify-content-between small text-muted mb-1">
          <span>Mailbox Capacity</span>
          <span>{{ $emails->total() }} msgs</span>
        </div>
        <div class="progress" style="height: 5px;">
          <div class="progress-bar bg-primary" role="progressbar" style="width: 35%;" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>
    </div>

    <!-- Email Main Workspace -->
    <div class="email-main" id="emailMain">

      <!-- Email List View -->
      <div class="email-list-view" id="emailListView" style="{{ $viewEmail ? 'display: none;' : '' }}">
        <!-- Header Toolbar -->
        <div class="email-list-header">
          <!-- Mobile Sidebar Toggle -->
          <button class="email-sidebar-toggle" id="emailSidebarToggle" aria-label="Open folders">
            <i class="bi bi-folder2-open"></i>
          </button>

          <div class="email-list-actions">
            <div class="form-check mb-0">
              <input class="form-check-input" type="checkbox" id="selectAllEmails" title="Select All">
            </div>
            <a href="{{ request()->fullUrl() }}" class="email-action-btn" title="Refresh">
              <i class="bi bi-arrow-clockwise"></i>
            </a>
          </div>

          <!-- Email Search -->
          <form action="{{ route('admin.email.index') }}" method="GET" class="email-search ms-auto me-3">
            <input type="hidden" name="folder" value="{{ $folder }}">
            @if(request('starred'))
              <input type="hidden" name="starred" value="1">
            @endif
            <i class="bi bi-search email-search-icon"></i>
            <input type="text" name="search" class="form-control" placeholder="Search conversations..." value="{{ request('search') }}">
          </form>

          <div class="email-list-pagination">
            <span class="email-pagination-info">{{ $emails->firstItem() ?? 0 }}-{{ $emails->lastItem() ?? 0 }} of {{ $emails->total() }}</span>
            @if($emails->onFirstPage())
              <button class="email-pagination-btn" disabled><i class="bi bi-chevron-left"></i></button>
            @else
              <a href="{{ $emails->previousPageUrl() }}" class="email-pagination-btn"><i class="bi bi-chevron-left"></i></a>
            @endif

            @if($emails->hasMorePages())
              <a href="{{ $emails->nextPageUrl() }}" class="email-pagination-btn"><i class="bi bi-chevron-right"></i></a>
            @else
              <button class="email-pagination-btn" disabled><i class="bi bi-chevron-right"></i></button>
            @endif
          </div>
        </div>

        <!-- Email Item List -->
        <div class="email-list">
          @forelse ($emails as $email)
            <div class="email-item {{ !$email->is_read ? 'unread' : '' }}" 
                 data-email-id="{{ $email->id }}"
                 onclick="openEmailDetails('{{ $email->id }}', '{{ addslashes($email->subject) }}', '{{ addslashes($email->sender_name) }}', '{{ addslashes($email->sender_email) }}', '{{ addslashes($email->created_at->format('M d, Y h:i A')) }}', '{{ addslashes(nl2br(e($email->body))) }}')">
              
              <div class="email-item-select" onclick="event.stopPropagation();">
                <input class="form-check-input email-checkbox" type="checkbox" value="{{ $email->id }}">
              </div>

              <button type="button" class="email-item-star {{ $email->is_starred ? 'starred' : '' }}" onclick="toggleStar(event, '{{ $email->id }}', this)">
                <i class="bi {{ $email->is_starred ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
              </button>

              <div class="email-item-avatar">
                <div class="avatar-circle rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 14px;">
                  {{ strtoupper(substr($email->sender_name ?: $email->sender_email, 0, 1)) }}
                </div>
              </div>

              <div class="email-item-content">
                <div class="email-item-header">
                  <span class="email-item-sender fw-semibold">{{ $email->sender_name ?: $email->sender_email }}</span>
                  <span class="email-item-time text-muted small">{{ $email->created_at->diffForHumans() }}</span>
                </div>
                <div class="email-item-subject fw-medium text-truncate">{{ $email->subject }}</div>
                <div class="email-item-preview text-muted small text-truncate">{{ Str::limit(strip_tags($email->body), 110) }}</div>
              </div>

              <div class="email-item-actions" onclick="event.stopPropagation();">
                <form action="{{ route('admin.email.destroy', $email->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Move conversation to trash?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm text-danger p-1" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </div>
          @empty
            <div class="py-5 text-center text-muted">
              <i class="bi bi-inbox display-4 d-block mb-3 text-secondary"></i>
              <h5 class="fw-bold">No messages in {{ request('starred') ? 'Starred' : ucfirst($folder) }}</h5>
              <p class="small text-muted mb-0">Conversations sent or received in this folder will appear here.</p>
            </div>
          @endforelse
        </div>
      </div>

      <!-- Email Detail View -->
      <div class="email-detail-view" id="emailDetailView" style="{{ $viewEmail ? 'display: flex;' : 'display: none;' }}">
        <div class="email-detail-header d-flex align-items-center justify-content-between p-3 border-bottom">
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" id="emailBackBtn">
              <i class="bi bi-arrow-left me-1"></i> Back to List
            </button>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1" id="detailFolderBadge">Conversation</span>
          </div>

          <div class="d-flex align-items-center gap-2" id="detailActions">
            <!-- Delete action injected via JS -->
          </div>
        </div>

        <div class="email-detail-content p-4 flex-grow-1 overflow-auto">
          <div class="d-flex align-items-start justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
              <div class="avatar-circle rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-5" id="detailAvatar" style="width: 48px; height: 48px;">
                M
              </div>
              <div>
                <h4 class="mb-1 fw-bold text-body" id="detailSubject">Select a message</h4>
                <div class="small text-muted">
                  From: <strong class="text-body" id="detailSenderName">Sender</strong> 
                  &lt;<span id="detailSenderEmail">email@domain.com</span>&gt;
                </div>
              </div>
            </div>
            <div class="text-muted small" id="detailDate">
              Date
            </div>
          </div>

          <div class="email-body-text fs-6 lh-lg text-body mb-4" id="detailBody" style="white-space: pre-line;">
            Email body content...
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Compose Email Modal -->
  <div class="modal fade" id="composeEmailModal" tabindex="-1" aria-labelledby="composeEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg">
        <form action="{{ route('admin.email.store') }}" method="POST">
          @csrf
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="composeEmailModalLabel">
              <i class="bi bi-pencil-square me-2"></i>New Message Dispatch
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label for="recipient_email" class="form-label fw-semibold">To (Recipient Email)</label>
              <input type="email" class="form-control" id="recipient_email" name="recipient_email" placeholder="editorial@xivig.com" required>
            </div>
            <div class="mb-3">
              <label for="subject" class="form-label fw-semibold">Subject Line</label>
              <input type="text" class="form-control" id="subject" name="subject" placeholder="Editorial dispatch update..." required>
            </div>
            <div class="mb-3">
              <label for="body" class="form-label fw-semibold">Message Body</label>
              <textarea class="form-control font-monospace" id="body" name="body" rows="8" placeholder="Compose your communication..." required></textarea>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_draft" value="1" id="is_draft">
              <label class="form-check-label text-muted small" for="is_draft">
                Save as draft instead of sending immediately
              </label>
            </div>
          </div>
          <div class="modal-footer bg-light-subtle">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-send me-1"></i> Send Dispatch
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  // Mobile drawer toggle for Email Sidebar
  const emailSidebar = document.getElementById('emailSidebar');
  const emailSidebarOverlay = document.getElementById('emailSidebarOverlay');
  const emailSidebarToggle = document.getElementById('emailSidebarToggle');
  const emailSidebarClose = document.getElementById('emailSidebarClose');

  if (emailSidebarToggle && emailSidebar) {
    emailSidebarToggle.addEventListener('click', () => {
      emailSidebar.classList.add('open');
      if (emailSidebarOverlay) emailSidebarOverlay.classList.add('show');
    });
  }

  if (emailSidebarClose && emailSidebar) {
    emailSidebarClose.addEventListener('click', () => {
      emailSidebar.classList.remove('open');
      if (emailSidebarOverlay) emailSidebarOverlay.classList.remove('show');
    });
  }

  if (emailSidebarOverlay && emailSidebar) {
    emailSidebarOverlay.addEventListener('click', () => {
      emailSidebar.classList.remove('open');
      emailSidebarOverlay.classList.remove('show');
    });
  }

  // Toggle Email Star
  function toggleStar(event, emailId, btn) {
    event.stopPropagation();
    btn.classList.toggle('starred');
    const icon = btn.querySelector('i');

    fetch(`/admin/email/${emailId}/star`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    }).then(res => res.json()).then(data => {
      if (data.is_starred) {
        icon.className = 'bi bi-star-fill text-warning';
      } else {
        icon.className = 'bi bi-star';
      }
    }).catch(err => {
      console.error('Star toggle failed', err);
    });
  }

  // Detail View Display
  const emailListView = document.getElementById('emailListView');
  const emailDetailView = document.getElementById('emailDetailView');
  const emailBackBtn = document.getElementById('emailBackBtn');

  function openEmailDetails(id, subject, senderName, senderEmail, date, body) {
    document.getElementById('detailSubject').innerText = subject;
    document.getElementById('detailSenderName').innerText = senderName;
    document.getElementById('detailSenderEmail').innerText = senderEmail;
    document.getElementById('detailDate').innerText = date;
    document.getElementById('detailAvatar').innerText = (senderName || senderEmail).charAt(0).toUpperCase();
    document.getElementById('detailBody').innerHTML = body;

    // Inject delete form
    document.getElementById('detailActions').innerHTML = `
      <form action="/admin/email/${id}" method="POST" onsubmit="return confirm('Delete message?');">
        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
          <i class="bi bi-trash me-1"></i> Delete
        </button>
      </form>
    `;

    // Mark as read visually
    const item = document.querySelector(`[data-email-id="${id}"]`);
    if (item) item.classList.remove('unread');

    // Switch views
    emailListView.style.display = 'none';
    emailDetailView.style.display = 'flex';
  }

  if (emailBackBtn) {
    emailBackBtn.addEventListener('click', () => {
      emailDetailView.style.display = 'none';
      emailListView.style.display = 'flex';
    });
  }
</script>
@endpush

@endsection
