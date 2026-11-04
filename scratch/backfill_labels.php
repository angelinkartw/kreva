<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Assessment;

$assessments = Assessment::whereNotNull('total_score')->get();

foreach ($assessments as $a) {
    $score = (float)$a->total_score;
    if ($score <= 2.33) {
        $a->kategori_kuartil = 'Kurang';
    } elseif ($score <= 3.66) {
        $a->kategori_kuartil = 'Cukup';
    } else {
        $a->kategori_kuartil = 'Baik';
    }
    $a->save();
}

echo "Backfilled " . $assessments->count() . " records with Kurang/Cukup/Baik labels.\n";
