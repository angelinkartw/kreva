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
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->default('owner');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The role column is part of the users schema and stores owner, employee, and admin roles.
    }
};
