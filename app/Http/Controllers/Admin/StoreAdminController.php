<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\Setting;
use App\Services\PaymentGatewayService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StoreAdminController extends Controller
{
    /**
     * Store Dashboard & Products Management (/admin/store/products).
     */
    public function products(Request $request): View
    {
        $query = Product::query();
        if ($request->input('view') === 'trash') {
            $query->onlyTrashed();
        }
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }
        if ($cat = trim((string) $request->input('category'))) {
            $query->where('category', $cat);
        }

        $products = $query->orderBy('sort_order')->orderByDesc('id')->paginate(15)->withQueryString();
        $categories = Product::query()->distinct()->pluck('category')->filter()->values();

        $stats = [
            'total_revenue' => (float) Order::where('payment_status', 'paid')->sum('total'),
            'paid_orders' => Order::where('payment_status', 'paid')->count(),
            'pending_orders' => Order::where('payment_status', 'pending')->count(),
            'active_products' => Product::where('is_active', true)->count(),
            'trash_count' => Product::onlyTrashed()->count(),
        ];

        $settings = Setting::getAll();
        $currencySymbol = Product::currencySymbol($settings->get('store_currency', 'USD') ?: 'USD');

        return view('admin.store.products', compact('products', 'categories', 'stats', 'currencySymbol'));
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);
        $data = $this->prepareProductPayload($request, $validated);

        $product = Product::create($data);

        try {
            ActivityLog::record('product.created', 'Store', "Created store product '{$product->title}' ({$product->sku}).", ['product_id' => $product->id]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.store.products')->with('success', "Product '{$product->title}' created successfully.");
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product->id);
        $data = $this->prepareProductPayload($request, $validated, $product);

        $product->update($data);

        try {
            ActivityLog::record('product.updated', 'Store', "Updated store product '{$product->title}'.", ['product_id' => $product->id]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.store.products')->with('success', "Product '{$product->title}' updated.");
    }

    public function toggleProduct(Product $product): RedirectResponse
    {
        $product->update(['is_active' => !$product->is_active]);
        $state = $product->is_active ? 'activated' : 'hidden from storefront';

        return back()->with('success', "Product '{$product->title}' {$state}.");
    }

    public function destroyProduct(Product $product): RedirectResponse
    {
        $title = $product->title;
        $product->delete();
        return back()->with('success', "Product '{$title}' moved to trash.");
    }

    public function restoreProduct(int $id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return back()->with('success', "Product '{$product->title}' restored.");
    }

    public function forceDeleteProduct(int $id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $title = $product->title;
        $product->forceDelete();
        return back()->with('success', "Product '{$title}' permanently deleted.");
    }

    /**
     * Orders & Fulfillment Console (/admin/store/orders).
     */
    public function orders(Request $request): View
    {
        $query = Order::with('items');

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('transaction_id', 'like', "%{$search}%");
            });
        }

        if ($payStatus = $request->input('payment_status')) {
            $query->where('payment_status', $payStatus);
        }

        if ($orderStatus = $request->input('order_status')) {
            $query->where('order_status', $orderStatus);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'gross_revenue' => (float) Order::where('payment_status', 'paid')->sum('total'),
            'paid_count' => Order::where('payment_status', 'paid')->count(),
            'pending_count' => Order::where('payment_status', 'pending')->count(),
            'failed_count' => Order::whereIn('payment_status', ['failed', 'refunded'])->count(),
        ];

        $currencySymbol = Product::currencySymbol(Setting::get('store_currency', 'USD') ?: 'USD');

        return view('admin.store.orders', compact('orders', 'stats', 'currencySymbol'));
    }

    public function updateOrderStatus(Request $request, Order $order, PaymentGatewayService $gatewayService): RedirectResponse
    {
        $validated = $request->validate([
            'order_status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
            'payment_status' => ['required', Rule::in(['pending', 'paid', 'failed', 'refunded'])],
        ]);

        if ($validated['payment_status'] === 'paid' && !$order->isPaid()) {
            $txId = $order->transaction_id ?: ('ADMIN-' . strtoupper(Str::random(8)));
            $gatewayService->markOrderPaid($order, $txId, ['manually_approved_by_admin' => true]);
            if ($validated['order_status'] !== 'completed') {
                $order->update(['order_status' => $validated['order_status']]);
            }
        } else {
            $order->update([
                'order_status' => $validated['order_status'],
                'payment_status' => $validated['payment_status'],
            ]);
        }

        return back()->with('success', "Order {$order->order_number} updated cleanly.");
    }

    public function exportOrders(): StreamedResponse
    {
        $orders = Order::with('items')->latest()->get();
        $filename = 'xivig-store-orders-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order #', 'Customer', 'Email', 'Company', 'Gateway', 'Payment Status', 'Order Status', 'Subtotal', 'Discount', 'Tax', 'Total', 'Currency', 'Transaction ID', 'Created At']);
            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_email,
                    $order->company_name,
                    $order->payment_gateway,
                    $order->payment_status,
                    $order->order_status,
                    $order->subtotal,
                    $order->discount_amount,
                    $order->tax_amount,
                    $order->total,
                    $order->currency,
                    $order->transaction_id,
                    $order->created_at?->toDateTimeString(),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function destroyOrder(Order $order): RedirectResponse
    {
        $num = $order->order_number;
        $order->delete();
        return back()->with('success', "Order {$num} archived.");
    }

    /**
     * Payment Gateways & Global Store Configuration (/admin/store/gateways).
     */
    public function gateways(): View
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();
        $settings = Setting::getAll();

        return view('admin.store.gateways', compact('gateways', 'settings'));
    }

    public function toggleGateway(PaymentGateway $gateway): RedirectResponse
    {
        $gateway->update(['is_enabled' => !$gateway->is_enabled]);
        $label = $gateway->is_enabled ? 'enabled' : 'disabled';

        return back()->with('success', "{$gateway->name} is now {$label} on checkout.");
    }

    public function updateGateway(Request $request, PaymentGateway $gateway): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'mode' => ['required', Rule::in(['sandbox', 'live'])],
            'is_enabled' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100'],
            'config' => ['nullable', 'array'],
        ]);

        $existingConfig = $gateway->config ?? [];
        $incomingConfig = $validated['config'] ?? [];
        $mergedConfig = $existingConfig;

        foreach ($incomingConfig as $key => $val) {
            // Skip overwriting saved secrets when left masked or blank unless explicitly cleared
            if (is_string($val) && str_contains($val, '••')) {
                continue;
            }
            if ($key === 'auto_approve') {
                $mergedConfig[$key] = (bool) $val;
                continue;
            }
            if ($val === null && in_array($key, ['secret_key', 'webhook_secret', 'key_secret', 'client_secret'], true)) {
                continue;
            }
            $mergedConfig[$key] = is_string($val) ? trim($val) : $val;
        }

        // Handle checkbox absence for sandbox_test auto_approve
        if ($gateway->driver === 'sandbox_test' && !array_key_exists('auto_approve', $incomingConfig)) {
            $mergedConfig['auto_approve'] = false;
        }

        $gateway->update([
            'name' => $validated['name'],
            'subtitle' => $validated['subtitle'] ?? $gateway->subtitle,
            'mode' => $validated['mode'],
            'is_enabled' => $request->boolean('is_enabled'),
            'sort_order' => (int) ($validated['sort_order'] ?? $gateway->sort_order),
            'config' => $mergedConfig,
        ]);

        try {
            ActivityLog::record('gateway.updated', 'Store', "Updated {$gateway->name} gateway ({$gateway->mode}).");
        } catch (\Throwable $e) {
        }

        return back()->with('success', "{$gateway->name} configuration saved.");
    }

    public function updateStoreSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_enabled' => ['nullable'],
            'store_currency' => ['required', Rule::in(['USD', 'EUR', 'GBP', 'INR', 'JPY'])],
            'store_tax_rate' => ['required', 'numeric', 'min:0', 'max:40'],
            'store_announcement' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::set('store_enabled', $request->boolean('store_enabled') ? '1' : '0', 'store');
        Setting::set('store_currency', $validated['store_currency'], 'store');
        Setting::set('store_tax_rate', (string) $validated['store_tax_rate'], 'store');
        Setting::set('store_announcement', (string) ($validated['store_announcement'] ?? ''), 'store');

        return back()->with('success', 'Global store settings updated.');
    }

    /**
     * Coupons / Promo Codes Console (/admin/store/coupons).
     */
    public function coupons(): View
    {
        $coupons = Coupon::latest()->get();
        return view('admin.store.coupons', compact('coupons'));
    }

    public function storeCoupon(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:40', 'unique:coupons,code'],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ]);

        Coupon::create([
            'code' => strtoupper(trim($validated['code'])),
            'type' => $validated['type'],
            'value' => $validated['value'],
            'min_order_amount' => $validated['min_order_amount'] ?? null,
            'max_uses' => $validated['max_uses'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Promo code ' . strtoupper(trim($validated['code'])) . ' created.');
    }

    public function toggleCoupon(Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        return back()->with('success', "Coupon {$coupon->code} " . ($coupon->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroyCoupon(Coupon $coupon): RedirectResponse
    {
        $code = $coupon->code;
        $coupon->delete();
        return back()->with('success', "Coupon {$code} deleted.");
    }

    protected function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('products', 'slug')->ignore($ignoreId)],
            'sku' => ['required', 'string', 'max:60', Rule::unique('products', 'sku')->ignore($ignoreId)],
            'category' => ['required', 'string', 'max:80'],
            'badge_label' => ['nullable', 'string', 'max:60'],
            'badge_color' => ['nullable', 'string', 'max:25'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'features_text' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:-1'],
            'download_url' => ['nullable', 'string', 'max:500'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    protected function prepareProductPayload(Request $request, array $validated, ?Product $existing = null): array
    {
        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        $features = [];
        if (!empty($validated['features_text'])) {
            $features = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $validated['features_text']))));
        } elseif ($existing) {
            $features = $existing->features ?? [];
        }

        $imagePath = $validated['image_url'] ?? ($existing?->image_url ?? 'xivig-blog/images/portfolio/project-nexus-core.webp');

        if ($request->hasFile('image_file')) {
            $dir = public_path('uploads/store');
            File::ensureDirectoryExists($dir);
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $imagePath = 'uploads/store/' . $filename;
        }

        return [
            'title' => $validated['title'],
            'slug' => $slug,
            'sku' => strtoupper(trim($validated['sku'])),
            'category' => $validated['category'],
            'badge_label' => $validated['badge_label'] ?? null,
            'badge_color' => ($validated['badge_color'] ?? null) ?: '#4f46e5',
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'features' => $features,
            'price' => $validated['price'],
            'sale_price' => !empty($validated['sale_price']) && (float) $validated['sale_price'] > 0 ? $validated['sale_price'] : null,
            'currency' => Setting::get('store_currency', 'USD') ?: 'USD',
            'stock_qty' => (int) $validated['stock_qty'],
            'is_digital' => $request->boolean('is_digital', true),
            'download_url' => $validated['download_url'] ?? null,
            'image_url' => $imagePath,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];
    }
}
