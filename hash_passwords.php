<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$users = DB::table('users')->get();
$count = 0;

foreach ($users as $user) {
    if (!str_starts_with($user->password, '$2y$') && strlen($user->password) < 50) {
        DB::table('users')
            ->where('id_user', $user->id_user)
            ->update(['password' => Hash::make($user->password)]);
        $count++;
        
        if ($count % 50 == 0) {
            echo "Proses: $count user telah di-hash...\n";
        }
    }
}

echo "Selesai! Berhasil meng-hash $count password user.\n";
