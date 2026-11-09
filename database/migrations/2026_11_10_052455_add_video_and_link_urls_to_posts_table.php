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
            if (!Schema::hasColumn('posts', 'video_url')) {
                $table->string('video_url', 1000)->nullable()->after('content');
            }
            if (!Schema::hasColumn('posts', 'external_url')) {
                $table->string('external_url', 1000)->nullable()->after('video_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'external_url')) {
                $table->dropColumn('external_url');
            }
            if (Schema::hasColumn('posts', 'video_url')) {
                $table->dropColumn('video_url');
            }
        });
    }
};
