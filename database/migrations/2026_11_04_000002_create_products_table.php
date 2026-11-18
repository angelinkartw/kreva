<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // The canonical products table is created by create_store_tables.
    }

    public function down(): void
    {
        // This migration does not own the canonical products table.
    }
};
