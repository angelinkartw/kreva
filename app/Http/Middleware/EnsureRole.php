<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /** Pemakaian: ->middleware('role:seller') atau 'role:admin,support' */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole(...$roles) || $user->status !== 'active') {
            abort(403, 'Anda tidak memiliki akses ke sumber daya ini.');
        }

        return $next($request);
    }
}
