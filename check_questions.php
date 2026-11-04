<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (Illuminate\Support\Facades\Schema::hasTable('questions')) {
    $questions = Illuminate\Support\Facades\DB::table('questions')->orderBy('id_factor')->orderBy('id_question')->get();
    foreach ($questions as $q) {
        echo "Factor: " . $q->id_factor . " | ID: " . $q->id_question . " | Q: " . $q->teks_pertanyaan . "\n";
    }
} else {
    echo "Table 'questions' does NOT exist.\n";
}
