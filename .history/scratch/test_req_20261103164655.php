<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/', 'GET')
);

echo "HTTP Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() !== 200) {
    echo "Content Preview: " . substr($response->getContent(), 0, 500) . "\n";
} else {
    echo "Success! Page rendered " . strlen($response->getContent()) . " bytes.\n";
}

