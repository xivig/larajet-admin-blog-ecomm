<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In - Xivig</title>
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
          <span class="fauth-kicker"><span class="fauth-kicker-dot"></span> Member Portal</span>
          <h1 class="fauth-title">Welcome back</h1>
          <p class="fauth-subtitle">Sign in to access your reader dashboard, saved insights, and store orders.</p>
        </div>

        @session('status')
          <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ $value }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endsession

        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center mb-1 fw-bold">
              <i class="bi bi-exclamation-triangle-fill me-2"></i> Sign In Notice
            </div>
            <ul class="mb-0 ps-3 small">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form class="fauth-form" method="POST" action="{{ route('login') }}" novalidate>
          @csrf

          <div class="fauth-field">
            <label for="email" class="form-label">Email address</label>
            <div class="fauth-input-wrap">
              <i class="bi bi-envelope fauth-input-icon"></i>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username">
            </div>
          </div>

          <div class="fauth-field">
            <div class="fauth-row-between">
              <label for="password" class="form-label">Password</label>
              @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="fauth-link">Forgot your password?</a>
              @endif
            </div>
            <div class="fauth-input-wrap fauth-input-wrap-toggle">
              <i class="bi bi-lock fauth-input-icon"></i>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••••••" required autocomplete="current-password">
              <button class="btn fauth-btn-toggle" type="button" id="togglePasswordBtn" aria-label="Show password">
                <i class="bi bi-eye" id="togglePasswordIcon"></i>
              </button>
            </div>
          </div>

          <div class="fauth-row-between mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember_me" name="remember" {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label" for="remember_me">Remember me</label>
            </div>
            <span class="text-muted small"><i class="bi bi-shield-check me-1 text-success"></i> Secure Session</span>
          </div>

          <button type="submit" class="btn fauth-btn-primary">
            <span>Log in</span>
            <i class="bi bi-arrow-right"></i>
          </button>
        </form>

        @if (Route::has('register'))
          <p class="fauth-foot-text">
            New to Xivig? <a href="{{ route('register') }}" class="fauth-link fw-semibold">Create an account</a>
          </p>
        @endif
      </div>
    </main>

    <footer class="fauth-footer">
      <div class="fauth-footer-copy">
        &copy; {{ date('Y') }} <a href="{{ url('/') }}" class="fw-semibold">Xivig</a>. Crafted for modern digital teams.
      </div>
      <div class="fauth-footer-links">
        <a href="{{ url('/') }}">Front Page</a>
        <span class="sep">&bull;</span>
        <a href="{{ url('/blog') }}">Blog Journal</a>
        <span class="sep">&bull;</span>
        <a href="{{ route('admin.login') }}">Admin Sign In</a>
      </div>
    </footer>
  </div>

  <script src="{{ asset('xivig-admin/js/main-FIhSewOI.js') }}"></script>
  <script>
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const pwdInput = document.getElementById('password');
    const pwdIcon = document.getElementById('togglePasswordIcon');

    if (toggleBtn && pwdInput) {
      toggleBtn.addEventListener('click', function() {
        if (pwdInput.type === 'password') {
          pwdInput.type = 'text';
          pwdIcon.className = 'bi bi-eye-slash';
        } else {
          pwdInput.type = 'password';
          pwdIcon.className = 'bi bi-eye';
        }
      });
    }

    function toggleTheme() {
      const current = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-bs-theme', current);
      document.documentElement.setAttribute('data-theme', current);
      localStorage.setItem('xivig-theme', current);
    }
  </script>
</body>

</html>
