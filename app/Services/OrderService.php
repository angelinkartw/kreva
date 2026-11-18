<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\DomainException;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\StoreOrder;
use App\Models\User;
use App\Services\Payment\PaymentGateway;
use App\Services\Shipping\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private OrderStateMachine $sm,
        private LedgerService $ledger,
        private ShippingService $shipping,
        private PaymentGateway $gateway,
    ) {}

    /** Ambil isi keranjang server dikelompokkan per toko. */
    public function cartGroups(User $user)
    {
        $cart = $user->cart()->with('items.variant.product.store')->first();

        return ($cart?->items ?? collect())
            ->filter(fn ($i) => $i->variant?->product?->store)
            ->groupBy(fn ($i) => $i->variant->product->store_id);
    }

    /** Opsi ongkir per toko untuk alamat tertentu (dihitung di server). */
    public function shippingOptions(User $user, Address $address): array
    {
        $result = [];
        foreach ($this->cartGroups($user) as $storeId => $items) {
            $store = $items->first()->variant->product->store;
            $weight = $items->sum(fn ($i) => $i->variant->product->weight_gram * $i->qty);
            $result[] = [
                'store_id' => $storeId,
                'store_name' => $store->name,
                'weight_gram' => $weight,
                'options' => $this->shipping->options($store, $address, $weight),
            ];
        }

        return $result;
    }

    /**
     * Buat pesanan: validasi ulang harga/stok/ongkir, kunci stok atomik, buat Order + StoreOrder + OrderItem,
     * lalu minta transaksi ke payment gateway. Seluruhnya dalam satu transaksi DB.
     *
     * @param  array{address_id:int, shipping: array<int, array{store_id:int, courier:string, service:string}>}  $data
     */
    public function create(User $buyer, array $data): Order
    {
        $address = $buyer->addresses()->findOrFail($data['address_id']);
        $groups = $this->cartGroups($buyer);

        if ($groups->isEmpty()) {
            throw new DomainException('Keranjang kosong.', 'cart_empty');
        }

        $chosen = collect($data['shipping'])->keyBy('store_id');
        foreach ($groups->keys() as $storeId) {
            if (! $chosen->has($storeId)) {
                throw new DomainException('Pilih kurir untuk setiap toko di keranjang.', 'shipping_missing');
            }
        }

        return DB::transaction(function () use ($buyer, $address, $groups, $chosen) {
            $order = new Order;
            $order->forceFill([
                'code' => 'KRV-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
                'buyer_id' => $buyer->getKey(),
                'status' => OrderStatus::PendingPayment,
                'total_items' => 0, 'total_shipping' => 0, 'total_paid' => 0,
                'address_snapshot' => $address->only(['recipient', 'phone', 'province', 'city', 'district', 'postal_code', 'full_address', 'latitude', 'longitude']),
                'expired_at' => now()->addHours((int) config('kreva.payment_expire_hours')),
            ])->save();

            // Kunci varian dengan urutan id konsisten (cegah deadlock) lalu potong stok.
            $locked = [];
            $allItems = $groups->flatten(1)->sortBy('variant_id');
            foreach ($allItems as $item) {
                $variant = ProductVariant::with('product')->whereKey($item->variant_id)->lockForUpdate()->first();
                $product = $variant?->product;

                if (! $variant || ! $product || ! $product->store
                    || $product->status !== 'active'
                    || ! $product->store->isVerified()) {
                    throw new DomainException('Produk "'.($product->name ?? 'tidak dikenal').'" tidak tersedia.', 'product_unavailable');
                }
                if ($variant->stock < $item->qty) {
                    throw new DomainException("Stok \"{$product->name}\" tidak mencukupi (tersisa {$variant->stock}).", 'out_of_stock');
                }

                $variant->decrement('stock', $item->qty);
                $locked[$item->id] = $variant;
            }

            $totalItems = 0;
            $totalShipping = 0;

            foreach ($groups as $storeId => $items) {
                $store = $items->first()->variant->product->store;
                $weight = $items->sum(fn ($i) => $i->variant->product->weight_gram * $i->qty);
                $pick = $chosen[$storeId];

                $option = collect($this->shipping->options($store, $address, $weight))
                    ->first(fn ($o) => $o['courier'] === $pick['courier'] && $o['service'] === $pick['service']);
                if (! $option) {
                    throw new DomainException("Layanan kurir untuk toko {$store->name} tidak tersedia.", 'shipping_invalid');
                }

                $so = new StoreOrder;
                $so->forceFill([
                    'order_id' => $order->id, 'store_id' => $storeId, 'status' => OrderStatus::PendingPayment,
                    'courier' => $option['courier'], 'service' => $option['service'],
                    'subtotal' => 0, 'shipping_cost' => $option['cost'],
                ])->save();

                $subtotal = 0;
                foreach ($items as $item) {
                    $variant = $locked[$item->id];
                    OrderItem::forceCreate([
                        'store_order_id' => $so->id,
                        'product_id' => $variant->product_id,
                        'variant_id' => $variant->id,
                        'name_snapshot' => $variant->product->name,
                        'variant_label_snapshot' => $variant->label,
                        'price_snapshot' => $variant->price,   // harga dari DB, bukan dari klien
                        'qty' => $item->qty,
                    ]);
                    $subtotal += $variant->price * $item->qty;
                }

                $so->forceFill(['subtotal' => $subtotal])->save();
                $totalItems += $subtotal;
                $totalShipping += $option['cost'];
            }

            $order->forceFill([
                'total_items' => $totalItems,
                'total_shipping' => $totalShipping,
                'total_paid' => $totalItems + $totalShipping,
            ])->save();

            $trx = $this->gateway->createTransaction($order, ['name' => $buyer->name, 'email' => $buyer->email, 'phone' => $buyer->phone]);
            Payment::forceCreate([
                'order_id' => $order->id, 'gateway' => $this->gateway->name(), 'method' => $trx['method'],
                'gateway_reference' => $trx['reference'], 'redirect_url' => $trx['redirect_url'],
                'status' => 'pending', 'amount' => $order->total_paid,
            ]);

            // Kosongkan item keranjang yang sudah dipesan
            $buyer->cart->items()->whereIn('id', $allItems->pluck('id'))->delete();

            return $order->load('storeOrders.items', 'payment');
        });
    }

    /** Kedaluwarsa (belum dibayar) -> kembalikan stok. */
    public function expire(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();
            if ($order->status !== OrderStatus::PendingPayment) {
                return;
            }
            $this->sm->transition($order, OrderStatus::Expired);
            foreach ($order->storeOrders as $so) {
                $this->sm->transition($so, OrderStatus::Expired);
                $this->restoreStock($so);
            }
            $order->payment?->forceFill(['status' => 'expired'])->save();
        });
    }

    /** Pembeli membatalkan: belum bayar -> batal; sudah bayar tapi belum diproses -> batal + refund. */
    public function cancelByBuyer(Order $order, ?string $reason = null): void
    {
        DB::transaction(function () use ($order, $reason) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if ($order->status === OrderStatus::PendingPayment) {
                $this->sm->transition($order, OrderStatus::Cancelled);
                foreach ($order->storeOrders as $so) {
                    $this->sm->transition($so, OrderStatus::Cancelled);
                    $this->restoreStock($so);
                }
                $order->payment?->forceFill(['status' => 'expired'])->save();

                return;
            }

            if ($order->status === OrderStatus::Paid && $order->storeOrders->every(fn ($s) => $s->status === OrderStatus::Paid)) {
                foreach ($order->storeOrders as $so) {
                    $this->refund($so, $reason ?? 'Dibatalkan pembeli');
                }
                $this->sm->transition($order, OrderStatus::Cancelled);

                return;
            }

            throw new DomainException('Pesanan tidak dapat dibatalkan pada status ini.', 'cannot_cancel');
        });
    }

    /** Batalkan sub-pesanan yang sudah dibayar: stok kembali, dana keluar dari escrow, refund ditandai untuk diproses admin. */
    public function refund(StoreOrder $so, string $reason): void
    {
        DB::transaction(function () use ($so, $reason) {
            $so = StoreOrder::whereKey($so->id)->lockForUpdate()->first();
            $this->sm->transition($so, OrderStatus::Cancelled);
            $so->forceFill(['cancel_reason' => $reason])->save();
            $this->restoreStock($so);

            $amount = $so->subtotal + $so->shipping_cost;
            $this->ledger->post('escrow', 0, 'refund', -$amount, $so, $reason);

            AuditLogger::log('order.refund_required', $so, ['amount' => $amount, 'reason' => $reason, 'order_code' => $so->order->code]);
            NotificationService::notify($so->order->buyer, 'order.refund', "Pesanan {$so->order->code} dibatalkan. Dana Rp ".number_format($amount, 0, ',', '.').' akan dikembalikan.');
        });
    }

    public function accept(StoreOrder $so): void
    {
        $this->sm->transition($so, OrderStatus::Processing);
        NotificationService::notify($so->order->buyer, 'order.processing', "Pesanan {$so->order->code} sedang diproses penjual.");
    }

    public function ship(StoreOrder $so, string $trackingNumber): void
    {
        DB::transaction(function () use ($so, $trackingNumber) {
            $this->sm->transition($so, OrderStatus::Shipped);
            $so->forceFill(['tracking_number' => $trackingNumber])->save();
            Shipment::forceCreate(['store_order_id' => $so->id, 'courier' => $so->courier, 'tracking_number' => $trackingNumber]);
            NotificationService::notify($so->order->buyer, 'order.shipped', "Pesanan {$so->order->code} dikirim. Resi: $trackingNumber");
        });
    }

    /** Pembeli konfirmasi diterima (atau job otomatis): -> DELIVERED -> COMPLETED. */
    public function confirmReceived(StoreOrder $so): void
    {
        DB::transaction(function () use ($so) {
            $so = StoreOrder::whereKey($so->id)->lockForUpdate()->first();
            if ($so->status === OrderStatus::Shipped) {
                $this->sm->transition($so, OrderStatus::Delivered);
            }
            $this->complete($so);
        });
    }

    /** Selesai: lepas dana dari escrow -> saldo penjual (dikurangi komisi). */
    public function complete(StoreOrder $so): void
    {
        DB::transaction(function () use ($so) {
            $this->sm->transition($so, OrderStatus::Completed);

            $gross = $so->subtotal + $so->shipping_cost;
            $rate = (float) Setting::get('commission_percent', config('kreva.commission_percent'));
            $commission = (int) round($so->subtotal * $rate / 100);

            $so->forceFill(['commission' => $commission])->save();

            $this->ledger->post('escrow', 0, 'release', -$gross, $so);
            $this->ledger->post('seller', $so->store_id, 'release', $gross - $commission, $so, 'Dana pesanan diteruskan');
            $this->ledger->post('platform', 0, 'commission', $commission, $so, "Komisi {$rate}%");

            foreach ($so->items as $item) {
                $item->product()->increment('sold_count', $item->qty);
            }

            NotificationService::notify($so->store->owner, 'order.completed', "Pesanan {$so->order->code} selesai. Dana masuk ke saldo Anda.");
        });
    }

    private function restoreStock(StoreOrder $so): void
    {
        foreach ($so->items as $item) {
            ProductVariant::whereKey($item->variant_id)->increment('stock', $item->qty);
        }
    }
}
