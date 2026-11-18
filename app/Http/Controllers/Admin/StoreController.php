<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\AuditLogger;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        return Store::with('owner:id,name,email')
            ->when($request->query('status'), fn ($q, $s) => $q->where('verification_status', $s))
            ->latest('id')->cursorPaginate(30);
    }

    /** Admin melihat KTP/rekening untuk verifikasi: akses dicatat di audit log. */
    public function show(Request $request, int $id)
    {
        $store = Store::with('owner:id,name,email,phone', 'bankAccount')->findOrFail($id);
        AuditLogger::log('admin.view_seller_identity', $store);

        $data = $store->makeVisible(['ktp_number', 'npwp_number'])->toArray();
        if ($store->bankAccount) {
            $data['bank_account'] = [
                'bank' => $store->bankAccount->bank, 'account_number_masked' => $store->bankAccount->masked(),
                'account_holder' => $store->bankAccount->account_holder, 'verified' => $store->bankAccount->verified,
                'name_matches_owner' => strcasecmp(trim($store->bankAccount->account_holder), trim($store->owner->name)) === 0,
            ];
        }

        return $data;
    }

    public function verify(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'in:verified,rejected,suspended'],
            'note' => ['nullable', 'string', 'max:500'],
            'bank_verified' => ['sometimes', 'boolean'],
        ]);
        $store = Store::with('bankAccount')->findOrFail($id);

        $store->forceFill([
            'verification_status' => $data['status'], 'verification_note' => $data['note'] ?? null,
            'verified_at' => $data['status'] === 'verified' ? now() : null,
        ])->save();

        if (isset($data['bank_verified']) && $store->bankAccount) {
            $store->bankAccount->forceFill(['verified' => $data['bank_verified']])->save();
        }

        AuditLogger::log('admin.store_'.$data['status'], $store, ['note' => $data['note'] ?? null]);
        NotificationService::notify($store->owner, 'store.verification', 'Status verifikasi toko Anda: '.$data['status']);

        return $store;
    }
}
