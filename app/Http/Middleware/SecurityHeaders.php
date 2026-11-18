<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        // Correlation ID untuk log terpusat
        $id = $request->header('X-Request-Id') ?: (string) Str::uuid();
        Log::withContext(['request_id' => $id]);

        $response = $next($request);

        $response->headers->add([
            'X-Request-Id' => $id,
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'Content-Security-Policy' => "default-src 'none'; frame-ancestors 'none'",
            'Cache-Control' => $response->headers->get('Cache-Control') ?: 'no-store',
        ]);

        return $response;
    }
}
