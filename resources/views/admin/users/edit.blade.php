@extends('layouts.admin')

@section('title', 'Edit User: ' . $user->name . ' - XivigAdmin PRO')

@section('content')
<div class="page-users-edit">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Edit User Account</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.users.index') }}" class="breadcrumb-item">Users</a>
        <span class="breadcrumb-item active">Edit #{{ $user->id }}</span>
      </nav>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Directory
    </a>
  </div>

  <div class="card" style="max-width: 780px;">
    <div class="card-header py-3">
      <h5 class="card-title mb-0">Modify Account Details</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label for="userName" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="userName" class="form-control" value="{{ old('name', $user->name) }}" required>
          </div>
          <div class="col-md-6">
            <label for="userEmail" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" id="userEmail" class="form-control" value="{{ old('email', $user->email) }}" required>
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label for="userPassword" class="form-label fw-semibold">New Password <small class="text-muted">(Leave blank to keep current)</small></label>
            <input type="password" name="password" id="userPassword" class="form-control">
          </div>
          <div class="col-md-6">
            <label for="userPasswordConfirm" class="form-label fw-semibold">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="userPasswordConfirm" class="form-control">
          </div>
        </div>

        <div class="mb-4">
          <label for="userAvatar" class="form-label fw-semibold">Change Avatar</label>
          @if ($user->profile_photo_path)
            <div class="mb-2">
              <img src="{{ asset($user->profile_photo_path) }}" alt="{{ $user->name }}" class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;">
            </div>
          @endif
          <input type="file" name="avatar" id="userAvatar" class="form-control" accept="image/*">
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold d-block">Security Authority &amp; System Roles</label>
          <div class="row g-2">
            @foreach ($roles as $role)
              <div class="col-md-6">
                <div class="border rounded p-2.5 h-100">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                      {{ $user->roles->contains('id', $role->id) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="role_{{ $role->id }}">
                      <span class="badge bg-{{ $role->color }}-subtle text-{{ $role->color }} me-1">{{ $role->name }}</span>
                    </label>
                    <div class="text-muted small mt-1" style="font-size: 11px;">{{ $role->description }}</div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <div class="mb-4">
          <div class="form-check form-switch">
            <input type="hidden" name="is_author" value="0">
            <input class="form-check-input" type="checkbox" name="is_author" id="isAuthor" value="1" {{ old('is_author', $user->author ? '1' : '0') == '1' ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="isAuthor">
              Editorial Author Contributor (Can compose and publish blog posts)
            </label>
          </div>
        </div>

        <div class="d-flex justify-content-between">
          <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Update User Account
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Reassign Authority Card -->
  <div class="card mt-4 border-warning-subtle" style="max-width: 780px;">
    <div class="card-header bg-warning-subtle py-3 d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title text-warning-emphasis mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-arrow-left-right"></i>
          <span>Reassign User Authority &amp; Ownership</span>
        </h5>
        <small class="text-muted">Transfer all publications, discussions, and author attribution to another active user</small>
      </div>
      <span class="badge bg-warning text-dark">{{ $user->posts_count }} Articles / {{ $user->comments_count }} Discussions</span>
    </div>
    <div class="card-body">
      <form action="{{ route('admin.users.reassign-authority', $user->id) }}" method="POST" id="editReassignAuthorityForm">
        @csrf
        <input type="hidden" name="action_type" value="transfer">

        <div class="mb-3">
          <label class="form-label fw-semibold">Target Recipient User <span class="text-danger">*</span></label>
          <select name="target_user_id" class="form-select" required>
            <option value="">-- Choose recipient user to take over authority --</option>
            @foreach ($otherUsers as $ou)
              <option value="{{ $ou->id }}">
                #{{ $ou->id }} - {{ $ou->name }} ({{ $ou->email }})
                @if ($ou->author) [Author] @endif
              </option>
            @endforeach
          </select>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="transfer_posts" value="1" id="edit_transfer_posts" checked>
              <label class="form-check-label fw-semibold small" for="edit_transfer_posts">
                Transfer all {{ $user->posts_count }} authored article(s)
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="transfer_comments" value="1" id="edit_transfer_comments" checked>
              <label class="form-check-label fw-semibold small" for="edit_transfer_comments">
                Transfer all {{ $user->comments_count }} discussion comment(s)
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="copy_roles" value="1" id="edit_copy_roles">
              <label class="form-check-label fw-semibold small" for="edit_copy_roles">
                Copy {{ $user->name }}'s security roles to recipient
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="delete_source_user" value="1" id="edit_delete_source_user">
              <label class="form-check-label fw-semibold small text-danger" for="edit_delete_source_user">
                Decommission &amp; delete this user after transfer
              </label>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-warning" onclick="confirmEditReassignAuthority()">
          <i class="bi bi-arrow-left-right me-1"></i> Execute Authority Reassignment
        </button>
      </form>
    </div>
  </div>

</div>

@push('scripts')
<script>
  function confirmEditReassignAuthority() {
    const form = document.getElementById('editReassignAuthorityForm');
    const select = form.querySelector('select[name="target_user_id"]');
    if (!select.value) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Recipient Required',
          text: 'Please select a recipient user to receive the reassigned authority.'
        });
      } else {
        alert('Please select a recipient user.');
      }
      return;
    }

    const recipientName = select.options[select.selectedIndex].text;
    const isDelete = document.getElementById('edit_delete_source_user').checked;

    if (window.showConfirmDialog) {
      window.showConfirmDialog({
        title: isDelete ? 'Reassign Authority & Decommission?' : 'Reassign User Authority?',
        text: 'Are you sure you want to reassign all authority and content from "{{ addslashes($user->name) }}" to ' + recipientName + '?' + (isDelete ? ' This user account will be permanently deleted.' : ''),
        icon: isDelete ? 'error' : 'warning',
        confirmButtonText: isDelete ? 'Yes, Reassign & Decommission' : 'Yes, Reassign Authority'
      }, function() {
        form.submit();
      });
    } else {
      if (confirm('Reassign authority to ' + recipientName + '?')) {
        form.submit();
      }
    }
  }
</script>
@endpush
@endsection
