<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account - Xivig</title>
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
    <main class="fauth-main fauth-main-wide">
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
          <span class="fauth-kicker"><span class="fauth-kicker-dot"></span> New Member Registration</span>
          <h1 class="fauth-title">Create your account</h1>
          <p class="fauth-subtitle">Join the Xivig community to publish comments, manage orders, and save articles.</p>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center mb-1 fw-bold">
              <i class="bi bi-exclamation-triangle-fill me-2"></i> Please Check Your Details
            </div>
            <ul class="mb-0 ps-3 small">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form class="fauth-form" method="POST" action="{{ route('register') }}" novalidate>
          @csrf

          <div class="fauth-field">
            <label for="name" class="form-label">Full Name</label>
            <div class="fauth-input-wrap">
              <i class="bi bi-person fauth-input-icon"></i>
              <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Jane Doe" required autofocus autocomplete="name">
            </div>
          </div>

          <div class="fauth-field">
            <label for="email" class="form-label">Email address</label>
            <div class="fauth-input-wrap">
              <i class="bi bi-envelope fauth-input-icon"></i>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="jane@example.com" required autocomplete="username">
            </div>
          </div>

          <div class="row g-3">
            <div class="col-sm-6">
              <div class="fauth-field">
                <label for="password" class="form-label">Password</label>
                <div class="fauth-input-wrap fauth-input-wrap-toggle">
                  <i class="bi bi-lock fauth-input-icon"></i>
                  <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Min. 8 chars" required autocomplete="new-password">
                  <button class="btn fauth-btn-toggle" type="button" data-pwd-target="password" aria-label="Show password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="fauth-field">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="fauth-input-wrap fauth-input-wrap-toggle">
                  <i class="bi bi-shield-check fauth-input-icon"></i>
                  <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repeat password" required autocomplete="new-password">
                  <button class="btn fauth-btn-toggle" type="button" data-pwd-target="password_confirmation" aria-label="Show password confirmation">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            @php
              $termsUrl = Route::has('terms.show') ? route('terms.show') : url('/page/terms-and-conditions');
              $privacyUrl = Route::has('policy.show') ? route('policy.show') : url('/page/privacy-policy');
            @endphp
            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
              <label class="form-check-label text-muted small" for="terms">
                I agree to the <a href="{{ $termsUrl }}" target="_blank" class="fauth-link">Terms of Service</a> &amp; <a href="{{ $privacyUrl }}" target="_blank" class="fauth-link">Privacy Policy</a>
              </label>
            </div>
          @endif

          <button type="submit" class="btn fauth-btn-primary">
            <span>Register</span>
            <i class="bi bi-arrow-right"></i>
          </button>
        </form>

        <p class="fauth-foot-text">
          Already registered? <a href="{{ route('login') }}" class="fauth-link fw-semibold">Sign in here</a>
        </p>
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
        <a href="{{ url('/ecomm') }}">Store</a>
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
