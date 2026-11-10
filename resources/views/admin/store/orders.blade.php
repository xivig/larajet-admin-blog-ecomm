@extends('layouts.admin')

@section('title', 'Store Orders & Fulfillment - XivigAdmin PRO')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
  <div>
    <h1 class="page-title mb-1">Orders &amp; License Fulfillment</h1>
    <nav class="breadcrumb mb-0">
      <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
      <span class="breadcrumb-item">E-Commerce Store</span>
      <span class="breadcrumb-item active">Orders</span>
    </nav>
  </div>
  <a href="{{ route('admin.store.orders.export') }}" class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
    <i class="bi bi-download me-1"></i> Export Orders CSV
  </a>
</div>

@include('admin.store.partials.nav')

<!-- Order KPI Strip -->
<div class="row g-3 mb-4">
  <div class="col-xl-3 col-sm-6">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-3">
        <div class="text-muted small fw-semibold">Confirmed Gross Revenue</div>
        <div class="fs-3 fw-bold text-success">{{ $currencySymbol }}{{ number_format($stats['gross_revenue'], 2) }}</div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-3">
        <div class="text-muted small fw-semibold">Paid &amp; Completed</div>
        <div class="fs-3 fw-bold text-primary">{{ $stats['paid_count'] }}</div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-3">
        <div class="text-muted small fw-semibold">Awaiting Settlement</div>
        <div class="fs-3 fw-bold text-warning">{{ $stats['pending_count'] }}</div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-3">
        <div class="text-muted small fw-semibold">Failed / Refunded</div>
        <div class="fs-3 fw-bold text-danger">{{ $stats['failed_count'] }}</div>
      </div>
    </div>
  </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-3">
    <form action="{{ route('admin.store.orders') }}" method="GET" class="row g-2 align-items-center">
      <div class="col-md-4">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-pill px-3" placeholder="Order #, email, name, or TXN ID...">
      </div>
      <div class="col-md-3">
        <select name="payment_status" class="form-select rounded-pill px-3">
          <option value="">All Payment Statuses</option>
          @foreach(['pending', 'paid', 'failed', 'refunded'] as $ps)
            <option value="{{ $ps }}" {{ request('payment_status') === $ps ? 'selected' : '' }}>{{ ucfirst($ps) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select name="order_status" class="form-select rounded-pill px-3">
          <option value="">All Order Statuses</option>
          @foreach(['pending', 'processing', 'completed', 'cancelled'] as $os)
            <option value="{{ $os }}" {{ request('order_status') === $os ? 'selected' : '' }}>{{ ucfirst($os) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary rounded-pill flex-grow-1 fw-semibold">Filter</button>
        <a href="{{ route('admin.store.orders') }}" class="btn btn-outline-secondary rounded-pill">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="ps-4">Order #</th>
          <th>Customer</th>
          <th>Gateway &amp; TXN</th>
          <th>Total</th>
          <th>Payment / Status</th>
          <th class="text-end pe-4">Manage</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td class="ps-4">
              <div class="fw-bold font-monospace">{{ $order->order_number }}</div>
              <small class="text-muted">{{ $order->created_at?->format('M d, Y H:i') }}</small>
            </td>
            <td>
              <div class="fw-semibold">{{ $order->customer_name }}</div>
              <div class="small text-muted">{{ $order->customer_email }}</div>
            </td>
            <td>
              <span class="badge bg-secondary-subtle text-body rounded-pill text-uppercase">{{ $order->payment_gateway }}</span>
              @if($order->transaction_id)
                <div class="small font-monospace text-muted mt-1">{{ $order->transaction_id }}</div>
              @endif
            </td>
            <td>
              <div class="fw-bold">{{ \App\Models\Product::currencySymbol($order->currency) }}{{ number_format((float) $order->total, 2) }}</div>
              <small class="text-muted">{{ $order->items->count() }} item(s)</small>
            </td>
            <td>
              @php
                $payColor = match($order->payment_status) { 'paid' => 'success', 'failed' => 'danger', 'refunded' => 'secondary', default => 'warning' };
              @endphp
              <span class="badge bg-{{ $payColor }}-subtle text-{{ $payColor }} rounded-pill text-uppercase">{{ $order->payment_status }}</span>
              <span class="badge bg-primary-subtle text-primary rounded-pill text-uppercase">{{ $order->order_status }}</span>
            </td>
            <td class="text-end pe-4">
              <a href="{{ route('ecomm.order.receipt', $order->order_number) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Open Public Receipt">
                <i class="bi bi-receipt"></i>
              </a>
              <button type="button"
                      class="btn btn-sm btn-outline-primary rounded-pill px-3 manage-order-btn"
                      data-order="{{ json_encode([
                          'number' => $order->order_number,
                          'update_url' => route('admin.store.orders.status', $order),
                          'customer' => $order->customer_name . ' (' . $order->customer_email . ')',
                          'payment_status' => $order->payment_status,
                          'order_status' => $order->order_status,
                          'items' => $order->items->map(fn ($i) => ['title' => $i->product_title, 'qty' => $i->quantity, 'total' => $i->line_total, 'license' => $i->license_key]),
                      ]) }}">
                Inspect / Update
              </button>
              <form action="{{ route('admin.store.orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive order {{ $order->order_number }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">No orders placed yet. Test checkout at <a href="{{ route('ecomm') }}" target="_blank">/ecomm</a>!</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($orders->hasPages())
    <div class="card-footer bg-transparent border-top px-4 py-3">
      {{ $orders->links() }}
    </div>
  @endif
