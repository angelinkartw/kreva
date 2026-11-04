<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$columns = DB::select('DESCRIBE assessments');
foreach ($columns as $c) {
    if ($c->Field == 'kategori_kuartil') {
        echo "Field: {$c->Field}\n";
        echo "Type: {$c->Type}\n";
        echo "Null: {$c->Null}\n";
    }
}
