<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create New Password - Xivig</title>
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
            <span class="badge-elite">MEMBER</span>
          </span>
        </a>
      </div>

      <div class="fauth-card">
        <div class="fauth-card-head">
          <span class="fauth-kicker"><i class="bi bi-shield-lock"></i> Account Security</span>
          <h1 class="fauth-title">Reset Password</h1>
          <p class="fauth-subtitle">Choose a strong, unique password for your account.</p>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <ul class="mb-0 ps-3 small">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form class="fauth-form" method="POST" action="{{ route('password.update') }}" novalidate>
          @csrf
          <input type="hidden" name="token" value="{{ $request->route('token') }}">

          <div class="fauth-field">
            <label for="email" class="form-label">Email address</label>
            <div class="fauth-input-wrap">
              <i class="bi bi-envelope fauth-input-icon"></i>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            </div>
          </div>

          <div class="fauth-field">
            <label for="password" class="form-label">New Password</label>
            <div class="fauth-input-wrap fauth-input-wrap-toggle">
              <i class="bi bi-lock fauth-input-icon"></i>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="At least 8 characters" required autocomplete="new-password">
              <button class="btn fauth-btn-toggle" type="button" data-pwd-target="password" aria-label="Show password">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="fauth-field">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="fauth-input-wrap fauth-input-wrap-toggle">
              <i class="bi bi-shield-check fauth-input-icon"></i>
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repeat password" required autocomplete="new-password">
              <button class="btn fauth-btn-toggle" type="button" data-pwd-target="password_confirmation" aria-label="Show confirm password">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn fauth-btn-primary">
            <span>Reset Password</span>
            <i class="bi bi-arrow-right"></i>
          </button>
        </form>
      </div>
    </main>

    <footer class="fauth-footer">
      <div class="fauth-footer-copy">
        &copy; {{ date('Y') }} <a href="{{ url('/') }}" class="fw-semibold">Xivig</a>. Crafted for modern digital teams.
      </div>
    </footer>
  </div>

  <script src="{{ asset('xivig-admin/js/main-FIhSewOI.js') }}"></script>
  <script>
    document.querySelectorAll('[data-pwd-target]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const input = document.getElementById(btn.dataset.pwdTarget);
        const icon = btn.querySelector('i');
        if (!input || !icon) return;
        const hidden = input.type === 'password';
        input.type = hidden ? 'text' : 'password';
        icon.className = hidden ? 'bi bi-eye-slash' : 'bi bi-eye';
      });
    });

    function toggleTheme() {
      const current = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-bs-theme', current);
      document.documentElement.setAttribute('data-theme', current);
      localStorage.setItem('xivig-theme', current);
    }
  </script>
</body>

</html>
