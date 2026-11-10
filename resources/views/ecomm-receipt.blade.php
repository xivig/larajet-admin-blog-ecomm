@extends('layouts.blog')
@section('title', 'Order Receipt ' . $order->order_number . ' - Xivig Store')
@section('content')

<section class="page-title" style="padding: 95px 0 40px 0;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 12px;">
            <div>
                <span class="badge-elite badge-elite-dark mb-2">
                    <span class="badge-dot pulse"></span> Official Order Receipt
                </span>
                <h1 class="font-weight-bold mb-1" style="letter-spacing: -0.02em;">Order #{{ $order->order_number }}</h1>
                <p class="text-muted mb-0 small">Placed on {{ $order->created_at?->format('M d, Y \a\t h:i A') }}</p>
            </div>
            <div class="d-flex flex-wrap" style="gap: 10px;">
                <button type="button" onclick="window.print()" class="btn-elite btn-elite-outline btn-sm">
                    <i class="bi bi-printer me-1 mr-1"></i> Print Invoice
                </button>
                <a href="{{ route('ecomm') }}" class="btn-elite btn-elite-primary btn-sm">
                    <i class="bi bi-shop me-1 mr-1"></i> Return to Store
                </a>
            </div>
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

        <!-- Order Status Banner -->
        <div class="portfolio-kpi-card d-block p-4 mb-4 h-auto">
            <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 16px;">
                <div class="d-flex align-items-center" style="gap: 14px;">
                    @if($order->isPaid())
                        <div class="portfolio-kpi-icon bg-success-subtle text-success">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold text-success mb-1">Payment Confirmed &amp; Licenses Unlocked</h5>
                            <div class="small text-muted">
                                Settled via <strong>{{ $gateway?->name ?? strtoupper($order->payment_gateway) }}</strong>
                                &middot; Transaction ID: <span class="font-monospace fw-bold">{{ $order->transaction_id }}</span>
                            </div>
                        </div>
                    @elseif($order->payment_status === 'failed')
                        <div class="portfolio-kpi-icon bg-danger-subtle text-danger">
                            <i class="bi bi-x-octagon-fill"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold text-danger mb-1">Payment Failed / Declined</h5>
                            <div class="small text-muted">Authorization did not complete. Retry below using any enabled payment method.</div>
                        </div>
                    @else
                        <div class="portfolio-kpi-icon bg-warning-subtle text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold text-warning mb-1">Awaiting Payment Settlement</h5>
                            <div class="small text-muted">
                                Selected Method: <strong>{{ $gateway?->name ?? strtoupper($order->payment_gateway) }}</strong>
                                @if($order->transaction_id) &middot; Ref: <span class="font-monospace">{{ $order->transaction_id }}</span> @endif
                            </div>
                        </div>
                    @endif
                </div>

                @if(!$order->isPaid() && $order->payment_gateway !== 'cod_bank')
                    <a href="{{ route('ecomm.gateway.sandbox', ['driver' => $order->payment_gateway, 'orderNumber' => $order->order_number]) }}" class="btn-elite btn-elite-primary btn-sm">
                        <i class="bi bi-credit-card me-1 mr-1"></i> Complete / Retry Payment
                    </a>
                @endif
            </div>

            @if(!$order->isPaid() && $order->payment_gateway === 'cod_bank')
                <div class="mt-4 p-3 rounded-4 border">
                    <h6 class="font-weight-bold mb-2"><i class="bi bi-bank2 text-primary me-1 mr-1"></i> Bank / Wire Settlement Instructions</h6>
                    <pre class="mb-0 small text-muted" style="white-space: pre-wrap; font-family: inherit;">{{ data_get($order->payment_payload, 'bank_instructions', $gateway?->configValue('bank_instructions')) }}</pre>
                </div>
            @endif
        </div>

        <div class="row g-4">
            <!-- Items & Unlocked License Key Vault -->
            <div class="col-lg-8">
                <div class="portfolio-kpi-card d-block p-4 h-auto">
                    <h5 class="font-weight-bold mb-3">Purchased Toolkits &amp; License Vault</h5>

                    <div class="d-flex flex-column" style="gap: 14px;">
                        @foreach($order->items as $item)
                            <div class="p-3 rounded-4 border">
                                <div class="d-flex flex-wrap justify-content-between align-items-start mb-2" style="gap: 10px;">
                                    <div>
                                        <h6 class="font-weight-bold mb-1">{{ $item->product_title }}</h6>
                                        <small class="text-muted">SKU: {{ $item->product_sku }} &middot; Qty: {{ $item->quantity }} &times; {{ $currencySymbol }}{{ number_format((float) $item->unit_price, 2) }}</small>
                                    </div>
                                    <div class="h6 font-weight-bold text-primary mb-0">
                                        {{ $currencySymbol }}{{ number_format((float) $item->line_total, 2) }}
                                    </div>
                                </div>

                                @if($order->isPaid())
                                    <div class="pt-2 mt-2 border-top d-flex flex-wrap align-items-center justify-content-between" style="gap: 10px;">
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                            <span class="small text-muted">License Key:</span>
                                            <span class="ecomm-license-key-box">{{ $item->license_key }}</span>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary py-1 px-2"
                                                    onclick="navigator.clipboard.writeText('{{ $item->license_key }}'); this.innerHTML='<i class=\'bi bi-check2 text-success\'></i> Copied';">
                                                <i class="bi bi-clipboard"></i> Copy
                                            </button>
                                        </div>

                                        @if($item->download_url)
                                            <a href="{{ $item->download_url }}" target="_blank" rel="noopener" class="btn-elite btn-elite-primary btn-sm py-1 px-3" style="font-size: 12px;">
                                                <i class="bi bi-cloud-arrow-down-fill me-1 mr-1"></i> Download Package
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="pt-2 mt-2 border-top small text-muted">
                                        <i class="bi bi-lock-fill me-1"></i> License keys &amp; download packages unlock automatically once payment settles.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Billing & Price Totals -->
            <div class="col-lg-4">
                <div class="portfolio-kpi-card d-block p-4 h-auto">
                    <h6 class="font-weight-bold mb-3">Billing Summary</h6>

                    <div class="small mb-3">
                        <div class="fw-bold">{{ $order->customer_name }}</div>
                        <div class="text-muted">{{ $order->customer_email }}</div>
                        @if($order->company_name)<div class="text-muted">{{ $order->company_name }}</div>@endif
                        @if($order->billing_address)<div class="text-muted mt-1">{{ $order->billing_address }} {{ $order->billing_city }} {{ $order->billing_country }}</div>@endif
                    </div>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Subtotal</span>
                            <span>{{ $currencySymbol }}{{ number_format((float) $order->subtotal, 2) }}</span>
                        </div>
                        @if((float) $order->discount_amount > 0)
                            <div class="d-flex justify-content-between small text-success mb-1">
                                <span>Discount ({{ $order->coupon_code }})</span>
                                <span>-{{ $currencySymbol }}{{ number_format((float) $order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        @if((float) $order->tax_amount > 0)
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Tax</span>
                                <span>{{ $currencySymbol }}{{ number_format((float) $order->tax_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                            <span class="fw-bold">Total</span>
                            <span class="h4 fw-bold text-primary mb-0" style="font-family: var(--font-heading);">
                                {{ $currencySymbol }}{{ number_format((float) $order->total, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
