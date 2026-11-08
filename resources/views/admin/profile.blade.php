@extends('layouts.admin')

@section('title', 'Admin Profile & Security - XivigAdmin PRO')

@section('content')
<div class="page-profile">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Admin Profile &amp; Credentials</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">Profile</span>
      </nav>
    </div>
  </div>

  <div class="row g-4">
    <!-- Left Column: Admin Card -->
    <div class="col-lg-4">
      <div class="card text-center mb-4">
        <div class="card-body pt-5 pb-4">
          <img src="{{ asset('admin-dist/img/1-DZLDOIIi.jpg') }}" alt="Admin" class="rounded-circle shadow mb-3 border border-3 border-primary" style="width: 100px; height: 100px; object-fit: cover;">
          <h4 class="card-title mb-1">{{ $admin->name ?? 'Admin User' }}</h4>
          <p class="text-muted small mb-2">{{ $admin->email ?? 'admin@xivig.com' }}</p>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill">
            ENTERPRISE ADMIN
          </span>

          <hr class="my-4">

          <div class="text-start">
            <div class="d-flex justify-content-between py-1.5 border-bottom small">
              <span class="text-muted">Guard</span>
              <span class="fw-semibold">admin</span>
            </div>
            <div class="d-flex justify-content-between py-1.5 border-bottom small">
              <span class="text-muted">Account ID</span>
              <span class="fw-semibold">#{{ $admin->id ?? 1 }}</span>
            </div>
            <div class="d-flex justify-content-between py-1.5 small">
              <span class="text-muted">Registered</span>
              <span class="fw-semibold">{{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Update Profile & Change Password -->
    <div class="col-lg-8">

      <!-- Update Profile Information -->
      <div class="card mb-4">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">Account Information</h5>
          <small class="text-muted">Update your administrative profile details and notification email</small>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label for="adminName" class="form-label fw-semibold">Administrator Name</label>
              <input type="text" name="name" id="adminName" class="form-control" value="{{ old('name', $admin->name ?? '') }}" required>
            </div>

            <div class="mb-3">
              <label for="adminEmail" class="form-label fw-semibold">Email Address</label>
              <input type="email" name="email" id="adminEmail" class="form-control" value="{{ old('email', $admin->email ?? '') }}" required>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Save Profile
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Update Password -->
      <div class="card">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">Update Password</h5>
          <small class="text-muted">Ensure your administrative access credentials remain secure</small>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label for="currentPassword" class="form-label fw-semibold">Current Password</label>
              <input type="password" name="current_password" id="currentPassword" class="form-control" required>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="newPassword" class="form-label fw-semibold">New Password</label>
                <input type="password" name="password" id="newPassword" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label for="confirmPassword" class="form-label fw-semibold">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" required>
              </div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-warning">
                <i class="bi bi-shield-lock me-1"></i> Update Password
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
