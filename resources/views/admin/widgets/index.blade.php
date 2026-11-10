@extends('layouts.admin')

@section('title', 'Modular Widgets Management - XivigAdmin PRO')

@section('content')
<div class="page-widgets-management">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Modular Widgets &amp; Footer Columns</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">Widgets</span>
      </nav>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
      <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2">
        <i class="bi bi-box-arrow-up-right"></i>
        <span>Preview Site</span>
      </a>
      @if($hasMissingFooterCols)
        <form action="{{ route('admin.widgets.sync-footer-defaults') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-repeat"></i>
            <span>Sync Missing Footer Columns</span>
          </button>
        </form>
      @endif
      <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: #5551ff; border-color: #5551ff;" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
        <i class="bi bi-plus-lg fs-5"></i>
        <span>Add Widget</span>
      </button>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Placement Area Selector Pills -->
  <div class="d-flex flex-wrap gap-2 mb-4 p-2 bg-white rounded-4 shadow-sm border">
    @foreach($allAreas as $key => $areaLabel)
      <a href="{{ route('admin.widgets.index', ['area' => $key]) }}" class="btn rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 {{ $selectedArea === $key ? 'btn-primary text-white shadow-sm' : 'btn-light text-secondary' }}" style="{{ $selectedArea === $key ? 'background: #5551ff; border-color: #5551ff;' : '' }}">
        @if($key === 'sidebar')
          <i class="bi bi-layout-sidebar-inset"></i>
        @elseif(str_starts_with($key, 'footer'))
          <i class="bi bi-layout-three-columns"></i>
        @else
          <i class="bi bi-window-dock"></i>
        @endif
        <span>{{ $areaLabel }}</span>
        <span class="badge {{ $selectedArea === $key ? 'bg-white text-dark' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">
          {{ $areaCounts[$key] ?? 0 }}
        </span>
      </a>
    @endforeach
  </div>

  <!-- Active Widgets List in Current Area -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div>
        <h5 class="fw-bold mb-0">{{ $allAreas[$selectedArea] ?? 'Area' }} Widgets</h5>
        <small class="text-muted">
          @if(str_starts_with($selectedArea, 'footer_'))
            Synchronized live with the front-end footer column &mdash; reorder, edit copy, link menus, or toggle visibility
          @else
            Widgets render sequentially according to display order
          @endif
        </small>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span id="widgetOrderSyncBadge" class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 d-none">
          Order synchronized
        </span>
        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fw-semibold">
          {{ $widgets->where('status', true)->count() }} visible / {{ $widgets->count() }} total
        </span>
      </div>
    </div>

    <div class="p-4">
      <div class="row g-3" id="widgetsSortableList">
        @forelse($widgets as $widget)
          @php
            $typeMeta = $widgetTypes[$widget->widget_type] ?? ['label' => ucfirst($widget->widget_type), 'icon' => 'fas fa-puzzle-piece'];
            $settings = $widget->settings ?? [];
          @endphp
          <div class="col-12 js-widget-card" id="widget-card-{{ $widget->id }}" data-id="{{ $widget->id }}">
            <div class="card border rounded-4 shadow-none p-3 d-flex flex-md-row justify-content-between align-items-md-center gap-3 {{ $widget->status ? 'bg-white' : 'bg-light opacity-75' }}">
              <div class="d-flex align-items-center gap-3">
                <div class="btn-group-vertical btn-group-sm" role="group" aria-label="Reorder {{ $widget->title }}">
                  <button type="button" class="btn btn-outline-secondary py-0 px-1 js-widget-up" title="Move Up">
                    <i class="bi bi-chevron-up" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-outline-secondary py-0 px-1 js-widget-down" title="Move Down">
                    <i class="bi bi-chevron-down" style="font-size: 11px;"></i>
                  </button>
                </div>
                <div class="p-3 rounded-3 text-white d-flex align-items-center justify-content-center flex-shrink-0" style="background: linear-gradient(135deg, #4f46e5, #06b6d4); width: 48px; height: 48px;">
                  <i class="{{ $typeMeta['icon'] }} fs-5"></i>
                </div>
                <div>
                  <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h6 class="fw-bold mb-0 text-dark">{{ $widget->title }}</h6>
                    <span class="badge bg-primary-subtle text-primary px-2.5 py-1 rounded-pill small font-weight-normal">
                      {{ $typeMeta['label'] }}
                    </span>
                    @if($widget->status)
                      <span class="badge bg-success-subtle text-success rounded-pill small">Visible</span>
                    @else
                      <span class="badge bg-secondary-subtle text-secondary rounded-pill small">Hidden</span>
                    @endif
                  </div>
                  <small class="text-muted">
                    Order: <strong class="text-dark js-widget-order-num">#{{ $widget->order }}</strong>
                    @if(!empty($settings['menu_location']))
                      &middot; Menu: <code>{{ $settings['menu_location'] }}</code>
                    @endif
                    @if(!empty($settings['limit']))
                      &middot; Limit: {{ $settings['limit'] }} items
                    @endif
                    @if(!empty($settings['email']))
                      &middot; {{ $settings['email'] }}
                    @endif
                    @if(!empty($widget->content))
                      &middot; {{ Str::limit(strip_tags($widget->content), 60) }}
                    @endif
                  </small>
                </div>
              </div>

              <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Edit --}}
                <button type="button"
                        class="btn btn-sm btn-outline-primary rounded-pill px-3 js-edit-widget"
                        data-bs-toggle="modal"
                        data-bs-target="#editWidgetModal"
                        data-action="{{ route('admin.widgets.update', $widget->id) }}"
                        data-area="{{ $widget->area }}"
                        data-widget-type="{{ $widget->widget_type }}"
                        data-title="{{ $widget->title }}"
                        data-content="{{ $widget->content ?? '' }}"
                        data-limit="{{ $settings['limit'] ?? '' }}"
                        data-menu-location="{{ $settings['menu_location'] ?? '' }}"
                        data-email="{{ $settings['email'] ?? '' }}"
                        data-phone="{{ $settings['phone'] ?? '' }}"
                        data-ad-code="{{ $settings['ad_code'] ?? '' }}"
                        data-status="{{ $widget->status ? '1' : '0' }}">
                  <i class="bi bi-pencil me-1"></i> Edit
                </button>

                {{-- Toggle Status --}}
                <form action="{{ route('admin.widgets.toggle', $widget->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm {{ $widget->status ? 'btn-outline-secondary' : 'btn-outline-success' }} rounded-pill px-3" title="Toggle visibility">
                    {{ $widget->status ? 'Hide' : 'Show' }}
                  </button>
                </form>

                {{-- Delete --}}
                <form action="{{ route('admin.widgets.destroy', $widget->id) }}"
                      method="POST"
                      class="d-inline js-swal-confirm"
                      data-confirm-title="Remove Widget?"
                      data-confirm-text="Remove widget &quot;{{ $widget->title }}&quot; from {{ $allAreas[$selectedArea] ?? 'this slot' }}?"
                      data-confirm-button="Yes, Remove">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" title="Delete widget">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5">
            <i class="bi bi-layout-sidebar fs-1 text-muted opacity-50 d-block mb-2"></i>
            <h6 class="fw-bold text-muted">No widgets configured in {{ $allAreas[$selectedArea] ?? 'this slot' }}.</h6>
            <p class="text-muted small">Click "Add Widget" to place interactive modules in this column.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

