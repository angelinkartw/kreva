<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_user' => ['required', 'string', 'max:100', 'unique:users,nama_user'],
            'age' => ['required', 'numeric'],
            'gender' => ['required', 'in:1,2'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Kategori Umur
        $ageInput = $request->age;
        $ageCategory = 3; // Default > 40
        if ($ageInput < 30) {
            $ageCategory = 1;
        } elseif ($ageInput <= 40) {
            $ageCategory = 2;
        }

        $user = User::create([
            'nama_user' => $request->nama_user,
            'age' => $ageCategory,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'role' => 'owner',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_user' => 'required_without:email|string',
            'email' => 'required_without:nama_user|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $username = $request->nama_user ?? $request->email;
        $user = User::where('nama_user', $username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial tidak valid'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $umkm = null;
        if ($user->id_umkm) {
            $umkm = \App\Models\Umkm::find($user->id_umkm);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diambil',
            'data' => [
                'user' => $user,
                'umkm' => $umkm
            ]
        ]);
    }
}
