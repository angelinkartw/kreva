<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$assessments = Illuminate\Support\Facades\DB::table('assessments')
    ->select('status', Illuminate\Support\Facades\DB::raw('count(*) as count'))
    ->groupBy('status')
    ->get();

echo "Assessment Status Distribution:\n";
foreach ($assessments as $a) {
    echo "- " . ($a->status ?? 'NULL') . ": " . $a->count . "\n";
}

$old_assessments = Illuminate\Support\Facades\DB::table('assessments')
    ->where('id_umkm', '<=', 428)
    ->select('status', Illuminate\Support\Facades\DB::raw('count(*) as count'))
    ->groupBy('status')
    ->get();

echo "\nAssessment Status Distribution (id_umkm <= 428):\n";
foreach ($old_assessments as $a) {
    echo "- " . ($a->status ?? 'NULL') . ": " . $a->count . "\n";
}

$first_old = Illuminate\Support\Facades\DB::table('assessments')
    ->where('id_umkm', '<=', 428)
    ->first();

echo "\nSample Old Assessment:\n";
print_r($first_old);

