<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Assessment;

$assessments = Assessment::whereNotNull('total_score')->get();

foreach ($assessments as $a) {
    $score = (float)$a->total_score;
    if ($score <= 2.0) {
        $a->kategori_kuartil = 'Kuartil 1';
    } elseif ($score <= 3.0) {
        $a->kategori_kuartil = 'Kuartil 2';
    } elseif ($score <= 4.0) {
        $a->kategori_kuartil = 'Kuartil 3';
    } else {
        $a->kategori_kuartil = 'Kuartil 4';
    }
    $a->save();
}

echo "Updated " . $assessments->count() . " records.\n";
