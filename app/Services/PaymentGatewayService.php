<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\AdminNotification;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    /**
     * Initiate checkout payment using the selected gateway configuration.
     *
     * @return array{redirect_url: string, driver: string, mode: string}
     */
    public function initiatePayment(Order $order, PaymentGateway $gateway): array
    {
        return match ($gateway->driver) {
            'stripe' => $this->initiateStripe($order, $gateway),
            'razorpay' => $this->initiateRazorpay($order, $gateway),
            'paypal' => $this->initiatePaypal($order, $gateway),
            'sandbox_test' => $this->initiateSandboxTest($order, $gateway),
            'cod_bank' => $this->initiateBankInvoice($order, $gateway),
            default => throw new \RuntimeException('Unsupported payment gateway selected.'),
        };
    }

    /**
     * Verify return/callback from gateway or sandbox terminal and finalize order state.
     */
    public function handleCallback(string $driver, Order $order, Request $request): bool
    {
        if ($order->isPaid()) {
            return true;
        }

        $gateway = PaymentGateway::where('driver', $driver)->first();

        // Explicit sandbox simulation test completion
        if ($request->filled('sandbox_decision')) {
            if ($request->input('sandbox_decision') === 'approve') {
                $txId = 'TXN-' . strtoupper($driver) . '-' . strtoupper(Str::random(10));
                $this->markOrderPaid($order, $txId, [
                    'gateway_mode' => 'sandbox_simulator',
                    'simulated_method' => $request->input('method_label', $driver),
                    'approved_at' => now()->toIso8601String(),
                ]);
                return true;
            }

            $order->update([
                'payment_status' => 'failed',
                'order_status' => 'cancelled',
                'payment_payload' => [
                    'gateway_mode' => 'sandbox_simulator',
                    'reason' => 'Payment declined in sandbox simulator',
                ],
            ]);
            return false;
        }

        return match ($driver) {
            'stripe' => $this->verifyStripeCallback($order, $gateway, $request),
            'razorpay' => $this->verifyRazorpayCallback($order, $gateway, $request),
            'paypal' => $this->verifyPaypalCallback($order, $gateway, $request),
            default => false,
        };
    }

    /**
     * Atomically mark an order paid, issue digital license keys, decrement inventory, and record audit logs.
     */
    public function markOrderPaid(Order $order, string $transactionId, array $payload = []): Order
    {
        if ($order->isPaid()) {
            return $order;
        }

        return DB::transaction(function () use ($order, $transactionId, $payload) {
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'completed',
                'transaction_id' => $transactionId,
                'payment_payload' => array_merge($order->payment_payload ?? [], $payload),
                'paid_at' => now(),
            ]);

            foreach ($order->items()->with('product')->get() as $item) {
                if (empty($item->license_key)) {
                    $item->license_key = sprintf(
                        'XIV-%s-%s-%s',
                        strtoupper(Str::random(4)),
                        strtoupper(Str::random(4)),
                        strtoupper(Str::random(4))
                    );
                }
                if ($item->product) {
                    $item->download_url = $item->download_url ?: $item->product->download_url;
                    if ($item->product->stock_qty > 0) {
                        $item->product->decrement('stock_qty', min($item->quantity, $item->product->stock_qty));
                    }
                    $item->product->increment('sales_count', $item->quantity);
                }
                $item->save();
            }

            try {
                AdminNotification::create([
                    'type' => 'success',
                    'title' => 'New Paid Store Order: ' . $order->order_number,
                    'message' => "{$order->customer_name} completed {$order->currency} " . number_format((float) $order->total, 2) . " via {$order->payment_gateway}.",
                    'link' => route('admin.store.orders'),
                    'is_read' => false,
                ]);
                ActivityLog::record(
                    'order.paid',
                    'Store',
                    "Order {$order->order_number} settled ({$transactionId}) via {$order->payment_gateway}.",
                    ['order_id' => $order->id, 'total' => $order->total]
                );
            } catch (\Throwable $e) {
                // Never block order settlement due to notification failure
            }

            return $order->fresh(['items']);
        });
    }

    /**
     * Initiate Stripe Checkout Session (Live REST API when keys configured, else Sandbox Simulator).
     */
    protected function initiateStripe(Order $order, PaymentGateway $gateway): array
    {
        if ($gateway->hasLiveApiCredentials()) {
            $secretKey = (string) $gateway->configValue('secret_key');
            $currency = strtolower($order->currency ?: 'usd');
            $zeroDecimal = in_array($currency, ['jpy', 'krw', 'vnd'], true);
            $unitMultiplier = $zeroDecimal ? 1 : 100;

            $response = Http::withToken($secretKey)
                ->asForm()
                ->timeout(15)
                ->post('https://api.stripe.com/v1/checkout/sessions', [
                    'mode' => 'payment',
                    'customer_email' => $order->customer_email,
                    'client_reference_id' => $order->order_number,
                    'success_url' => route('ecomm.callback', ['driver' => 'stripe', 'orderNumber' => $order->order_number]) . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('ecomm.order.receipt', $order->order_number) . '?cancelled=1',
                    'line_items[0][price_data][currency]' => $currency,
                    'line_items[0][price_data][product_data][name]' => 'Xivig Order ' . $order->order_number,
                    'line_items[0][price_data][product_data][description]' => $order->items->count() . ' Digital / Enterprise License Item(s)',
                    'line_items[0][price_data][unit_amount]' => (int) round((float) $order->total * $unitMultiplier),
                    'line_items[0][quantity]' => 1,
                ]);

            if ($response->successful() && ($checkoutUrl = $response->json('url'))) {
                $order->update([
                    'payment_payload' => [
                        'stripe_session_id' => $response->json('id'),
                        'gateway_mode' => $gateway->mode,
                    ],
                ]);
                return [
                    'redirect_url' => $checkoutUrl,
                    'driver' => 'stripe',
                    'mode' => $gateway->mode,
                ];
            }
        }

        return [
            'redirect_url' => route('ecomm.gateway.sandbox', ['driver' => 'stripe', 'orderNumber' => $order->order_number]),
            'driver' => 'stripe',
            'mode' => 'sandbox',
        ];
    }

    /**
     * Initiate Razorpay Order (Live REST API when keys configured, else Sandbox Simulator).
     */
    protected function initiateRazorpay(Order $order, PaymentGateway $gateway): array
    {
        if ($gateway->hasLiveApiCredentials()) {
            $keyId = (string) $gateway->configValue('key_id');
            $keySecret = (string) $gateway->configValue('key_secret');
            $currency = strtoupper($order->currency ?: 'USD');

            $response = Http::withBasicAuth($keyId, $keySecret)
                ->timeout(15)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => (int) round((float) $order->total * 100),
                    'currency' => $currency,
                    'receipt' => $order->order_number,
                    'notes' => [
                        'customer_email' => $order->customer_email,
                    ],
                ]);

            if ($response->successful() && ($rpOrderId = $response->json('id'))) {
                $order->update([
                    'payment_payload' => [
                        'razorpay_order_id' => $rpOrderId,
                        'gateway_mode' => $gateway->mode,
                    ],
                ]);
                return [
                    'redirect_url' => route('ecomm.gateway.sandbox', [
                        'driver' => 'razorpay',
                        'orderNumber' => $order->order_number,
                        'rp_order_id' => $rpOrderId,
                    ]),
                    'driver' => 'razorpay',
                    'mode' => $gateway->mode,
                ];
            }
        }

        return [
            'redirect_url' => route('ecomm.gateway.sandbox', ['driver' => 'razorpay', 'orderNumber' => $order->order_number]),
            'driver' => 'razorpay',
            'mode' => 'sandbox',
        ];
    }

    /**
     * Initiate PayPal REST v2 Order (Live/Sandbox API when keys configured, else Sandbox Simulator).
     */
    protected function initiatePaypal(Order $order, PaymentGateway $gateway): array
    {
        if ($gateway->hasLiveApiCredentials()) {
            $baseUrl = $gateway->mode === 'live'
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com';

            $tokenRes = Http::withBasicAuth(
                (string) $gateway->configValue('client_id'),
                (string) $gateway->configValue('client_secret')
            )->asForm()->timeout(15)->post($baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

            if ($tokenRes->successful() && ($accessToken = $tokenRes->json('access_token'))) {
                $orderRes = Http::withToken($accessToken)
                    ->timeout(15)
                    ->post($baseUrl . '/v2/checkout/orders', [
                        'intent' => 'CAPTURE',
                        'purchase_units' => [[
                            'reference_id' => $order->order_number,
                            'amount' => [
                                'currency_code' => strtoupper($order->currency ?: 'USD'),
                                'value' => number_format((float) $order->total, 2, '.', ''),
                            ],
                        ]],
                        'application_context' => [
                            'return_url' => route('ecomm.callback', ['driver' => 'paypal', 'orderNumber' => $order->order_number]),
                            'cancel_url' => route('ecomm.order.receipt', $order->order_number) . '?cancelled=1',
                        ],
                    ]);

                if ($orderRes->successful()) {
                    $approveLink = collect($orderRes->json('links', []))->firstWhere('rel', 'approve');
                    if (!empty($approveLink['href'])) {
                        $order->update([
                            'payment_payload' => [
                                'paypal_order_id' => $orderRes->json('id'),
                                'gateway_mode' => $gateway->mode,
                            ],
                        ]);
                        return [
                            'redirect_url' => $approveLink['href'],
                            'driver' => 'paypal',
                            'mode' => $gateway->mode,
                        ];
                    }
                }
            }
        }

        return [
            'redirect_url' => route('ecomm.gateway.sandbox', ['driver' => 'paypal', 'orderNumber' => $order->order_number]),
            'driver' => 'paypal',
            'mode' => 'sandbox',
        ];
    }

    /**
     * Local developer instant test gateway.
     */
    protected function initiateSandboxTest(Order $order, PaymentGateway $gateway): array
    {
        if ($gateway->configValue('auto_approve')) {
            $txId = 'TXN-TEST-' . strtoupper(Str::random(10));
            $this->markOrderPaid($order, $txId, ['gateway_mode' => 'auto_approved_sandbox']);
            return [
                'redirect_url' => route('ecomm.order.receipt', $order->order_number),
                'driver' => 'sandbox_test',
                'mode' => 'sandbox',
            ];
        }

        return [
            'redirect_url' => route('ecomm.gateway.sandbox', ['driver' => 'sandbox_test', 'orderNumber' => $order->order_number]),
            'driver' => 'sandbox_test',
            'mode' => 'sandbox',
        ];
    }

    /**
     * Direct Invoice / Wire Transfer gateway (provisional pending state for Admin verification).
     */
    protected function initiateBankInvoice(Order $order, PaymentGateway $gateway): array
    {
        $order->update([
            'payment_status' => 'pending',
            'order_status' => 'processing',
            'transaction_id' => 'WIRE-' . strtoupper(Str::random(8)),
            'payment_payload' => [
                'bank_instructions' => (string) $gateway->configValue('bank_instructions', 'Please reference your Order Number when sending your wire transfer.'),
            ],
        ]);

        try {
            AdminNotification::create([
                'type' => 'info',
                'title' => 'New Wire Transfer Order: ' . $order->order_number,
                'message' => "{$order->customer_name} placed {$order->currency} " . number_format((float) $order->total, 2) . ' awaiting bank confirmation.',
                'link' => route('admin.store.orders'),
                'is_read' => false,
            ]);
        } catch (\Throwable $e) {
        }

        return [
            'redirect_url' => route('ecomm.order.receipt', $order->order_number),
            'driver' => 'cod_bank',
            'mode' => $gateway->mode,
        ];
    }

    protected function verifyStripeCallback(Order $order, ?PaymentGateway $gateway, Request $request): bool
    {
        $sessionId = (string) $request->query('session_id');
        if (!$gateway || !$gateway->hasLiveApiCredentials() || $sessionId === '') {
            return false;
        }

        $res = Http::withToken((string) $gateway->configValue('secret_key'))
            ->timeout(15)
            ->get("https://api.stripe.com/v1/checkout/sessions/{$sessionId}");

        if ($res->successful() && $res->json('payment_status') === 'paid') {
            $txId = (string) ($res->json('payment_intent') ?: $sessionId);
            $this->markOrderPaid($order, $txId, ['stripe_session_id' => $sessionId]);
            return true;
        }

        return false;
    }

    protected function verifyRazorpayCallback(Order $order, ?PaymentGateway $gateway, Request $request): bool
    {
        $rpOrderId = (string) $request->input('razorpay_order_id');
        $rpPaymentId = (string) $request->input('razorpay_payment_id');
        $rpSignature = (string) $request->input('razorpay_signature');

        if (!$gateway || !$gateway->hasLiveApiCredentials() || !$rpOrderId || !$rpPaymentId || !$rpSignature) {
            return false;
        }

        $expected = hash_hmac('sha256', $rpOrderId . '|' . $rpPaymentId, (string) $gateway->configValue('key_secret'));
        if (hash_equals($expected, $rpSignature)) {
            $this->markOrderPaid($order, $rpPaymentId, [
                'razorpay_order_id' => $rpOrderId,
                'razorpay_payment_id' => $rpPaymentId,
            ]);
            return true;
        }

        return false;
    }

    protected function verifyPaypalCallback(Order $order, ?PaymentGateway $gateway, Request $request): bool
    {
        $token = (string) $request->query('token');
        if (!$gateway || !$gateway->hasLiveApiCredentials() || $token === '') {
            return false;
        }

        $baseUrl = $gateway->mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $tokenRes = Http::withBasicAuth(
            (string) $gateway->configValue('client_id'),
            (string) $gateway->configValue('client_secret')
        )->asForm()->timeout(15)->post($baseUrl . '/v1/oauth2/token', [
            'grant_type' => 'client_credentials',
        ]);

        if (!$tokenRes->successful() || !($accessToken = $tokenRes->json('access_token'))) {
            return false;
        }

        $captureRes = Http::withToken($accessToken)
            ->withBody('{}', 'application/json')
            ->timeout(15)
            ->post("{$baseUrl}/v2/checkout/orders/{$token}/capture");

        if ($captureRes->successful() && $captureRes->json('status') === 'COMPLETED') {
            $captureId = (string) (data_get($captureRes->json(), 'purchase_units.0.payments.captures.0.id') ?: $token);
            $this->markOrderPaid($order, $captureId, ['paypal_order_id' => $token]);
            return true;
        }

        return false;
    }
}
