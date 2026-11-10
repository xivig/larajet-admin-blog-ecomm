<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow guest comments and replies when user_id is null.
     */
    public function up(): void
    {
        if (Schema::hasTable('comments') && Schema::hasColumn('comments', 'user_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }

        if (Schema::hasTable('comment_replies') && Schema::hasColumn('comment_replies', 'user_id')) {
            Schema::table('comment_replies', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }
    }

    /**
     * Safe no-op down to preserve existing guest records.
     */
    public function down(): void
    {
        // Intentionally left blank.
    }
};
