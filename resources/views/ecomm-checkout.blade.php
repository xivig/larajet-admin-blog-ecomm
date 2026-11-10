@extends('layouts.blog')
@section('title', 'Secure Checkout - Xivig Store')
@section('content')

<section class="page-title" style="padding: 40px 0 32px 0;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 12px;">
            <div>
                <span class="badge-elite badge-elite-dark mb-2">
                    <span class="badge-dot pulse"></span> Encrypted 256-Bit Checkout
                </span>
                <h1 class="font-weight-bold mb-1" style="letter-spacing: -0.02em;">Complete Your Order</h1>
                <p class="text-muted mb-0 small">Instant digital license keys are provisioned immediately upon payment authorization.</p>
            </div>
            <a href="{{ route('ecomm') }}" class="btn-elite btn-elite-outline btn-sm">
                <i class="bi bi-arrow-left me-1 mr-1"></i> Back to Catalog
            </a>
        </div>
    </div>
</section>

<section class="section py-5 ecomm-catalog">
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success rounded-4 mb-4"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger rounded-4 mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger rounded-4 mb-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ecomm.checkout.process') }}" method="POST" id="ecommCheckoutForm">
            @csrf
            <div class="row g-4">
                <!-- Left: Customer Details & Payment Gateway Selector -->
                <div class="col-lg-7">
                    <!-- 1. Billing & License Recipient -->
                    <div class="portfolio-kpi-card d-block p-4 mb-4 h-auto">
                        <h5 class="font-weight-bold mb-3 d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2 mr-2 d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 12px;">1</span>
                            Billing &amp; License Delivery Details
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small font-weight-bold mb-1">Full Name *</label>
                                <input type="text" name="customer_name" class="form-control form-control-elite" value="{{ old('customer_name', $user?->name) }}" placeholder="Jane Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold mb-1">Work Email (Receives Licenses) *</label>
                                <input type="email" name="customer_email" class="form-control form-control-elite" value="{{ old('customer_email', $user?->email) }}" placeholder="jane@company.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold mb-1">Phone / WhatsApp</label>
                                <input type="text" name="customer_phone" class="form-control form-control-elite" value="{{ old('customer_phone') }}" placeholder="+1 555 0199">
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold mb-1">Company / Organization</label>
                                <input type="text" name="company_name" class="form-control form-control-elite" value="{{ old('company_name') }}" placeholder="Acme Corp (Optional)">
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold mb-1">Country *</label>
                                <select name="billing_country" class="form-select form-control-elite" required>
                                    @foreach(['US' => 'United States', 'IN' => 'India', 'GB' => 'United Kingdom', 'DE' => 'Germany', 'FR' => 'France', 'CA' => 'Canada', 'AU' => 'Australia', 'SG' => 'Singapore', 'OTHER' => 'International'] as $code => $label)
                                        <option value="{{ $code }}" {{ old('billing_country', 'US') === $code ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold mb-1">City / Region</label>
                                <input type="text" name="billing_city" class="form-control form-control-elite" value="{{ old('billing_city') }}" placeholder="New York / London / Bengaluru">
                            </div>
                            <div class="col-12">
                                <label class="small font-weight-bold mb-1">Billing Street Address</label>
                                <input type="text" name="billing_address" class="form-control form-control-elite" value="{{ old('billing_address') }}" placeholder="100 Enterprise Plaza, Suite 400">
                            </div>
                            <div class="col-12">
                                <label class="small font-weight-bold mb-1">Order Notes / License Team Instructions</label>
                                <textarea name="notes" rows="2" class="form-control form-control-elite" placeholder="Optional VAT / PO number or deployment email...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Dynamic Admin-Controlled Payment Gateways -->
                    <div class="portfolio-kpi-card d-block p-4 h-auto">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap: 8px;">
                            <h5 class="font-weight-bold mb-0 d-flex align-items-center">
                                <span class="badge bg-primary rounded-circle me-2 mr-2 d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 12px;">2</span>
                                Select Payment Gateway
                            </h5>
                            <span class="small text-muted"><i class="bi bi-shield-check text-success me-1"></i> Configured via Admin Panel</span>
                        </div>

                        <div class="d-flex flex-column" style="gap: 12px;">
                            @foreach($gateways as $idx => $gw)
                                @php
                                    $selectedGw = old('payment_gateway', $gateways->first()->driver);
                                @endphp
                                <div class="position-relative">
                                    <input type="radio"
                                           name="payment_gateway"
                                           id="gw_{{ $gw->driver }}"
                                           value="{{ $gw->driver }}"
                                           class="d-none ecomm-gateway-radio"
                                           {{ $selectedGw === $gw->driver ? 'checked' : '' }}
                                           required>
                                    <label for="gw_{{ $gw->driver }}" class="ecomm-gateway-option mb-0">
                                        <span class="ecomm-gateway-icon">
                                            <i class="bi {{ $gw->icon ?: 'bi-credit-card-2-front-fill' }}"></i>
                                        </span>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                                <span class="font-weight-bold">{{ $gw->name }}</span>
                                                <span class="badge {{ $gw->mode === 'live' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill" style="font-size: 10px;">
                                                    {{ $gw->driver === 'cod_bank' ? 'Direct Invoice' : ($gw->hasLiveApiCredentials() && $gw->mode === 'live' ? 'LIVE API' : 'SANDBOX TEST') }}
                                                </span>
                                            </div>
                                            <small class="text-muted d-block mt-1">{{ $gw->subtitle }}</small>
                                        </div>
                                        <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right: Order Summary & Promo Code -->
                <div class="col-lg-5">
                    <div class="portfolio-kpi-card d-block p-4 h-auto sticky-top" style="top: 96px;">
                        <h5 class="font-weight-bold mb-3 d-flex align-items-center">
                            <i class="bi bi-receipt-cutoff text-primary me-2 mr-2"></i> Order Summary ({{ $cart['count'] }})
                        </h5>

                        <div class="d-flex flex-column mb-3" style="gap: 12px; max-height: 290px; overflow-y: auto;">
                            @foreach($cart['items'] as $item)
                                <div class="d-flex align-items-center pb-2 border-bottom" style="gap: 12px;">
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" class="rounded-3 flex-shrink-0" style="width: 52px; height: 52px; object-fit: cover;">
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="small fw-bold text-truncate">{{ $item['title'] }}</div>
                                        <small class="text-muted">Qty: {{ $item['quantity'] }} &middot; {{ $cart['currency_symbol'] }}{{ number_format($item['unit_price'], 2) }}</small>
                                    </div>
                                    <div class="fw-bold small">{{ $cart['currency_symbol'] }}{{ number_format($item['line_total'], 2) }}</div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Inline Promo Code Form (handled cleanly via JS post) -->
                        <div class="mb-3">
                            <label class="small text-muted fw-bold mb-1">Promo Code (e.g. ELITE20)</label>
                            <div class="position-relative w-100">
                                <input type="text" id="checkoutCouponInput" class="form-control form-control-elite" style="height: 46px; border-radius: 999px; padding: 8px 96px 8px 18px; font-size: 14px;" value="{{ $cart['coupon_code'] }}" placeholder="Enter promo code">
                                <button type="button" id="checkoutCouponApplyBtn" data-coupon-url="{{ route('ecomm.cart.coupon') }}" class="btn-elite btn-elite-primary btn-sm position-absolute" style="top: 50%; right: 5px; transform: translateY(-50%); border-radius: 999px; height: 36px; padding: 0 18px; display: inline-flex; align-items: center; justify-content: center; z-index: 5;">Apply</button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Subtotal</span>
                            <span>{{ $cart['currency_symbol'] }}{{ number_format($cart['subtotal'], 2) }}</span>
                        </div>
                        @if($cart['discount'] > 0)
                            <div class="d-flex justify-content-between small text-success mb-1">
                                <span>Discount ({{ $cart['coupon_code'] }})</span>
                                <span>-{{ $cart['currency_symbol'] }}{{ number_format($cart['discount'], 2) }}</span>
                            </div>
                        @endif
                        @if($cart['tax'] > 0)
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span>Tax ({{ $cart['tax_rate'] }}%)</span>
                                <span>{{ $cart['currency_symbol'] }}{{ number_format($cart['tax'], 2) }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2 border-top mb-4">
                            <span class="h6 font-weight-bold mb-0">Total Due</span>
                            <span class="h3 font-weight-bold text-primary mb-0" style="font-family: var(--font-heading);">
                                {{ $cart['currency_symbol'] }}{{ number_format($cart['total'], 2) }}
                            </span>
                        </div>

                        <button type="submit" class="btn-elite btn-elite-primary w-100 justify-content-center py-3">
                            <i class="bi bi-lock-fill me-2 mr-2"></i> Authorize &amp; Pay {{ $cart['currency_symbol'] }}{{ number_format($cart['total'], 2) }}
                        </button>

                        <div class="text-center mt-3 small text-muted">
                            <i class="bi bi-shield-check text-success me-1"></i> Instant license provisioning &amp; verifiable invoice receipt
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection
