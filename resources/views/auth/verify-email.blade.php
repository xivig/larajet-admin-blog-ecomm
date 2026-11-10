<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Email - Xivig</title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="icon" type="image/png" href="{{ asset('xivig-public/images/favicon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('xivig-admin/vendor/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('xivig-admin/css/main-BBIlupvD.css') }}?v={{ file_exists(public_path('xivig-admin/css/main-BBIlupvD.css')) ? filemtime(public_path('xivig-admin/css/main-BBIlupvD.css')) : '1' }}">

  <script>
    (function() {
      const saved = localStorage.getItem('xivig-theme') || localStorage.getItem('xivig_admin_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-bs-theme', saved);
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
</head>

<body class="fauth-page">
  <div class="fauth-ambient-canvas" aria-hidden="true">
    <div class="fauth-glow fauth-glow-indigo"></div>
    <div class="fauth-glow fauth-glow-rose"></div>
    <div class="fauth-glow fauth-glow-emerald"></div>
    <div class="fauth-grid-overlay"></div>
  </div>

  <div class="fauth-topbar">
    <button type="button" class="fauth-theme-btn theme-toggle" aria-label="Toggle color theme" title="Toggle color theme" onclick="toggleTheme()">
      <i class="bi bi-moon-stars theme-icon-dark"></i>
      <i class="bi bi-sun theme-icon-light"></i>
    </button>
  </div>

  <div class="fauth-container">
    <main class="fauth-main">
      <div class="fauth-brand-center">
        <a href="{{ url('/') }}" class="fauth-brand" aria-label="Xivig home">
          <span class="fauth-brand-logo">
            <img src="{{ asset('xivig-admin/img/xivig-logo-CDpcaf-E.png') }}" alt="Xivig" class="brand-logo-img">
          </span>
          <span class="fauth-brand-text">
            <strong class="brand-title">Xi<span class="brand-gradient">vig</span></strong>
            <span class="badge-elite">VERIFY</span>
          </span>
        </a>
      </div>

      <div class="fauth-card">
        <div class="fauth-card-head">
          <span class="fauth-kicker"><i class="bi bi-envelope-check"></i> Email Verification</span>
          <h1 class="fauth-title">Verify your email</h1>
          <p class="fauth-subtitle">Before continuing, please verify your email address by clicking the link we just emailed you. If you didn't receive it, request another link below.</p>
        </div>

        @if (session('status') == 'verification-link-sent')
          <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i> A new verification link has been sent to the email address provided in your profile settings.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form class="fauth-form" method="POST" action="{{ route('verification.send') }}">
          @csrf
          <button type="submit" class="btn fauth-btn-primary">
            <span>Resend Verification Email</span>
            <i class="bi bi-send"></i>
          </button>
        </form>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <a href="{{ route('profile.show') }}" class="fauth-link">Edit Profile</a>
          <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link p-0 text-decoration-none text-danger small fw-semibold">Log Out</button>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script src="{{ asset('xivig-admin/js/main-FIhSewOI.js') }}"></script>
  <script>
    function toggleTheme() {
      const current = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-bs-theme', current);
      document.documentElement.setAttribute('data-theme', current);
      localStorage.setItem('xivig-theme', current);
    }
  </script>
</body>

</html>