</div>

<!-- Inspect & Update Order Modal -->
<div class="modal fade" id="manageOrderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="manageOrderForm" method="POST" class="modal-content rounded-4 border-0 shadow">
      @csrf
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold">Order <span id="ordModalNumber" class="font-monospace"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="small text-muted mb-3" id="ordModalCustomer"></div>

        <h6 class="fw-bold small text-uppercase text-muted mb-2">Items &amp; License Keys</h6>
        <div id="ordModalItems" class="mb-4 d-flex flex-column gap-2"></div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-bold">Payment Status (Setting "Paid" auto-generates license keys)</label>
            <select name="payment_status" id="ordModalPayStatus" class="form-select">
              <option value="pending">Pending</option>
              <option value="paid">Paid (Issue Licenses)</option>
              <option value="failed">Failed</option>
              <option value="refunded">Refunded</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">Order Fulfillment Status</label>
            <select name="order_status" id="ordModalOrderStatus" class="form-select">
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-3">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Update Order</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl = document.getElementById('manageOrderModal');
  var form = document.getElementById('manageOrderForm');
  var bsModal = null;

  document.querySelectorAll('.manage-order-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var o = JSON.parse(this.getAttribute('data-order') || '{}');
      form.action = o.update_url;
      document.getElementById('ordModalNumber').textContent = o.number || '';
      document.getElementById('ordModalCustomer').textContent = o.customer || '';
      document.getElementById('ordModalPayStatus').value = o.payment_status || 'pending';
      document.getElementById('ordModalOrderStatus').value = o.order_status || 'pending';

      var html = '';
      (o.items || []).forEach(function(item) {
        html += '<div class="p-2.5 rounded-3 border d-flex justify-content-between align-items-center">' +
          '<div><div class="fw-semibold small">' + item.title + ' (x' + item.qty + ')</div>' +
          '<div class="small font-monospace text-muted">' + (item.license ? 'Key: ' + item.license : 'Pending payment') + '</div></div>' +
          '<div class="fw-bold small">' + Number(item.total).toFixed(2) + '</div>' +
        '</div>';
      });
      document.getElementById('ordModalItems').innerHTML = html;

      if (window.bootstrap && window.bootstrap.Modal) {
        bsModal = bsModal || window.bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();
      }
    });
  });
});
</script>
@endsection
