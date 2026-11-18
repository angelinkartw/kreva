<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // The canonical order tables are created by create_order_tables.
    }

    public function down(): void
    {
        // This migration does not own the canonical order tables.
    }
};
