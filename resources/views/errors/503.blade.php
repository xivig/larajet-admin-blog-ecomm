<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scheduled Maintenance - Error 503 | XivigAdmin</title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Favicons -->
  <link rel="icon" type="image/x-icon" href="{{ asset('admin-dist/img/favicon-DgHNGhkV.ico') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('admin-dist/img/favicon-32x32-ChisqE3Y.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin-dist/img/favicon-16x16-BokJ4EXI.png') }}">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Vendor & Core CSS Files -->
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

<body class="na-error-body">
  <!-- Ambient Glowing Mesh Canvas -->
  <div class="fauth-ambient-canvas" aria-hidden="true">
    <div class="fauth-glow fauth-glow-indigo"></div>
    <div class="fauth-glow fauth-glow-rose"></div>
    <div class="fauth-glow fauth-glow-emerald"></div>
    <div class="fauth-grid-overlay"></div>
  </div>

  <!-- Floating Theme Switcher Button -->
  <div class="fauth-topbar">
    <button type="button" class="fauth-theme-btn theme-toggle" aria-label="Toggle color theme" title="Toggle color theme" onclick="toggleTheme()">
      <i class="bi bi-moon-stars theme-icon-dark"></i>
      <i class="bi bi-sun theme-icon-light"></i>
    </button>
  </div>

  <div class="na-error-page na-error-maintenance">
    <div class="na-error-shell">
      <header class="na-error-head">
        <a href="{{ url('/') }}" class="na-error-logo" aria-label="XivigAdmin home">
          <img src="{{ asset('admin-dist/img/xivig-logo-CDpcaf-E.png') }}" alt="XivigAdmin" class="na-error-logo-img">
          <span class="brand-title">Xivig<span class="brand-gradient">Admin</span></span>
          <span class="badge-elite">PRO</span>
        </a>
        <span class="na-error-status"><span class="status-dot"></span> Maintenance Mode</span>
      </header>

      <div class="na-error-layout">
        <div class="na-error-main">
          <div>
            <span class="na-error-icon warning"><i class="bi bi-tools"></i></span>
            <h2 class="na-error-title">Scheduled infrastructure maintenance</h2>
            <p class="na-error-text">Our platform operations team is currently executing planned kernel updates and database optimizations. All stored state and analytics pipelines are completely protected.</p>

            <!-- Integrated Live Uptime Box with Button Inside -->
            <form class="na-error-search-wrap" action="#" method="POST" onsubmit="event.preventDefault(); alert('Subscribed! You will receive an immediate ping when systems return to 100% operational status.');">
              <i class="bi bi-broadcast na-error-search-icon"></i>
              <input type="email" class="na-error-search-input" placeholder="Enter email to get notified when systems are back online..." autocomplete="email" required>
              <button type="submit" class="na-error-search-btn btn-warning-gradient">
                <span>Notify Uptime</span>
                <i class="bi bi-bell-fill"></i>
              </button>
            </form>
          </div>

          <div class="na-error-actions">
            <button type="button" class="btn btn-primary" onclick="window.location.reload()"><i class="bi bi-arrow-clockwise"></i> Check Live Status</button>
            <a href="mailto:support@xivig.com" class="btn btn-outline-secondary"><i class="bi bi-envelope"></i> Contact Operations</a>
          </div>
        </div>

        <aside class="na-error-side">
          <h6>Maintenance Progress</h6>
          <div class="na-error-step"><i class="bi bi-check2-circle"></i> Service isolation activated</div>
          <div class="na-error-step active"><i class="bi bi-arrow-repeat"></i> Database cluster indexing in progress</div>
          <div class="na-error-step"><i class="bi bi-clock"></i> Verification &amp; automated smoke tests</div>

          <h6 class="mt-3">Status Desk</h6>
          <div class="small text-muted mb-2">Estimated completion: Under 15 minutes</div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" style="width: 70%;"></div>
          </div>
        </aside>
      </div>
    </div>
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
