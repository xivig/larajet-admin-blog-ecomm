<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set New Password - XivigAdmin</title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicons -->
  <link rel="icon" type="image/x-icon" href="{{ asset('admin-dist/img/favicon-DgHNGhkV.ico') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('admin-dist/img/favicon-32x32-ChisqE3Y.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin-dist/img/favicon-16x16-BokJ4EXI.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('admin-dist/img/apple-touch-icon-D_uz1V6f.png') }}">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Core CSS Files -->
  <link rel="stylesheet" href="{{ asset('admin-dist/vendor/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('admin-dist/css/main-BBIlupvD.css') }}">

  <!-- Theme Controller Pre-loader -->
  <script>
    (function() {
      const saved = localStorage.getItem('xivig_admin_theme') || localStorage.getItem('xivig-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-bs-theme', saved);
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
</head>

<body class="fauth-page">
  <!-- Elite Ambient Background Canvas & Glowing Mesh -->
  <div class="fauth-ambient-canvas" aria-hidden="true">
    <div class="fauth-glow fauth-glow-indigo"></div>
    <div class="fauth-glow fauth-glow-rose"></div>
    <div class="fauth-glow fauth-glow-emerald"></div>
    <div class="fauth-grid-overlay"></div>
  </div>

  <!-- Quick Theme Switcher Button -->
  <div class="fauth-topbar">
    <button type="button" class="fauth-theme-btn theme-toggle" aria-label="Toggle color theme" title="Toggle color theme" onclick="toggleTheme()">
      <i class="bi bi-moon-stars theme-icon-dark"></i>
      <i class="bi bi-sun theme-icon-light"></i>
    </button>
  </div>

  <!-- Center Container -->
  <div class="fauth-container">
    <main class="fauth-main">
      <div class="fauth-brand-center">
        <a href="{{ url('/') }}" class="fauth-brand" aria-label="XivigAdmin home">
          <span class="fauth-brand-logo">
            <img src="{{ asset('admin-dist/img/xivig-logo-CDpcaf-E.png') }}" alt="XivigAdmin" class="brand-logo-img">
          </span>
          <span class="fauth-brand-text">
            <strong class="brand-title">Xivig<span class="brand-gradient">Admin</span></strong>
            <span class="badge-elite">PRO</span>
          </span>
        </a>
      </div>

      <div class="fauth-card">
        <div class="fauth-card-head">
          <span class="fauth-kicker"><i class="bi bi-shield-lock"></i> Secure Password Change</span>
          <h1 class="fauth-title">Create new password</h1>
          <p class="fauth-subtitle">Choose a robust, unique password to secure your admin account.</p>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center mb-1 fw-bold">
              <i class="bi bi-exclamation-triangle-fill me-2"></i> Password Error
            </div>
            <ul class="mb-0 ps-3 small">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form class="fauth-form" method="POST" action="{{ route('admin.password.update') }}" novalidate>
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <div class="fauth-field">
            <label for="email" class="form-label">Email address</label>
            <div class="fauth-input-wrap">
              <i class="bi bi-envelope fauth-input-icon"></i>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly autocomplete="email">
            </div>
          </div>

          <div class="fauth-field">
            <label for="password" class="form-label">New Password</label>
            <div class="fauth-input-wrap">
              <i class="bi bi-lock fauth-input-icon"></i>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="At least 8 characters" required autofocus autocomplete="new-password">
            </div>
          </div>

          <div class="fauth-field">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <div class="fauth-input-wrap">
              <i class="bi bi-shield-check fauth-input-icon"></i>
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repeat new password" required autocomplete="new-password">
            </div>
          </div>

          <button type="submit" class="btn fauth-btn-primary">
            <span>Update Password &amp; Sign In</span>
            <i class="bi bi-arrow-right"></i>
          </button>
        </form>

        <p class="fauth-foot-text">
          Remembered your credentials? <a href="{{ route('admin.login') }}" class="fauth-link fw-semibold">Return to sign in</a>
        </p>
      </div>
    </main>

    <footer class="fauth-footer">
      <div class="fauth-footer-copy">
        &copy; {{ date('Y') }} <a href="{{ url('/') }}" class="fw-semibold">XivigAdmin</a>. Crafted for elite digital enterprises.
        <span class="badge-elite-sm">v2.0.0 Pro</span>
      </div>
      <div class="fauth-footer-links">
        <a href="{{ url('/blog') }}" target="_blank">Blog Journal</a>
        <span class="sep">&bull;</span>
        <a href="{{ url('/') }}" target="_blank">Public Site</a>
        <span class="sep">&bull;</span>
        <a href="mailto:support@xivig.com">Support Desk</a>
      </div>
    </footer>
  </div>

  <script src="{{ asset('admin-dist/js/main-FIhSewOI.js') }}"></script>
  <script>
    function toggleTheme() {
      const current = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-bs-theme', current);
      document.documentElement.setAttribute('data-theme', current);
      localStorage.setItem('xivig_admin_theme', current);
    }
  </script>
</body>

</html>
