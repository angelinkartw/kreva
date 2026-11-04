<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = Illuminate\Support\Facades\DB::table('users')->select('nama_user', 'password')->limit(5)->get();
foreach ($users as $user) {
    echo "Username: " . $user->nama_user . "\n";
    echo "Hash: " . $user->password . "\n";
    echo "-------------------\n";
}
