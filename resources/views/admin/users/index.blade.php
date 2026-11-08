@extends('layouts.admin')

@section('title', 'Users Directory & Authority - XivigAdmin PRO')

@section('content')
<div class="page-users-index">

  <!-- Page Header & Actions -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Users Directory &amp; Authority</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item">Management</span>
        <span class="breadcrumb-item active">Users &amp; Authority</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
        <i class="bi bi-shield-lock"></i>
        <span>Manage Roles</span>
      </a>
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-person-plus"></i>
        <span>Add New User</span>
      </a>
    </div>
  </div>

  <!-- Metric Badges -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm p-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-primary-subtle text-primary rounded-3 p-3">
            <i class="bi bi-people-fill fs-4"></i>
          </div>
          <div>
            <div class="fs-5 fw-bold mb-0">{{ count($users) }}</div>
            <small class="text-muted">Total Accounts</small>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm p-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-success-subtle text-success rounded-3 p-3">
            <i class="bi bi-feather fs-4"></i>
          </div>
          <div>
            <div class="fs-5 fw-bold mb-0">{{ $users->whereNotNull('author')->count() }}</div>
            <small class="text-muted">Active Authors</small>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm p-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-3">
            <i class="bi bi-shield-shaded fs-4"></i>
          </div>
          <div>
            <div class="fs-5 fw-bold mb-0">{{ $users->filter(fn($u) => $u->roles->count() > 0)->count() }}</div>
            <small class="text-muted">Privileged Staff</small>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm p-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-info-subtle text-info rounded-3 p-3">
            <i class="bi bi-journal-check fs-4"></i>
          </div>
          <div>
            <div class="fs-5 fw-bold mb-0">{{ $users->where('posts_count', '>', 0)->count() }}</div>
            <small class="text-muted">Publishing Users</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Users Table -->
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
      <div>
        <h5 class="card-title mb-0">Platform User Accounts &amp; Authority</h5>
        <small class="text-muted">Manage user privileges, editorial attribution, and authority transfers</small>
      </div>
      <span class="badge bg-primary-subtle text-primary">{{ count($users) }} Users</span>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="usersTable" class="table table-hover align-middle simple-datatable">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th>User</th>
              <th>Email</th>
              <th>Authority &amp; Roles</th>
              <th>Articles</th>
              <th>Discussions</th>
              <th>Joined</th>
              <th data-sortable="false" class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
              <tr>
                <td>{{ $user->id }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    @if ($user->profile_photo_path)
                      <img src="{{ asset($user->profile_photo_path) }}" alt="{{ $user->name }}" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;">
                    @else
                      <div class="avatar-initials bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 13px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                      </div>
                    @endif
                    <div>
                      <a href="{{ route('admin.users.show', $user->id) }}" class="fw-semibold text-decoration-none text-reset">
                        {{ $user->name }}
                      </a>
                      <div class="text-muted small" style="font-size: 11px;">ID: #{{ $user->id }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <a href="mailto:{{ $user->email }}" class="text-decoration-none text-reset small">{{ $user->email }}</a>
                </td>
                <td>
                  <div class="d-flex flex-wrap gap-1 align-items-center">
                    @forelse ($user->roles as $role)
                      <span class="badge bg-{{ $role->color }}-subtle text-{{ $role->color }} px-2 py-1">
                        {{ $role->name }}
                      </span>
                    @empty
                      <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Standard Reader</span>
                    @endforelse

                    @if ($user->author)
                      <span class="badge bg-success-subtle text-success px-2 py-1" title="Author Nickname: {{ $user->author->nickname }}">
                        <i class="bi bi-feather me-0.5"></i> Author
                      </span>
                    @endif
                  </div>
                </td>
                <td>
                  <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5">
                    {{ $user->posts_count }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-info-subtle text-info rounded-pill px-2.5">
                    {{ $user->comments_count }}
                  </span>
                </td>
                <td>
                  <span class="small text-muted">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-info" title="View Profile & Authority">
                      <i class="bi bi-eye"></i>
                    </a>
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reassignModal-{{ $user->id }}" title="Reassign Authority">
                      <i class="bi bi-arrow-left-right"></i>
                    </button>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary" title="Edit User">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-outline-danger" title="Delete User" onclick="handleDeleteUser('{{ $user->id }}', '{{ addslashes($user->name) }}', {{ $user->posts_count }}, {{ $user->comments_count }})">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>

                  <form id="delete-user-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

{{-- Reassign Authority Modals --}}
@foreach ($users as $user)
  <div class="modal fade" id="reassignModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-warning-subtle py-3">
          <div class="d-flex align-items-center gap-2">
            <div class="bg-warning text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
              <i class="bi bi-arrow-left-right"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold text-warning-emphasis mb-0">Reassign Authority: {{ $user->name }}</h5>
              <small class="text-muted">Manage security roles or transfer publication ownership</small>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-0">
          <!-- Nav Tabs -->
          <ul class="nav nav-tabs nav-fill border-bottom px-3 pt-2" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active fw-semibold" id="transfer-tab-{{ $user->id }}" data-bs-toggle="tab" data-bs-target="#transfer-content-{{ $user->id }}" type="button" role="tab">
                <i class="bi bi-arrow-left-right me-1 text-warning"></i> Transfer Publications &amp; Content
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link fw-semibold" id="roles-tab-{{ $user->id }}" data-bs-toggle="tab" data-bs-target="#roles-content-{{ $user->id }}" type="button" role="tab">
                <i class="bi bi-shield-check me-1 text-primary"></i> Edit Security Roles
              </button>
            </li>
          </ul>

          <div class="tab-content p-4">
            <!-- Tab 1: Transfer Content Authority -->
            <div class="tab-pane fade show active" id="transfer-content-{{ $user->id }}" role="tabpanel">
              <form action="{{ route('admin.users.reassign-authority', $user->id) }}" method="POST" id="reassignForm-{{ $user->id }}">
                @csrf
                <input type="hidden" name="action_type" value="transfer">

                <div class="alert alert-light border d-flex align-items-center justify-content-between p-3 mb-3">
                  <div>
                    <span class="fw-semibold">Current Footprint for {{ $user->name }}:</span>
                    <div class="text-muted small">ID #{{ $user->id }} &bull; {{ $user->email }}</div>
                  </div>
                  <div class="d-flex gap-2">
                    <span class="badge bg-primary-subtle text-primary fs-6">{{ $user->posts_count }} Articles</span>
                    <span class="badge bg-info-subtle text-info fs-6">{{ $user->comments_count }} Discussions</span>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold">Target Recipient User <span class="text-danger">*</span></label>
                  <select name="target_user_id" class="form-select" required>
                    <option value="">-- Choose recipient user to take over authority --</option>
                    @foreach ($users as $other)
                      @if ($other->id !== $user->id)
                        <option value="{{ $other->id }}">
                          #{{ $other->id }} - {{ $other->name }} ({{ $other->email }})
                          @if ($other->author) [Author] @endif
                        </option>
                      @endif
                    @endforeach
                  </select>
                  <small class="text-muted">All selected publications and attribution will be reassigned to this user.</small>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="transfer_posts" value="1" id="tp_{{ $user->id }}" checked>
                      <label class="form-check-label fw-semibold small" for="tp_{{ $user->id }}">
                        Transfer all {{ $user->posts_count }} authored article(s)
                      </label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="transfer_comments" value="1" id="tc_{{ $user->id }}" checked>
                      <label class="form-check-label fw-semibold small" for="tc_{{ $user->id }}">
                        Transfer all {{ $user->comments_count }} discussion comment(s)
                      </label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="copy_roles" value="1" id="cr_{{ $user->id }}">
                      <label class="form-check-label fw-semibold small" for="cr_{{ $user->id }}">
                        Copy {{ $user->name }}'s security roles to recipient
                      </label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="delete_source_user" value="1" id="du_{{ $user->id }}">
                      <label class="form-check-label fw-semibold small text-danger" for="du_{{ $user->id }}">
                        Decommission &amp; delete {{ $user->name }} after reassignment
                      </label>
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-warning" onclick="submitReassignTransfer('{{ $user->id }}', '{{ addslashes($user->name) }}')">
                    <i class="bi bi-arrow-left-right me-1"></i> Execute Authority Reassignment
                  </button>
                </div>
              </form>
            </div>

            <!-- Tab 2: Edit Security Roles -->
            <div class="tab-pane fade" id="roles-content-{{ $user->id }}" role="tabpanel">
              <form action="{{ route('admin.users.reassign-authority', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="action_type" value="roles_only">

                <div class="mb-3">
                  <label class="form-label fw-semibold d-block">System Security Roles</label>
                  <div class="row g-2">
                    @foreach ($roles as $r)
                      <div class="col-md-6">
                        <div class="border rounded p-2.5 h-100">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $r->id }}" id="role_{{ $user->id }}_{{ $r->id }}"
                              {{ $user->roles->contains('id', $r->id) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="role_{{ $user->id }}_{{ $r->id }}">
                              <span class="badge bg-{{ $r->color }}-subtle text-{{ $r->color }} me-1">{{ $r->name }}</span>
                            </label>
                            <div class="text-muted small mt-1" style="font-size: 11px;">{{ $r->description }}</div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>

                <div class="mb-4">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_author" id="author_switch_{{ $user->id }}" value="1" {{ $user->author ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="author_switch_{{ $user->id }}">
                      Editorial Author Contributor (Can compose and publish blog posts)
                    </label>
                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-shield-check me-1"></i> Save Security Roles
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endforeach

@push('scripts')
<script>
  function submitReassignTransfer(userId, userName) {
    const form = document.getElementById('reassignForm-' + userId);
    const select = form.querySelector('select[name="target_user_id"]');
    if (!select.value) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Recipient Required',
          text: 'Please select a recipient user account to take over authority.'
        });
      } else {
        alert('Please select a recipient user.');
      }
      return;
    }

    const recipientName = select.options[select.selectedIndex].text.trim();
    const isDelete = form.querySelector('input[name="delete_source_user"]').checked;

    if (window.showConfirmDialog) {
      window.showConfirmDialog({
        title: isDelete ? 'Reassign Authority & Decommission?' : 'Reassign User Authority?',
        text: 'Are you sure you want to reassign all authority and content from "' + userName + '" to ' + recipientName + '?' + (isDelete ? ' This account will be safely deleted after transfer.' : ''),
        icon: isDelete ? 'error' : 'warning',
        confirmButtonText: isDelete ? 'Yes, Reassign & Decommission' : 'Yes, Reassign Authority'
      }, function() {
        form.submit();
      });
    } else {
      if (confirm('Reassign authority from "' + userName + '" to ' + recipientName + '?')) {
        form.submit();
      }
    }
  }

  function handleDeleteUser(id, name, postsCount, commentsCount) {
    if (postsCount > 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Reassign Authority Required',
          html: '<p class="mb-2">User <strong>"' + name + '"</strong> is the author of <strong>' + postsCount + '</strong> publication(s).</p>' +
                '<p class="small text-muted mb-0">To protect content and attribution integrity, you must reassign their authority before deleting this account.</p>',
          showCancelButton: true,
          confirmButtonText: '<i class="bi bi-arrow-left-right me-1"></i> Reassign Authority Now',
          cancelButtonText: 'Cancel',
          customClass: {
            confirmButton: 'btn btn-warning rounded-pill px-4',
            cancelButton: 'btn btn-secondary rounded-pill px-4'
          },
          buttonsStyling: false
        }).then(function(result) {
          if (result.isConfirmed) {
            const modalEl = document.getElementById('reassignModal-' + id);
            if (modalEl) {
              const modal = new bootstrap.Modal(modalEl);
              // Also pre-check decommission
              const delCheckbox = modalEl.querySelector('input[name="delete_source_user"]');
              if (delCheckbox) delCheckbox.checked = true;
              modal.show();
            }
          }
        });
      } else {
        alert('Cannot delete user "' + name + '" because they have ' + postsCount + ' articles. Please reassign authority first.');
      }
      return;
    }

    if (window.showConfirmDialog) {
      showConfirmDialog({
        title: 'Delete User Account?',
        text: 'Are you sure you want to delete user "' + name + '"? This will remove their profile and comments (' + commentsCount + ').',
        icon: 'warning',
        confirmButtonText: 'Yes, Delete User'
      }, function() {
        document.getElementById('delete-user-' + id).submit();
      });
    } else {
      if (confirm('Delete user "' + name + '"?')) {
        document.getElementById('delete-user-' + id).submit();
      }
    }
  }
</script>
@endpush
@endsection