</div>

<!-- Modal: Add Widget -->
<div class="modal fade" id="addWidgetModal" tabindex="-1" aria-labelledby="addWidgetModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <form action="{{ route('admin.widgets.store') }}" method="POST" id="addWidgetForm">
        @csrf
        <div class="modal-header border-bottom py-3 px-4">
          <h5 class="modal-title fw-bold" id="addWidgetModalLabel">Add Modular Widget</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          @include('admin.widgets._form-fields', ['prefix' => 'add'])
        </div>
        <div class="modal-footer border-top py-3 px-4">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: #5551ff; border-color: #5551ff;">Create Widget</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Edit Widget -->
<div class="modal fade" id="editWidgetModal" tabindex="-1" aria-labelledby="editWidgetModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <form action="" method="POST" id="editWidgetForm">
        @csrf
        @method('PUT')
        <div class="modal-header border-bottom py-3 px-4">
          <h5 class="modal-title fw-bold" id="editWidgetModalLabel">Edit Modular Widget</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          @include('admin.widgets._form-fields', ['prefix' => 'edit'])
        </div>
        <div class="modal-footer border-top py-3 px-4">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: #5551ff; border-color: #5551ff;">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const syncFieldVisibility = (formEl, type) => {
    if (!formEl) return;
    const toggle = (selector, show) => {
      const el = formEl.querySelector(selector);
      if (!el) return;
      el.classList.toggle('d-none', !show);
      el.querySelectorAll('input, select, textarea').forEach((field) => {
        field.disabled = !show;
      });
    };

    const showLimit = ['recent_posts', 'trending', 'popular', 'categories', 'tags', 'pages_list', 'archive', 'navigation_menu'].includes(type);
    const showMenu = ['navigation_menu', 'pages_list'].includes(type);
    const showBrand = type === 'brand_info';
    const showAd = ['ads', 'custom_html'].includes(type);

    toggle('.js-limit-group', showLimit);
    toggle('.js-menu-group', showMenu);
    toggle('.js-brand-group', showBrand);
    toggle('.js-ad-group', showAd);
  };

  [document.getElementById('addWidgetForm'), document.getElementById('editWidgetForm')].forEach((form) => {
    if (!form) return;
    const typeSelect = form.querySelector('.js-widget-type');
    if (typeSelect) {
      syncFieldVisibility(form, typeSelect.value);
      typeSelect.addEventListener('change', () => syncFieldVisibility(form, typeSelect.value));
    }
  });

  const editForm = document.getElementById('editWidgetForm');
  document.querySelectorAll('.js-edit-widget').forEach((btn) => {
    btn.addEventListener('click', () => {
      if (!editForm) return;
      editForm.action = btn.dataset.action || '';
      editForm.querySelector('.js-widget-area').value = btn.dataset.area || 'footer_1';
      editForm.querySelector('.js-widget-type').value = btn.dataset.widgetType || 'custom_html';
      editForm.querySelector('.js-widget-title').value = btn.dataset.title || '';
      editForm.querySelector('.js-widget-content').value = btn.dataset.content || '';
      editForm.querySelector('.js-widget-limit').value = btn.dataset.limit || '';
      editForm.querySelector('.js-widget-menu-location').value = btn.dataset.menuLocation || '';
      editForm.querySelector('.js-widget-email').value = btn.dataset.email || '';
      editForm.querySelector('.js-widget-phone').value = btn.dataset.phone || '';
      editForm.querySelector('.js-widget-ad-code').value = btn.dataset.adCode || '';
      editForm.querySelector('.js-widget-status').checked = btn.dataset.status === '1';
      syncFieldVisibility(editForm, editForm.querySelector('.js-widget-type').value);
    });
  });

  const listEl = document.getElementById('widgetsSortableList');
  const badgeEl = document.getElementById('widgetOrderSyncBadge');
  if (!listEl) return;

  const saveWidgetOrder = async () => {
    const cards = Array.from(listEl.querySelectorAll('.js-widget-card'));
    const order = cards.map((card, idx) => {
      const numEl = card.querySelector('.js-widget-order-num');
      if (numEl) numEl.textContent = '#' + (idx + 1);
      return Number(card.dataset.id);
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    const res = await fetch('{{ route('admin.widgets.reorder') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ order }),
    });

    if (res.ok && badgeEl) {
      badgeEl.classList.remove('d-none');
      clearTimeout(window.__widgetSyncTimer);
      window.__widgetSyncTimer = setTimeout(() => badgeEl.classList.add('d-none'), 2200);
    }
  };

  listEl.addEventListener('click', (e) => {
    const upBtn = e.target.closest('.js-widget-up');
    const downBtn = e.target.closest('.js-widget-down');
    if (!upBtn && !downBtn) return;

    const card = e.target.closest('.js-widget-card');
    if (!card) return;

    if (upBtn && card.previousElementSibling) {
      listEl.insertBefore(card, card.previousElementSibling);
      saveWidgetOrder();
    } else if (downBtn && card.nextElementSibling) {
      listEl.insertBefore(card.nextElementSibling, card);
      saveWidgetOrder();
    }
  });
});
</script>
@endpush
