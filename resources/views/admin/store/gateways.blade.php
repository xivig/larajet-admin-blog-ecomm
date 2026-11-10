@extends('layouts.admin')

@section('title', 'Payment Gateways & Store Settings - XivigAdmin PRO')

@section('content')
<div class="page-header mb-3">
  <h1 class="page-title mb-1">Payment Gateways &amp; Store Controls</h1>
  <nav class="breadcrumb mb-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-item">E-Commerce Store</span>
    <span class="breadcrumb-item active">Payment Gateways</span>
  </nav>
</div>

@include('admin.store.partials.nav')

<!-- Global Store Engine Controls -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div>
        <h5 class="fw-bold mb-0"><i class="bi bi-sliders2 text-primary me-2"></i>Global Storefront Settings</h5>
        <div class="text-muted small">Control currency, tax percentage, announcement bar, and checkout availability on /ecomm.</div>
      </div>
    </div>

    <form action="{{ route('admin.store.settings.update') }}" method="POST">
      @csrf
      <div class="row g-3 align-items-end">
        <div class="col-md-2">
          <div class="form-check form-switch fs-6 mb-2">
            <input type="hidden" name="store_enabled" value="0">
            <input class="form-check-input" type="checkbox" name="store_enabled" id="storeEnabledSwitch" value="1"
              {{ ($settings->get('store_enabled', '1') === '1') ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="storeEnabledSwitch">Store Live</label>
          </div>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-bold">Currency</label>
          @php $cur = $settings->get('store_currency', 'USD'); @endphp
          <select name="store_currency" class="form-select">
            <option value="USD" {{ $cur === 'USD' ? 'selected' : '' }}>USD ($)</option>
            <option value="EUR" {{ $cur === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
            <option value="GBP" {{ $cur === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
            <option value="INR" {{ $cur === 'INR' ? 'selected' : '' }}>INR (₹)</option>
            <option value="JPY" {{ $cur === 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-bold">Tax Rate (%)</label>
          <input type="number" step="0.01" min="0" max="40" name="store_tax_rate" class="form-control" value="{{ $settings->get('store_tax_rate', '0') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold">Storefront Announcement Banner</label>
          <input type="text" name="store_announcement" class="form-control" value="{{ $settings->get('store_announcement') }}" placeholder="e.g. Use ELITE20 for 20% off">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold">Save Store</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Payment Gateways Grid -->
<div class="row g-4">
  @foreach($gateways as $gw)
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
              <i class="bi {{ $gw->icon ?: 'bi-credit-card' }} fs-4"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-0">{{ $gw->name }}</h5>
              <span class="small text-muted font-monospace">driver: {{ $gw->driver }}</span>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <span class="badge {{ $gw->mode === 'live' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill text-uppercase">
              {{ $gw->mode }}
            </span>
            <form action="{{ route('admin.store.gateways.toggle', $gw) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-sm rounded-pill px-3 fw-semibold {{ $gw->is_enabled ? 'btn-success' : 'btn-outline-secondary' }}">
                {{ $gw->is_enabled ? 'Enabled' : 'Disabled' }}
              </button>
            </form>
          </div>
        </div>

        <div class="card-body p-4">
          <form action="{{ route('admin.store.gateways.update', $gw) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
              <div class="col-md-7">
                <label class="form-label small fw-bold">Checkout Display Title</label>
                <input type="text" name="name" class="form-control" value="{{ $gw->name }}" required>
              </div>
              <div class="col-md-5">
                <label class="form-label small fw-bold">Environment Mode</label>
                <select name="mode" class="form-select">
                  <option value="sandbox" {{ $gw->mode === 'sandbox' ? 'selected' : '' }}>Sandbox (Test)</option>
                  <option value="live" {{ $gw->mode === 'live' ? 'selected' : '' }}>Live (Production)</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label small fw-bold">Checkout Subtitle</label>
                <input type="text" name="subtitle" class="form-control" value="{{ $gw->subtitle }}">
              </div>

              @if($gw->driver === 'stripe')
                <div class="col-12">
                  <label class="form-label small fw-bold">Stripe Publishable Key</label>
                  <input type="text" name="config[publishable_key]" class="form-control font-monospace" value="{{ $gw->configValue('publishable_key') }}" placeholder="pk_test_... or pk_live_...">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Stripe Secret Key</label>
                  <input type="password" name="config[secret_key]" class="form-control font-monospace" value="{{ \App\Models\PaymentGateway::maskSecret($gw->configValue('secret_key')) }}" placeholder="sk_test_... (leave empty in sandbox test)">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Webhook Signing Secret</label>
                  <input type="password" name="config[webhook_secret]" class="form-control font-monospace" value="{{ \App\Models\PaymentGateway::maskSecret($gw->configValue('webhook_secret')) }}" placeholder="whsec_...">
                </div>
                <div class="col-12">
                  <div class="p-2 rounded-3 bg-body-secondary small d-flex justify-content-between align-items-center">
                    <span class="text-truncate font-monospace">Webhook: {{ url('/webhooks/payment/stripe') }}</span>
                    <button type="button" class="btn btn-sm btn-link py-0" onclick="navigator.clipboard.writeText('{{ url('/webhooks/payment/stripe') }}'); showToast('Stripe Webhook URL copied!');">Copy</button>
                  </div>
                </div>
              @elseif($gw->driver === 'razorpay')
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Razorpay Key ID</label>
                  <input type="text" name="config[key_id]" class="form-control font-monospace" value="{{ $gw->configValue('key_id') }}" placeholder="rzp_test_...">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Razorpay Key Secret</label>
                  <input type="password" name="config[key_secret]" class="form-control font-monospace" value="{{ \App\Models\PaymentGateway::maskSecret($gw->configValue('key_secret')) }}" placeholder="Secret key">
                </div>
                <div class="col-12">
                  <label class="form-label small fw-bold">Webhook Secret</label>
                  <input type="password" name="config[webhook_secret]" class="form-control font-monospace" value="{{ \App\Models\PaymentGateway::maskSecret($gw->configValue('webhook_secret')) }}" placeholder="Optional webhook secret">
                </div>
                <div class="col-12">
                  <div class="p-2 rounded-3 bg-body-secondary small d-flex justify-content-between align-items-center">
                    <span class="text-truncate font-monospace">Webhook: {{ url('/webhooks/payment/razorpay') }}</span>
                    <button type="button" class="btn btn-sm btn-link py-0" onclick="navigator.clipboard.writeText('{{ url('/webhooks/payment/razorpay') }}'); showToast('Razorpay Webhook URL copied!');">Copy</button>
                  </div>
                </div>
              @elseif($gw->driver === 'paypal')
                <div class="col-md-6">
                  <label class="form-label small fw-bold">PayPal Client ID</label>
                  <input type="text" name="config[client_id]" class="form-control font-monospace" value="{{ $gw->configValue('client_id') }}" placeholder="PayPal REST Client ID">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">PayPal Client Secret</label>
                  <input type="password" name="config[client_secret]" class="form-control font-monospace" value="{{ \App\Models\PaymentGateway::maskSecret($gw->configValue('client_secret')) }}" placeholder="PayPal REST Secret">
                </div>
              @elseif($gw->driver === 'sandbox_test')
                <div class="col-12">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="config[auto_approve]" id="sandboxAutoApprove" value="1" {{ $gw->configValue('auto_approve') ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="sandboxAutoApprove">Instant Auto-Approve (skip interactive terminal screen)</label>
                  </div>
                </div>
              @elseif($gw->driver === 'cod_bank')
                <div class="col-12">
                  <label class="form-label small fw-bold">Wire / Bank Settlement Instructions</label>
                  <textarea name="config[bank_instructions]" rows="3" class="form-control font-monospace small">{{ $gw->configValue('bank_instructions') }}</textarea>
                </div>
              @endif

              <div class="col-md-6">
                <label class="form-label small fw-bold">Display Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ $gw->sort_order }}">
              </div>
              <div class="col-md-6 d-flex align-items-end">
                <input type="hidden" name="is_enabled" value="0">
                <div class="form-check form-switch mb-2">
                  <input class="form-check-input" type="checkbox" name="is_enabled" id="enableGw_{{ $gw->id }}" value="1" {{ $gw->is_enabled ? 'checked' : '' }}>
                  <label class="form-check-label small fw-bold" for="enableGw_{{ $gw->id }}">Active on Checkout</label>
                </div>
              </div>
            </div>

            <div class="pt-3 mt-3 border-top text-end">
              <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-sm">
                Save {{ $gw->name }} Settings
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endsection
