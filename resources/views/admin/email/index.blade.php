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
      <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#composeModal">
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
        <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#composeModal">
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
                <span class="email-nav-badge" id="inboxUnreadBadge">{{ $counts['unread'] }}</span>
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
            <span>Discussions</span>
          </li>
          <li class="email-label-item px-3 py-1.5 d-flex align-items-center gap-2 small">
            <span class="email-label-dot" style="background: var(--bs-warning, #ffc107);"></span>
            <span>Operations</span>
          </li>
        </ul>
      </div>

      <!-- Storage Widget -->
      <div class="email-storage mt-auto p-3">
        <div class="email-storage-info d-flex justify-content-between small text-muted mb-1">
          <span>Mailbox Usage</span>
          <span>{{ $emails->total() }} conversations</span>
        </div>
        <div class="progress" style="height: 5px;">
          <div class="progress-bar bg-primary" role="progressbar" style="width: 42%;" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>
    </div>

    <!-- Email Main Workspace -->
    <div class="email-main" id="emailMain">

      <!-- Email List View -->
      <div class="email-list-view" id="emailListView">
        <!-- Header Toolbar (Matching Template Exactly) -->
        <div class="email-list-header">
          <!-- Mobile Sidebar Toggle -->
          <button type="button" class="email-sidebar-toggle" id="emailSidebarToggle" aria-label="Open folders">
            <i class="bi bi-folder2-open"></i>
          </button>

          <!-- Action Buttons Group (Checkbox, Refresh, Archive/Restore, Trash, Dropdown) -->
          <div class="email-list-actions">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="selectAll" title="Select All Conversations">
            </div>

            <button type="button" class="email-action-btn" id="btnRefresh" title="Refresh" onclick="window.location.reload();">
              <i class="bi bi-arrow-clockwise"></i>
            </button>

            @if($folder === 'trash')
              <button type="button" class="email-action-btn text-success" id="btnRestore" title="Restore Selected to Inbox">
                <i class="bi bi-arrow-counterclockwise"></i>
              </button>
              <button type="button" class="email-action-btn text-danger" id="btnDelete" title="Permanently Delete Selected">
                <i class="bi bi-trash"></i>
              </button>
            @else
              <button type="button" class="email-action-btn" id="btnArchive" title="Archive / Move to Sent">
                <i class="bi bi-archive"></i>
              </button>
              <button type="button" class="email-action-btn" id="btnDelete" title="Move Selected to Trash">
                <i class="bi bi-trash"></i>
              </button>
            @endif

            <div class="dropdown">
              <button type="button" class="email-action-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                <i class="bi bi-three-dots-vertical"></i>
              </button>
              <ul class="dropdown-menu">
                @if($folder === 'trash')
                  <li>
                    <button type="button" class="dropdown-item" id="menuRestoreSelected">
                      <i class="bi bi-arrow-counterclockwise me-2"></i>Restore selected
                    </button>
                  </li>
                  <li>
                    <form action="{{ route('admin.email.empty-trash') }}" method="POST" onsubmit="return confirm('Empty entire trash? This permanently purges all deleted messages.');">
                      @csrf
                      <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-trash3 me-2"></i>Empty trash
                      </button>
                    </form>
                  </li>
                @else
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.email.mark-all-read') }}">
                      <i class="bi bi-check2-all me-2"></i>Mark all as read
                    </a>
                  </li>
                  <li>
                    <button type="button" class="dropdown-item" id="menuStarSelected">
                      <i class="bi bi-star me-2"></i>Star selected
                    </button>
                  </li>
                  <li>
                    <button type="button" class="dropdown-item" id="menuUnstarSelected">
                      <i class="bi bi-star-half me-2"></i>Unstar selected
                    </button>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <button type="button" class="dropdown-item text-danger" id="menuDeleteSelected">
                      <i class="bi bi-trash me-2"></i>Move selected to trash
                    </button>
                  </li>
                @endif
              </ul>
            </div>
          </div>

          <!-- Email Search -->
          <form action="{{ route('admin.email.index') }}" method="GET" class="email-search ms-auto me-3">
            <input type="hidden" name="folder" value="{{ $folder }}">
            @if(request('starred'))
              <input type="hidden" name="starred" value="1">
            @endif
            <i class="bi bi-search email-search-icon"></i>
            <input type="text" name="search" class="form-control" placeholder="Search emails..." value="{{ request('search') }}">
          </form>

          <!-- Pagination Controls -->
          <div class="email-list-pagination">
            <span class="email-pagination-info">{{ $emails->firstItem() ?? 0 }}-{{ $emails->lastItem() ?? 0 }} of {{ $emails->total() }}</span>
            @if($emails->onFirstPage())
              <button type="button" class="email-pagination-btn" disabled><i class="bi bi-chevron-left"></i></button>
            @else
              <a href="{{ $emails->previousPageUrl() }}" class="email-pagination-btn"><i class="bi bi-chevron-left"></i></a>
            @endif

            @if($emails->hasMorePages())
              <a href="{{ $emails->nextPageUrl() }}" class="email-pagination-btn"><i class="bi bi-chevron-right"></i></a>
            @else
              <button type="button" class="email-pagination-btn" disabled><i class="bi bi-chevron-right"></i></button>
            @endif
          </div>
        </div>

        <!-- Email Item List -->
        <div class="email-list">
          @forelse ($emails as $email)
            <div class="email-item {{ !$email->is_read ? 'unread' : '' }}" 
                 data-email-id="{{ $email->id }}">
              
              <!-- Checkbox (Multi-Selection) -->
              <div class="email-item-select">
                <input class="form-check-input email-item-checkbox" type="checkbox" value="{{ $email->id }}">
              </div>

              <!-- Star Toggle Button -->
              <button type="button" class="email-item-star {{ $email->is_starred ? 'starred' : '' }}" data-email-id="{{ $email->id }}" title="Toggle Star">
                <i class="bi {{ $email->is_starred ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
              </button>

              <!-- Sender Avatar -->
              <div class="email-item-avatar">
                <div class="avatar-circle rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 14px;">
                  {{ strtoupper(substr($email->sender_name ?: $email->sender_email, 0, 1)) }}
                </div>
              </div>

              <!-- Content Preview -->
              <div class="email-item-content">
                <div class="email-item-header">
                  <span class="email-item-sender fw-semibold">{{ $email->sender_name ?: $email->sender_email }}</span>
                  <span class="email-item-time text-muted small">{{ $email->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="email-item-subject fw-medium text-truncate">
                  {{ $email->subject }}
                  @if(!empty($email->attachments) && count($email->attachments) > 0)
                    <i class="bi bi-paperclip email-item-attachment text-muted ms-1" title="{{ count($email->attachments) }} Attachment(s)"></i>
                  @endif
                </div>
                <div class="email-item-preview text-muted small text-truncate">{{ Str::limit(strip_tags($email->body), 110) }}</div>
              </div>

              <!-- Hidden Raw Elements for Error-Free JS Payload Loading -->
              <div class="email-raw-sender-email d-none">{{ $email->sender_email }}</div>
              <div class="email-raw-recipient-email d-none">{{ $email->recipient_email }}</div>
              <div class="email-raw-cc-email d-none">{{ $email->cc_email ?? '' }}</div>
              <div class="email-raw-body d-none">{!! nl2br(e($email->body)) !!}</div>
              <div class="email-raw-attachments d-none">{{ json_encode($email->attachments ?? []) }}</div>

              <!-- Quick Action (Restore / Delete) -->
              <div class="email-item-actions">
                @if($folder === 'trash')
                  <button type="button" class="btn btn-sm text-success p-1 single-restore-btn" data-email-id="{{ $email->id }}" title="Restore to Inbox">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                  <button type="button" class="btn btn-sm text-danger p-1 single-delete-btn" data-email-id="{{ $email->id }}" title="Permanently Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                @else
                  <button type="button" class="btn btn-sm text-danger p-1 single-delete-btn" data-email-id="{{ $email->id }}" title="Move to Trash">
                    <i class="bi bi-trash"></i>
                  </button>
                @endif
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

      <!-- Email Detail View (Native Template Architecture) -->
      <div class="email-detail-view" id="emailDetailView" style="display: none;">
        <!-- Detail Header -->
        <div class="email-detail-header">
          <button type="button" class="email-action-btn email-back-btn" id="emailBackBtn" title="Back to list">
            <i class="bi bi-arrow-left"></i>
          </button>

          <div class="email-detail-actions">
            <button type="button" class="email-action-btn text-success d-none" id="detailRestoreBtn" title="Restore to inbox">
              <i class="bi bi-arrow-counterclockwise"></i>
            </button>

            <form id="detailDeleteForm" action="#" method="POST" class="d-inline" onsubmit="return confirm('{{ $folder === 'trash' ? 'Permanently delete this conversation?' : 'Move this conversation to trash?' }}');">
              @csrf
              @method('DELETE')
              <button type="submit" class="email-action-btn" title="{{ $folder === 'trash' ? 'Permanently Delete' : 'Move to Trash' }}">
                <i class="bi bi-trash"></i>
              </button>
            </form>

            <button type="button" class="email-action-btn" id="detailReplyBtn" title="Reply to message">
              <i class="bi bi-reply"></i>
            </button>
          </div>
        </div>

        <!-- Detail Content -->
        <div class="email-detail-content">
          <div class="email-detail-subject">
            <h4 id="detailSubject" class="mb-0">Select a message</h4>
            <div class="email-detail-labels">
              <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1" id="detailFolderBadge">{{ ucfirst($folder) }}</span>
            </div>
          </div>

          <!-- Email Message Body -->
          <div class="email-message">
            <div class="email-message-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
              <div class="email-message-sender d-flex align-items-center gap-3">
                <div class="avatar-circle rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-5" id="detailAvatar" style="width: 44px; height: 44px;">
                  M
                </div>
                <div class="email-message-sender-info">
                  <div class="email-message-sender-name fw-semibold">
                    <span id="detailSenderName">Sender Name</span>
                    <span class="email-message-sender-email text-muted small">&lt;<span id="detailSenderEmail">email@example.com</span>&gt;</span>
                  </div>
                  <div class="email-message-recipients text-muted small">
                    To: <span id="detailRecipientEmail">me</span>
                    <span id="detailCcWrapper" class="d-none"> | Cc: <span id="detailCcEmail"></span></span>
                  </div>
                </div>
              </div>
              <div class="email-message-meta text-muted small" id="detailDate">
                Jan 20, 2026, 10:30 AM
              </div>
            </div>

            <div class="email-message-body fs-6 lh-lg text-body mb-4" id="detailBody" style="white-space: pre-wrap; font-family: 'Plus Jakarta Sans', sans-serif;">
              Message content...
            </div>

            <!-- Email Attachments (Matching Template) -->
            <div class="email-attachments" id="detailAttachmentsSection" style="display: none;">
              <div class="email-attachments-header">
                <i class="bi bi-paperclip"></i>
                <span id="detailAttachmentsCount">0 Attachments</span>
              </div>
              <div class="email-attachments-list" id="detailAttachmentsList"></div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Hidden Form for Multi-Select Bulk Actions -->
  <form id="bulkEmailForm" action="{{ route('admin.email.bulk') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput" value="">
    <div id="bulkEmailInputs"></div>
  </form>

  <!-- Compose Email Modal (Matches resources/dist/apps-email.html & screenshot) -->
  <div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="composeModalLabel">New Message</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <form class="email-compose-form" id="emailComposeForm" action="{{ route('admin.email.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="is_draft" id="isDraftInput" value="0">

            <div class="mb-3">
              <label class="form-label">To</label>
              <input type="email" class="form-control" name="recipient_email" id="recipient_email" placeholder="Recipients" required>
            </div>

            <div class="row mb-3">
              <div class="col-6">
                <a href="#" class="text-muted small text-decoration-none" id="showCcBtn">Add Cc</a>
              </div>
              <div class="col-6 text-end">
                <a href="#" class="text-muted small text-decoration-none" id="showBccBtn">Add Bcc</a>
              </div>
            </div>

            <div class="mb-3 d-none" id="ccField">
              <label class="form-label">Cc</label>
              <input type="email" class="form-control" name="cc_email" id="cc_email" placeholder="Cc recipients">
            </div>

            <div class="mb-3 d-none" id="bccField">
              <label class="form-label">Bcc</label>
              <input type="email" class="form-control" name="bcc_email" id="bcc_email" placeholder="Bcc recipients">
            </div>

            <div class="mb-3">
              <label class="form-label">Subject</label>
              <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea class="form-control" name="body" id="body" rows="10" placeholder="Write your message here..." required></textarea>
            </div>

            <!-- Hidden File Inputs -->
            <input type="file" name="attachments[]" id="composeFileInput" multiple class="d-none">
            <input type="file" id="composeImageInput" accept="image/*" multiple class="d-none">

            <!-- Attachments List Container -->
            <div class="email-compose-attachments" id="composeAttachmentsContainer"></div>
          </form>
        </div>
        <div class="modal-footer justify-content-between">
          <div class="email-compose-tools">
            <button type="button" class="email-compose-tool" id="attachFileBtn" title="Attach file">
              <i class="bi bi-paperclip"></i>
            </button>
            <button type="button" class="email-compose-tool" id="insertLinkBtn" title="Insert link">
              <i class="bi bi-link-45deg"></i>
            </button>
            <button type="button" class="email-compose-tool" id="insertEmojiBtn" title="Insert emoji">
              <i class="bi bi-emoji-smile"></i>
            </button>
            <button type="button" class="email-compose-tool" id="insertImageBtn" title="Insert image">
              <i class="bi bi-image"></i>
            </button>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Discard</button>
            <button type="submit" form="emailComposeForm" class="btn btn-primary" id="btnSubmitSend">
              <i class="bi bi-send me-1"></i>Send
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // 1. Mobile Drawer Navigation
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

    // 2. Multi-Selection Checkbox Logic
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.email-item-checkbox');

    if (selectAllCheckbox) {
      selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(cb => {
          cb.checked = selectAllCheckbox.checked;
        });
      });
    }

    itemCheckboxes.forEach(cb => {
      cb.addEventListener('change', function(e) {
        e.stopPropagation();
        const total = itemCheckboxes.length;
        const checkedCount = document.querySelectorAll('.email-item-checkbox:checked').length;
        if (selectAllCheckbox) {
          selectAllCheckbox.checked = (checkedCount === total && total > 0);
          selectAllCheckbox.indeterminate = (checkedCount > 0 && checkedCount < total);
        }
      });
    });

    // Helper to get checked email IDs
    function getSelectedIds() {
      const selected = [];
      document.querySelectorAll('.email-item-checkbox:checked').forEach(cb => {
        selected.push(cb.value);
      });
      return selected;
    }

    // Helper to submit bulk form
    function submitBulkAction(actionName) {
      const ids = getSelectedIds();
      if (ids.length === 0) {
        alert('Please select at least one conversation first.');
        return;
      }

      const form = document.getElementById('bulkEmailForm');
      const actionInput = document.getElementById('bulkActionInput');
      const inputsContainer = document.getElementById('bulkEmailInputs');

      actionInput.value = actionName;
      inputsContainer.innerHTML = '';

      ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        inputsContainer.appendChild(input);
      });

      form.submit();
    }

    // 3. Toolbar Action Buttons
    const isTrashFolder = '{{ $folder }}' === 'trash';

    const btnArchive = document.getElementById('btnArchive');
    if (btnArchive) {
      btnArchive.addEventListener('click', () => submitBulkAction('archive'));
    }

    const btnRestore = document.getElementById('btnRestore');
    if (btnRestore) {
      btnRestore.addEventListener('click', () => submitBulkAction('restore'));
    }

    const btnDelete = document.getElementById('btnDelete');
    if (btnDelete) {
      btnDelete.addEventListener('click', () => {
        const confirmMsg = isTrashFolder
          ? 'Permanently delete selected conversation(s)? This action cannot be undone.'
          : 'Move selected conversation(s) to trash?';
        if (confirm(confirmMsg)) {
          submitBulkAction(isTrashFolder ? 'delete' : 'trash');
        }
      });
    }

    const menuDeleteSelected = document.getElementById('menuDeleteSelected');
    if (menuDeleteSelected) {
      menuDeleteSelected.addEventListener('click', () => {
        if (confirm('Move selected conversation(s) to trash?')) {
          submitBulkAction('trash');
        }
      });
    }

    const menuRestoreSelected = document.getElementById('menuRestoreSelected');
    if (menuRestoreSelected) {
      menuRestoreSelected.addEventListener('click', () => submitBulkAction('restore'));
    }

    const menuStarSelected = document.getElementById('menuStarSelected');
    if (menuStarSelected) {
      menuStarSelected.addEventListener('click', () => submitBulkAction('star'));
    }

    const menuUnstarSelected = document.getElementById('menuUnstarSelected');
    if (menuUnstarSelected) {
      menuUnstarSelected.addEventListener('click', () => submitBulkAction('unstar'));
    }

    // 4. Single Delete Button
    document.querySelectorAll('.single-delete-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const emailId = this.dataset.emailId;
        const confirmMsg = isTrashFolder 
          ? 'Permanently delete this conversation? This action cannot be undone.'
          : 'Move this conversation to trash?';
        if (confirm(confirmMsg)) {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = `/admin/email/${emailId}`;

          const token = document.createElement('input');
          token.type = 'hidden';
          token.name = '_token';
          token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          form.appendChild(token);

          const method = document.createElement('input');
          method.type = 'hidden';
          method.name = '_method';
          method.value = 'DELETE';
          form.appendChild(method);

          document.body.appendChild(form);
          form.submit();
        }
      });
    });

    // 5. Single Restore Button (for Trash folder)
    document.querySelectorAll('.single-restore-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const emailId = this.dataset.emailId;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/email/${emailId}/restore`;

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(token);

        document.body.appendChild(form);
        form.submit();
      });
    });

    // 6. Star Toggle Logic
    document.querySelectorAll('.email-item-star').forEach(starBtn => {
      starBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const emailId = this.dataset.emailId;
        const icon = this.querySelector('i');

        fetch(`/admin/email/${emailId}/star`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        }).then(() => {
          this.classList.toggle('starred');
          if (this.classList.contains('starred')) {
            icon.className = 'bi bi-star-fill text-warning';
          } else {
            icon.className = 'bi bi-star';
          }
        }).catch(err => console.error('Star toggle failed', err));
      });
    });

    // 7. Helper for Attachment Icons
    function getFileIcon(filename) {
      if (!filename) return 'bi-file-earmark';
      const ext = filename.split('.').pop().toLowerCase();
      if (['pdf'].includes(ext)) return 'bi-file-earmark-pdf';
      if (['doc', 'docx'].includes(ext)) return 'bi-file-earmark-word';
      if (['xls', 'xlsx', 'csv'].includes(ext)) return 'bi-file-earmark-spreadsheet';
      if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].includes(ext)) return 'bi-file-earmark-image';
      if (['zip', 'rar', 'tar', 'gz'].includes(ext)) return 'bi-file-earmark-zip';
      if (['mp3', 'wav', 'ogg'].includes(ext)) return 'bi-file-earmark-music';
      if (['mp4', 'mov', 'avi'].includes(ext)) return 'bi-file-earmark-play';
      if (['js', 'html', 'css', 'php', 'json'].includes(ext)) return 'bi-file-earmark-code';
      return 'bi-file-earmark';
    }

    // 8. Read Mail: Item Click -> Show Detail View
    const emailListView = document.getElementById('emailListView');
    const emailDetailView = document.getElementById('emailDetailView');
    const emailBackBtn = document.getElementById('emailBackBtn');

    document.querySelectorAll('.email-item').forEach(item => {
      item.addEventListener('click', function(e) {
        // Prevent opening if clicked inside checkbox, star, or actions
        if (e.target.closest('.email-item-select') || e.target.closest('.email-item-star') || e.target.closest('.email-item-actions')) {
          return;
        }

        const emailId = this.dataset.emailId;
        const subject = this.querySelector('.email-item-subject').textContent;
        const sender = this.querySelector('.email-item-sender').textContent;
        const time = this.querySelector('.email-item-time').textContent;
        const senderEmail = this.querySelector('.email-raw-sender-email').textContent;
        const recipientEmail = this.querySelector('.email-raw-recipient-email').textContent;
        const ccEmail = this.querySelector('.email-raw-cc-email') ? this.querySelector('.email-raw-cc-email').textContent : '';
        const bodyContent = this.querySelector('.email-raw-body').innerHTML;

        // Parse attachments
        let attachments = [];
        const rawAtt = this.querySelector('.email-raw-attachments');
        if (rawAtt && rawAtt.textContent.trim()) {
          try {
            attachments = JSON.parse(rawAtt.textContent);
          } catch (err) {
            attachments = [];
          }
        }

        // Populate detail view fields
        document.getElementById('detailSubject').textContent = subject;
        document.getElementById('detailSenderName').textContent = sender;
        document.getElementById('detailSenderEmail').textContent = senderEmail;
        document.getElementById('detailRecipientEmail').textContent = recipientEmail;
        document.getElementById('detailDate').textContent = time;
        document.getElementById('detailAvatar').textContent = sender.trim().charAt(0).toUpperCase();
        document.getElementById('detailBody').innerHTML = bodyContent;

        // Cc Email field
        const ccWrapper = document.getElementById('detailCcWrapper');
        const ccSpan = document.getElementById('detailCcEmail');
        if (ccEmail && ccEmail.trim()) {
          ccSpan.textContent = ccEmail;
          ccWrapper.classList.remove('d-none');
        } else {
          ccWrapper.classList.add('d-none');
        }

        // Render attachments in reading pane
        const detailAttachmentsSection = document.getElementById('detailAttachmentsSection');
        const detailAttachmentsCount = document.getElementById('detailAttachmentsCount');
        const detailAttachmentsList = document.getElementById('detailAttachmentsList');

        if (attachments && attachments.length > 0) {
          detailAttachmentsCount.textContent = `${attachments.length} Attachment${attachments.length > 1 ? 's' : ''}`;
          detailAttachmentsList.innerHTML = '';
          attachments.forEach(att => {
            const attItem = document.createElement('div');
            attItem.className = 'email-attachment-item';
            attItem.innerHTML = `
              <div class="email-attachment-icon">
                <i class="bi ${getFileIcon(att.name)}"></i>
              </div>
              <div class="email-attachment-info">
                <div class="email-attachment-name">${att.name}</div>
                <div class="email-attachment-size">${att.size || ''}</div>
              </div>
              <div class="email-attachment-actions">
                <a href="/${att.path}" target="_blank" class="email-attachment-btn" title="Preview">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="/${att.path}" download="${att.name}" class="email-attachment-btn" title="Download">
                  <i class="bi bi-download"></i>
                </a>
              </div>
            `;
            detailAttachmentsList.appendChild(attItem);
          });
          detailAttachmentsSection.style.display = 'block';
        } else {
          detailAttachmentsSection.style.display = 'none';
        }

        // Populate detail delete form action
        const detailDeleteForm = document.getElementById('detailDeleteForm');
        if (detailDeleteForm) {
          detailDeleteForm.action = `/admin/email/${emailId}`;
        }

        // Detail restore button for trash view
        const detailRestoreBtn = document.getElementById('detailRestoreBtn');
        if (detailRestoreBtn) {
          if (isTrashFolder) {
            detailRestoreBtn.classList.remove('d-none');
            detailRestoreBtn.onclick = function() {
              const form = document.createElement('form');
              form.method = 'POST';
              form.action = `/admin/email/${emailId}/restore`;
              const token = document.createElement('input');
              token.type = 'hidden';
              token.name = '_token';
              token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
              form.appendChild(token);
              document.body.appendChild(form);
              form.submit();
            };
          } else {
            detailRestoreBtn.classList.add('d-none');
          }
        }

        // Wire reply button to open modal with prefilled data
        const replyBtn = document.getElementById('detailReplyBtn');
        if (replyBtn) {
          replyBtn.onclick = function() {
            const composeModal = new bootstrap.Modal(document.getElementById('composeModal'));
            document.getElementById('recipient_email').value = senderEmail;
            document.getElementById('subject').value = subject.startsWith('Re: ') ? subject : `Re: ${subject}`;
            composeModal.show();
          };
        }

        // Switch view
        emailListView.style.display = 'none';
        emailDetailView.style.display = 'flex';

        // Mark as read visually
        this.classList.remove('unread');

        // Send AJAX request to mark as read in database
        fetch(`/admin/email/${emailId}/mark-read`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        }).then(res => res.json()).then(data => {
          const badge = document.getElementById('inboxUnreadBadge');
          if (badge && typeof data.unread_count !== 'undefined') {
            if (data.unread_count > 0) {
              badge.textContent = data.unread_count;
            } else {
              badge.remove();
            }
          }
        }).catch(err => console.log('Mark read failed', err));
      });
    });

    // Back button returns to list view
    if (emailBackBtn) {
      emailBackBtn.addEventListener('click', function() {
        emailDetailView.style.display = 'none';
        emailListView.style.display = 'flex';
      });
    }

    // 9. Compose Modal Logic & Attachments Management
    const showCcBtn = document.getElementById('showCcBtn');
    const showBccBtn = document.getElementById('showBccBtn');
    const ccField = document.getElementById('ccField');
    const bccField = document.getElementById('bccField');

    if (showCcBtn && ccField) {
      showCcBtn.addEventListener('click', function(e) {
        e.preventDefault();
        ccField.classList.remove('d-none');
        this.classList.add('d-none');
      });
    }

    if (showBccBtn && bccField) {
      showBccBtn.addEventListener('click', function(e) {
        e.preventDefault();
        bccField.classList.remove('d-none');
        this.classList.add('d-none');
      });
    }

    const attachFileBtn = document.getElementById('attachFileBtn');
    const insertImageBtn = document.getElementById('insertImageBtn');
    const composeFileInput = document.getElementById('composeFileInput');
    const composeImageInput = document.getElementById('composeImageInput');
    const composeAttachmentsContainer = document.getElementById('composeAttachmentsContainer');
    let dataTransfer = new DataTransfer();

    if (attachFileBtn && composeFileInput) {
      attachFileBtn.addEventListener('click', () => composeFileInput.click());
    }

    if (insertImageBtn && composeImageInput) {
      insertImageBtn.addEventListener('click', () => composeImageInput.click());
    }

    function addFilesToDataTransfer(fileList) {
      Array.from(fileList).forEach(file => {
        const exists = Array.from(dataTransfer.files).some(f => f.name === file.name && f.size === file.size);
        if (!exists) {
          dataTransfer.items.add(file);
        }
      });
      composeFileInput.files = dataTransfer.files;
      renderComposeAttachments();
    }

    if (composeFileInput) {
      composeFileInput.addEventListener('change', function() {
        addFilesToDataTransfer(this.files);
      });
    }

    if (composeImageInput) {
      composeImageInput.addEventListener('change', function() {
        addFilesToDataTransfer(this.files);
      });
    }

    function renderComposeAttachments() {
      if (!composeAttachmentsContainer) return;
      composeAttachmentsContainer.innerHTML = '';
      Array.from(dataTransfer.files).forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'email-compose-attachment';
        item.innerHTML = `
          <i class="bi ${getFileIcon(file.name)}"></i>
          <span>${file.name}</span>
          <button type="button" class="email-compose-attachment-remove" data-index="${index}" title="Remove file">
            <i class="bi bi-x"></i>
          </button>
        `;
        composeAttachmentsContainer.appendChild(item);
      });
    }

    if (composeAttachmentsContainer) {
      composeAttachmentsContainer.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.email-compose-attachment-remove');
        if (removeBtn) {
          const removeIdx = parseInt(removeBtn.dataset.index);
          const newDt = new DataTransfer();
          Array.from(dataTransfer.files).forEach((file, idx) => {
            if (idx !== removeIdx) {
              newDt.items.add(file);
            }
          });
          dataTransfer = newDt;
          composeFileInput.files = dataTransfer.files;
          renderComposeAttachments();
        }
      });
    }

    // Insert Link Tool
    const insertLinkBtn = document.getElementById('insertLinkBtn');
    const bodyTextarea = document.getElementById('body');
    if (insertLinkBtn && bodyTextarea) {
      insertLinkBtn.addEventListener('click', function() {
        const url = prompt('Enter link URL:', 'https://');
        if (url) {
          const selStart = bodyTextarea.selectionStart;
          const selEnd = bodyTextarea.selectionEnd;
          const selectedText = bodyTextarea.value.substring(selStart, selEnd) || 'link';
          const linkMarkdown = `[${selectedText}](${url})`;
          bodyTextarea.setRangeText(linkMarkdown, selStart, selEnd, 'end');
          bodyTextarea.focus();
        }
      });
    }

    // Insert Emoji Tool
    const insertEmojiBtn = document.getElementById('insertEmojiBtn');
    if (insertEmojiBtn && bodyTextarea) {
      insertEmojiBtn.addEventListener('click', function() {
        const emojis = ['😊', '👍', '🎉', '💼', '🚀', '✨', '💡', '📌'];
        const randomEmoji = emojis[Math.floor(Math.random() * emojis.length)];
        const selStart = bodyTextarea.selectionStart;
        const selEnd = bodyTextarea.selectionEnd;
        bodyTextarea.setRangeText(randomEmoji, selStart, selEnd, 'end');
        bodyTextarea.focus();
      });
    }
  });
</script>
@endpush

@endsection
