<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        App::setLocale('id');

        try {
            $request->validate([
                'nama_user' => ['required', 'string', 'max:100', 'unique:'.User::class.',nama_user'],
                'account_type' => ['required', 'in:seller,buyer'],
                'age' => ['nullable', 'required_if:account_type,seller', 'numeric', 'min:13', 'max:120'],
                'gender' => ['nullable', 'required_if:account_type,seller', Rule::in(['1', '2'])],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ], [
                'password.confirmed' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi di atas.',
            ]);
        } catch (QueryException $exception) {
            Log::error('Registration could not validate the user because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('database_error', 'Pendaftaran sementara tidak tersedia karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        $ageCategory = null;
        $gender = null;
        if ($request->account_type === 'seller') {
            $ageCategory = 3;
            $gender = $request->gender;
            if ($request->age < 30) {
                $ageCategory = 1;
            } elseif ($request->age <= 40) {
                $ageCategory = 2;
            }
        }

        try {
            $user = User::create([
                'nama_user' => $request->nama_user,
                'age' => $ageCategory,
                'gender' => $gender,
                'password' => Hash::make($request->password),
                'role' => 'owner',
                'account_type' => $request->account_type,
            ]);
        } catch (QueryException $exception) {
            Log::error('Registration could not save the user because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('database_error', 'Akun belum dapat dibuat karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        event(new Registered($user));

        Auth::login($user);

        $destination = $user->account_type === 'buyer' ? 'buyer.dashboard' : 'seller.dashboard';

        return redirect(route($destination, absolute: false));
    }
}
