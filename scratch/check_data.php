<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$a = DB::table('assessments')->whereNotNull('answers')->latest()->first();

echo "Full Record Dump:\n";
print_r((array)$a);
