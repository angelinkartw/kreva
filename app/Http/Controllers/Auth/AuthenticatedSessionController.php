<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (QueryException $exception) {
            Log::error('Login could not query the user because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return back()
                ->withInput($request->only('username', 'account_type'))
                ->with('database_error', 'Login sementara tidak tersedia karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        $request->session()->regenerate();

        $home = match (true) {
            $request->user()->role === 'employee' => route('dashboard.asesmen', absolute: false),
            $request->user()->account_type === 'buyer' => route('buyer.dashboard', absolute: false),
            default => route('seller.dashboard', absolute: false),
        };

        return redirect()->intended($home);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
