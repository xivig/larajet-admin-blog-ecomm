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

      <!-- Navigation Folders (Matching Template & Screenshot) -->
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

          <li class="email-nav-item {{ ($folder === 'spam' && !request('starred')) ? 'active' : '' }}">
            <a href="{{ route('admin.email.index', ['folder' => 'spam']) }}" class="d-flex align-items-center justify-content-between text-decoration-none text-reset w-100">
              <span class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-octagon"></i>
                <span>Spam</span>
              </span>
              @if ($counts['spam'] > 0)
                <span class="badge bg-warning-subtle text-warning rounded-pill">{{ $counts['spam'] }}</span>
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

      <!-- Labels Section (Matching Screenshot) -->
      <div class="email-labels mt-4">
        <div class="email-labels-header d-flex align-items-center justify-content-between px-3 py-2 text-muted small text-uppercase fw-semibold">
          <span>Labels</span>
          <button type="button" class="btn btn-link btn-sm p-0 text-muted" title="Manage Labels" onclick="window.SwalToast ? window.SwalToast.fire({icon:'info', title:'Label manager ready.'}) : null;">
            <i class="bi bi-plus"></i>
          </button>
        </div>
        <ul class="email-labels-list list-unstyled mb-0">
          <li class="email-label-item px-3 py-1.5 d-flex align-items-center gap-2 small">
            <span class="email-label-dot" style="background: #198754;"></span>
            <span>Work</span>
          </li>
          <li class="email-label-item px-3 py-1.5 d-flex align-items-center gap-2 small">
            <span class="email-label-dot" style="background: #dc3545;"></span>
            <span>Important</span>
          </li>
          <li class="email-label-item px-3 py-1.5 d-flex align-items-center gap-2 small">
            <span class="email-label-dot" style="background: #0dcaf0;"></span>
            <span>Personal</span>
          </li>
          <li class="email-label-item px-3 py-1.5 d-flex align-items-center gap-2 small">
            <span class="email-label-dot" style="background: #ffc107;"></span>
            <span>Finance</span>
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
                    <form id="formEmptyTrash" action="{{ route('admin.email.empty-trash') }}" method="POST">
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
              <div class="email-raw-starred d-none">{{ $email->is_starred ? '1' : '0' }}</div>
              <div class="email-raw-folder d-none">{{ $email->folder }}</div>
              <div class="email-raw-body d-none">{!! nl2br(e($email->body)) !!}</div>
              <div class="email-raw-attachments d-none">{{ json_encode($email->attachments ?? []) }}</div>

              <!-- Segmented Action Buttons (Copy/Restore, Quick Reply, Delete) -->
              <div class="email-item-actions ms-auto flex-shrink-0">
                <div class="btn-group btn-group-sm" role="group" aria-label="Conversation Actions">
                  @if($folder === 'trash')
                    <button type="button" class="btn btn-outline-success single-restore-btn" data-email-id="{{ $email->id }}" title="Restore to Inbox">
                      <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                  @else
                    <button type="button" class="btn btn-outline-success single-copy-btn" data-email-id="{{ $email->id }}" title="Copy conversation details">
                      <i class="bi bi-copy"></i>
                    </button>
                  @endif

                  <button type="button" class="btn btn-outline-primary single-quick-reply-btn" data-email-id="{{ $email->id }}" title="Quick Reply">
                    <i class="bi bi-pencil"></i>
                  </button>

                  <button type="button" class="btn btn-outline-danger single-delete-btn" data-email-id="{{ $email->id }}" title="{{ $folder === 'trash' ? 'Permanently Delete' : 'Move to Trash' }}">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
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

      <!-- Email Detail View (Pixel-Matched to resources/dist/apps-email.html & Screenshot) -->
      <div class="email-detail-view" id="emailDetailView" style="display: none;">
        <!-- Email Detail Header -->
        <div class="email-detail-header">
          <button type="button" class="email-back-btn" id="emailBackBtn" title="Back to list">
            <i class="bi bi-arrow-left"></i>
          </button>

          <div class="email-detail-actions">
            <!-- Archive -->
            <button type="button" class="email-action-btn" id="detailArchiveBtn" title="Archive">
              <i class="bi bi-archive"></i>
            </button>

            <!-- Delete with SweetAlert -->
            <button type="button" class="email-action-btn" id="detailDeleteBtn" title="Delete">
              <i class="bi bi-trash"></i>
            </button>

            <!-- Mark as unread -->
            <button type="button" class="email-action-btn" id="detailMarkUnreadBtn" title="Mark as unread">
              <i class="bi bi-envelope"></i>
            </button>

            <!-- Move to folder -->
            <div class="dropdown d-inline-block">
              <button type="button" class="email-action-btn" data-bs-toggle="dropdown" title="Move to folder">
                <i class="bi bi-folder"></i>
              </button>
              <ul class="dropdown-menu">
                <li><h6 class="dropdown-header">Move to</h6></li>
                <li><button type="button" class="dropdown-item detail-move-folder" data-folder="inbox"><i class="bi bi-inbox me-2"></i>Inbox</button></li>
                <li><button type="button" class="dropdown-item detail-move-folder" data-folder="sent"><i class="bi bi-send me-2"></i>Sent</button></li>
                <li><button type="button" class="dropdown-item detail-move-folder" data-folder="draft"><i class="bi bi-file-earmark me-2"></i>Drafts</button></li>
                <li><button type="button" class="dropdown-item detail-move-folder" data-folder="spam"><i class="bi bi-exclamation-octagon me-2"></i>Spam</button></li>
                <li><hr class="dropdown-divider"></li>
                <li><button type="button" class="dropdown-item detail-move-folder text-danger" data-folder="trash"><i class="bi bi-trash me-2"></i>Trash</button></li>
              </ul>
            </div>

            <!-- Labels -->
            <div class="dropdown d-inline-block">
              <button type="button" class="email-action-btn" data-bs-toggle="dropdown" title="Labels">
                <i class="bi bi-tag"></i>
              </button>
              <ul class="dropdown-menu">
                <li><h6 class="dropdown-header">Apply label</h6></li>
                <li><button type="button" class="dropdown-item detail-set-label" data-label="Work" data-color="#198754"><span class="badge rounded-circle p-1 me-2" style="background:#198754;"> </span>Work</button></li>
                <li><button type="button" class="dropdown-item detail-set-label" data-label="Important" data-color="#dc3545"><span class="badge rounded-circle p-1 me-2" style="background:#dc3545;"> </span>Important</button></li>
                <li><button type="button" class="dropdown-item detail-set-label" data-label="Personal" data-color="#0dcaf0"><span class="badge rounded-circle p-1 me-2" style="background:#0dcaf0;"> </span>Personal</button></li>
                <li><button type="button" class="dropdown-item detail-set-label" data-label="Finance" data-color="#ffc107"><span class="badge rounded-circle p-1 me-2" style="background:#ffc107;"> </span>Finance</button></li>
              </ul>
            </div>

            <!-- More Actions Dropdown (Print, Forward, Report spam, Block sender) -->
            <div class="dropdown d-inline-block">
              <button type="button" class="email-action-btn" data-bs-toggle="dropdown" title="More options">
                <i class="bi bi-three-dots-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><button type="button" class="dropdown-item" id="detailPrintBtn"><i class="bi bi-printer me-2"></i>Print</button></li>
                <li><button type="button" class="dropdown-item" id="detailForwardBtn"><i class="bi bi-forward me-2"></i>Forward</button></li>
                <li><button type="button" class="dropdown-item" id="detailReportSpamBtn"><i class="bi bi-flag me-2"></i>Report spam</button></li>
                <li><hr class="dropdown-divider"></li>
                <li><button type="button" class="dropdown-item text-danger" id="detailBlockSenderBtn"><i class="bi bi-shield-check me-2"></i>Block sender</button></li>
              </ul>
            </div>
          </div>

          <!-- Navigation Prev/Next -->
          <div class="email-detail-nav ms-auto">
            <button type="button" class="email-pagination-btn" id="detailPrevBtn" title="Previous message">
              <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" class="email-pagination-btn" id="detailNextBtn" title="Next message">
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>

        <!-- Email Detail Content -->
        <div class="email-detail-content">
          <div class="email-detail-subject d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h4 id="detailSubject" class="mb-0 fw-bold">Project Update</h4>
            <div class="email-detail-labels">
              <span class="email-item-label badge rounded-pill px-3 py-1.5 fw-medium" id="detailLabelBadge" style="background: #198754; color: #fff;">Work</span>
            </div>
          </div>

          <!-- Email Message Header & Metadata -->
          <div class="email-message">
            <div class="email-message-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
              <div class="email-message-sender d-flex align-items-center gap-3">
                <div class="avatar-circle rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-5" id="detailAvatar" style="width: 44px; height: 44px;">
                  S
                </div>
                <div class="email-message-sender-info">
                  <div class="email-message-sender-name fw-semibold">
                    <span id="detailSenderName">Sarah Wilson</span>
                    <span class="email-message-sender-email text-muted small">&lt;<span id="detailSenderEmail">sarah.wilson@example.com</span>&gt;</span>
                  </div>
                  <div class="email-message-recipients text-muted small">
                    to me, <span id="detailRecipientEmail">admin@xivig.com</span>
                    <span id="detailCcWrapper" class="d-none"> | Cc: <span id="detailCcEmail"></span></span>
                  </div>
                </div>
              </div>

              <!-- Message Action Metadata -->
              <div class="email-message-meta d-flex align-items-center gap-2">
                <span class="email-message-date text-muted small" id="detailDate">Jan 20, 2026, 10:30 AM</span>
                <button type="button" class="email-action-btn email-star-btn" id="detailStarBtn" title="Star message">
                  <i class="bi bi-star"></i>
                </button>
                <button type="button" class="email-action-btn" id="detailReplyBtn" title="Reply">
                  <i class="bi bi-reply"></i>
                </button>
                <div class="dropdown d-inline-block">
                  <button type="button" class="email-action-btn" data-bs-toggle="dropdown" title="More">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><button type="button" class="dropdown-item" id="metaReplyBtn"><i class="bi bi-reply me-2"></i>Reply</button></li>
                    <li><button type="button" class="dropdown-item" id="metaReplyAllBtn"><i class="bi bi-reply-all me-2"></i>Reply all</button></li>
                    <li><button type="button" class="dropdown-item" id="metaForwardBtn"><i class="bi bi-forward me-2"></i>Forward</button></li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Email Body Content -->
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

          <!-- Quick Reply (Matching Template) -->
          <div class="email-quick-reply mt-4">
            <div class="avatar-circle rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 13px;">
              {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="email-quick-reply-input flex-grow-1">
              <textarea id="quickReplyBody" placeholder="Click here to reply..." class="form-control" rows="2"></textarea>
            </div>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-1" id="btnQuickReplySend" title="Send Reply">
              <i class="bi bi-send"></i>
              <span class="d-none d-sm-inline">Reply</span>
            </button>
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

<style>
/* Segmented Action Button Group in Email Item Row (Matches Template & Screenshot) */
.email-item-actions {
  display: inline-flex;
  align-items: center;
  opacity: 0.9;
  transition: opacity 0.2s ease;
}
.email-item:hover .email-item-actions {
  opacity: 1;
}
.email-item-actions .btn-group {
  display: inline-flex;
  vertical-align: middle;
}
.email-item-actions .btn-group .btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 28px;
  padding: 0;
  font-size: 0.8125rem;
  background-color: transparent;
  transition: all 0.15s ease-in-out;
  box-shadow: none !important;
}
.email-item-actions .btn-group .btn-outline-success {
  border: 1px solid #10b981;
  color: #10b981;
  border-top-left-radius: 8px;
  border-bottom-left-radius: 8px;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}
