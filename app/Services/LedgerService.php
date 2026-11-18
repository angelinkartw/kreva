<?php

namespace App\Services;

use App\Models\LedgerEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Setiap perubahan saldo = entri baru (append-only).
 * Akun: escrow (dana ditahan platform), seller (per store_id), platform (komisi).
 */
class LedgerService
{
    public function post(string $accountType, int $accountId, string $type, int $amount, ?Model $ref = null, ?string $note = null): LedgerEntry
    {
        return DB::transaction(function () use ($accountType, $accountId, $type, $amount, $ref, $note) {
            $last = LedgerEntry::where('account_type', $accountType)->where('account_id', $accountId)
                ->orderByDesc('id')->lockForUpdate()->value('balance_after') ?? 0;

            return LedgerEntry::forceCreate([
                'account_type' => $accountType,
                'account_id' => $accountId,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $last + $amount,
                'reference_type' => $ref?->getMorphClass(),
                'reference_id' => $ref?->getKey(),
                'note' => $note,
            ]);
        });
    }

    public function balance(string $accountType, int $accountId = 0): int
    {
        return (int) (LedgerEntry::where('account_type', $accountType)->where('account_id', $accountId)
            ->orderByDesc('id')->value('balance_after') ?? 0);
    }
}
