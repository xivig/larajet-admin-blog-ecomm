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
        Schema::table('pictures', function (Blueprint $table) {
            $table->string('alt_text')->nullable()->after('name');
            $table->text('caption')->nullable()->after('alt_text');
            $table->text('description')->nullable()->after('caption');
            $table->string('mime_type', 100)->nullable()->after('description');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
            $table->string('dimensions', 50)->nullable()->after('file_size');
            $table->string('folder', 100)->default('general')->after('dimensions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pictures', function (Blueprint $table) {
            $table->dropColumn([
                'alt_text',
                'caption',
                'description',
                'mime_type',
                'file_size',
                'dimensions',
                'folder',
            ]);
        });
    }
};
