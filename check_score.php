<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = Illuminate\Support\Facades\DB::table('users')->where('nama_user', 'Responden 1')->first();
if ($user) {
    echo "User: " . $user->nama_user . " (ID: " . $user->id_user . ")\n";
    $assessments = Illuminate\Support\Facades\DB::table('assessments')->where('id_user', $user->id_user)->get();
    foreach ($assessments as $a) {
        echo "Assessment ID: " . $a->id_assessment . "\n";
        echo "Total Score: " . $a->total_score . "\n";
        echo "OV: " . $a->score_ov . " | LDI: " . $a->score_ldi . " | INS: " . $a->score_ins . "\n";
        echo "OPS: " . $a->score_ops . " | WEQ: " . $a->score_weq . " | ECT: " . $a->score_ect . "\n";
        echo "-------------------\n";
    }
    $avg = Illuminate\Support\Facades\DB::table('assessments')->where('id_user', $user->id_user)->avg('total_score');
    echo "Average Score: " . $avg . "\n";
} else {
    echo "User not found.\n";
}
