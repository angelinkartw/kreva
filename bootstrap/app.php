<?php

use App\Http\Middleware\EnsureAccountType;
use App\Http\Middleware\RestrictEmployeeFromOwnerPanel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectUsersTo(function (Request $request): string {
            return match (true) {
                $request->user()->role === 'employee' => route('dashboard.asesmen', absolute: false),
                $request->user()->account_type === 'buyer' => route('marketplace.index', absolute: false),
                $request->user()->account_type === 'seller' => route('seller.dashboard', absolute: false),
                default => route('dashboard', absolute: false),
            };
        });
        $middleware->alias([
            'non.employee' => RestrictEmployeeFromOwnerPanel::class,
            'account.type' => EnsureAccountType::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
