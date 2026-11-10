<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirm Password - Xivig</title>
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
          <span class="fauth-kicker"><i class="bi bi-shield-lock"></i> Security Check</span>
          <h1 class="fauth-title">Confirm Password</h1>
          <p class="fauth-subtitle">This is a secure area of the application. Please confirm your password before continuing.</p>
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

        <form class="fauth-form" method="POST" action="{{ route('password.confirm') }}" novalidate>
          @csrf

          <div class="fauth-field">
            <label for="password" class="form-label">Password</label>
            <div class="fauth-input-wrap fauth-input-wrap-toggle">
              <i class="bi bi-lock fauth-input-icon"></i>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••••••" required autocomplete="current-password" autofocus>
              <button class="btn fauth-btn-toggle" type="button" id="togglePasswordBtn" aria-label="Show password">
                <i class="bi bi-eye" id="togglePasswordIcon"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn fauth-btn-primary">
            <span>Confirm</span>
            <i class="bi bi-arrow-right"></i>
          </button>
        </form>
      </div>
    </main>
  </div>

  <script src="{{ asset('xivig-admin/js/main-FIhSewOI.js') }}"></script>
  <script>
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const pwdInput = document.getElementById('password');
    const pwdIcon = document.getElementById('togglePasswordIcon');

    if (toggleBtn && pwdInput) {
      toggleBtn.addEventListener('click', function() {
        const hidden = pwdInput.type === 'password';
        pwdInput.type = hidden ? 'text' : 'password';
        pwdIcon.className = hidden ? 'bi bi-eye-slash' : 'bi bi-eye';
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
