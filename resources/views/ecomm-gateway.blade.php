@extends('layouts.blog')
@section('title', 'Payment Authorization Terminal - Xivig Store')
@section('content')

@php
    $rpOrderId = request()->query('rp_order_id') ?: data_get($order->payment_payload, 'razorpay_order_id');
    $useLiveRazorpayModal = $driver === 'razorpay' && $gateway->hasLiveApiCredentials() && !empty($rpOrderId);
@endphp

<section class="section py-5" style="min-height: 75vh;">
    <div class="container" style="padding-top: 60px;">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="portfolio-kpi-card d-block p-4 p-md-5 h-auto shadow-lg">
                    <div class="text-center mb-4">
                        <span class="badge-elite mb-2">
                            <span class="badge-dot pulse"></span>
                            {{ $useLiveRazorpayModal ? 'Razorpay Live Checkout' : 'Hosted Payment Authorization Terminal' }}
                        </span>
                        <h3 class="font-weight-bold mb-1">{{ $gateway->name }}</h3>
                        <p class="text-muted small mb-0">Order Reference: <strong class="font-monospace">{{ $order->order_number }}</strong></p>
                    </div>

                    <div class="p-3 rounded-4 border mb-4 text-center" style="background: rgba(79, 70, 229, 0.06);">
                        <div class="small text-muted mb-1">Authorizing Payment Amount</div>
                        <div class="display-6 font-weight-bold text-primary" style="font-family: var(--font-heading);">
                            {{ $currencySymbol }}{{ number_format((float) $order->total, 2) }} <small class="fs-6 text-muted">{{ $order->currency }}</small>
                        </div>
                        <div class="small text-muted mt-1">{{ $order->customer_name }} &middot; {{ $order->customer_email }}</div>
                    </div>

                    @if($useLiveRazorpayModal)
                        <div class="text-center">
                            <p class="small text-muted mb-4">Click below to launch the official Razorpay secure payment modal.</p>
                            <button type="button" id="launchRazorpayBtn" class="btn-elite btn-elite-primary w-100 justify-content-center py-3 mb-3">
                                <i class="bi bi-lightning-charge-fill me-2 mr-2"></i> Pay Now with Razorpay
                            </button>
                            <a href="{{ route('ecomm.order.receipt', $order->order_number) }}" class="btn btn-sm btn-link text-muted">
                                Cancel &amp; Return to Order
                            </a>
                        </div>

                        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var options = {
                                key: "{{ $gateway->configValue('key_id') }}",
                                amount: "{{ (int) round((float) $order->total * 100) }}",
                                currency: "{{ strtoupper($order->currency ?: 'USD') }}",
                                name: "{{ config('app.name', 'Xivig Store') }}",
                                description: "Order {{ $order->order_number }}",
                                order_id: "{{ $rpOrderId }}",
                                callback_url: "{{ route('ecomm.callback', ['driver' => 'razorpay', 'orderNumber' => $order->order_number]) }}",
                                prefill: {
                                    name: @json($order->customer_name),
                                    email: @json($order->customer_email),
                                    contact: @json($order->customer_phone ?? '')
                                },
                                theme: { color: "#4f46e5" }
                            };
                            var rzp = new Razorpay(options);
                            document.getElementById('launchRazorpayBtn')?.addEventListener('click', function() {
                                rzp.open();
                            });
                            rzp.open();
                        });
                        </script>
                    @else
                        <!-- Interactive Sandbox Gateway Test Terminal -->
                        <div class="alert alert-info small rounded-4 mb-4">
                            <i class="bi bi-info-circle-fill me-1 mr-1"></i>
                            <strong>Sandbox Simulator Mode:</strong> Configure live merchant credentials in
                            <strong>Admin &rarr; Store &rarr; Payment Gateways</strong> to switch to external processor redirects, or test payment states below.
                        </div>

                        <div class="p-3 rounded-4 border mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold"><i class="bi {{ $gateway->icon }} text-primary me-1"></i> Test Instrument</span>
                                <span class="badge bg-success-subtle text-success rounded-pill small">TLS Verified</span>
                            </div>
                            @if($driver === 'razorpay')
                                <div class="small font-monospace text-muted">UPI ID: success@razorpay &middot; Test Card: 4111 •••• •••• 1111</div>
                            @elseif($driver === 'paypal')
                                <div class="small font-monospace text-muted">PayPal Sandbox Buyer: sb-buyer@business.example.com</div>
                            @else
                                <div class="small font-monospace text-muted">Test Card: 4242 •••• •••• 4242 &middot; Exp: 12/29 &middot; CVC: 424</div>
                            @endif
                        </div>

                        <form action="{{ route('ecomm.callback', ['driver' => $driver, 'orderNumber' => $order->order_number]) }}" method="POST" class="d-grid gap-2">
                            @csrf
                            <input type="hidden" name="method_label" value="{{ $gateway->name }} ({{ $gateway->mode }})">
                            <button type="submit" name="sandbox_decision" value="approve" class="btn-elite btn-elite-primary w-100 justify-content-center py-3 mb-2">
                                <i class="bi bi-check-circle-fill me-2 mr-2"></i> Authorize &amp; Complete Payment ({{ $currencySymbol }}{{ number_format((float) $order->total, 2) }})
                            </button>
                            <button type="submit" name="sandbox_decision" value="decline" class="btn btn-outline-danger rounded-pill w-100 py-2 fw-semibold">
                                <i class="bi bi-x-circle me-1 mr-1"></i> Simulate Declined Card / Failure
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
