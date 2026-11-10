<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
  <ul class="nav nav-pills gap-2 bg-body-secondary p-1.5 rounded-4 shadow-2xs">
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('admin.store.products') ? 'active' : 'text-body' }} rounded-pill px-3 py-1.5 fw-semibold" href="{{ route('admin.store.products') }}">
        <i class="bi bi-box-seam me-1"></i> Products &amp; Catalog
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('admin.store.orders') ? 'active' : 'text-body' }} rounded-pill px-3 py-1.5 fw-semibold" href="{{ route('admin.store.orders') }}">
        <i class="bi bi-receipt-cutoff me-1"></i> Orders &amp; Fulfillment
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('admin.store.gateways') ? 'active' : 'text-body' }} rounded-pill px-3 py-1.5 fw-semibold" href="{{ route('admin.store.gateways') }}">
        <i class="bi bi-credit-card-2-front me-1"></i> Payment Gateways &amp; Settings
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('admin.store.coupons') ? 'active' : 'text-body' }} rounded-pill px-3 py-1.5 fw-semibold" href="{{ route('admin.store.coupons') }}">
        <i class="bi bi-ticket-perforated me-1"></i> Promo Coupons
      </a>
    </li>
  </ul>

  <a href="{{ route('ecomm') }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold btn-sm">
    <i class="bi bi-box-arrow-up-right me-1"></i> Open Live Store (/ecomm)
  </a>
</div>
