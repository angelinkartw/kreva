<?php

namespace App\Http\Controllers\Seller;

use App\Enums\Role;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Models\SellerBankAccount;
use App\Models\Store;
use App\Services\AuditLogger;
use App\Services\AuthService;
use App\Support\Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    /** Wizard pendaftaran toko: pembeli mengajukan toko -> status pending -> diverifikasi admin. */
    public function onboard(Request $request)
    {
        $user = $request->user();
        if ($user->store()->exists()) {
            throw new DomainException('Anda sudah memiliki toko.', 'store_exists');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'story' => ['nullable', 'string', 'max:5000'],
            'province' => ['required', 'string', 'max:80'], 'city' => ['required', 'string', 'max:80'],
            'district' => ['required', 'string', 'max:80'], 'postal_code' => ['required', 'digits:5'],
            'origin_address' => ['required', 'string', 'max:500'],
            'ktp_number' => ['required', 'digits:16'],
            'npwp_number' => ['nullable', 'string', 'max:25'],
            'bank' => ['required', 'array'],
            'bank.bank' => ['required', 'string', 'max:50'],
            'bank.account_number' => ['required', 'digits_between:6,20'],
            'bank.account_holder' => ['required', 'string', 'max:100'],
        ]);

        return DB::transaction(function () use ($user, $data) {
            $bank = $data['bank'];
            unset($data['bank']);

            $store = $user->store()->create([
                ...$data,
                'description' => Html::clean($data['description'] ?? null),
                'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(5)),
                'enabled_couriers' => ['jne', 'jnt', 'sicepat'],
            ]);
            $store->bankAccount()->create([...$bank, 'verified' => false, 'changed_at' => now()]);

            $user->forceFill(['role' => Role::Seller])->save();
            AuditLogger::log('seller.onboarded', $store);

            return response()->json($store, 201);
        });
    }

    public function show(Request $request)
    {
        return $this->store($request)->load('bankAccount');
    }

    public function update(Request $request)
    {
        $store = $this->store($request);
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'story' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'logo' => ['sometimes', 'nullable', 'string', 'max:300'],
            'banner' => ['sometimes', 'nullable', 'string', 'max:300'],
            'province' => ['sometimes', 'string', 'max:80'], 'city' => ['sometimes', 'string', 'max:80'],
            'district' => ['sometimes', 'string', 'max:80'], 'postal_code' => ['sometimes', 'digits:5'],
            'origin_address' => ['sometimes', 'string', 'max:500'],
            'operating_hours' => ['sometimes', 'nullable', 'array'],
            'enabled_couriers' => ['sometimes', 'array', 'min:1'],
            'enabled_couriers.*' => ['in:jne,jnt,sicepat,anteraja,pos'],
        ]);
        foreach (['description', 'story'] as $k) {
            if (array_key_exists($k, $data)) {
                $data[$k] = Html::clean($data[$k]);
            }
        }
        $store->update($data);

        return $store;
    }

    /** Langkah 1 ganti rekening: kirim OTP ke email penjual. */
    public function bankOtp(Request $request, AuthService $auth)
    {
        $auth->sendOtp('email', $request->user()->email, 'bank_change');

        return response()->json(['message' => 'Kode verifikasi dikirim ke email Anda.']);
    }

    /** Langkah 2: ganti rekening (wajib OTP). Rekening ditandai belum terverifikasi + masa tunggu sebelum penarikan. */
    public function updateBank(Request $request, AuthService $auth)
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
            'bank' => ['required', 'string', 'max:50'],
            'account_number' => ['required', 'digits_between:6,20'],
            'account_holder' => ['required', 'string', 'max:100'],
        ]);
        $auth->verifyOtp($request->user()->email, 'bank_change', $data['code']);

        $store = $this->store($request);
        $account = $store->bankAccount ?? new SellerBankAccount(['store_id' => $store->id]);
        $account->store_id = $store->id;
        $account->fill([
            'bank' => $data['bank'], 'account_number' => $data['account_number'],
            'account_holder' => $data['account_holder'], 'verified' => false, 'changed_at' => now(),
        ])->save();

        AuditLogger::log('seller.bank_changed', $store);

        return response()->json(['message' => 'Rekening diperbarui. Menunggu verifikasi admin dan masa tunggu penarikan.']);
    }

    private function store(Request $request): Store
    {
        return $request->user()->store ?? abort(404, 'Toko belum dibuat.');
    }
}
