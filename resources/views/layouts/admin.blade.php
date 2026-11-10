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
  <script src="{{ asset('xivig-admin/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('xivig-admin/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('xivig-admin/vendor/quill/quill.js') }}"></script>
  <script src="{{ asset('xivig-admin/vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
  <script>
    if (typeof window.Swal === 'undefined' && typeof window.Sweetalert2 !== 'undefined') {
      window.Swal = window.Sweetalert2;
    }
    if (typeof window.swal === 'undefined' && typeof window.Swal !== 'undefined') {
      window.swal = window.Swal;
    }
  </script>

  <!-- Template Main JS File -->
  <script src="{{ asset('xivig-admin/js/main-FIhSewOI.js') }}"></script>

  <!-- SweetAlert2 Helper Suite & Theme Toggle Script -->
  <script>
    // Universal SweetAlert reference
    const getSwal = () => (window.Swal || window.Sweetalert2 || window.swal || null);

    // Global SweetAlert2 Toast configuration
    window.SwalToast = (() => {
      const s = getSwal();
      if (!s || !s.mixin) return null;
      return s.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        didOpen: (toast) => {
          const swal = getSwal();
          if (swal && swal.stopTimer) {
            toast.addEventListener('mouseenter', swal.stopTimer);
            toast.addEventListener('mouseleave', swal.resumeTimer);
          }
        }
      });
    })();

    // Global toast helper
    window.showToast = function(message, icon = 'success') {
      if (window.SwalToast) {
        window.SwalToast.fire({
          icon: icon,
          title: message
        });
      } else {
        const s = getSwal();
        if (s && s.fire) {
          s.fire({
            icon: icon,
            title: message,
            timer: 3000,
            showConfirmButton: false
          });
        } else {
          alert(message);
        }
      }
    };

    // Global Confirm Dialog helper (bulletproof SweetAlert2 + native fallback)
    window.showConfirmDialog = function(options = {}, onConfirm) {
      const opts = typeof options === 'string' ? { title: options } : (options || {});
      const title = opts.title || 'Are you sure?';
      const text = opts.text || 'This action cannot be undone.';
      const icon = opts.icon || 'warning';
      const confirmButtonText = opts.confirmButtonText || 'Yes, Proceed';
      const cancelButtonText = opts.cancelButtonText || 'Cancel';
      const isDanger = (icon === 'warning' || icon === 'error' || (opts.confirmButtonColor && opts.confirmButtonColor.includes('dc2626')));
      const confirmButtonClass = opts.confirmButtonClass || (isDanger ? 'btn btn-danger px-4 rounded-pill' : 'btn btn-primary px-4 rounded-pill');
      const cancelButtonClass = opts.cancelButtonClass || 'btn btn-outline-secondary px-4 rounded-pill me-2';

      const s = getSwal();

      if (!s || !s.fire) {
        if (window.confirm(text ? (title + '\n' + text) : title)) {
          if (typeof onConfirm === 'function') {
            try { onConfirm(); } catch (e) { console.error('Confirm callback error:', e); }
          }
          return Promise.resolve({ isConfirmed: true, value: true });
        }
        return Promise.resolve({ isConfirmed: false, value: false });
      }

      return s.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        reverseButtons: opts.reverseButtons !== undefined ? opts.reverseButtons : true,
        customClass: {
          popup: 'rounded-4 shadow-lg border-0',
          confirmButton: confirmButtonClass,
          cancelButton: cancelButtonClass
        },
        buttonsStyling: false
      }).then((result) => {
        if (result && (result.isConfirmed || result.value === true)) {
          if (typeof onConfirm === 'function') {
            try {
              onConfirm();
            } catch (err) {
              console.error('Error executing confirm callback:', err);
            }
          }
        }
        return result;
      }).catch((err) => {
        console.warn('SweetAlert encounter:', err);
        if (window.confirm(text ? (title + '\n' + text) : title)) {
          if (typeof onConfirm === 'function') {
            onConfirm();
          }
        }
      });
    };

    var showConfirmDialog = window.showConfirmDialog;
    var showToast = window.showToast;

    document.addEventListener('submit', function(e) {
      const form = e.target && e.target.closest ? e.target.closest('form.js-swal-confirm') : null;
      if (!form || form.dataset.swalConfirmed === '1') {
        return;
      }
      e.preventDefault();
      window.showConfirmDialog({
        title: form.dataset.confirmTitle || 'Are you sure?',
        text: form.dataset.confirmText || 'This action cannot be undone.',
        icon: form.dataset.confirmIcon || 'warning',
        confirmButtonText: form.dataset.confirmButton || 'Yes, Delete',
        confirmButtonColor: '#dc2626'
      }, function() {
        form.dataset.swalConfirmed = '1';
        HTMLFormElement.prototype.submit.call(form);
      });
    });

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

  {{-- SweetAlert2 Session Flash Triggers --}}
  @if (session('success'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (window.showToast) {
          window.showToast(@json(session('success')), 'success');
        }
      });
    </script>
  @endif

  @if (session('error'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const s = window.Swal || window.Sweetalert2 || window.swal;
        if (s && s.fire) {
          s.fire({
            icon: 'error',
            title: 'Notice',
            text: @json(session('error')),
            customClass: { confirmButton: 'btn btn-primary px-4 rounded-pill' },
            buttonsStyling: false
          });
        }
      });
    </script>
  @endif

  @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const s = window.Swal || window.Sweetalert2 || window.swal;
        if (s && s.fire) {
          const errs = @json($errors->all()).map(e => '• ' + e).join('<br>');
          s.fire({
            icon: 'error',
            title: 'Please review the following:',
            html: '<div class="text-start mt-2 text-danger">' + errs + '</div>',
            customClass: { confirmButton: 'btn btn-primary px-4 rounded-pill' },
            buttonsStyling: false
          });
        }
      });
    </script>
  @endif

  <style>
    .swal2-container {
      z-index: 99999 !important;
    }
    .swal2-popup {
      border-radius: 1rem !important;
      font-family: 'Plus Jakarta Sans', sans-serif !important;
    }
    [data-bs-theme="dark"] .swal2-popup {
      background-color: #1e293b !important;
      color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .swal2-title,
    [data-bs-theme="dark"] .swal2-html-container {
      color: #f1f5f9 !important;
    }

    /* Normalize button groups and handle any form wrappers seamlessly */
    .btn-group {
      display: inline-flex !important;
      vertical-align: middle;
    }
    .btn-group > .btn,
    .btn-group > form > .btn {
      position: relative;
      flex: 1 1 auto;
    }
    .btn-group > .btn:not(:first-child),
    .btn-group > form:not(:first-child) > .btn,
    .btn-group > form + .btn {
      margin-left: -1px !important;
    }
    .btn-group > .btn:hover,
    .btn-group > .btn:focus,
    .btn-group > .btn:active,
    .btn-group > form > .btn:hover,
    .btn-group > form > .btn:focus,
    .btn-group > form > .btn:active {
      z-index: 2;
    }
    .btn-group > .btn:not(:first-child):not(:last-child),
    .btn-group > form:not(:first-child):not(:last-child) > .btn {
      border-radius: 0 !important;
    }
    .btn-group > .btn:first-child,
    .btn-group > form:first-child > .btn {
      border-top-left-radius: var(--bs-border-radius-sm, 0.25rem) !important;
      border-bottom-left-radius: var(--bs-border-radius-sm, 0.25rem) !important;
      border-top-right-radius: 0 !important;
      border-bottom-right-radius: 0 !important;
      margin-left: 0 !important;
    }
    .btn-group > .btn:last-child,
    .btn-group > form:last-child > .btn {
      border-top-right-radius: var(--bs-border-radius-sm, 0.25rem) !important;
      border-bottom-right-radius: var(--bs-border-radius-sm, 0.25rem) !important;
      border-top-left-radius: 0 !important;
      border-bottom-left-radius: 0 !important;
    }
    .btn-group > .btn:first-child:last-child,
    .btn-group > form:first-child:last-child > .btn {
      border-radius: var(--bs-border-radius-sm, 0.25rem) !important;
    }
    .btn-group > form.d-inline {
      display: inline-flex !important;
      margin: 0 !important;
    }
  </style>

  @stack('scripts')
</body>
</html>
