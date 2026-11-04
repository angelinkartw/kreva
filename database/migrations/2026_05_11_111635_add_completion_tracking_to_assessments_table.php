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
        if (Schema::hasTable('assessments')) {
            Schema::table('assessments', function (Blueprint $table) {
                if (! Schema::hasColumn('assessments', 'owner_finished')) {
                    $table->boolean('owner_finished')->default(false);
                }
                if (! Schema::hasColumn('assessments', 'employee_finished')) {
                    $table->boolean('employee_finished')->default(false);
                }
                if (! Schema::hasColumn('assessments', 'id_owner')) {
                    $table->unsignedBigInteger('id_owner')->nullable();
                }
                if (! Schema::hasColumn('assessments', 'id_employee')) {
                    $table->unsignedBigInteger('id_employee')->nullable();
                }
                if (! Schema::hasColumn('assessments', 'answers')) {
                    $table->json('answers')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('assessments')) {
            $columns = array_filter(
                ['owner_finished', 'employee_finished', 'id_owner', 'id_employee', 'answers'],
                fn (string $column): bool => Schema::hasColumn('assessments', $column)
            );

            if ($columns !== []) {
                Schema::table('assessments', function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        }
    }
};
