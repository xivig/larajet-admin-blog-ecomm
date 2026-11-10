<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Two-Factor Challenge - Xivig</title>
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
            <span class="badge-elite">2FA</span>
          </span>
        </a>
      </div>

      <div class="fauth-card">
        <div class="fauth-card-head">
          <span class="fauth-kicker"><i class="bi bi-shield-check"></i> Verification</span>
          <h1 class="fauth-title">Two-Factor Security</h1>
          <p class="fauth-subtitle" id="twoFactorSubtitle">Please confirm access by entering the authentication code from your authenticator app.</p>
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

        <form class="fauth-form" method="POST" action="{{ route('two-factor.login') }}" novalidate>
          @csrf

          <div id="totpSection" class="fauth-field">
            <label for="code" class="form-label">Authenticator Code</label>
            <div class="fauth-input-wrap">
              <i class="bi bi-123 fauth-input-icon"></i>
              <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" inputmode="numeric" autofocus autocomplete="one-time-code" placeholder="••••••">
            </div>
          </div>

          <div id="recoverySection" class="fauth-field d-none">
            <label for="recovery_code" class="form-label">Emergency Recovery Code</label>
            <div class="fauth-input-wrap">
              <i class="bi bi-key fauth-input-icon"></i>
              <input type="text" class="form-control @error('recovery_code') is-invalid @enderror" id="recovery_code" name="recovery_code" autocomplete="one-time-code" placeholder="abcde-12345">
            </div>
          </div>

          <button type="submit" class="btn fauth-btn-primary">
            <span>Log in</span>
            <i class="bi bi-arrow-right"></i>
          </button>

          <div class="text-center mt-3">
            <button type="button" id="toggleRecoveryBtn" class="btn btn-link text-decoration-none text-muted small p-0">
              Use a recovery code
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script src="{{ asset('xivig-admin/js/main-FIhSewOI.js') }}"></script>
  <script>
    let recoveryMode = false;
    const totpSection = document.getElementById('totpSection');
    const recoverySection = document.getElementById('recoverySection');
    const subtitle = document.getElementById('twoFactorSubtitle');
    const toggleBtn = document.getElementById('toggleRecoveryBtn');

    if (toggleBtn) {
      toggleBtn.addEventListener('click', function() {
        recoveryMode = !recoveryMode;
        totpSection.classList.toggle('d-none', recoveryMode);
        recoverySection.classList.toggle('d-none', !recoveryMode);
        subtitle.textContent = recoveryMode
          ? 'Please confirm access by entering one of your emergency recovery codes.'
          : 'Please confirm access by entering the authentication code from your authenticator app.';
        toggleBtn.textContent = recoveryMode ? 'Use an authentication code' : 'Use a recovery code';
        document.getElementById(recoveryMode ? 'recovery_code' : 'code')?.focus();
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
