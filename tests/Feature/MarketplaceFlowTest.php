<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MarketplaceFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private Store $store;
    private User $buyer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::forceCreate(['name' => 'Sari', 'email' => 's@t.test', 'password' => 'Password#12345', 'role' => Role::Seller, 'status' => 'active', 'email_verified_at' => now()]);
        $this->store = Store::forceCreate([
            'user_id' => $this->seller->id, 'name' => 'Toko', 'slug' => 'toko', 'province' => 'Jawa Barat', 'city' => 'Bandung',
            'verification_status' => 'verified', 'verified_at' => now(),
        ]);
        $cat = Category::create(['name' => 'Kerajinan', 'slug' => 'kerajinan']);
        $this->product = $this->store->products()->create(['category_id' => $cat->id, 'name' => 'Selendang', 'slug' => 'selendang-x', 'status' => 'active', 'weight_gram' => 500]);
        $this->product->variants()->create(['label' => 'Merah', 'price' => 100000, 'stock' => 5]);
        $this->product->refreshPriceMin();

        $this->buyer = User::forceCreate(['name' => 'Rina', 'email' => 'b@t.test', 'password' => 'Password#12345', 'role' => Role::Buyer, 'status' => 'active', 'email_verified_at' => now()]);
        $this->buyer->addresses()->create([
            'recipient' => 'Rina', 'phone' => '6281200000000', 'province' => 'Jawa Barat', 'city' => 'Bandung', 'district' => 'Coblong',
            'postal_code' => '40132', 'full_address' => 'Jl. A', 'is_default' => true,
        ]);
    }

    private function placeOrder(int $qty = 2): array
    {
        Sanctum::actingAs($this->buyer);
        $variant = $this->product->variants()->first();
        $this->postJson('/api/v1/cart/items', ['variant_id' => $variant->id, 'qty' => $qty])->assertOk();

        $addressId = $this->buyer->addresses()->first()->id;
        $res = $this->postJson('/api/v1/orders', [
            'address_id' => $addressId,
            'shipping' => [['store_id' => $this->store->id, 'courier' => 'jne', 'service' => 'reguler']],
        ])->assertCreated();

        return $res->json();
    }

    public function test_catalog_lists_only_visible_products(): void
    {
        $this->getJson('/api/v1/products')->assertOk()->assertJsonCount(1, 'data');
        $this->product->update(['status' => 'draft']);
        $this->getJson('/api/v1/products')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_full_order_lifecycle_with_idempotent_webhook_and_ledger(): void
    {
        $order = $this->placeOrder(2);
        $this->assertSame(207000, $order['total_paid']); // 200.000 + ongkir 7.000
        $this->assertSame(3, $this->product->variants()->first()->stock); // stok terkunci

        // Pembayaran: webhook dikirim dua kali -> hanya diproses sekali
        $this->postJson("/api/v1/dev/payments/{$order['code']}/simulate", ['status' => 'paid'])->assertOk()->assertJson(['result' => 'processed']);
        $this->postJson("/api/v1/dev/payments/{$order['code']}/simulate", ['status' => 'paid'])->assertOk()->assertJson(['result' => 'duplicate']);
        $this->assertSame(207000, app(LedgerService::class)->balance('escrow'));

        // Penjual memproses & mengirim
        Sanctum::actingAs($this->seller);
        $soId = $order['store_orders'][0]['id'];
        $this->patchJson("/api/v1/seller/orders/$soId/accept")->assertOk();
        $this->patchJson("/api/v1/seller/orders/$soId/ship", ['tracking_number' => 'JNE12345'])->assertOk();

        // Pembeli konfirmasi -> dana dilepas ke penjual dikurangi komisi 5% dari subtotal
        Sanctum::actingAs($this->buyer);
        $this->postJson("/api/v1/store-orders/$soId/confirm-received")->assertOk();

        $ledger = app(LedgerService::class);
        $this->assertSame(0, $ledger->balance('escrow'));
        $this->assertSame(207000 - 10000, $ledger->balance('seller', $this->store->id));
        $this->assertSame(10000, $ledger->balance('platform'));

        // Ulasan sekarang diperbolehkan
        $itemId = $order['store_orders'][0]['items'][0]['id'];
        $this->postJson('/api/v1/reviews', ['order_item_id' => $itemId, 'rating' => 5, 'comment' => 'Bagus'])->assertCreated();
        $this->assertEquals(5.0, $this->product->fresh()->rating_avg);
    }

    public function test_cannot_review_before_order_completed(): void
    {
        $order = $this->placeOrder(1);
        $this->postJson('/api/v1/reviews', ['order_item_id' => $order['store_orders'][0]['items'][0]['id'], 'rating' => 5])->assertStatus(422);
    }

    public function test_cannot_add_more_than_stock(): void
    {
        Sanctum::actingAs($this->buyer);
        $this->postJson('/api/v1/cart/items', ['variant_id' => $this->product->variants()->first()->id, 'qty' => 6])->assertStatus(422);
    }

    public function test_expired_order_restores_stock(): void
    {
        $order = $this->placeOrder(2);
        \App\Models\Order::whereKey($order['id'])->update(['expired_at' => now()->subMinute()]);

        $this->artisan('kreva:expire-orders')->assertSuccessful();

        $this->assertSame(5, $this->product->variants()->first()->stock);
        $this->assertSame('EXPIRED', \App\Models\Order::find($order['id'])->status->value);
    }

    public function test_webhook_with_invalid_signature_is_rejected(): void
    {
        $this->postJson('/api/v1/webhooks/payment', ['order_id' => 'KRV-X', 'status_code' => '200', 'gross_amount' => '1000.00', 'signature_key' => 'salah'])->assertForbidden();
    }

    public function test_buyer_cannot_see_other_buyers_order(): void
    {
        $order = $this->placeOrder(1);
        $other = User::forceCreate(['name' => 'X', 'email' => 'x@t.test', 'password' => 'Password#12345', 'role' => Role::Buyer, 'status' => 'active', 'email_verified_at' => now()]);
        Sanctum::actingAs($other);
        $this->getJson('/api/v1/orders/'.$order['id'])->assertNotFound(); // IDOR dicegah
    }

    public function test_seller_cannot_touch_other_sellers_product(): void
    {
        $other = User::forceCreate(['name' => 'Y', 'email' => 'y@t.test', 'password' => 'Password#12345', 'role' => Role::Seller, 'status' => 'active', 'email_verified_at' => now()]);
        Store::forceCreate(['user_id' => $other->id, 'name' => 'T2', 'slug' => 't2', 'verification_status' => 'verified']);
        Sanctum::actingAs($other);
        $this->patchJson('/api/v1/seller/products/'.$this->product->id, ['name' => 'Hack'])->assertNotFound();
    }

    public function test_buyer_cannot_access_seller_or_admin_routes(): void
    {
        Sanctum::actingAs($this->buyer);
        $this->getJson('/api/v1/seller/products')->assertForbidden();
        $this->getJson('/api/v1/admin/stores')->assertForbidden();
    }
}
