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
        if (Schema::hasTable('umkm') && ! Schema::hasColumn('umkm', 'id_user')) {
            Schema::table('umkm', function (Blueprint $table) {
                $table->unsignedBigInteger('id_user')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('umkm') && Schema::hasColumn('umkm', 'id_user')) {
            Schema::table('umkm', function (Blueprint $table) {
                $table->dropColumn('id_user');
            });
        }
    }
};
