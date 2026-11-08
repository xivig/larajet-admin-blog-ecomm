@extends('layouts.admin')

@section('title', 'Comments Moderation - XivigAdmin PRO')

@section('content')
<div class="page-comments">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Discussions &amp; Reader Comments</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.posts.index') }}" class="breadcrumb-item">Editorial</a>
        <span class="breadcrumb-item active">Comments Moderation</span>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
        <i class="bi bi-check-circle me-1"></i> {{ $counts['approved'] }} Approved
      </span>
      <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">
        <i class="bi bi-hourglass-split me-1"></i> {{ $counts['pending'] }} Pending
      </span>
      <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
        <i class="bi bi-shield-x me-1"></i> {{ $counts['spam'] }} Spam
      </span>
    </div>
  </div>

  <!-- Status Filter Pills & Search -->
  <div class="card mb-4">
    <div class="card-body p-3">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <ul class="nav nav-pills gap-1">
          <li class="nav-item">
            <a class="nav-link {{ request('status', 'all') === 'all' ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">
              All <span class="badge bg-dark-subtle text-dark ms-1">{{ $counts['all'] }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}" href="{{ route('admin.comments.index', ['status' => 'approved']) }}">
              Approved <span class="badge bg-success-subtle text-success ms-1">{{ $counts['approved'] }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}" href="{{ route('admin.comments.index', ['status' => 'pending']) }}">
              Pending <span class="badge bg-warning-subtle text-warning ms-1">{{ $counts['pending'] }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request('status') === 'spam' ? 'active' : '' }}" href="{{ route('admin.comments.index', ['status' => 'spam']) }}">
              Spam <span class="badge bg-danger-subtle text-danger ms-1">{{ $counts['spam'] }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request('status') === 'trash' ? 'active text-danger' : '' }}" href="{{ route('admin.comments.index', ['status' => 'trash']) }}">
              <i class="bi bi-trash me-1"></i> Trash <span class="badge {{ request('status') === 'trash' ? 'bg-danger text-white' : 'bg-danger-subtle text-danger' }} ms-1">{{ $counts['trash'] ?? 0 }}</span>
            </a>
          </li>
        </ul>

        <form action="{{ route('admin.comments.index') }}" method="GET" class="d-flex gap-2">
          @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
          @endif
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Search comments or authors..." value="{{ request('search') }}" style="min-width: 220px;">
          <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
          @if(request('search'))
            <a href="{{ route('admin.comments.index', ['status' => request('status')]) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
          @endif
        </form>
      </div>
    </div>
  </div>

  <!-- Comments Table -->
  <div class="card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Discussion Threads</h5>
      <span class="text-muted small">Showing {{ $comments->firstItem() ?? 0 }}-{{ $comments->lastItem() ?? 0 }} of {{ $comments->total() }}</span>
    </div>
    <div class="card-body p-0">
      <form id="bulkForm" action="{{ route('admin.comments.bulk') }}" method="POST">
        @csrf
        
        <div class="p-3 bg-body-tertiary border-bottom d-flex align-items-center gap-2">
          <select name="action" class="form-select form-select-sm w-auto" required>
            <option value="">Bulk Actions</option>
            @if(request('status') === 'trash')
              <option value="restore">Restore Selected</option>
              <option value="force_delete">Permanently Delete Selected</option>
            @else
              <option value="approve">Mark as Approved</option>
              <option value="pending">Mark as Pending</option>
              <option value="spam">Mark as Spam</option>
              <option value="delete">Move to Trash</option>
            @endif
          </select>
          <button type="submit" class="btn btn-sm btn-primary">Apply</button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 40px;" class="text-center">
                  <input type="checkbox" id="selectAll" class="form-check-input">
                </th>
                <th style="width: 200px;">Author</th>
                <th>Comment</th>
                <th style="width: 220px;">Article</th>
                <th style="width: 100px;">Status</th>
                <th style="width: 130px;">Submitted</th>
                <th style="width: 160px;" class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($comments as $comment)
                <tr>
                  <td class="text-center">
                    <input type="checkbox" name="ids[]" value="{{ $comment->id }}" class="form-check-input item-check">
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="avatar-initials bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 13px; flex-shrink: 0;">
                        {{ strtoupper(substr($comment->user->name ?? 'A', 0, 1)) }}
                      </div>
                      <div class="text-truncate" style="max-width: 150px;">
                        <div class="fw-semibold text-truncate">{{ $comment->user->name ?? 'Reader' }}</div>
                        <small class="text-muted text-truncate d-block">{{ $comment->user->email ?? 'no-email' }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="mb-1 text-break">{{ $comment->comment }}</div>
                    @if ($comment->replies && $comment->replies->count() > 0)
                      <div class="mt-2 ps-3 border-start border-2 border-primary-subtle">
                        @foreach ($comment->replies as $rep)
                          <div class="small text-muted mb-1">
                            <span class="fw-bold text-primary">{{ $rep->user->name ?? 'Staff' }}:</span> {{ $rep->reply }}
                          </div>
                        @endforeach
                      </div>
                    @endif
                  </td>
                  <td>
                    @if ($comment->post)
                      <a href="{{ route('single-post', $comment->post->slug) }}" target="_blank" class="fw-semibold text-decoration-none text-truncate d-block" style="max-width: 200px;" title="{{ $comment->post->title }}">
                        {{ $comment->post->title }}
                      </a>
                      <small class="text-muted">ID: #{{ $comment->post->id }}</small>
                    @else
                      <span class="text-muted small">Post Deleted</span>
                    @endif
                  </td>
                  <td>
                    @if ($comment->trashed())
                      <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="bi bi-trash me-1"></i> Trashed</span>
                    @elseif ($comment->status === 'approved')
                      <span class="badge bg-success-subtle text-success px-2 py-1">Approved</span>
                    @elseif ($comment->status === 'pending')
                      <span class="badge bg-warning-subtle text-warning px-2 py-1">Pending</span>
                    @else
                      <span class="badge bg-danger-subtle text-danger px-2 py-1">Spam</span>
                    @endif
                  </td>
                  <td>
                    <span class="small text-muted">{{ $comment->created_at ? $comment->created_at->diffForHumans() : 'N/A' }}</span>
                  </td>
                  <td class="text-end">
                    @if ($comment->trashed())
                      <div class="btn-group btn-group-sm">
                        <form action="{{ route('admin.comments.restore', $comment->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-outline-success" title="Restore Comment">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                          </button>
                        </form>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmForceDeleteComment('{{ $comment->id }}')" title="Permanently Delete">
                          <i class="bi bi-x-octagon me-1"></i> Delete
                        </button>
                      </div>

                      <form id="force-delete-form-{{ $comment->id }}" action="{{ route('admin.comments.force-delete', $comment->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                      </form>
                    @else
                      <div class="btn-group btn-group-sm">
                        @if ($comment->status !== 'approved')
                          <a href="{{ route('admin.comments.approve', $comment->id) }}" class="btn btn-outline-success" title="Approve Comment">
                            <i class="bi bi-check-lg"></i>
                          </a>
                        @endif

                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#replyModal-{{ $comment->id }}" title="Reply to Reader">
                          <i class="bi bi-reply-fill"></i>
                        </button>

                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editCommentModal-{{ $comment->id }}" title="Edit Comment">
                          <i class="bi bi-pencil"></i>
                        </button>

                        <button type="button" class="btn btn-outline-danger" onclick="deleteComment('{{ $comment->id }}')" title="Move to Trash">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-chat-square-dots fs-1 d-block mb-2 text-muted"></i>
                    No comments found in this view.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </form>

      @if ($comments->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
          {{ $comments->links() }}
        </div>
      @endif
    </div>
  </div>

</div>

{{-- Reply and Edit Modals (Rendered outside of table for DOM integrity) --}}
@foreach ($comments as $comment)
  <!-- Reply Modal -->
  <div class="modal fade" id="replyModal-{{ $comment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('admin.comments.reply', $comment->id) }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Reply to {{ $comment->user->name ?? 'Reader' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="p-3 bg-light rounded mb-3 small">
              <strong>Original Comment:</strong>
              <div class="text-muted mt-1">{{ $comment->comment }}</div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Your Official Response</label>
              <textarea name="reply" class="form-control" rows="4" placeholder="Write helpful response as administrator..." required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Send Reply</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Comment Modal -->
  <div class="modal fade" id="editCommentModal-{{ $comment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('admin.comments.update', $comment->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">Edit Comment #{{ $comment->id }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Comment Text</label>
              <textarea name="comment" class="form-control" rows="4" required>{{ $comment->comment }}</textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Status</label>
              <select name="status" class="form-select">
                <option value="approved" {{ $comment->status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending" {{ $comment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="spam" {{ $comment->status === 'spam' ? 'selected' : '' }}>Spam</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Hidden Delete Form -->
  <form id="delete-form-{{ $comment->id }}" action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
  </form>
@endforeach

@push('scripts')
<script>
  // Select All Checkbox
  const selectAll = document.getElementById('selectAll');
  if (selectAll) {
    selectAll.addEventListener('change', function() {
      document.querySelectorAll('.item-check').forEach(cb => cb.checked = selectAll.checked);
    });
  }

  function deleteComment(id) {
    showConfirmDialog({
      title: 'Move to Trash?',
      text: 'Move this comment to Trash?',
      icon: 'warning',
      confirmButtonText: 'Yes, Move'
    }, function() {
      document.getElementById('delete-form-' + id).submit();
    });
  }

  function confirmForceDeleteComment(id) {
    showConfirmDialog({
      title: 'Permanent Deletion',
      text: 'Are you sure you want to permanently delete this comment and its replies? This action cannot be undone.',
      icon: 'error',
      confirmButtonText: 'Delete Permanently'
    }, function() {
      document.getElementById('force-delete-form-' + id).submit();
    });
  }

  // Intercept Bulk Action Form with SweetAlert2
  const bulkForm = document.getElementById('bulkForm');
  if (bulkForm) {
    bulkForm.addEventListener('submit', function(e) {
      const selected = document.querySelectorAll('.item-check:checked');
      if (selected.length === 0) {
        e.preventDefault();
        Swal.fire({
          icon: 'info',
          title: 'Selection Required',
          text: 'Please select at least one comment using the checkboxes.',
          customClass: { confirmButton: 'btn btn-primary px-4 rounded-pill' },
          buttonsStyling: false
        });
        return false;
      }

      const action = bulkForm.querySelector('select[name="action"]').value;
      if (action === 'delete') {
        e.preventDefault();
        showConfirmDialog({
          title: 'Move Selected to Trash?',
          text: 'Move ' + selected.length + ' selected comment(s) to Trash?',
          icon: 'warning',
          confirmButtonText: 'Move to Trash'
        }, function() {
          bulkForm.submit();
        });
      } else if (action === 'force_delete') {
        e.preventDefault();
        showConfirmDialog({
          title: 'Permanently Delete Selected?',
          text: 'Are you sure you want to permanently remove ' + selected.length + ' comment(s) and their replies? This cannot be undone.',
          icon: 'error',
          confirmButtonText: 'Delete Permanently'
        }, function() {
          bulkForm.submit();
        });
      }
    });
  }
</script>
@endpush
@endsection
