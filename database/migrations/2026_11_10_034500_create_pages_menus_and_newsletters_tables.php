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
        // 1. Static Pages
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->longText('content')->nullable();
                $table->text('excerpt')->nullable();
                $table->string('template', 50)->default('default');
                $table->boolean('status')->default(true); // true = published, false = draft
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 2. Navigation Menus
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique(); // header, footer, mobile, sidebar
                $table->string('location', 50)->default('header');
                $table->timestamps();
            });
        }

        // 3. Navigation Menu Items
        if (!Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('title');
                $table->string('url');
                $table->string('target', 20)->default('_self'); // _self, _blank
                $table->string('icon', 50)->nullable();
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // 4. Newsletter Subscribers
        if (!Schema::hasTable('newsletter_subscribers')) {
            Schema::create('newsletter_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('name')->nullable();
                $table->string('status', 30)->default('active'); // active, unsubscribed, pending
                $table->string('source', 50)->default('website');
                $table->timestamp('subscribed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('pages');
    }
};
