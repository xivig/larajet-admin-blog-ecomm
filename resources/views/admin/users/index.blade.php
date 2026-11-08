@extends('layouts.admin')

@section('title', 'Users Directory - XivigAdmin PRO')

@section('content')
<div class="page-users-index">

  <!-- Page Header & Actions -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Users Directory &amp; Accounts</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item">Management</span>
        <span class="breadcrumb-item active">Users</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-person-plus"></i>
        <span>Add New User</span>
      </a>
    </div>
  </div>

  <!-- Users Table -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
      <div>
        <h5 class="card-title mb-0">Registered Platform Users</h5>
        <small class="text-muted">Manage contributors, authors, and reader accounts</small>
      </div>
      <span class="badge bg-primary-subtle text-primary">{{ count($users) }} Total Users</span>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="usersTable" class="table table-hover align-middle simple-datatable">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th>User</th>
              <th>Email</th>
              <th>Role / Author</th>
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
                      <img src="{{ asset($user->profile_photo_path) }}" alt="{{ $user->name }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                    @else
                      <div class="avatar-initials bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 13px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                      </div>
                    @endif
                    <div>
                      <div class="fw-semibold">{{ $user->name }}</div>
                      <div class="text-muted small" style="font-size: 11px;">ID: #{{ $user->id }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <a href="mailto:{{ $user->email }}" class="text-decoration-none text-reset small">{{ $user->email }}</a>
                </td>
                <td>
                  @if ($user->author)
                    <span class="badge bg-success-subtle text-success px-2.5 py-1">Author Contributor</span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">Standard Reader</span>
                  @endif
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
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary" title="Edit User">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-outline-danger" title="Delete User" onclick="confirmDeleteUser('{{ $user->id }}', '{{ addslashes($user->name) }}')">
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

@push('scripts')
<script>
  function confirmDeleteUser(id, name) {
    showConfirmDialog({
      title: 'Delete User Account?',
      text: 'Are you sure you want to delete user "' + name + '"? This will also remove their associated comments and cannot be undone.',
      icon: 'warning',
      confirmButtonText: 'Yes, Delete User'
    }, function() {
      document.getElementById('delete-user-' + id).submit();
    });
  }
</script>
@endpush
@endsection
