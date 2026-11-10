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
        // 1. URL Management: 301/302 Redirect Rules
        if (!Schema::hasTable('redirect_rules')) {
            Schema::create('redirect_rules', function (Blueprint $table) {
                $table->id();
                $table->string('source_path', 1000)->index();
                $table->string('target_path', 1000);
                $table->smallInteger('status_code')->default(301); // 301 Permanent, 302 Temporary
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedBigInteger('hits_count')->default(0);
                $table->timestamp('last_accessed_at')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. URL Management: 404 Not Found Logger
        if (!Schema::hasTable('not_found_logs')) {
            Schema::create('not_found_logs', function (Blueprint $table) {
                $table->id();
                $table->string('url', 1000)->index();
                $table->string('referer', 1000)->nullable();
                $table->string('ip', 50)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->unsignedBigInteger('hits_count')->default(1);
                $table->boolean('is_resolved')->default(false)->index();
                $table->timestamp('last_seen_at')->useCurrent();
                $table->timestamps();
            });
        }

        // 3. URL Management: Slug History for automatic 301 redirection on slug changes
        if (!Schema::hasTable('slug_histories')) {
            Schema::create('slug_histories', function (Blueprint $table) {
                $table->id();
                $table->string('model_type', 100)->index(); // Post, Page
                $table->unsignedBigInteger('model_id')->index();
                $table->string('old_slug', 255)->index();
                $table->string('new_slug', 255);
                $table->timestamps();
            });
        }

        // 4. Search Intelligence & Analytics
        if (!Schema::hasTable('search_logs')) {
            Schema::create('search_logs', function (Blueprint $table) {
                $table->id();
                $table->string('query', 255)->index();
                $table->unsignedInteger('results_count')->default(0);
                $table->unsignedBigInteger('hits_count')->default(1);
                $table->string('ip', 50)->nullable();
                $table->timestamp('last_searched_at')->useCurrent();
                $table->timestamps();
            });
        }

        // 5. Modular Widgets System
        if (!Schema::hasTable('widgets')) {
            Schema::create('widgets', function (Blueprint $table) {
                $table->id();
                $table->string('area', 50)->default('sidebar')->index(); // sidebar, footer_1, footer_2, footer_3, header
                $table->string('widget_type', 50)->index(); // recent_posts, trending, popular, ads, newsletter, categories, archive, tags, custom_html
                $table->string('title');
                $table->longText('content')->nullable();
                $table->json('settings')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
            });
        }

        // 6. Enhanced Comments Moderation & Interactivity
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'status')) {
                $table->string('status', 30)->default('approved')->index()->after('comment'); // pending, approved, rejected, spam
            }
            if (!Schema::hasColumn('comments', 'guest_name')) {
                $table->string('guest_name', 100)->nullable()->after('status');
            }
            if (!Schema::hasColumn('comments', 'guest_email')) {
                $table->string('guest_email', 255)->nullable()->after('guest_name');
            }
            if (!Schema::hasColumn('comments', 'guest_website')) {
                $table->string('guest_website', 255)->nullable()->after('guest_email');
            }
            if (!Schema::hasColumn('comments', 'likes_count')) {
                $table->unsignedInteger('likes_count')->default(0)->after('guest_website');
            }
            if (!Schema::hasColumn('comments', 'reports_count')) {
                $table->unsignedInteger('reports_count')->default(0)->after('likes_count');
            }
            if (!Schema::hasColumn('comments', 'ip_address')) {
                $table->string('ip_address', 50)->nullable()->after('reports_count');
            }
        });

        // 7. Enhanced Comment Replies
        Schema::table('comment_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('comment_replies', 'status')) {
                $table->string('status', 30)->default('approved')->index()->after('reply');
            }
            if (!Schema::hasColumn('comment_replies', 'guest_name')) {
                $table->string('guest_name', 100)->nullable()->after('status');
            }
            if (!Schema::hasColumn('comment_replies', 'guest_email')) {
                $table->string('guest_email', 255)->nullable()->after('guest_name');
            }
            if (!Schema::hasColumn('comment_replies', 'likes_count')) {
                $table->unsignedInteger('likes_count')->default(0)->after('guest_email');
            }
            if (!Schema::hasColumn('comment_replies', 'reports_count')) {
                $table->unsignedInteger('reports_count')->default(0)->after('likes_count');
            }
        });

        // 8. Image Optimization enhancements on pictures table
        Schema::table('pictures', function (Blueprint $table) {
            if (!Schema::hasColumn('pictures', 'webp_path')) {
                $table->string('webp_path', 500)->nullable()->after('folder');
            }
            if (!Schema::hasColumn('pictures', 'responsive_urls')) {
                $table->json('responsive_urls')->nullable()->after('webp_path');
            }
            if (!Schema::hasColumn('pictures', 'title')) {
                $table->string('title', 255)->nullable()->after('alt_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirect_rules');
        Schema::dropIfExists('not_found_logs');
        Schema::dropIfExists('slug_histories');
        Schema::dropIfExists('search_logs');
        Schema::dropIfExists('widgets');
    }
};
