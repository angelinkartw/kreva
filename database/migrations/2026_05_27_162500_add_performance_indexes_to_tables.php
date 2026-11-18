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
        $indexes = [
            'assessments' => [
                [['id_umkm', 'status', 'created_at'], 'idx_assessments_umkm_status_created'],
                [['id_umkm'], 'idx_assessments_id_umkm'],
            ],
            'umkm' => [
                [['industry'], 'idx_umkm_industry'],
                [['usia_usaha'], 'idx_umkm_usia_usaha'],
                [['id_user'], 'idx_umkm_id_user'],
            ],
            'users' => [
                [['id_umkm', 'role'], 'idx_users_umkm_role'],
            ],
            'responses' => [
                [['id_assessment', 'id_user'], 'idx_responses_assessment_user'],
            ],
        ];

        foreach ($indexes as $tableName => $tableIndexes) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($tableIndexes as [$columns, $indexName]) {
                if (! Schema::hasColumns($tableName, $columns) || Schema::hasIndex($tableName, $indexName)) {
                    continue;
                }

                Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexes = [
            'assessments' => ['idx_assessments_umkm_status_created', 'idx_assessments_id_umkm'],
            'umkm' => ['idx_umkm_industry', 'idx_umkm_usia_usaha', 'idx_umkm_id_user'],
            'users' => ['idx_users_umkm_role'],
            'responses' => ['idx_responses_assessment_user'],
        ];

        foreach ($indexes as $tableName => $indexNames) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($indexNames as $indexName) {
                if (Schema::hasIndex($tableName, $indexName)) {
                    Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                        $table->dropIndex($indexName);
                    });
                }
            }
        }
    }
};
