<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('admin.partials.head')

<body>
  @include('admin.partials.header')
  @include('admin.partials.sidebar')

  <main class="main">
    <div class="main-content">

      {{-- Global Flash Messages --}}
      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
          <i class="bi bi-check-circle-fill me-2 fs-5"></i>
          <div>{{ session('success') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
          <div>{{ session('error') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
          <div class="d-flex align-items-center mb-2 font-weight-bold">
            <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
            <span>Please correct the errors below:</span>
          </div>
          <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      {{-- Main Page Content --}}
      @yield('content')

    </div>

    @include('admin.partials.footer')
  </main>

  <!-- Vendor JS Files -->
  <script src="{{ asset('admin-dist/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('admin-dist/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('admin-dist/vendor/quill/quill.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('admin-dist/js/main-4QRCOyTp.js') }}"></script>

  <!-- Theme Toggle & Datatables auto-init script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Dark/Light Theme Switching
      const themeToggleBtns = document.querySelectorAll('.theme-toggle');
      themeToggleBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
          const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
          document.documentElement.setAttribute('data-bs-theme', newTheme);
          document.documentElement.setAttribute('data-theme', newTheme);
          localStorage.setItem('xivig_admin_theme', newTheme);
          localStorage.setItem('xivig-theme', newTheme);
        });
      });

      // Auto-initialize simpleDatatables for any element with .datatable or .simple-datatable
      document.querySelectorAll('.simple-datatable, #postsDataTable, #categoriesTable, #tagsTable, #commentsTable').forEach(function(tableEl) {
        if (typeof simpleDatatables !== 'undefined' && tableEl) {
          new simpleDatatables.DataTable(tableEl, {
            perPage: 10,
            perPageSelect: [5, 10, 25, 50, 100],
            labels: {
              placeholder: 'Search records...',
              perPage: '{select} per page',
              noRows: 'No records found',
              info: 'Showing {start} to {end} of {rows} records'
            }
          });
        }
      });
    });
  </script>

  @stack('scripts')
</body>
</html>
