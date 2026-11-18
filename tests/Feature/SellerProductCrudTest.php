<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::forceCreate([
            'nama_user' => 'penjual-test',
            'email' => 'penjual-test@example.test',
            'password' => Hash::make('Password#12345'),
            'role' => 'owner',
            'account_type' => 'seller',
        ]);
    }

    public function test_seller_can_create_update_and_delete_a_product_visible_to_buyers(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'Makanan', 'slug' => 'makanan']);
        $otherCategory = Category::create(['name' => 'Minuman', 'slug' => 'minuman']);

        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), [
                'name' => 'Kopi Lokal',
                'category_id' => $category->id,
                'description' => 'Kopi dari kebun lokal.',
                'price' => 45000,
                'stock' => 12,
                'weight_gram' => 250,
                'image' => UploadedFile::fake()->createWithContent(
                    'kopi.jpg',
                    file_get_contents(public_path('images/craft1.jpg')),
                ),
            ])
            ->assertRedirect(route('seller.products.index'));

        $product = Product::query()->with('variants')->firstOrFail();
        $this->assertSame('active', $product->status);
        $this->assertSame(45000, $product->price_min);
        $this->assertSame(12, $product->variants->first()->stock);
        $imagePath = $product->images()->value('path');
        $this->assertNotNull($imagePath);
        Storage::disk('public')->assertExists($imagePath);

        $this->get(route('marketplace.index'))
            ->assertOk()
            ->assertSee('Kopi Lokal')
            ->assertSee('Makanan')
            ->assertSee(Storage::url($imagePath));
        $this->get(route('home'))->assertOk()->assertSee(Storage::url($imagePath));
        $this->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('Hapus')
            ->assertSee(route('seller.products.destroy', ['product' => $product->id]), false)
            ->assertDontSee('name="image"', false)
            ->assertSee(Storage::url($imagePath));

        $buyer = User::forceCreate([
            'nama_user' => 'pembeli-test',
            'email' => 'pembeli-test@example.test',
            'password' => Hash::make('Password#12345'),
            'role' => 'owner',
            'account_type' => 'buyer',
        ]);
        $this->actingAs($buyer)->get(route('buyer.dashboard'))
            ->assertOk()
            ->assertSee(Storage::url($imagePath));

        $this->actingAs($this->seller);
        $this->get(route('marketplace.index', ['category_id' => $otherCategory->id]))
            ->assertOk()
            ->assertDontSee('Kopi Lokal');

        $this->put(route('seller.products.update', $product), [
            'name' => 'Kopi Lokal Premium',
            'category_id' => $otherCategory->id,
            'description' => 'Kopi pilihan.',
            'price' => 50000,
            'stock' => 8,
            'weight_gram' => 250,
        ])->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Kopi Lokal Premium',
            'price_min' => 50000,
        ]);
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'price' => 50000,
            'stock' => 8,
        ]);

        $this->get(route('seller.dashboard'))->assertOk();
        $this->deleteJson(route('seller.products.destroy', ['product' => $product->id]))
            ->assertOk()
            ->assertJson([
                'message' => 'Produk berhasil dihapus.',
                'stats' => [
                    'total_products' => 0,
                    'active_products' => 0,
                    'out_of_stock' => 0,
                ],
            ]);
        $this->deleteJson(route('seller.products.destroy', ['product' => $product->id]))
            ->assertOk()
            ->assertJson([
                'message' => 'Produk sudah tidak tersedia di etalase.',
                'stats' => ['total_products' => 0],
            ]);
        $this->get(route('seller.dashboard'))->assertOk();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->get(route('marketplace.index'))->assertDontSee('Kopi Lokal Premium');
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_seller_cannot_edit_another_sellers_product(): void
    {
        $otherSeller = User::forceCreate([
            'nama_user' => 'penjual-lain',
            'email' => 'penjual-lain@example.test',
            'password' => Hash::make('Password#12345'),
            'role' => 'owner',
            'account_type' => 'seller',
        ]);
        $store = Store::forceCreate([
            'user_id' => $otherSeller->id_user,
            'name' => 'Toko lain',
            'slug' => 'toko-lain',
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
        $category = Category::create(['name' => 'Makanan', 'slug' => 'makanan']);
        $product = $store->products()->create([
            'category_id' => $category->id,
            'name' => 'Produk asli',
            'slug' => 'produk-asli',
            'status' => 'active',
            'weight_gram' => 100,
        ]);

        $this->actingAs($otherSeller)
            ->put(route('seller.products.update', $product), [
                'name' => 'Produk diubah',
                'category_id' => $category->id,
                'price' => 1000,
                'stock' => 1,
                'weight_gram' => 100,
            ])
            ->assertNotFound();

        $this->assertNotSame('Produk diubah', $product->fresh()->name);
    }
}
