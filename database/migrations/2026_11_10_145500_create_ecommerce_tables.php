<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('sku')->unique();
                $table->string('category')->default('Starters')->index();
                $table->string('badge_label')->nullable();
                $table->string('badge_color')->default('#4f46e5');
                $table->text('short_description')->nullable();
                $table->longText('description')->nullable();
                $table->json('features')->nullable();
                $table->decimal('price', 10, 2);
                $table->decimal('sale_price', 10, 2)->nullable();
                $table->string('currency', 8)->default('USD');
                $table->integer('stock_qty')->default(-1); // -1 = unlimited digital
                $table->boolean('is_digital')->default(true);
                $table->string('download_url')->nullable();
                $table->string('image_url')->nullable();
                $table->decimal('rating', 3, 2)->default(4.90);
                $table->unsignedInteger('reviews_count')->default(24);
                $table->unsignedInteger('sales_count')->default(0);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true)->index();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('payment_gateways')) {
            Schema::create('payment_gateways', function (Blueprint $table) {
                $table->id();
                $table->string('driver')->unique(); // stripe, razorpay, paypal, cod_bank, sandbox_test
                $table->string('name');
                $table->string('subtitle')->nullable();
                $table->string('icon')->default('bi-credit-card-2-front-fill');
                $table->boolean('is_enabled')->default(false)->index();
                $table->string('mode', 20)->default('sandbox'); // sandbox | live
                $table->json('config')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('type', 20)->default('percent'); // percent | fixed
                $table->decimal('value', 10, 2);
                $table->decimal('min_order_amount', 10, 2)->nullable();
                $table->unsignedInteger('max_uses')->nullable();
                $table->unsignedInteger('uses_count')->default(0);
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('customer_name');
                $table->string('customer_email')->index();
                $table->string('customer_phone')->nullable();
                $table->string('company_name')->nullable();
                $table->string('billing_address')->nullable();
                $table->string('billing_city')->nullable();
                $table->string('billing_country')->default('US');
                $table->text('notes')->nullable();
                $table->decimal('subtotal', 10, 2);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('tax_amount', 10, 2)->default(0);
                $table->decimal('total', 10, 2);
                $table->string('currency', 8)->default('USD');
                $table->string('coupon_code')->nullable();
                $table->string('payment_gateway')->index();
                $table->string('payment_status', 30)->default('pending')->index(); // pending, paid, failed, refunded
                $table->string('order_status', 30)->default('pending')->index(); // pending, processing, completed, cancelled
                $table->string('transaction_id')->nullable()->index();
                $table->json('payment_payload')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->string('product_title');
                $table->string('product_sku')->nullable();
                $table->decimal('unit_price', 10, 2);
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('line_total', 10, 2);
                $table->string('license_key')->nullable();
                $table->string('download_url')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('payment_gateways');
        Schema::dropIfExists('products');
    }
};
