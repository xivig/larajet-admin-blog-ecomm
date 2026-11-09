<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Server Anomaly - Error 500 | XivigAdmin</title>
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

  <div class="na-error-page na-error-500">
    <div class="na-error-shell">
      <header class="na-error-head">
        <a href="{{ route('admin.dashboard') }}" class="na-error-logo" aria-label="XivigAdmin home">
          <img src="{{ asset('admin-dist/img/xivig-logo-CDpcaf-E.png') }}" alt="XivigAdmin" class="na-error-logo-img">
          <span class="brand-title">Xivig<span class="brand-gradient">Admin</span></span>
          <span class="badge-elite">PRO</span>
        </a>
        <span class="na-error-status"><span class="status-dot"></span> Incident 500</span>
      </header>

      <div class="na-error-layout">
        <div class="na-error-main">
          <div>
            <span class="na-error-icon danger"><i class="bi bi-exclamation-octagon"></i></span>
            <h2 class="na-error-title">Internal cluster anomaly detected</h2>
            <p class="na-error-text">A temporary backend microservice incident interrupted this transaction. Automated monitoring has captured execution telemetry and self-healing orchestration is active.</p>

            <div class="na-error-meta">
              <span><i class="bi bi-activity"></i> Telemetry Logged</span>
              <span><i class="bi bi-cpu"></i> Runtime: PHP {{ PHP_VERSION }}</span>
              <span><i class="bi bi-arrow-repeat"></i> Status: Captured</span>
            </div>

            <!-- Integrated Notification Box with Button Inside -->
            <form class="na-error-search-wrap" action="#" method="POST" onsubmit="event.preventDefault(); alert('Incident subscription confirmed. System operations team has been alerted.');">
              <i class="bi bi-envelope-at na-error-search-icon"></i>
              <input type="email" class="na-error-search-input" placeholder="Notify me when incident is resolved (name@company.com)..." autocomplete="email" required>
              <button type="submit" class="na-error-search-btn btn-danger-gradient">
                <span>Subscribe</span>
                <i class="bi bi-bell-fill"></i>
              </button>
            </form>
          </div>

          <div class="na-error-actions">
            <button type="button" class="btn btn-primary" onclick="window.location.reload()"><i class="bi bi-arrow-clockwise"></i> Retry Request</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-house"></i> Return to Workspace</a>
          </div>
        </div>

        <aside class="na-error-side">
          <h6>System Status</h6>
          <div class="na-error-step active"><i class="bi bi-arrow-repeat"></i> Kernel incident auto-logged</div>
          <div class="na-error-step"><i class="bi bi-check2-circle"></i> Database connections verified</div>
          <div class="na-error-step"><i class="bi bi-check2-circle"></i> Data persistence secure</div>

          <h6 class="mt-3">Fast Diagnostics</h6>
          <a href="{{ route('admin.activity-logs.index') }}" class="na-error-quick-link">
            <span><i class="bi bi-journal-text me-2"></i> Audit Activity Logs</span>
            <i class="bi bi-arrow-right"></i>
          </a>
          <a href="{{ route('admin.backups.index') }}" class="na-error-quick-link">
            <span><i class="bi bi-database-check me-2"></i> System Backups</span>
            <i class="bi bi-arrow-right"></i>
          </a>
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
