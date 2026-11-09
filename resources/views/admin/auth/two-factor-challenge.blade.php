<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Two-Factor Authentication - XivigAdmin Enterprise</title>
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

  <style>
    .auth-code-input {
      letter-spacing: 0.35em;
      font-size: 1.5rem;
      text-align: center;
      font-family: 'JetBrains Mono', monospace;
      font-weight: 700;
    }
  </style>

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
            <span class="badge-elite">2FA SECURE</span>
          </span>
        </a>
      </div>

      <div class="fauth-card">
        <div class="fauth-card-head text-center">
          <div class="mx-auto mb-3 d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary" style="width: 60px; height: 60px;">
            <i class="bi bi-shield-lock-fill fs-2"></i>
          </div>
          <span class="fauth-kicker justify-content-center"><span class="fauth-kicker-dot"></span> Two-Step Verification</span>
          <h1 class="fauth-title" id="challengeTitle">Enter Security Code</h1>
          <p class="fauth-subtitle" id="challengeSubtitle">
            Open your <strong>Google Authenticator</strong> or TOTP app and enter the 6-digit authentication code.
          </p>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center mb-1 fw-bold">
              <i class="bi bi-exclamation-triangle-fill me-2"></i> Verification Notice
            </div>
            <ul class="mb-0 ps-3 small">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form class="fauth-form" method="POST" action="{{ route('admin.two-factor.challenge.store') }}" novalidate>
          @csrf

          <!-- TOTP 6-digit Code Input -->
          <div id="totpSection" class="fauth-field">
            <label for="code" class="form-label text-center d-block">6-Digit Authenticator Code</label>
            <div class="fauth-input-wrap">
              <input type="text" class="form-control auth-code-input @error('code') is-invalid @enderror" id="code" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="7" placeholder="••••••" autofocus autocomplete="one-time-code">
            </div>
            <div class="form-text text-center small mt-1">Codes rotate every 30 seconds</div>
          </div>

          <!-- Emergency Recovery Code Input -->
          <div id="recoverySection" class="fauth-field d-none">
            <label for="recovery_code" class="form-label text-center d-block">Emergency Recovery Code</label>
            <div class="fauth-input-wrap">
              <i class="bi bi-key fauth-input-icon"></i>
              <input type="text" class="form-control @error('recovery_code') is-invalid @enderror" id="recovery_code" name="recovery_code" placeholder="abcde-12345" autocomplete="one-time-code">
            </div>
            <div class="form-text text-center small mt-1">Each recovery code can only be used once</div>
          </div>

          <button type="submit" class="btn fauth-btn-primary mt-3">
            <span>Verify &amp; Continue</span>
            <i class="bi bi-arrow-right"></i>
          </button>

          <div class="text-center mt-3">
            <button type="button" id="toggleModeBtn" class="btn btn-link text-decoration-none text-muted small p-0">
              <i class="bi bi-arrow-left-right me-1"></i> <span id="toggleModeText">Lost device? Use emergency recovery code</span>
            </button>
          </div>

          <div class="text-center mt-3 pt-3 border-top">
            <a href="{{ route('admin.login') }}" class="text-muted small text-decoration-none">
              <i class="bi bi-box-arrow-left me-1"></i> Cancel and return to sign in
            </a>
          </div>
        </form>
      </div>
    </main>

    <footer class="fauth-footer">
      <div class="fauth-footer-copy">
        &copy; {{ date('Y') }} <a href="{{ url('/') }}" class="fw-semibold">XivigAdmin</a>. Enterprise Security Shield Active.
        <span class="badge-elite-sm">2FA Enforced</span>
      </div>
    </footer>
  </div>

  <script src="{{ asset('admin-dist/js/main-FIhSewOI.js') }}"></script>
  <script>
    let isRecoveryMode = false;
    const totpSection = document.getElementById('totpSection');
    const recoverySection = document.getElementById('recoverySection');
    const toggleBtn = document.getElementById('toggleModeBtn');
    const toggleText = document.getElementById('toggleModeText');
    const title = document.getElementById('challengeTitle');
    const subtitle = document.getElementById('challengeSubtitle');
    const codeInput = document.getElementById('code');
    const recoveryInput = document.getElementById('recovery_code');

    toggleBtn.addEventListener('click', function() {
      isRecoveryMode = !isRecoveryMode;
      if (isRecoveryMode) {
        totpSection.classList.add('d-none');
        recoverySection.classList.remove('d-none');
        toggleText.textContent = 'Have your authenticator app? Use 6-digit code';
        title.textContent = 'Emergency Recovery Code';
        subtitle.textContent = 'Enter one of the 8-character recovery codes you saved when enabling 2FA.';
        recoveryInput.focus();
      } else {
        recoverySection.classList.add('d-none');
        totpSection.classList.remove('d-none');
        toggleText.textContent = 'Lost device? Use emergency recovery code';
        title.textContent = 'Enter Security Code';
        subtitle.innerHTML = 'Open your <strong>Google Authenticator</strong> or TOTP app and enter the 6-digit authentication code.';
        codeInput.focus();
      }
    });

    // Theme toggle
    function toggleTheme() {
      const current = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-bs-theme', current);
      document.documentElement.setAttribute('data-theme', current);
      localStorage.setItem('xivig_admin_theme', current);
    }
  </script>
</body>

</html>