.email-item-actions .btn-group .btn-outline-success:hover {
  background-color: rgba(16, 185, 129, 0.15);
  color: #059669;
  border-color: #10b981;
}
.email-item-actions .btn-group .btn-outline-primary {
  border: 1px solid #3b82f6;
  color: #3b82f6;
  border-radius: 0;
  margin-left: -1px;
}
.email-item-actions .btn-group .btn-outline-primary:hover {
  background-color: rgba(59, 130, 246, 0.15);
  color: #2563eb;
  border-color: #3b82f6;
}
.email-item-actions .btn-group .btn-outline-danger {
  border: 1px solid #ef4444;
  color: #ef4444;
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
  margin-left: -1px;
}
.email-item-actions .btn-group .btn-outline-danger:hover {
  background-color: rgba(239, 68, 68, 0.15);
  color: #dc2626;
  border-color: #ef4444;
}

@media print {
  body * {
    visibility: hidden;
  }
  #emailDetailView, #emailDetailView * {
    visibility: visible;
  }
  #emailDetailView {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    background: #fff;
  }
  .email-detail-header, .email-quick-reply, .email-detail-nav, .email-sidebar {
    display: none !important;
  }
}
</style>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    let currentActiveEmailId = null;
    const isTrashFolder = '{{ $folder }}' === 'trash';

    // Helper to safely trigger SweetAlert2 Confirm or fallback
    function confirmDialog(title, text, confirmBtnText, onConfirmed) {
      if (typeof window.showConfirmDialog === 'function') {
        window.showConfirmDialog({
          title: title,
          text: text,
          icon: 'warning',
          confirmButtonText: confirmBtnText || 'Yes, Proceed',
          confirmButtonClass: 'btn btn-danger px-4 rounded-pill',
          cancelButtonText: 'Cancel'
        }, onConfirmed);
      } else if (window.Swal) {
        window.Swal.fire({
          title: title,
          text: text,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: confirmBtnText || 'Yes, Proceed',
          customClass: {
            confirmButton: 'btn btn-danger px-4 rounded-pill',
            cancelButton: 'btn btn-outline-secondary px-4 rounded-pill me-2'
          },
          buttonsStyling: false
        }).then(result => {
          if (result && result.isConfirmed) {
            onConfirmed();
          }
        });
      } else {
        if (confirm(`${title}\n${text}`)) {
          onConfirmed();
        }
      }
    }

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
        if (window.SwalToast) {
          window.SwalToast.fire({ icon: 'warning', title: 'Please select at least one conversation first.' });
        } else {
          alert('Please select at least one conversation first.');
        }
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
    const btnArchive = document.getElementById('btnArchive');
    if (btnArchive) {
      btnArchive.addEventListener('click', () => submitBulkAction('archive'));
    }

    const btnRestore = document.getElementById('btnRestore');
    if (btnRestore) {
      btnRestore.addEventListener('click', () => submitBulkAction('restore'));
    }

    // Toolbar Delete with SweetAlert2
    function triggerBulkDelete() {
      const ids = getSelectedIds();
      if (ids.length === 0) {
        if (window.SwalToast) {
          window.SwalToast.fire({ icon: 'warning', title: 'Please select at least one conversation first.' });
        } else {
          alert('Please select at least one conversation first.');
        }
        return;
      }

      const isTrash = isTrashFolder;
      confirmDialog(
        isTrash ? 'Permanently Delete Conversations?' : 'Move to Trash?',
        isTrash 
          ? `Permanently purge ${ids.length} selected conversation(s)? This action cannot be undone.` 
          : `Move ${ids.length} selected conversation(s) to trash?`,
        isTrash ? 'Yes, Delete Permanently' : 'Yes, Move to Trash',
        function() {
          submitBulkAction(isTrash ? 'delete' : 'trash');
        }
      );
    }

    const btnDelete = document.getElementById('btnDelete');
    if (btnDelete) {
      btnDelete.addEventListener('click', triggerBulkDelete);
    }

    const menuDeleteSelected = document.getElementById('menuDeleteSelected');
    if (menuDeleteSelected) {
      menuDeleteSelected.addEventListener('click', triggerBulkDelete);
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

    // Empty Trash confirmation with SweetAlert2
    const formEmptyTrash = document.getElementById('formEmptyTrash');
    if (formEmptyTrash) {
      formEmptyTrash.addEventListener('submit', function(e) {
        e.preventDefault();
        confirmDialog(
          'Empty Entire Trash?',
          'All messages in the trash folder will be permanently purged. This cannot be undone.',
          'Yes, Empty Trash',
          () => formEmptyTrash.submit()
        );
      });
    }

    // 4. Single Delete Button with SweetAlert2 (from Button Group)
    document.querySelectorAll('.single-delete-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const emailId = this.dataset.emailId;
        const isTrash = isTrashFolder;

        confirmDialog(
          isTrash ? 'Permanently Delete Conversation?' : 'Move to Trash?',
          isTrash 
            ? 'This message will be permanently purged from the database and cannot be recovered.' 
            : 'This message will be moved to your Trash folder.',
          isTrash ? 'Yes, Delete Permanently' : 'Yes, Move to Trash',
          function() {
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
        );
      });
    });

    // 5. Single Quick Reply Button (from Button Group - Blue Pencil)
    document.querySelectorAll('.single-quick-reply-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const row = this.closest('.email-item');
        if (!row) return;

        const sender = row.querySelector('.email-item-sender').textContent.trim();
        const senderEmail = row.querySelector('.email-raw-sender-email').textContent.trim();
        const subject = row.querySelector('.email-item-subject').textContent.trim();

        const composeModalEl = document.getElementById('composeModal');
        const composeModal = new bootstrap.Modal(composeModalEl);

        const modalTitle = document.getElementById('composeModalLabel');
        if (modalTitle) modalTitle.textContent = `Quick Reply to ${sender}`;

        document.getElementById('recipient_email').value = senderEmail;
        document.getElementById('subject').value = subject.startsWith('Re: ') ? subject : `Re: ${subject}`;

        const bodyArea = document.getElementById('body');
        if (bodyArea) {
          bodyArea.value = '';
          bodyArea.placeholder = `Write your quick reply to ${sender}...`;
        }

        composeModal.show();
        setTimeout(() => {
          if (bodyArea) bodyArea.focus();
        }, 350);
      });
    });

    // 6. Single Copy / Duplicate Button (from Button Group - Green Copy)
    document.querySelectorAll('.single-copy-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const row = this.closest('.email-item');
        if (!row) return;

        const subject = row.querySelector('.email-item-subject').textContent.trim();
        const sender = row.querySelector('.email-item-sender').textContent.trim();
        const senderEmail = row.querySelector('.email-raw-sender-email').textContent.trim();
        const body = row.querySelector('.email-raw-body').innerText.trim();

        const summary = `From: ${sender} <${senderEmail}>\nSubject: ${subject}\n\n${body}`;

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(summary).then(() => {
            if (window.SwalToast) {
              window.SwalToast.fire({ icon: 'success', title: 'Conversation copied to clipboard!' });
            }
          }).catch(() => fallbackCopy(summary));
        } else {
          fallbackCopy(summary);
        }

        function fallbackCopy(text) {
          const ta = document.createElement('textarea');
          ta.value = text;
          ta.style.position = 'fixed';
          ta.style.opacity = '0';
          document.body.appendChild(ta);
          ta.select();
          document.execCommand('copy');
          document.body.removeChild(ta);
          if (window.SwalToast) {
            window.SwalToast.fire({ icon: 'success', title: 'Conversation copied to clipboard!' });
          }
        }
      });
    });

    // 7. Single Restore Button (for Trash folder - Green Button)
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

    // 8. Star Toggle Logic
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
        }).then(res => res.json()).then(data => {
          const isStarred = data.is_starred;
          this.classList.toggle('starred', isStarred);
          if (icon) {
            icon.className = isStarred ? 'bi bi-star-fill text-warning' : 'bi bi-star';
          }
        }).catch(err => console.error('Star toggle failed', err));
      });
    });

    // 9. Helper for Attachment Icons
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

    // 10. Read Mail: Item Click -> Show Detail View
    const emailListView = document.getElementById('emailListView');
    const emailDetailView = document.getElementById('emailDetailView');
    const emailBackBtn = document.getElementById('emailBackBtn');
    const detailPrevBtn = document.getElementById('detailPrevBtn');
    const detailNextBtn = document.getElementById('detailNextBtn');

    function updateDetailNavButtons() {
      const items = Array.from(document.querySelectorAll('.email-item'));
      const currentIndex = items.findIndex(el => el.dataset.emailId === String(currentActiveEmailId));
      if (detailPrevBtn) detailPrevBtn.disabled = (currentIndex <= 0);
      if (detailNextBtn) detailNextBtn.disabled = (currentIndex < 0 || currentIndex >= items.length - 1);
    }

    if (detailPrevBtn) {
      detailPrevBtn.addEventListener('click', function() {
        const items = Array.from(document.querySelectorAll('.email-item'));
        const currentIndex = items.findIndex(el => el.dataset.emailId === String(currentActiveEmailId));
        if (currentIndex > 0) {
          items[currentIndex - 1].click();
        }
      });
    }

    if (detailNextBtn) {
      detailNextBtn.addEventListener('click', function() {
        const items = Array.from(document.querySelectorAll('.email-item'));
        const currentIndex = items.findIndex(el => el.dataset.emailId === String(currentActiveEmailId));
        if (currentIndex >= 0 && currentIndex < items.length - 1) {
          items[currentIndex + 1].click();
        }
      });
    }

    document.querySelectorAll('.email-item').forEach(item => {
      item.addEventListener('click', function(e) {
        // Prevent opening if clicked inside checkbox, star, or action button group
        if (e.target.closest('.email-item-select') || e.target.closest('.email-item-star') || e.target.closest('.email-item-actions')) {
          return;
        }

        const emailId = this.dataset.emailId;
        currentActiveEmailId = emailId;
        updateDetailNavButtons();

        const subject = this.querySelector('.email-item-subject').textContent.trim();
        const sender = this.querySelector('.email-item-sender').textContent.trim();
        const time = this.querySelector('.email-item-time').textContent.trim();
        const senderEmail = this.querySelector('.email-raw-sender-email').textContent.trim();
        const recipientEmail = this.querySelector('.email-raw-recipient-email').textContent.trim();
        const ccEmail = this.querySelector('.email-raw-cc-email') ? this.querySelector('.email-raw-cc-email').textContent.trim() : '';
        const isStarred = this.querySelector('.email-raw-starred') ? this.querySelector('.email-raw-starred').textContent === '1' : false;
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
        document.getElementById('detailAvatar').textContent = sender.charAt(0).toUpperCase();
        document.getElementById('detailBody').innerHTML = bodyContent;

        // Star State in Detail
        const detailStarBtn = document.getElementById('detailStarBtn');
        if (detailStarBtn) {
          detailStarBtn.classList.toggle('starred', isStarred);
          const icon = detailStarBtn.querySelector('i');
          if (icon) icon.className = isStarred ? 'bi bi-star-fill text-warning' : 'bi bi-star';
        }

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

    // Detail Action: Delete with SweetAlert2
    const detailDeleteBtn = document.getElementById('detailDeleteBtn');
    if (detailDeleteBtn) {
      detailDeleteBtn.addEventListener('click', function() {
        if (!currentActiveEmailId) return;
        const isTrash = isTrashFolder;

        confirmDialog(
          isTrash ? 'Permanently Delete Conversation?' : 'Move to Trash?',
          isTrash 
            ? 'This conversation will be permanently purged and cannot be recovered.' 
            : 'Move this conversation to the Trash folder?',
          isTrash ? 'Yes, Delete Permanently' : 'Yes, Move to Trash',
          function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/email/${currentActiveEmailId}`;

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
        );
      });
    }

    // Detail Action: Archive
    const detailArchiveBtn = document.getElementById('detailArchiveBtn');
    if (detailArchiveBtn) {
      detailArchiveBtn.addEventListener('click', function() {
        if (!currentActiveEmailId) return;
        fetch(`/admin/email/${currentActiveEmailId}/move`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ folder: 'sent' })
        }).then(() => {
          if (window.SwalToast) window.SwalToast.fire({ icon: 'success', title: 'Conversation archived.' });
          setTimeout(() => window.location.reload(), 500);
        });
      });
    }

    // Detail Action: Mark Unread
    const detailMarkUnreadBtn = document.getElementById('detailMarkUnreadBtn');
    if (detailMarkUnreadBtn) {
      detailMarkUnreadBtn.addEventListener('click', function() {
        if (!currentActiveEmailId) return;
        fetch(`/admin/email/${currentActiveEmailId}/mark-unread`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        }).then(res => res.json()).then(data => {
          if (window.SwalToast) window.SwalToast.fire({ icon: 'info', title: 'Marked as unread.' });
          const rowItem = document.querySelector(`[data-email-id="${currentActiveEmailId}"]`);
          if (rowItem) rowItem.classList.add('unread');
          const badge = document.getElementById('inboxUnreadBadge');
          if (badge && data.unread_count !== undefined) {
            badge.textContent = data.unread_count;
          }
          emailDetailView.style.display = 'none';
          emailListView.style.display = 'flex';
        });
      });
    }

    // Detail Action: Move to Folder
    document.querySelectorAll('.detail-move-folder').forEach(btn => {
      btn.addEventListener('click', function() {
        if (!currentActiveEmailId) return;
        const target = this.dataset.folder;
        fetch(`/admin/email/${currentActiveEmailId}/move`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ folder: target })
        }).then(res => res.json()).then(data => {
          if (window.SwalToast) window.SwalToast.fire({ icon: 'success', title: `Moved to ${target.charAt(0).toUpperCase() + target.slice(1)}.` });
          setTimeout(() => window.location.reload(), 500);
        });
      });
    });

    // Detail Action: Apply Labels
    document.querySelectorAll('.detail-set-label').forEach(btn => {
      btn.addEventListener('click', function() {
        const label = this.dataset.label;
        const color = this.dataset.color;
        const badge = document.getElementById('detailLabelBadge');
        if (badge) {
          badge.textContent = label;
          badge.style.background = color;
        }
        if (window.SwalToast) window.SwalToast.fire({ icon: 'success', title: `Label "${label}" set.` });
      });
    });

    // Detail Action: Print
    const detailPrintBtn = document.getElementById('detailPrintBtn');
    if (detailPrintBtn) {
      detailPrintBtn.addEventListener('click', () => window.print());
    }

    // Detail Action: Forward
    function openForwardModal() {
      const subject = document.getElementById('detailSubject').textContent.trim();
      const body = document.getElementById('detailBody').innerText.trim();
      const sender = document.getElementById('detailSenderName').textContent.trim();
      const senderEmail = document.getElementById('detailSenderEmail').textContent.trim();
      const date = document.getElementById('detailDate').textContent.trim();

      const composeModal = new bootstrap.Modal(document.getElementById('composeModal'));
      document.getElementById('recipient_email').value = '';
      document.getElementById('subject').value = subject.startsWith('Fwd: ') ? subject : `Fwd: ${subject}`;
      document.getElementById('body').value = `\n\n---------- Forwarded message ---------\nFrom: ${sender} <${senderEmail}>\nDate: ${date}\nSubject: ${subject}\n\n${body}`;
      composeModal.show();
    }

    const detailForwardBtn = document.getElementById('detailForwardBtn');
    if (detailForwardBtn) detailForwardBtn.addEventListener('click', openForwardModal);
    const metaForwardBtn = document.getElementById('metaForwardBtn');
    if (metaForwardBtn) metaForwardBtn.addEventListener('click', openForwardModal);

    // Detail Action: Report Spam with SweetAlert2
    const detailReportSpamBtn = document.getElementById('detailReportSpamBtn');
    if (detailReportSpamBtn) {
      detailReportSpamBtn.addEventListener('click', function() {
        if (!currentActiveEmailId) return;
        confirmDialog(
          'Report as Spam?',
          'This conversation will be marked as spam and moved to the Spam folder.',
          'Yes, Report Spam',
          function() {
            fetch(`/admin/email/${currentActiveEmailId}/spam`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            }).then(() => {
              if (window.SwalToast) window.SwalToast.fire({ icon: 'success', title: 'Moved to Spam.' });
              setTimeout(() => window.location.reload(), 500);
            });
          }
        );
      });
    }

    // Detail Action: Block Sender with SweetAlert2
    const detailBlockSenderBtn = document.getElementById('detailBlockSenderBtn');
    if (detailBlockSenderBtn) {
      detailBlockSenderBtn.addEventListener('click', function() {
        const sender = document.getElementById('detailSenderEmail').textContent.trim();
        confirmDialog(
          'Block Sender?',
          `Are you sure you want to block ${sender}? Messages from this address will be automatically filtered.`,
          'Yes, Block Sender',
          function() {
            if (window.SwalToast) window.SwalToast.fire({ icon: 'success', title: `${sender} has been blocked.` });
          }
        );
      });
    }

    // Detail Action: Star Toggle
    const detailStarBtn = document.getElementById('detailStarBtn');
    if (detailStarBtn) {
      detailStarBtn.addEventListener('click', function() {
        if (!currentActiveEmailId) return;
        fetch(`/admin/email/${currentActiveEmailId}/star`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        }).then(res => res.json()).then(data => {
          const isStarred = data.is_starred;
          detailStarBtn.classList.toggle('starred', isStarred);
          const icon = detailStarBtn.querySelector('i');
          if (icon) icon.className = isStarred ? 'bi bi-star-fill text-warning' : 'bi bi-star';

          const rowStar = document.querySelector(`.email-item-star[data-email-id="${currentActiveEmailId}"]`);
          if (rowStar) {
            rowStar.classList.toggle('starred', isStarred);
            const rowIcon = rowStar.querySelector('i');
            if (rowIcon) rowIcon.className = isStarred ? 'bi bi-star-fill text-warning' : 'bi bi-star';
          }
        });
      });
    }

    // Detail Action: Reply Buttons
    function openReplyModal(isReplyAll = false) {
      const senderEmail = document.getElementById('detailSenderEmail').textContent.trim();
      const subject = document.getElementById('detailSubject').textContent.trim();
      const ccEmail = document.getElementById('detailCcEmail') ? document.getElementById('detailCcEmail').textContent.trim() : '';

      const composeModal = new bootstrap.Modal(document.getElementById('composeModal'));
      document.getElementById('recipient_email').value = senderEmail;
      document.getElementById('subject').value = subject.startsWith('Re: ') ? subject : `Re: ${subject}`;
      
      if (isReplyAll && ccEmail) {
        const ccField = document.getElementById('ccField');
        const ccInput = document.getElementById('cc_email');
        if (ccField && ccInput) {
          ccField.classList.remove('d-none');
          ccInput.value = ccEmail;
        }
      }
      composeModal.show();
    }

    const detailReplyBtn = document.getElementById('detailReplyBtn');
    if (detailReplyBtn) detailReplyBtn.addEventListener('click', () => openReplyModal(false));
    const metaReplyBtn = document.getElementById('metaReplyBtn');
    if (metaReplyBtn) metaReplyBtn.addEventListener('click', () => openReplyModal(false));
    const metaReplyAllBtn = document.getElementById('metaReplyAllBtn');
    if (metaReplyAllBtn) metaReplyAllBtn.addEventListener('click', () => openReplyModal(true));

    // Detail Action: Quick Reply Form
    const btnQuickReplySend = document.getElementById('btnQuickReplySend');
    const quickReplyBody = document.getElementById('quickReplyBody');
    if (btnQuickReplySend && quickReplyBody) {
      btnQuickReplySend.addEventListener('click', function() {
        const replyText = quickReplyBody.value.trim();
        if (!replyText) {
          if (window.SwalToast) window.SwalToast.fire({ icon: 'warning', title: 'Please type a reply message first.' });
          return;
        }

        const senderEmail = document.getElementById('detailSenderEmail').textContent.trim();
        const subject = document.getElementById('detailSubject').textContent.trim();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.email.store") }}';

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(token);

        const recInput = document.createElement('input');
        recInput.type = 'hidden';
        recInput.name = 'recipient_email';
        recInput.value = senderEmail;
        form.appendChild(recInput);

        const subInput = document.createElement('input');
        subInput.type = 'hidden';
        subInput.name = 'subject';
        subInput.value = subject.startsWith('Re: ') ? subject : `Re: ${subject}`;
        form.appendChild(subInput);

        const bodyInput = document.createElement('input');
        bodyInput.type = 'hidden';
        bodyInput.name = 'body';
        bodyInput.value = replyText;
        form.appendChild(bodyInput);

        document.body.appendChild(form);
        form.submit();
      });
    }

    // 11. Compose Modal Logic & Attachments Management
    const composeModalEl = document.getElementById('composeModal');
    if (composeModalEl) {
      composeModalEl.addEventListener('hidden.bs.modal', function() {
        const modalTitle = document.getElementById('composeModalLabel');
        if (modalTitle) modalTitle.textContent = 'New Message';
        const bodyArea = document.getElementById('body');
        if (bodyArea) bodyArea.placeholder = 'Write your message here...';
      });
    }

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
