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
        if (Schema::hasTable('questions') && ! Schema::hasColumn('questions', 'target_role')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->enum('target_role', ['owner', 'employee', 'both'])->default('both');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('questions') && Schema::hasColumn('questions', 'target_role')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('target_role');
            });
        }
    }
};
