<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

// Find a user who is an owner with assessment data (e.g. id = 1) and user with no assessment data (e.g. new user)
$users = User::where('role', 'owner')->limit(5)->get();

foreach ($users as $user) {
    echo "=====================================\n";
    echo "Testing routes for User ID: {$user->id_user} (Name: {$user->name}, Role: {$user->role}, UMKM: {$user->id_umkm})\n";
    echo "=====================================\n";

    $routes = [
        'dashboard' => '/dashboard',
        'enam-faktor' => '/enam-faktor',
        'rekomendasi' => '/rekomendasi',
        'tambah-umkm' => '/tambah-umkm',
        'manajemen-umkm' => '/manajemen-umkm',
        'tambah-karyawan' => '/tambah-karyawan',
        'asesmen' => '/asesmen',
        'faktor-detail' => '/faktor/1'
    ];

    foreach ($routes as $name => $uri) {
        try {
            // Simulate the request
            $request = Illuminate\Http\Request::create($uri, 'GET');
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
            
            // Set auth
            auth()->login($user);

            $response = $app->handle($request);
            $status = $response->getStatusCode();

            echo "Route: $uri -> Status: $status\n";
            if ($status === 500) {
                if ($response->exception) {
                    echo "  Error: " . $response->exception->getMessage() . "\n";
                    echo "  File: " . $response->exception->getFile() . ":" . $response->exception->getLine() . "\n";
                    echo "  Stacktrace:\n" . substr($response->exception->getTraceAsString(), 0, 500) . "\n";
                } else {
                    echo "  Content: " . substr($response->getContent(), 0, 300) . "\n";
                }
            }
        } catch (\Exception $e) {
            echo "Route: $uri -> Exception: " . $e->getMessage() . "\n";
            echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }
}
