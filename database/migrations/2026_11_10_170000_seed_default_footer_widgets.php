<?php

use App\Models\Widget;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Populate default modular widgets across all five footer columns.
     */
    public function up(): void
    {
        if (!Schema::hasTable('widgets')) {
            return;
        }

        Widget::ensureDefaultFooterWidgets();
    }

    /**
     * Reverse is a safe no-op to preserve user-configured widgets.
     */
    public function down(): void
    {
        // Intentionally left blank.
    }
};
