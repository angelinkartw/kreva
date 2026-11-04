<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Karyawan (role employee) hanya boleh mengakses halaman asesmen;
 * akses ke rute lain diarahkan ke halaman asesmen.
 */
class RestrictEmployeeFromOwnerPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'employee') {
            return redirect()->route('dashboard.asesmen')
                ->with('info', 'Sebagai karyawan, Anda hanya dapat mengakses Asesmen Organisasi.');
        }

        if ($user && $user->account_type === 'buyer') {
            return redirect()->route('marketplace.index');
        }

        return $next($request);
    }
}
