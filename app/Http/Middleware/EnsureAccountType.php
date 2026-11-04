<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountType
{
    public function handle(Request $request, Closure $next, string ...$allowedTypes): Response
    {
        if (! $request->user() || ! in_array($request->user()->account_type, $allowedTypes, true)) {
            abort(403, 'Akun Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
