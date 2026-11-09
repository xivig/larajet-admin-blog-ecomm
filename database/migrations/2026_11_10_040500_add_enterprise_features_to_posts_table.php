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
        Schema::table('posts', function (Blueprint $table) {
            // Blog Publishing & Visibility Features
            $table->string('post_status')->default('draft')->index()->after('status');
            $table->timestamp('scheduled_at')->nullable()->index()->after('published_at');
            $table->boolean('is_sticky')->default(false)->index()->after('scheduled_at');
            $table->boolean('is_featured')->default(false)->index()->after('is_sticky');
            $table->string('visibility')->default('public')->index()->after('is_featured');
            $table->string('password')->nullable()->after('visibility');
            $table->unsignedInteger('reading_time')->default(1)->after('password');
            $table->unsignedBigInteger('views_count')->default(0)->after('reading_time');

            // Professional SEO Meta Fields
            $table->string('meta_title')->nullable()->after('views_count');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords', 500)->nullable()->after('meta_description');
            $table->string('canonical_url', 500)->nullable()->after('meta_keywords');
            $table->string('meta_author')->nullable()->after('canonical_url');
            $table->string('meta_robots')->default('index, follow')->nullable()->after('meta_author');
            $table->string('meta_language', 20)->default('en')->nullable()->after('meta_robots');
            $table->string('geo_region', 50)->nullable()->after('meta_language');
            $table->string('geo_placename', 100)->nullable()->after('geo_region');
            $table->string('geo_position', 100)->nullable()->after('geo_placename');
            $table->string('meta_rating', 50)->default('general')->nullable()->after('geo_position');
            $table->string('meta_copyright')->nullable()->after('meta_rating');

            // Open Graph & Social Meta
            $table->string('og_title')->nullable()->after('meta_copyright');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image', 500)->nullable()->after('og_description');
            $table->string('twitter_card', 50)->default('summary_large_image')->nullable()->after('og_image');

            // Schema.org Structured Data
            $table->string('schema_type', 50)->default('Article')->nullable()->after('twitter_card');
            $table->json('faq_schema')->nullable()->after('schema_type');
            $table->json('video_schema')->nullable()->after('faq_schema');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'post_status',
                'scheduled_at',
                'is_sticky',
                'is_featured',
                'visibility',
                'password',
                'reading_time',
                'views_count',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'canonical_url',
                'meta_author',
                'meta_robots',
                'meta_language',
                'geo_region',
                'geo_placename',
                'geo_position',
                'meta_rating',
                'meta_copyright',
                'og_title',
                'og_description',
                'og_image',
                'twitter_card',
                'schema_type',
                'faq_schema',
                'video_schema',
            ]);
        });
    }
};
