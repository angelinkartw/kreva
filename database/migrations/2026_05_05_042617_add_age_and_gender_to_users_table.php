<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan nullable() agar tidak error pada data user yang sudah ada sebelumnya
            $table->integer('age')->nullable()->after('email'); 
            $table->string('gender')->nullable()->after('age');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Untuk menghapus kolom jika melakukan rollback
            $table->dropColumn(['age', 'gender']);
        });
    }
};