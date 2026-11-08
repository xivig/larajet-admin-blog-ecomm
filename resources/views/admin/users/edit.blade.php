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

        <div class="d-flex justify-content-between">
          <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Update User
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
