<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = Illuminate\Support\Facades\DB::select('SHOW TABLES');
echo "Tables in Database:\n";
foreach ($tables as $table) {
    $tableName = current((array)$table);
    echo "- $tableName\n";
    $columns = Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `$tableName`");
    foreach ($columns as $column) {
        echo "  * {$column->Field} ({$column->Type})\n";
    }
}
