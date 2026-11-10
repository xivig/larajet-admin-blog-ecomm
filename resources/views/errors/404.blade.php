<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Page Not Found - Error 404 | XivigAdmin</title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Favicons -->
  <link rel="icon" type="image/x-icon" href="{{ asset('xivig-admin/img/favicon-DgHNGhkV.ico') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('xivig-admin/img/favicon-32x32-ChisqE3Y.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('xivig-admin/img/favicon-16x16-BokJ4EXI.png') }}">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Vendor & Core CSS Files -->
  <link rel="stylesheet" href="{{ asset('xivig-admin/vendor/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('xivig-admin/css/main-BBIlupvD.css') }}">

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

  <div class="na-error-page na-error-404">
    <div class="na-error-shell">
      <header class="na-error-head">
        <a href="{{ route('admin.dashboard') }}" class="na-error-logo" aria-label="XivigAdmin home">
          <img src="{{ asset('xivig-admin/img/xivig-logo-CDpcaf-E.png') }}" alt="XivigAdmin" class="na-error-logo-img">
          <span class="brand-title">Xivig<span class="brand-gradient">Admin</span></span>
          <span class="badge-elite">PRO</span>
        </a>
        <span class="na-error-status"><span class="status-dot"></span> Error 404</span>
      </header>

      <div class="na-error-layout">
        <div class="na-error-main">
          <div>
            <h1 class="na-error-code">404</h1>
            <h2 class="na-error-title">Page not found in workspace</h2>
            <p class="na-error-text">The requested resource could not be located. It may have been relocated, decommissioned, or you might have followed an expired link.</p>

            <!-- Integrated Search Box with Button Inside -->
            <form class="na-error-search-wrap" action="{{ route('admin.posts.index') }}" method="GET">
              <i class="bi bi-search na-error-search-icon"></i>
              <input type="search" name="q" class="na-error-search-input" placeholder="Search articles, categories, analytics, or docs..." autocomplete="off" required>
              <button type="submit" class="na-error-search-btn">
                <span>Search System</span>
                <i class="bi bi-arrow-right"></i>
              </button>
            </form>
          </div>

          <div class="na-error-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary"><i class="bi bi-house"></i> Home Dashboard</a>
            <button type="button" class="btn btn-outline-secondary" onclick="history.back()"><i class="bi bi-arrow-left"></i> Go Back</button>
          </div>
        </div>

        <aside class="na-error-side">
          <h6>Recommended Routes</h6>
          <a href="{{ route('admin.dashboard') }}" class="na-error-quick-link">
            <span><i class="bi bi-speedometer2 me-2"></i> Executive Dashboard</span>
            <i class="bi bi-arrow-right"></i>
          </a>
          <a href="{{ route('admin.posts.index') }}" class="na-error-quick-link">
            <span><i class="bi bi-journal-text me-2"></i> Editorial Articles</span>
            <i class="bi bi-arrow-right"></i>
          </a>
          <a href="{{ url('/blog') }}" class="na-error-quick-link">
            <span><i class="bi bi-newspaper me-2"></i> Public Journal</span>
            <i class="bi bi-arrow-right"></i>
          </a>
          <a href="{{ route('admin.file-manager.index') }}" class="na-error-quick-link">
            <span><i class="bi bi-folder2-open me-2"></i> File Manager</span>
            <i class="bi bi-arrow-right"></i>
          </a>
        </aside>
      </div>
    </div>
  </div>

  <script src="{{ asset('xivig-admin/js/main-FIhSewOI.js') }}"></script>
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
