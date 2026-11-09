@extends('layouts.admin')

@section('title', 'Admin Profile & Security - XivigAdmin PRO')

@section('content')
<div class="page-profile">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Admin Profile &amp; Security Suite</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">Profile &amp; Security</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
        <i class="bi bi-shield-check me-1"></i> Fortify Security Active
      </span>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('info'))
    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
      <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
      <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Action could not be completed:</div>
      <ul class="mb-0 ps-3 small">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="row g-4">
    <!-- Left Column: Admin Card & Security Badges -->
    <div class="col-lg-4">
      <div class="card text-center mb-4">
        <div class="card-body pt-5 pb-4">
          @php
            $currentAvatar = ($admin && $admin->profile_photo_path) 
              ? asset($admin->profile_photo_path) 
              : asset('admin-dist/img/1-DZLDOIIi.jpg');
          @endphp
          <div class="position-relative d-inline-block">
            <img id="avatarDisplay" src="{{ $currentAvatar }}" alt="{{ $admin->name ?? 'Admin' }}" class="rounded-circle shadow mb-3 border border-3 border-primary" style="width: 110px; height: 110px; object-fit: cover;">
            @if ($admin && $admin->hasEnabledTwoFactorAuthentication())
              <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success p-2 border border-2 border-white" title="2FA Active">
                <i class="bi bi-shield-fill-check"></i>
              </span>
            @endif
          </div>
          <h4 class="card-title mb-1">{{ $admin->name ?? 'Admin User' }}</h4>
          <p class="text-muted small mb-2">{{ $admin->email ?? 'admin@xivig.com' }}</p>

          <div class="d-flex justify-content-center gap-2 mb-3">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">
              ENTERPRISE ADMIN
            </span>
            @if ($admin && $admin->hasVerifiedEmail())
              <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small" title="Email Verified">
                <i class="bi bi-patch-check-fill me-1"></i> Verified
              </span>
            @else
              <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill small" title="Email Unverified">
                <i class="bi bi-exclamation-circle me-1"></i> Unverified
              </span>
            @endif
          </div>

          <hr class="my-3">

          <div class="text-start">
            <div class="d-flex justify-content-between py-1.5 border-bottom small">
              <span class="text-muted">Guard / Stack</span>
              <span class="fw-semibold">admin / fortify</span>
            </div>
            <div class="d-flex justify-content-between py-1.5 border-bottom small">
              <span class="text-muted">Two-Factor Auth</span>
              @if ($admin && $admin->hasEnabledTwoFactorAuthentication())
                <span class="badge bg-success-subtle text-success border border-success-subtle">Google Authenticator</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary">Disabled</span>
              @endif
            </div>
            <div class="d-flex justify-content-between py-1.5 border-bottom small">
              <span class="text-muted">Active Sessions</span>
              <span class="fw-semibold text-primary">{{ count($activeSessions) }} device(s)</span>
            </div>
            <div class="d-flex justify-content-between py-1.5 small">
              <span class="text-muted">Registered</span>
              <span class="fw-semibold">{{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A' }}</span>
            </div>
          </div>

          @if ($admin && !$admin->hasVerifiedEmail())
            <div class="mt-4 pt-3 border-top text-center">
              <form action="{{ route('admin.profile.verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                  <i class="bi bi-send me-1"></i> Resend Email Verification Link
                </button>
              </form>
            </div>
          @endif
        </div>
      </div>

      <!-- Quick Navigation Card -->
      <div class="card mb-4">
        <div class="card-header py-3">
          <h6 class="card-title mb-0 small text-uppercase fw-bold text-muted">Security Center Navigation</h6>
        </div>
        <div class="list-group list-group-flush small">
          <a href="#section-profile" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2.5">
            <span><i class="bi bi-person-gear me-2 text-primary"></i> Account Profile &amp; Photo</span>
            <i class="bi bi-chevron-right text-muted"></i>
          </a>
          <a href="#section-password" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2.5">
            <span><i class="bi bi-key me-2 text-warning"></i> Change Password</span>
            <i class="bi bi-chevron-right text-muted"></i>
          </a>
          <a href="#section-2fa" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2.5">
            <span><i class="bi bi-shield-lock me-2 text-success"></i> 2FA (Google Authenticator)</span>
            <i class="bi bi-chevron-right text-muted"></i>
          </a>
          <a href="#section-sessions" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2.5">
            <span><i class="bi bi-laptop me-2 text-info"></i> Active Sessions &amp; Devices</span>
            <i class="bi bi-chevron-right text-muted"></i>
          </a>
          <a href="#section-history" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2.5">
            <span><i class="bi bi-clock-history me-2 text-secondary"></i> Device Login History</span>
            <i class="bi bi-chevron-right text-muted"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Right Column: Security Configurations -->
    <div class="col-lg-8">

      <!-- 1. Account Information & Avatar Upload -->
      <div id="section-profile" class="card mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">Account Information &amp; Photo</h5>
            <small class="text-muted">Update your administrative credentials and custom avatar</small>
          </div>
          <span class="badge bg-light text-dark border">General</span>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="adminName" class="form-label fw-semibold">Administrator Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="adminName" class="form-control" value="{{ old('name', $admin->name ?? '') }}" required>
              </div>

              <div class="col-md-6">
                <label for="adminEmail" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" id="adminEmail" class="form-control" value="{{ old('email', $admin->email ?? '') }}" required>
              </div>
            </div>

            <!-- Avatar Upload -->
            <div class="mb-4">
              <label for="avatarInput" class="form-label fw-semibold">Profile Photo / Avatar</label>
              <input type="file" name="avatar" id="avatarInput" class="form-control" accept="image/*">
              <div class="form-text small">Upload JPG, PNG, WEBP, or SVG file (Max 5MB). Photo updates dynamically across header and sidebar.</div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Save Profile &amp; Photo
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- 2. Update Password -->
      <div id="section-password" class="card mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">Update Password</h5>
            <small class="text-muted">Ensure your administrative access credentials remain secure</small>
          </div>
          <span class="badge bg-light text-dark border">Credentials</span>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label for="currentPassword" class="form-label fw-semibold">Current Password</label>
              <input type="password" name="current_password" id="currentPassword" class="form-control" required placeholder="Enter current password">
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="newPassword" class="form-label fw-semibold">New Password</label>
                <input type="password" name="password" id="newPassword" class="form-control" required placeholder="Enter new strong password">
              </div>
              <div class="col-md-6">
                <label for="confirmPassword" class="form-label fw-semibold">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" required placeholder="Re-type new password">
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

      <!-- 3. Two-Factor Authentication (Google Authenticator) -->
      <div id="section-2fa" class="card mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">Two-Factor Authentication (2FA)</h5>
            <small class="text-muted">Add additional security using Google Authenticator, Authy, or any TOTP app</small>
          </div>
          @if ($admin && $admin->hasEnabledTwoFactorAuthentication())
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill">
              <i class="bi bi-shield-fill-check me-1"></i> ACTIVE
            </span>
          @else
            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1.5 rounded-pill">
              INACTIVE
            </span>
          @endif
        </div>
        <div class="card-body">
          @if ($admin && $admin->hasEnabledTwoFactorAuthentication())
            <!-- 2FA is currently ACTIVE -->
            <div class="alert alert-success d-flex align-items-start gap-3 mb-4">
              <i class="bi bi-shield-check fs-3 flex-shrink-0 text-success"></i>
              <div>
                <h6 class="alert-heading fw-bold mb-1">Two-Factor Authentication is currently activated</h6>
                <p class="mb-0 small">
                  Your administrator account is protected. Every time you log in, you will be prompted to provide a 6-digit one-time passcode generated by your Google Authenticator or compatible TOTP application.
                </p>
              </div>
            </div>

            <!-- Emergency Recovery Codes Section -->
            <div class="card border mb-4">
              <div class="card-header bg-light py-2.5 d-flex justify-content-between align-items-center">
                <span class="fw-semibold small"><i class="bi bi-key-fill text-warning me-1"></i> Emergency Recovery Codes</span>
                <span class="badge bg-secondary-subtle text-secondary small">8 Single-Use Codes</span>
              </div>
              <div class="card-body">
                <p class="small text-muted mb-3">
                  Store these emergency recovery codes in a safe password manager. If you lose access to your authenticator phone, each of these codes can be used once to regain access.
                </p>

                <div class="bg-dark text-light p-3 rounded font-monospace small mb-3">
                  <div class="row g-2">
                    @foreach ($admin->recoveryCodes() as $code)
                      <div class="col-sm-6">
                        <code class="text-info">{{ $code }}</code>
                      </div>
                    @endforeach
                  </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                  <a href="{{ route('admin.profile.two-factor.download-codes') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-download me-1"></i> Download Codes (.txt)
                  </a>
                  <form action="{{ route('admin.profile.two-factor.recovery-codes') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary" onclick="return confirm('Regenerate emergency recovery codes? Existing unconsumed codes will become invalid.')">
                      <i class="bi bi-arrow-clockwise me-1"></i> Regenerate Codes
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <!-- Disable 2FA Action -->
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
              <span class="small text-muted">To turn off two-factor authentication, verify your current password:</span>
              <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#disable2faModal">
                <i class="bi bi-shield-x me-1"></i> Disable 2FA
              </button>
            </div>

            <!-- Modal to confirm 2FA Disable -->
            <div class="modal fade" id="disable2faModal" tabindex="-1" aria-labelledby="disable2faModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="{{ route('admin.profile.two-factor.disable') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                      <h5 class="modal-title" id="disable2faModalLabel">Disable Two-Factor Authentication</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p class="small text-muted">
                        Are you sure you want to disable Two-Factor Authentication? Your account will only be protected by your password.
                      </p>
                      <div class="mb-3">
                        <label for="disable_password" class="form-label fw-semibold">Current Password</label>
                        <input type="password" name="current_password" id="disable_password" class="form-control" required placeholder="Enter password to confirm">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-danger">Disable 2FA Protection</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          @elseif ($qrCodeSvg && $twoFactorSecret)
            <!-- 2FA Setup in Progress: Display QR Code & Confirmation Form -->
            <div class="alert alert-warning mb-4">
              <div class="d-flex align-items-center mb-1 fw-bold">
                <i class="bi bi-qr-code-scan me-2 fs-5"></i> Step 2: Scan QR Code with Google Authenticator
              </div>
              <p class="small mb-0">
                Scan the QR code below using <strong>Google Authenticator</strong>, Microsoft Authenticator, or Authy on your mobile device. If you cannot scan the code, enter the setup key manually.
              </p>
            </div>

            <div class="row align-items-center mb-4 g-4">
              <div class="col-md-5 text-center">
                <div class="p-3 bg-white rounded border d-inline-block shadow-sm">
                  {!! $qrCodeSvg !!}
                </div>
              </div>
              <div class="col-md-7">
                <h6 class="fw-bold mb-2">Can't scan? Use this setup key:</h6>
                <div class="input-group mb-3">
                  <input type="text" class="form-control font-monospace" value="{{ $twoFactorSecret }}" readonly id="secretKeyField">
                  <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('{{ $twoFactorSecret }}'); alert('Secret key copied to clipboard!');">
                    <i class="bi bi-clipboard"></i> Copy
                  </button>
                </div>
                <div class="small text-muted mb-2">
                  <i class="bi bi-info-circle me-1"></i> Account identifier: <code>{{ $admin->email }}</code>
                </div>
              </div>
            </div>

            <!-- Confirm Code Form -->
            <form action="{{ route('admin.profile.two-factor.confirm') }}" method="POST" class="border-top pt-4">
              @csrf
              <h6 class="fw-bold mb-2">Step 3: Enter 6-Digit Code to Activate</h6>
              <p class="small text-muted mb-3">Enter the 6-digit numeric verification code shown in your authenticator app to complete setup:</p>
              
              <div class="row g-2 align-items-center">
                <div class="col-sm-5">
                  <input type="text" name="code" class="form-control font-monospace fs-5 text-center @error('code') is-invalid @enderror" placeholder="123456" maxlength="7" required autofocus autocomplete="one-time-code">
                </div>
                <div class="col-sm-7 d-flex gap-2">
                  <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i> Confirm &amp; Activate 2FA
                  </button>
                  <a href="{{ route('admin.profile') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
              </div>
            </form>

          @else
            <!-- 2FA is currently INACTIVE -->
            <div class="d-flex align-items-start gap-3 mb-4">
              <div class="p-3 rounded-circle bg-primary-subtle text-primary flex-shrink-0">
                <i class="bi bi-google fs-3"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Protect your administrative account with 2FA</h6>
                <p class="small text-muted mb-0">
                  When Two-Factor Authentication is enabled, logging in will require both your password and a secure one-time passcode generated by your Google Authenticator or compatible app.
                </p>
              </div>
            </div>

            <form action="{{ route('admin.profile.two-factor.enable') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-shield-plus me-1"></i> Enable Google Authenticator 2FA
              </button>
            </form>
          @endif
        </div>
      </div>

      <!-- 4. Active Browser Sessions & Devices -->
      <div id="section-sessions" class="card mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">Active Browser Sessions &amp; Devices</h5>
            <small class="text-muted">Manage active logins and revoke access from unrecognized devices</small>
          </div>
          <span class="badge bg-light text-dark border">{{ count($activeSessions) }} Active</span>
        </div>
        <div class="card-body">
          <p class="small text-muted mb-3">
            If you notice unfamiliar devices or locations, log out of other sessions immediately and update your password.
          </p>

          <div class="list-group mb-4">
            @forelse ($activeSessions as $session)
              <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="p-2.5 rounded bg-light text-secondary flex-shrink-0 fs-4">
                    @if ($session->device_type === 'Mobile')
                      <i class="bi bi-phone"></i>
                    @elseif ($session->device_type === 'Tablet')
                      <i class="bi bi-tablet"></i>
                    @else
                      <i class="bi bi-laptop"></i>
                    @endif
                  </div>
                  <div>
                    <div class="fw-semibold">
                      {{ $session->platform }} &bull; {{ $session->browser }}
                      @if ($session->is_current_device)
                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-2 small">
                          This Device
                        </span>
                      @endif
                    </div>
                    <div class="small text-muted">
                      IP: <code>{{ $session->ip_address }}</code> &bull; Last active: {{ $session->last_active }}
                    </div>
                  </div>
                </div>

                @if (!$session->is_current_device)
                  <form action="{{ route('admin.profile.sessions.destroy', $session->id) }}" method="POST" onsubmit="return confirm('Revoke this device session?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger mt-2 mt-sm-0">
                      <i class="bi bi-x-circle me-1"></i> Revoke
                    </button>
                  </form>
                @endif
              </div>
            @empty
              <div class="list-group-item text-center text-muted py-4 small">
                No database sessions recorded. Ensure <code>SESSION_DRIVER=database</code> is configured in <code>.env</code>.
              </div>
            @endforelse
          </div>

          @if (count($activeSessions) > 1)
            <div class="text-end border-top pt-3">
              <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#logoutOthersModal">
                <i class="bi bi-box-arrow-right me-1"></i> Log Out Other Browser Sessions
              </button>
            </div>

            <!-- Modal for Log Out Other Sessions -->
            <div class="modal fade" id="logoutOthersModal" tabindex="-1" aria-labelledby="logoutOthersModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="{{ route('admin.profile.sessions.logout-others') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                      <h5 class="modal-title" id="logoutOthersModalLabel">Log Out Other Browser Sessions</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p class="small text-muted">
                        Please enter your password to confirm you would like to log out of all other devices and browser sessions across all devices.
                      </p>
                      <div class="mb-3">
                        <label for="logout_password" class="form-label fw-semibold">Current Password</label>
                        <input type="password" name="password" id="logout_password" class="form-control" required placeholder="Enter password to confirm">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-danger">Log Out All Other Devices</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

      <!-- 5. Device Login History -->
      <div id="section-history" class="card">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">Recent Device Login History</h5>
            <small class="text-muted">Audit log of recent authentication events, device signatures, and IP addresses</small>
          </div>
          <span class="badge bg-light text-dark border">Audit</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
              <thead class="table-light">
                <tr>
                  <th>Timestamp</th>
                  <th>Device / Client</th>
                  <th>IP Address</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($loginHistories as $history)
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ $history->created_at->format('M d, Y') }}</div>
                      <div class="text-muted small">{{ $history->created_at->format('h:i:s A') }} ({{ $history->created_at->diffForHumans() }})</div>
                    </td>
                    <td>
                      <div>
                        @if ($history->device_type === 'Mobile')
                          <i class="bi bi-phone text-primary me-1"></i>
                        @elseif ($history->device_type === 'Tablet')
                          <i class="bi bi-tablet text-info me-1"></i>
                        @else
                          <i class="bi bi-laptop text-secondary me-1"></i>
                        @endif
                        <strong>{{ $history->platform }}</strong> &bull; {{ $history->browser }}
                      </div>
                      <div class="text-muted text-truncate small" style="max-width: 280px;" title="{{ $history->user_agent }}">
                        {{ $history->user_agent }}
                      </div>
                    </td>
                    <td>
                      <code>{{ $history->ip_address }}</code>
                    </td>
                    <td>
                      <span class="badge {{ $history->status_badge_class }}">
                        {{ ucfirst($history->status) }}
                      </span>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                      No device login history recorded yet. Future logins will be recorded here automatically.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

@push('scripts')
<script>
  // Dynamic Avatar preview
  document.getElementById('avatarInput')?.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('avatarDisplay').src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
</script>
@endpush
@endsection
