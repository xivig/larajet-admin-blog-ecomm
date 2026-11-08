<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'XivigAdmin - Elite Blog Command Center')</title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicons -->
  <link rel="icon" type="image/x-icon" href="{{ asset('admin-dist/img/favicon-DgHNGhkV.ico') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('admin-dist/img/favicon-32x32-ChisqE3Y.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin-dist/img/favicon-16x16-BokJ4EXI.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('admin-dist/img/apple-touch-icon-D_uz1V6f.png') }}">

  <!-- Google Fonts - Plus Jakarta Sans (Modern UI) + Outfit (Headings) + JetBrains Mono (Code/Metrics) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link rel="stylesheet" href="{{ asset('admin-dist/vendor/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('admin-dist/vendor/remixicon/remixicon.css') }}">
  <link rel="stylesheet" href="{{ asset('admin-dist/vendor/phosphor-icons/phosphor-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('admin-dist/vendor/quill/quill.snow.css') }}">
  <link rel="stylesheet" href="{{ asset('admin-dist/vendor/simple-datatables/style.css') }}">
  <link rel="stylesheet" href="{{ asset('admin-dist/vendor/sweetalert2/sweetalert2.min.css') }}">

  <!-- Core SweetAlert2 v11 Suite (Loaded in head for universal availability) -->
  <script src="{{ asset('admin-dist/vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
  <script>
    if (typeof window.Swal === 'undefined' && typeof window.Sweetalert2 !== 'undefined') {
      window.Swal = window.Sweetalert2;
    }
    if (typeof window.swal === 'undefined' && typeof window.Swal !== 'undefined') {
      window.swal = window.Swal;
    }
  </script>

  <!-- Template Main CSS File (Compiled SCSS + Bootstrap 5 + Dark/Light Mode) -->
  <link rel="stylesheet" href="{{ asset('admin-dist/css/main-DjChhAQn.css') }}">

  <!-- Theme Controller Pre-loader & Extension Error Guard -->
  <script>
    (function() {
      const saved = localStorage.getItem('xivig_admin_theme') || localStorage.getItem('xivig-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-bs-theme', saved);
      document.documentElement.setAttribute('data-theme', saved);

      // Catch and suppress browser extension port disconnects
      window.addEventListener('unhandledrejection', function(e) {
        if (e.reason && (e.reason.message || '').includes('Could not establish connection')) {
          e.preventDefault();
        }
      });
      window.addEventListener('error', function(e) {
        if ((e.message || '').includes('Could not establish connection')) {
          e.preventDefault();
        }
      });
    })();
  </script>

  <style>
    /* Professional polish for XivigAdmin Blog Center */
    .brand-logo-img { height: 32px; width: auto; object-fit: contain; }
    .nav-badge-pill { font-size: 0.72rem; padding: 0.2rem 0.55rem; border-radius: 999px; font-weight: 700; }
    .post-thumb-sm { width: 44px; height: 44px; border-radius: 8px; object-fit: cover; }
    .stat-metric-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .stat-metric-card:hover { transform: translateY(-2px); }
    .ql-container { min-height: 320px; font-size: 15px; font-family: 'Plus Jakarta Sans', sans-serif; }
    .badge-draft { background-color: rgba(234, 179, 8, 0.15); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.3); }
    .badge-published { background-color: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3); }
  </style>

  @stack('styles')
</head>
