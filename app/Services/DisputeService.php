<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\DomainException;
use App\Models\Dispute;
use App\Models\StoreOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DisputeService
{
    public function __construct(private OrderStateMachine $sm, private OrderService $orders) {}

    public function open(StoreOrder $so, User $buyer, string $reason): Dispute
    {
        return DB::transaction(function () use ($so, $buyer, $reason) {
            $so = StoreOrder::whereKey($so->id)->lockForUpdate()->first();
            $this->sm->transition($so, OrderStatus::Disputed);

            $d = Dispute::forceCreate(['store_order_id' => $so->id, 'buyer_id' => $buyer->id, 'reason' => $reason]);
            NotificationService::notify($so->store->owner, 'dispute.opened', "Komplain baru pada pesanan {$so->order->code}.");
            AuditLogger::log('dispute.opened', $d, [], $buyer->id);

            return $d;
        });
    }

    /** @param string $decision refund|release */
    public function resolve(Dispute $d, string $decision, ?string $note, User $admin): void
    {
        DB::transaction(function () use ($d, $decision, $note, $admin) {
            $d = Dispute::whereKey($d->id)->lockForUpdate()->first();
            if ($d->status !== 'open') {
                throw new DomainException('Komplain sudah diselesaikan.', 'dispute_closed');
            }
            $so = StoreOrder::whereKey($d->store_order_id)->lockForUpdate()->first();

            if ($decision === 'refund') {
                $this->orders->refund($so, 'Sengketa dimenangkan pembeli');
            } else {
                $this->orders->complete($so);
            }

            $d->forceFill([
                'status' => $decision === 'refund' ? 'refunded' : 'resolved',
                'resolution_note' => $note, 'resolved_by' => $admin->id, 'resolved_at' => now(),
            ])->save();

            AuditLogger::log('dispute.resolved', $d, ['decision' => $decision], $admin->id);
        });
    }
}
