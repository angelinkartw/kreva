<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\User;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->role === 'employee') {
            $umkm = Umkm::find($user->id_umkm);
        } else {
            $umkm = Umkm::where('id_user', $user->id_user)->first();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data UMKM berhasil diambil',
            'data' => $umkm
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya owner yang dapat mendaftarkan UMKM.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_umkm' => 'required|string|max:255',
            'industry' => 'required',
            'usia_usaha' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $existingUmkm = Umkm::where('id_user', $user->id_user)->first();
        if ($existingUmkm) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki satu UMKM terdaftar.'
            ], 400);
        }

        try {
            $umkm = Umkm::create([
                'nama_umkm' => $request->nama_umkm,
                'industry' => $request->industry,
                'usia_usaha' => $request->usia_usaha,
                'id_user' => $user->id_user,
            ]);

            $user->update(['id_umkm' => $umkm->id_umkm]);

            // Invalidate cache
            Cache::forget('global_stats_total_umkm');
            Cache::forget('global_industry_counts');
            Cache::forget('global_age_counts');

            return response()->json([
                'success' => true,
                'message' => 'UMKM berhasil didaftarkan',
                'data' => $umkm
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftarkan UMKM: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKaryawan(Request $request)
    {
        $user = $request->user();
        
        if (!$user->id_umkm) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna belum terdaftar di UMKM manapun.'
            ], 400);
        }

        $karyawan = User::where('id_umkm', $user->id_umkm)
            ->where('role', 'employee')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar karyawan berhasil diambil',
            'data' => $karyawan
        ]);
    }

    public function storeKaryawan(Request $request)
    {
        $owner = $request->user();

        if ($owner->role !== 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya owner yang dapat menambahkan karyawan.'
            ], 403);
        }

        if (!$owner->id_umkm) {
            return response()->json([
                'success' => false,
                'message' => 'Daftarkan UMKM Anda terlebih dahulu sebelum menambahkan karyawan.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'nama_user' => 'required|string|max:255',
            'gender' => 'required',
            'age' => 'required|numeric',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $ageInput = $request->age;
        $ageCategory = 3;
        if ($ageInput < 30) {
            $ageCategory = 1;
        } elseif ($ageInput <= 40) {
            $ageCategory = 2;
        }

        try {
            $newEmployee = User::create([
                'nama_user' => $request->nama_user,
                'gender' => $request->gender,
                'age' => $ageCategory,
                'password' => Hash::make($request->password),
                'role' => 'employee',
                'id_umkm' => $owner->id_umkm,
            ]);

            $latestAssessment = Assessment::where('id_umkm', $owner->id_umkm)
                ->where('status', 'Selesai')
                ->latest()
                ->first();

            if ($latestAssessment) {
                $latestAssessment->update([
                    'status' => 'Menunggu',
                    'finished_at' => null,
                    'employee_finished' => false
                ]);
            }

            // Invalidate cache
            Cache::forget("umkm_latest_assessment_{$owner->id_umkm}");
            Cache::forget("umkm_avg_factors_{$owner->id_umkm}");
            Cache::forget("umkm_avg_total_score_{$owner->id_umkm}");
            if ($latestAssessment) {
                Cache::forget("umkm_insight_{$owner->id_umkm}_{$latestAssessment->id_assessment}");
            }
            Cache::forget("umkm_insight_empty_{$owner->id_umkm}");

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil didaftarkan',
                'data' => $newEmployee
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat akun karyawan: ' . $e->getMessage()
            ], 500);
        }
    }
}
