<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Memeriksa tabel migrations...\n";

// Pastikan tabel migrations ada
if (!Schema::hasTable('migrations')) {
    echo "Tabel migrations belum ada, membuat tabel...\n";
    \Illuminate\Support\Facades\Artisan::call('migrate:install');
    echo "Tabel migrations berhasil dibuat.\n";
}

$existingMigrations = DB::table('migrations')->pluck('migration')->toArray();

$migrationsToMarkAsDone = [
    '0001_01_01_000000_create_users_table',
    '0001_01_01_000001_create_cache_table',
    '0001_01_01_000002_create_jobs_table',
    '2026_05_05_042617_add_age_and_gender_to_users_table',
    '2026_05_11_063351_add_id_user_to_umkm_table',
    '2026_05_11_074831_add_admin_role_to_users_table',
    '2026_05_11_091436_add_target_role_to_questions_table',
    '2026_05_11_111635_add_completion_tracking_to_assessments_table',
    '2026_05_11_114245_change_kategori_kuartil_type_in_assessments_table',
    '2026_05_22_144559_create_personal_access_tokens_table'
];

$batch = DB::table('migrations')->max('batch') ?? 0;
$batch++;

$inserted = 0;
foreach ($migrationsToMarkAsDone as $migration) {
    if (!in_array($migration, $existingMigrations)) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
        echo "Menandai migration selesai: $migration\n";
        $inserted++;
    }
}

if ($inserted > 0) {
    echo "Berhasil memperbaiki status database. $inserted file migration ditandai selesai.\n";
    echo "Sekarang Anda bisa menjalankan 'php artisan migrate' dengan aman!\n";
} else {
    echo "Semua migration lama sudah tercatat di tabel migrations. Tidak ada perubahan yang diperlukan.\n";
}
