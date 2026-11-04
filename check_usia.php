<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dist = Illuminate\Support\Facades\DB::table('umkm')
    ->select('usia_usaha', Illuminate\Support\Facades\DB::raw('count(*) as total'))
    ->groupBy('usia_usaha')
    ->orderBy('usia_usaha')
    ->get();

foreach ($dist as $d) {
    echo "Usia: " . $d->usia_usaha . " | Total: " . $d->total . "\n";
}
