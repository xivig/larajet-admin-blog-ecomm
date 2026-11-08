@extends('layouts.admin')

@section('title', 'Add New User - XivigAdmin PRO')

@section('content')
<div class="page-users-create">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Create User Account</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.users.index') }}" class="breadcrumb-item">Users</a>
        <span class="breadcrumb-item active">Create</span>
      </nav>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Directory
    </a>
  </div>

  <div class="card" style="max-width: 780px;">
    <div class="card-header py-3">
      <h5 class="card-title mb-0">User Account Information</h5>
      <small class="text-muted">Register a new reader, contributor, or staff member</small>
    </div>
    <div class="card-body">
      <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label for="userName" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="userName" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required>
          </div>
          <div class="col-md-6">
            <label for="userEmail" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" id="userEmail" class="form-control" placeholder="john@example.com" value="{{ old('email') }}" required>
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label for="userPassword" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" id="userPassword" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label for="userPasswordConfirm" class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" id="userPasswordConfirm" class="form-control" required>
          </div>
        </div>

        <div class="mb-3">
          <label for="userAvatar" class="form-label fw-semibold">Avatar Image</label>
          <input type="file" name="avatar" id="userAvatar" class="form-control" accept="image/*">
        </div>

        <div class="mb-4">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_author" id="isAuthor" value="1" checked>
            <label class="form-check-label fw-semibold" for="isAuthor">
              Register as Editorial Author (Can be assigned to write blog posts)
            </label>
          </div>
        </div>

        <div class="d-flex justify-content-between">
          <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-person-check me-1"></i> Create User
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
