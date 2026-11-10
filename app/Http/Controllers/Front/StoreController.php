<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\Setting;
use App\Services\PaymentGatewayService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public const CART_SESSION_KEY = 'ecomm_cart';
    public const COUPON_SESSION_KEY = 'ecomm_coupon';

    /**
     * Public Storefront Catalog (/ecomm).
     */
    public function index(Request $request): View
    {
        $settings = Setting::getAll();
        $storeEnabled = $settings->get('store_enabled', '1') === '1';
        $currency = $settings->get('store_currency', 'USD') ?: 'USD';
        $currencySymbol = Product::currencySymbol($currency);

        $products = Product::active()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $categories = $products->groupBy('category')->map(fn ($items) => $items->count());
        $gateways = PaymentGateway::enabled()->get();
        $cart = $this->buildCartSummary();

        return view('ecomm', compact(
            'products',
            'categories',
            'gateways',
            'cart',
            'settings',
            'storeEnabled',
            'currency',
            'currencySymbol'
        ));
    }

    /**
     * Return JSON cart summary for interactive UI drawer / badges.
     */
    public function getCart(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'cart' => $this->buildCartSummary(),
        ]);
    }

    /**
     * Add product item to session cart (supports AJAX and normal POST).
     */
    public function addToCart(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
            'buy_now' => ['nullable', 'boolean'],
        ]);

        $product = Product::active()->find($validated['product_id']);
        if (!$product) {
            return $this->cartErrorResponse($request, 'This product is currently unavailable.', 404);
        }

        $qtyToAdd = (int) ($validated['quantity'] ?? 1);
        $cart = session(self::CART_SESSION_KEY, []);
        $key = (string) $product->id;
        $newQty = (int) ($cart[$key]['quantity'] ?? 0) + $qtyToAdd;

        if (!$product->isInStock($newQty)) {
            return $this->cartErrorResponse($request, "Insufficient stock for {$product->title}.");
        }

        $cart[$key] = [
            'product_id' => $product->id,
            'quantity' => $newQty,
        ];
        session([self::CART_SESSION_KEY => $cart]);

        $summary = $this->buildCartSummary();

        if ($request->boolean('buy_now')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$product->title} added — redirecting to checkout...",
                    'cart' => $summary,
                    'redirect_url' => route('ecomm.checkout'),
                ]);
            }
            return redirect()->route('ecomm.checkout');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$product->title} added to cart.",
                'cart' => $summary,
            ]);
        }

        return back()->with('success', "{$product->title} added to cart.");
    }

    /**
     * Update cart item quantity or remove when quantity <= 0.
     */
    public function updateCart(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0', 'max:50'],
        ]);

        $cart = session(self::CART_SESSION_KEY, []);
        $key = (string) $validated['product_id'];
        $qty = (int) $validated['quantity'];

        if ($qty <= 0) {
            unset($cart[$key]);
        } else {
            $product = Product::active()->find($validated['product_id']);
            if (!$product || !$product->isInStock($qty)) {
                return $this->cartErrorResponse($request, 'Requested quantity exceeds available stock.');
            }
            $cart[$key] = [
                'product_id' => $product->id,
                'quantity' => $qty,
            ];
        }

        session([self::CART_SESSION_KEY => $cart]);
        if (empty($cart)) {
            session()->forget(self::COUPON_SESSION_KEY);
        }

        $summary = $this->buildCartSummary();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'cart' => $summary,
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    /**
     * Clear cart and applied coupon.
     */
    public function clearCart(Request $request): JsonResponse|RedirectResponse
    {
        session()->forget([self::CART_SESSION_KEY, self::COUPON_SESSION_KEY]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $this->buildCartSummary(),
            ]);
        }

        return back()->with('success', 'Cart cleared.');
    }

    /**
     * Apply or remove promotional coupon code.
     */
    public function applyCoupon(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->boolean('remove')) {
            session()->forget(self::COUPON_SESSION_KEY);
            $summary = $this->buildCartSummary();
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Promo code removed.', 'cart' => $summary])
                : back()->with('success', 'Promo code removed.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $code = strtoupper(trim($validated['code']));
        $coupon = Coupon::whereRaw('UPPER(code) = ?', [$code])->first();

        if (!$coupon) {
            return $this->cartErrorResponse($request, "Promo code '{$code}' was not found.", 422);
        }

        $current = $this->buildCartSummary();
        if ($errorMsg = $coupon->validateForSubtotal($current['subtotal'])) {
            return $this->cartErrorResponse($request, $errorMsg, 422);
        }

        session([self::COUPON_SESSION_KEY => $coupon->code]);
        $updated = $this->buildCartSummary();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Promo code {$coupon->code} applied!",
                'cart' => $updated,
            ]);
        }

        return back()->with('success', "Promo code {$coupon->code} applied!");
    }

    /**
     * Checkout page showing billing form, cart totals & admin-enabled gateways.
     */
    public function checkout(): View|RedirectResponse
    {
        if (Setting::get('store_enabled', '1') !== '1') {
            return redirect()->route('ecomm')->with('error', 'Store checkout is currently paused by administrator.');
        }

        $cart = $this->buildCartSummary();
        if (empty($cart['items'])) {
            return redirect()->route('ecomm')->with('error', 'Your shopping cart is empty. Add a toolkit or blueprint first.');
        }

        $gateways = PaymentGateway::enabled()->get();
        if ($gateways->isEmpty()) {
            return redirect()->route('ecomm')->with('error', 'No payment gateway is currently enabled in Admin Settings.');
        }

        $user = Auth::user();
        return view('ecomm-checkout', compact('cart', 'gateways', 'user'));
    }

    /**
     * Validate order, persist inside transaction, clear cart & trigger payment gateway.
     */
    public function processCheckout(Request $request, PaymentGatewayService $gatewayService): RedirectResponse
    {
        if (Setting::get('store_enabled', '1') !== '1') {
            return redirect()->route('ecomm')->with('error', 'Store checkout is currently paused.');
        }

        $cart = $this->buildCartSummary();
        if (empty($cart['items'])) {
            return redirect()->route('ecomm')->with('error', 'Your shopping cart is empty.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:120'],
            'billing_address' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['nullable', 'string', 'max:100'],
            'billing_country' => ['required', 'string', 'max:60'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_gateway' => ['required', 'string'],
        ]);

        $gateway = PaymentGateway::where('driver', $validated['payment_gateway'])
            ->where('is_enabled', true)
            ->first();

        if (!$gateway) {
            return back()->withInput()->with('error', 'Selected payment gateway is unavailable.');
        }

        // Re-verify live inventory & create authoritative Order inside transaction
        $order = DB::transaction(function () use ($validated, $cart, $gateway, $request) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'company_name' => $validated['company_name'] ?? null,
                'billing_address' => $validated['billing_address'] ?? null,
                'billing_city' => $validated['billing_city'] ?? null,
                'billing_country' => $validated['billing_country'],
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $cart['subtotal'],
                'discount_amount' => $cart['discount'],
                'tax_amount' => $cart['tax'],
                'total' => $cart['total'],
                'currency' => $cart['currency'],
                'coupon_code' => $cart['coupon_code'],
                'payment_gateway' => $gateway->driver,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'ip_address' => $request->ip(),
            ]);

            foreach ($cart['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if (!$product || !$product->is_active || !$product->isInStock($item['quantity'])) {
                    throw new \RuntimeException("Product '{$item['title']}' is no longer available in requested quantity.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'product_sku' => $product->sku,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'],
                    'download_url' => $product->download_url,
                ]);
            }

            if (!empty($cart['coupon_code'])) {
                Coupon::where('code', $cart['coupon_code'])->increment('uses_count');
            }

            return $order->load('items');
        });

        $authorizedOrders = (array) session('ecomm_authorized_orders', []);
        if (!in_array($order->order_number, $authorizedOrders, true)) {
            $authorizedOrders[] = $order->order_number;
            session(['ecomm_authorized_orders' => $authorizedOrders]);
        }

        session()->forget([self::CART_SESSION_KEY, self::COUPON_SESSION_KEY]);

        try {
            $initiation = $gatewayService->initiatePayment($order, $gateway);
            return redirect()->away($initiation['redirect_url']);
        } catch (\Throwable $e) {
            return redirect()->route('ecomm.order.receipt', $order->order_number)
                ->with('error', 'Order created, but gateway initialization encountered an error: ' . $e->getMessage());
        }
    }

    /**
     * Hosted Sandbox Simulator / Razorpay JS Bridge page.
     */
    public function sandboxGateway(Request $request, string $driver, string $orderNumber): View|RedirectResponse
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        if ($order->isPaid()) {
            return redirect()->route('ecomm.order.receipt', $order->order_number);
        }

        $gateway = PaymentGateway::where('driver', $driver)->firstOrFail();

        $isLiveRazorpayModal = $driver === 'razorpay'
            && $gateway->hasLiveApiCredentials()
            && !empty(data_get($order->payment_payload, 'razorpay_order_id'));

        if (!$isLiveRazorpayModal && (app()->isProduction() || $gateway->mode === 'live' || $gateway->hasLiveApiCredentials())) {
            abort(403, 'Sandbox payment terminal is disabled for live or production gateways.');
        }

        $currencySymbol = Product::currencySymbol($order->currency);

        return view('ecomm-gateway', compact('order', 'gateway', 'driver', 'currencySymbol'));
    }

    /**
     * Handle payment gateway callback (Live API verification or Sandbox decision).
     */
    public function gatewayCallback(Request $request, string $driver, string $orderNumber, PaymentGatewayService $gatewayService): RedirectResponse
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        if ($order->isPaid()) {
            return redirect()->route('ecomm.order.receipt', $order->order_number)
                ->with('success', 'Payment confirmed! Your digital licenses are unlocked below.');
        }

        $ok = $gatewayService->handleCallback($driver, $order, $request);

        if ($ok) {
            return redirect()->route('ecomm.order.receipt', $order->order_number)
                ->with('success', 'Payment authorized successfully! Instant digital licenses generated.');
        }

        return redirect()->route('ecomm.order.receipt', $order->order_number)
            ->with('error', 'Payment could not be verified or was declined. You may retry checkout below.');
    }

    /**
     * Asynchronous Payment Gateway Webhook Endpoint (Stripe / Razorpay).
     */
    public function webhook(Request $request, string $driver, PaymentGatewayService $gatewayService): JsonResponse
    {
        $gateway = PaymentGateway::where('driver', $driver)->first();
        if (!$gateway) {
            return response()->json(['error' => 'Gateway not configured'], 404);
        }

        $payload = $request->all();

        if ($driver === 'stripe') {
            $webhookSecret = (string) $gateway->configValue('webhook_secret');
            if ($webhookSecret === '' || str_contains($webhookSecret, '••')) {
                return response()->json(['error' => 'Stripe webhook secret not configured'], 403);
            }

            $sigHeader = (string) $request->header('Stripe-Signature');
            if (!$this->verifyStripeSignature($request->getContent(), $sigHeader, $webhookSecret)) {
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            if (($payload['type'] ?? '') === 'checkout.session.completed') {
                $orderNumber = data_get($payload, 'data.object.client_reference_id');
                $txId = (string) (data_get($payload, 'data.object.payment_intent') ?: data_get($payload, 'data.object.id'));
                if ($orderNumber && ($order = Order::where('order_number', $orderNumber)->first())) {
                    $gatewayService->markOrderPaid($order, $txId, ['webhook_event' => $payload['type']]);
                }
            }

            return response()->json(['received' => true]);
        }

        if ($driver === 'razorpay') {
            $webhookSecret = (string) $gateway->configValue('webhook_secret');
            if ($webhookSecret === '' || str_contains($webhookSecret, '••')) {
                return response()->json(['error' => 'Razorpay webhook secret not configured'], 403);
            }

            $sigHeader = (string) $request->header('X-Razorpay-Signature');
            $expected = hash_hmac('sha256', $request->getContent(), $webhookSecret);
            if (!hash_equals($expected, $sigHeader)) {
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            if (($payload['event'] ?? '') === 'payment.captured') {
                $rpOrderId = data_get($payload, 'payload.payment.entity.order_id');
                $rpPayId = (string) data_get($payload, 'payload.payment.entity.id');
                $order = Order::where('payment_payload->razorpay_order_id', $rpOrderId)->first();
                if ($order) {
                    $gatewayService->markOrderPaid($order, $rpPayId, ['webhook_event' => 'payment.captured']);
                }
            }

            return response()->json(['received' => true]);
        }

        return response()->json(['error' => 'Unsupported webhook driver'], 400);
    }

    /**
     * Customer Order Receipt, License Key Vault & Bank Transfer Instructions.
     */
    public function orderReceipt(Request $request, string $orderNumber): View
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        $isAuthorizedAdmin = Auth::guard('admin')->check();
        $isAuthorizedOwner = Auth::check() && $order->user_id && (int) $order->user_id === (int) Auth::id();
        $isSessionCheckoutOrder = in_array($order->order_number, (array) session('ecomm_authorized_orders', []), true);

        if (!$isAuthorizedAdmin && !$isAuthorizedOwner && !$isSessionCheckoutOrder && !$request->hasValidSignature()) {
            abort(403, 'Unauthorized access to order receipt.');
        }

        $gateway = PaymentGateway::where('driver', $order->payment_gateway)->first();
        $currencySymbol = Product::currencySymbol($order->currency);

        return view('ecomm-receipt', compact('order', 'gateway', 'currencySymbol'));
    }

    /**
     * Authoritative cart calculation hydrated from live DB products & store settings.
     */
    protected function buildCartSummary(): array
    {
        $rawCart = session(self::CART_SESSION_KEY, []);
        $currency = Setting::get('store_currency', 'USD') ?: 'USD';
        $currencySymbol = Product::currencySymbol($currency);
        $taxRate = max(0.0, (float) Setting::get('store_tax_rate', '0'));

        $items = [];
        $subtotal = 0.0;
        $totalCount = 0;
        $validCart = [];

        if (!empty($rawCart)) {
            $products = Product::active()->whereIn('id', array_keys($rawCart))->get()->keyBy('id');

            foreach ($rawCart as $id => $entry) {
                $product = $products->get((int) $id);
                if (!$product) {
                    continue;
                }
                $qty = max(1, min(50, (int) ($entry['quantity'] ?? 1)));
                if ($product->stock_qty >= 0) {
                    $qty = min($qty, $product->stock_qty);
                    if ($qty <= 0) {
                        continue;
                    }
                }

                $unitPrice = $product->effective_price;
                $lineTotal = round($unitPrice * $qty, 2);

                $items[] = [
                    'product_id' => $product->id,
                    'slug' => $product->slug,
                    'sku' => $product->sku,
                    'title' => $product->title,
                    'category' => $product->category,
                    'image_url' => $product->resolved_image_url,
                    'unit_price' => $unitPrice,
                    'original_price' => (float) $product->price,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                ];

                $validCart[(string) $product->id] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                ];
                $subtotal += $lineTotal;
                $totalCount += $qty;
            }
        }

        // Persist sanitized cart
        if ($validCart !== $rawCart) {
            session([self::CART_SESSION_KEY => $validCart]);
        }

        $subtotal = round($subtotal, 2);
        $discount = 0.0;
        $appliedCouponCode = session(self::COUPON_SESSION_KEY);

        if ($appliedCouponCode && $subtotal > 0) {
            $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($appliedCouponCode)])->first();
            if ($coupon && $coupon->validateForSubtotal($subtotal) === null) {
                $discount = $coupon->calculateDiscount($subtotal);
                $appliedCouponCode = $coupon->code;
            } else {
                session()->forget(self::COUPON_SESSION_KEY);
                $appliedCouponCode = null;
            }
        } else {
            $appliedCouponCode = null;
        }

        $taxable = max(0.0, $subtotal - $discount);
        $tax = round($taxable * ($taxRate / 100), 2);
        $total = round($taxable + $tax, 2);

        return [
            'items' => $items,
            'count' => $totalCount,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax_rate' => $taxRate,
            'tax' => $tax,
            'total' => $total,
            'coupon_code' => $appliedCouponCode,
            'currency' => $currency,
            'currency_symbol' => $currencySymbol,
        ];
    }

    protected function cartErrorResponse(Request $request, string $message, int $status = 422): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'cart' => $this->buildCartSummary(),
            ], $status);
        }

        return back()->with('error', $message);
    }

    protected function verifyStripeSignature(string $payload, string $sigHeader, string $secret): bool
    {
        $parts = [];
        foreach (explode(',', $sigHeader) as $item) {
            [$k, $v] = array_pad(explode('=', trim($item), 2), 2, '');
            if ($k && $v) {
                $parts[$k] = $v;
            }
        }
        if (empty($parts['t']) || empty($parts['v1'])) {
            return false;
        }
        $signedPayload = $parts['t'] . '.' . $payload;
        $expectedSig = hash_hmac('sha256', $signedPayload, $secret);
        return hash_equals($expectedSig, $parts['v1']);
    }
}
