@extends('layouts.admin')

@section('title', 'Promo Coupons - XivigAdmin PRO')

@section('content')
<div class="page-header mb-3">
  <h1 class="page-title mb-1">Promotional Discount Coupons</h1>
  <nav class="breadcrumb mb-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-item">E-Commerce Store</span>
    <span class="breadcrumb-item active">Promo Coupons</span>
  </nav>
</div>

@include('admin.store.partials.nav')

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Create New Coupon</h5>
        <form action="{{ route('admin.store.coupons.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label small fw-bold">Coupon Code *</label>
            <input type="text" name="code" class="form-control text-uppercase font-monospace" placeholder="SPRING25" required>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">Discount Type</label>
              <select name="type" class="form-select">
                <option value="percent">Percent (%)</option>
                <option value="fixed">Fixed Amount</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">Value *</label>
              <input type="number" step="0.01" min="0.01" name="value" class="form-control" value="20" required>
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">Min Subtotal</label>
              <input type="number" step="0.01" min="0" name="min_order_amount" class="form-control" placeholder="50.00">
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">Max Uses</label>
              <input type="number" min="1" name="max_uses" class="form-control" placeholder="Unlimited">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">Expiration Date (Optional)</label>
            <input type="date" name="expires_at" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold">Create Promo Code</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-4">Code</th>
              <th>Discount</th>
              <th>Rules &amp; Uses</th>
              <th>Status</th>
              <th class="text-end pe-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($coupons as $coupon)
              <tr>
                <td class="ps-4 fw-bold font-monospace">{{ $coupon->code }}</td>
                <td>
                  {{ $coupon->type === 'percent' ? number_format((float) $coupon->value, 0) . '% OFF' : '$' . number_format((float) $coupon->value, 2) . ' OFF' }}
                </td>
                <td>
                  <div class="small">Used: <strong>{{ $coupon->uses_count }}</strong> {{ $coupon->max_uses ? '/ ' . $coupon->max_uses : '(unlimited)' }}</div>
                  @if($coupon->min_order_amount)
                    <small class="text-muted">Min order: ${{ number_format((float) $coupon->min_order_amount, 2) }}</small>
                  @endif
                </td>
                <td>
                  <form action="{{ route('admin.store.coupons.toggle', $coupon) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm rounded-pill px-3 {{ $coupon->is_active ? 'btn-success-subtle text-success' : 'btn-secondary-subtle text-secondary' }}">
                      {{ $coupon->is_active ? 'Active' : 'Disabled' }}
                    </button>
                  </form>
                </td>
                <td class="text-end pe-4">
                  <form action="{{ route('admin.store.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete coupon {{ $coupon->code }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2"><i class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">No promo codes created yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
