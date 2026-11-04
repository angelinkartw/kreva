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
        foreach (['assessments', 'umkm', 'users', 'responses'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                return;
            }
        }

        // Index untuk tabel assessments
        Schema::table('assessments', function (Blueprint $table) {
            // Index kombinasi untuk pencarian spesifik dan sorting (DashboardController)
            $table->index(['id_umkm', 'status', 'created_at'], 'idx_assessments_umkm_status_created');
            // Index untuk join dengan UMKM dan agregasi
            $table->index('id_umkm', 'idx_assessments_id_umkm');
        });

        // Index untuk tabel umkm
        Schema::table('umkm', function (Blueprint $table) {
            // Index untuk agregasi group by industry & usia_usaha
            $table->index('industry', 'idx_umkm_industry');
            $table->index('usia_usaha', 'idx_umkm_usia_usaha');
            // Index untuk relasi
            $table->index('id_user', 'idx_umkm_id_user');
        });

        // Index untuk tabel users
        Schema::table('users', function (Blueprint $table) {
            // Index kombinasi untuk menghitung jumlah karyawan per UMKM
            $table->index(['id_umkm', 'role'], 'idx_users_umkm_role');
        });

        // Index untuk tabel responses
        Schema::table('responses', function (Blueprint $table) {
            // Index untuk pengecekan "hasSubmitted" dan aggregasi per assessment
            $table->index(['id_assessment', 'id_user'], 'idx_responses_assessment_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['assessments', 'umkm', 'users', 'responses'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                return;
            }
        }

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropIndex('idx_assessments_umkm_status_created');
            $table->dropIndex('idx_assessments_id_umkm');
        });

        Schema::table('umkm', function (Blueprint $table) {
            $table->dropIndex('idx_umkm_industry');
            $table->dropIndex('idx_umkm_usia_usaha');
            $table->dropIndex('idx_umkm_id_user');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_umkm_role');
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex('idx_responses_assessment_user');
        });
    }
};
