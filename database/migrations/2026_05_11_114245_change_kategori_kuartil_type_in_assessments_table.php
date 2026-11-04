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
        if (Schema::hasTable('assessments') && Schema::hasColumn('assessments', 'kategori_kuartil')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->string('kategori_kuartil', 50)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('assessments') && Schema::hasColumn('assessments', 'kategori_kuartil')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->enum('kategori_kuartil', ['Kurang', 'Cukup', 'Baik'])->nullable()->change();
            });
        }
    }
};
