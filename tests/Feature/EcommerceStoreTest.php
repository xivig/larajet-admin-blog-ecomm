<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EcommerceStoreTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): Admin
    {
        return Admin::first() ?? Admin::create([
            'name' => 'Store Admin',
            'email' => 'store-admin-' . uniqid() . '@xivig.test',
            'password' => Hash::make('Password123!'),
        ]);
    }

    public function test_storefront_renders_catalog_and_active_gateways(): void
    {
        $response = $this->get('/ecomm');

        $response->assertStatus(200);
        $response->assertSee('Enterprise SaaS Core &amp; Multi-Tenant Engine', false);
        $response->assertSee('Add to Cart');
    }

    public function test_cart_coupon_checkout_and_sandbox_payment_authorization_flow(): void
    {
        $product = Product::where('slug', 'enterprise-saas-core-multi-tenant')->firstOrFail();
        $initialSales = (int) $product->sales_count;

        // 1. Add quantity 2 to cart
        $addRes = $this->postJson(route('ecomm.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $addRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('cart.count', 2)
            ->assertJsonPath('cart.subtotal', 298);

        // 2. Apply ELITE20 (20% off => $59.60 discount => $238.40 total)
        $couponRes = $this->postJson(route('ecomm.cart.coupon'), [
            'code' => 'ELITE20',
        ]);
        $couponRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('cart.discount', 59.6)
            ->assertJsonPath('cart.total', 238.4);

        // 3. Open Checkout page
        $this->get(route('ecomm.checkout'))
            ->assertStatus(200)
            ->assertSee('Stripe Checkout')
            ->assertSee('238.40');

        // 4. Submit Checkout via Stripe Sandbox
        $checkoutRes = $this->post(route('ecomm.checkout.process'), [
            'customer_name' => 'Ada Lovelace',
            'customer_email' => 'ada@enterprise.dev',
            'billing_country' => 'GB',
            'payment_gateway' => 'stripe',
        ]);
        $checkoutRes->assertRedirect();

        $order = Order::where('customer_email', 'ada@enterprise.dev')->latest('id')->firstOrFail();
        $this->assertEquals(238.40, (float) $order->total);
        $this->assertEquals('pending', $order->payment_status);

        // 5. Simulate Payment Authorization via callback
        $cbRes = $this->post(route('ecomm.callback', ['driver' => 'stripe', 'orderNumber' => $order->order_number]), [
            'sandbox_decision' => 'approve',
            'method_label' => 'Stripe Test Card',
        ]);
        $cbRes->assertRedirect(route('ecomm.order.receipt', $order->order_number));

        $order->refresh();
        $product->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('completed', $order->order_status);
        $this->assertNotNull($order->transaction_id);
        $this->assertStringStartsWith('XIV-', (string) $order->items->first()->license_key);
        $this->assertEquals($initialSales + 2, (int) $product->sales_count);

        // 6. Verify Receipt shows License Key
        $this->get(route('ecomm.order.receipt', $order->order_number))
            ->assertStatus(200)
            ->assertSee($order->items->first()->license_key);
    }

    public function test_wire_transfer_gateway_holds_licenses_until_admin_marks_paid(): void
    {
        $product = Product::where('slug', 'elite-executive-admin-suite')->firstOrFail();

        $this->postJson(route('ecomm.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->post(route('ecomm.checkout.process'), [
            'customer_name' => 'Alan Turing',
            'customer_email' => 'alan@bletchley.org',
            'billing_country' => 'GB',
            'payment_gateway' => 'cod_bank',
        ])->assertRedirect();

        $order = Order::where('customer_email', 'alan@bletchley.org')->latest('id')->firstOrFail();
        $this->assertEquals('pending', $order->payment_status);
        $this->assertNull($order->items->first()->license_key);

        // Receipt should hold license key & display wire instructions
        $this->get(route('ecomm.order.receipt', $order->order_number))
            ->assertStatus(200)
            ->assertSee('Awaiting Payment Settlement')
            ->assertSee('Bank / Wire Settlement Instructions');

        // Admin marks order paid -> auto-issues license key
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin')
            ->post(route('admin.store.orders.status', $order), [
                'payment_status' => 'paid',
                'order_status' => 'completed',
            ])->assertRedirect();

        $order->refresh();
        $this->assertTrue($order->isPaid());
        $this->assertNotNull($order->items->first()->license_key);
    }

    public function test_admin_can_control_products_gateways_orders_and_coupons(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'admin')->get(route('admin.store.products'))->assertStatus(200);
        $this->actingAs($admin, 'admin')->get(route('admin.store.orders'))->assertStatus(200);
        $this->actingAs($admin, 'admin')->get(route('admin.store.gateways'))->assertStatus(200);
        $this->actingAs($admin, 'admin')->get(route('admin.store.coupons'))->assertStatus(200);

        // Create new product from Admin Panel
        $this->actingAs($admin, 'admin')->post(route('admin.store.products.store'), [
            'title' => 'Admin Added Neural Telemetry SDK',
            'sku' => 'XIV-ADMIN-999',
            'category' => 'Starters',
            'price' => 299.00,
            'stock_qty' => 15,
            'is_active' => '1',
            'is_featured' => '1',
            'is_digital' => '1',
        ])->assertRedirect(route('admin.store.products'));

        $this->assertDatabaseHas('products', ['sku' => 'XIV-ADMIN-999']);
        $this->get('/ecomm')->assertStatus(200)->assertSee('Admin Added Neural Telemetry SDK');

        // Configure Gateway & preserve secret masking
        $stripe = PaymentGateway::where('driver', 'stripe')->firstOrFail();
        $this->actingAs($admin, 'admin')->put(route('admin.store.gateways.update', $stripe), [
            'name' => 'Stripe Enterprise',
            'subtitle' => 'Cards & Link',
            'mode' => 'live',
            'is_enabled' => '1',
            'sort_order' => 1,
            'config' => [
                'publishable_key' => 'pk_live_sample123',
                'secret_key' => 'sk_live_secret456',
            ],
        ])->assertRedirect();

        $stripe->refresh();
        $this->assertEquals('Stripe Enterprise', $stripe->name);
        $this->assertEquals('sk_live_secret456', $stripe->configValue('secret_key'));

        // Update with masked placeholder should NOT overwrite actual secret
        $this->actingAs($admin, 'admin')->put(route('admin.store.gateways.update', $stripe), [
            'name' => 'Stripe Enterprise',
            'subtitle' => 'Updated Subtitle',
            'mode' => 'live',
            'is_enabled' => '1',
            'config' => [
                'publishable_key' => 'pk_live_sample123',
                'secret_key' => 'sk_li••••••••••••t456',
            ],
        ])->assertRedirect();

        $stripe->refresh();
        $this->assertEquals('sk_live_secret456', $stripe->configValue('secret_key'));

        // Toggle store maintenance mode off via Admin Store Settings & verify frontend notice
        $this->actingAs($admin, 'admin')->post(route('admin.store.settings.update'), [
            'store_enabled' => '0',
            'store_currency' => 'EUR',
            'store_tax_rate' => '10',
            'store_announcement' => 'Holiday Maintenance Window',
        ])->assertRedirect();

        $this->get('/ecomm')->assertStatus(200)->assertSee('Storefront Temporarily Under Scheduled Update');

        // Reset settings cache inside transaction teardown
        Setting::clearCache();
    }
}
