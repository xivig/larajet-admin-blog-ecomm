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
        // 1. Visitors Tracking
        if (!Schema::hasTable('visitors')) {
            Schema::create('visitors', function (Blueprint $table) {
                $table->id();
                $table->string('ip', 50)->index();
                $table->string('session_id', 100)->nullable()->index();
                $table->text('user_agent')->nullable();
                $table->string('device', 30)->default('Desktop')->index(); // Desktop, Mobile, Tablet, Bot
                $table->string('browser', 50)->default('Chrome')->index(); // Chrome, Safari, Firefox, Edge, etc.
                $table->string('platform', 50)->default('Windows')->index(); // Windows, iOS, Android, macOS, Linux
                $table->string('country', 100)->default('United States')->index();
                $table->string('country_code', 5)->default('US')->index();
                $table->string('city', 100)->nullable();
                $table->unsignedBigInteger('hits_count')->default(1);
                $table->timestamp('first_visited_at')->useCurrent();
                $table->timestamp('last_visited_at')->useCurrent();
                $table->timestamps();
            });
        }

        // 2. Page Views Tracking
        if (!Schema::hasTable('page_views')) {
            Schema::create('page_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visitor_id')->nullable()->constrained('visitors')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('url', 1000);
                $table->string('path', 500)->index();
                $table->string('referer', 1000)->nullable();
                $table->string('ip', 50)->nullable();
                $table->string('device', 30)->default('Desktop')->index();
                $table->string('browser', 50)->default('Chrome')->index();
                $table->string('country', 100)->default('United States')->index();
                $table->string('session_id', 100)->nullable();
                $table->timestamps();
            });
        }

        // 3. Post Views Tracking
        if (!Schema::hasTable('post_views')) {
            Schema::create('post_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
                $table->foreignId('visitor_id')->nullable()->constrained('visitors')->nullOnDelete();
                $table->string('ip', 50)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->string('referer', 1000)->nullable();
                $table->timestamps();
            });
        }

        // 4. Aggregated Analytics Metrics (Daily / Monthly)
        if (!Schema::hasTable('analytics_metrics')) {
            Schema::create('analytics_metrics', function (Blueprint $table) {
                $table->id();
                $table->string('metric_key', 50)->index(); // visitors, pageviews, sessions, bounce_rate
                $table->bigInteger('metric_value')->default(0);
                $table->string('dimension', 50)->nullable()->index(); // device, browser, country, referer
                $table->string('dimension_value', 100)->nullable()->index();
                $table->date('date')->index();
                $table->timestamps();
            });
        }

        // 5. Contact Inquiries & Direct Messaging
        if (!Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('email', 255)->index();
                $table->string('phone', 50)->nullable();
                $table->string('subject', 255)->nullable();
                $table->text('message');
                $table->string('status', 30)->default('new')->index(); // new, read, replied, archived
                $table->string('ip_address', 50)->nullable();
                $table->text('reply_message')->nullable();
                $table->timestamp('replied_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 6. Advertisements & Monetization Engine
        if (!Schema::hasTable('advertisements')) {
            Schema::create('advertisements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('location', 50)->default('sidebar')->index(); // sidebar, header, in_article, footer
                $table->string('ad_type', 30)->default('banner'); // banner, script, adsense
                $table->string('image_path', 500)->nullable();
                $table->string('target_url', 1000)->nullable();
                $table->longText('script_code')->nullable();
                $table->unsignedBigInteger('impressions_count')->default(0);
                $table->unsignedBigInteger('clicks_count')->default(0);
                $table->boolean('status')->default(true)->index();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->timestamps();
            });
        }

        // 7. Post Revisions & Audit Trail
        if (!Schema::hasTable('post_revisions')) {
            Schema::create('post_revisions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title');
                $table->longText('content')->nullable();
                $table->text('excerpt')->nullable();
                $table->string('revision_notes', 255)->nullable();
                $table->timestamps();
            });
        }

        // 8. FAQs (Frequently Asked Questions & Schema)
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category', 50)->default('General')->index();
                $table->integer('order')->default(0);
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
            });
        }

        // 9. Social Links & Profiles
        if (!Schema::hasTable('social_links')) {
            Schema::create('social_links', function (Blueprint $table) {
                $table->id();
                $table->string('platform', 50); // twitter, github, linkedin, facebook, youtube, instagram
                $table->string('url', 500);
                $table->string('icon', 50)->nullable();
                $table->integer('order')->default(0);
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
            });
        }

        // 10. Theme System
        if (!Schema::hasTable('themes')) {
            Schema::create('themes', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 100)->unique();
                $table->text('description')->nullable();
                $table->string('preview_image', 500)->nullable();
                $table->boolean('is_active')->default(false)->index();
                $table->json('settings')->nullable(); // primary_color, font_family, dark_mode_default
                $table->timestamps();
            });
        }

        // 11. Countries Database
        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->index();
                $table->string('code', 5)->unique(); // US, GB, FR, DE, IN, JP
                $table->string('iso3', 5)->nullable();
                $table->string('flag_emoji', 10)->nullable();
                $table->unsignedBigInteger('visitors_count')->default(0);
                $table->timestamps();
            });
        }

        // 12. Multi-language Localization
        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('code', 10)->unique(); // en, fr, de, es, ja
                $table->string('locale', 20)->default('en_US');
                $table->boolean('is_default')->default(false)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        // 13. Polymorphic SEO Metadata
        if (!Schema::hasTable('seo_metas')) {
            Schema::create('seo_metas', function (Blueprint $table) {
                $table->id();
                $table->string('seoable_type', 100)->index(); // Post, Page, Category
                $table->unsignedBigInteger('seoable_id')->index();
                $table->string('meta_title', 255)->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords', 500)->nullable();
                $table->string('canonical_url', 1000)->nullable();
                $table->string('og_image', 500)->nullable();
                $table->string('schema_type', 50)->default('Article');
                $table->string('indexing_rules', 100)->default('index, follow');
                $table->timestamps();

                $table->unique(['seoable_type', 'seoable_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_metas');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('post_revisions');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('analytics_metrics');
        Schema::dropIfExists('post_views');
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('visitors');
    }
};
