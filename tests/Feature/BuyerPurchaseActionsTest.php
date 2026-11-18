<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BuyerPurchaseActionsTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private Product $product;
    private int $firstVariantId;
    private int $secondVariantId;

    protected function setUp(): void
    {
        parent::setUp();

        $seller = User::forceCreate([
            'nama_user' => 'seller-test',
            'email' => 'seller-purchase@example.test',
            'password' => Hash::make('Password#12345'),
            'role' => 'owner',
            'account_type' => 'seller',
        ]);
        $store = Store::forceCreate([
            'user_id' => $seller->getKey(),
            'name' => 'Toko Uji',
            'slug' => 'toko-uji',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'verification_status' => 'verified',
        ]);
        $category = Category::create(['name' => 'Kerajinan', 'slug' => 'kerajinan-uji']);
        $this->product = $store->products()->create([
            'category_id' => $category->id,
            'name' => 'Produk Uji',
            'slug' => 'produk-uji',
            'status' => 'active',
            'weight_gram' => 500,
        ]);
        $this->firstVariantId = $this->product->variants()->create([
            'label' => 'Varian satu',
            'price' => 25000,
            'stock' => 5,
        ])->id;
        $this->secondVariantId = $this->product->variants()->create([
            'label' => 'Varian dua',
            'price' => 30000,
            'stock' => 3,
        ])->id;
        $this->product->refreshPriceMin();

        $this->buyer = User::forceCreate([
            'nama_user' => 'buyer-test',
            'email' => 'buyer-purchase@example.test',
            'password' => Hash::make('Password#12345'),
            'role' => 'owner',
            'account_type' => 'buyer',
        ]);
        $this->buyer->addresses()->create([
            'recipient' => 'Pembeli Uji',
            'phone' => '08123456789',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postal_code' => '40132',
            'full_address' => 'Jalan Uji 1',
            'is_default' => true,
        ]);
    }

    public function test_buyer_can_add_a_variant_to_the_persistent_cart(): void
    {
        $this->actingAs($this->buyer)
            ->post(route('cart.items.store'), ['variant_id' => $this->firstVariantId, 'qty' => 2])
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->buyer->cart()->value('id'),
            'variant_id' => $this->firstVariantId,
            'qty' => 2,
        ]);
    }

    public function test_buy_now_checks_out_the_selected_variant_without_losing_the_existing_cart(): void
    {
        $this->actingAs($this->buyer)
            ->post(route('cart.items.store'), ['variant_id' => $this->firstVariantId, 'qty' => 2])
            ->assertRedirect(route('cart.index'));

        $this->post(route('checkout.buy-now'), ['variant_id' => $this->secondVariantId, 'qty' => 1])
            ->assertRedirect(route('checkout.create'));

        $this->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('Varian dua')
            ->assertDontSee('Varian satu');

        $this->get(route('cart.index'))->assertOk();
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->buyer->cart()->value('id'),
            'variant_id' => $this->firstVariantId,
            'qty' => 2,
        ]);
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $this->buyer->cart()->value('id'),
            'variant_id' => $this->secondVariantId,
        ]);
    }
}
