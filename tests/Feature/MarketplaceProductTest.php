<?php

use App\Models\Product;
use App\Models\Order;
use App\Models\User;

function marketplaceUser(string $username, string $accountType = 'seller'): User
{
    return User::create([
        'nama_user' => $username,
        'password' => 'password',
        'role' => 'owner',
        'account_type' => $accountType,
        'age' => 1,
        'gender' => 2,
    ]);
}

test('sellers can create update and delete their products', function () {
    $seller = marketplaceUser('seller1');

    $this->actingAs($seller)
        ->post(route('seller.products.store'), [
            'nama_produk' => 'Sambal UMKM',
            'kategori' => 'Makanan',
            'deskripsi' => 'Sambal rumahan',
            'harga' => 25000,
            'stok' => 12,
        ])
        ->assertRedirect(route('seller.products.index'));

    $product = Product::firstOrFail();
    $this->assertSame($seller->id_user, $product->id_user);

    $this->put(route('seller.products.update', $product), [
        'nama_produk' => 'Sambal Premium',
        'kategori' => 'Makanan',
        'deskripsi' => 'Sambal rumahan premium',
        'harga' => 30000,
        'stok' => 8,
    ])->assertRedirect(route('seller.products.index'));

    $this->assertDatabaseHas('products', [
        'id_produk' => $product->id_produk,
        'nama_produk' => 'Sambal Premium',
        'stok' => 8,
    ]);

    $this->delete(route('seller.products.destroy', $product))
        ->assertRedirect(route('seller.products.index'));

    $this->assertDatabaseMissing('products', ['id_produk' => $product->id_produk]);
});

test('sellers cannot change another seller product and buyers cannot access product management', function () {
    $owner = marketplaceUser('owner1');
    $otherSeller = marketplaceUser('seller2');
    $buyer = marketplaceUser('buyer1', 'buyer');
    $product = Product::create([
        'id_user' => $owner->id_user,
        'nama_produk' => 'Produk Privat',
        'kategori' => 'Kerajinan',
        'harga' => 50000,
        'stok' => 3,
    ]);

    $this->actingAs($otherSeller)
        ->get(route('seller.products.edit', $product))
        ->assertNotFound();

    $this->actingAs($buyer)
        ->get(route('seller.products.index'))
        ->assertForbidden();

    $this->actingAs($buyer)
        ->get(route('dashboard'))
        ->assertForbidden();

    $this->actingAs($buyer)
        ->get(route('buyer.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Pembeli')
        ->assertSee('Produk Privat');

    $this->actingAs($otherSeller)
        ->get(route('buyer.dashboard'))
        ->assertForbidden();
});

test('seller dashboard shows the authenticated sellers products', function () {
    $seller = marketplaceUser('seller-dashboard');
    Product::create([
        'id_user' => $seller->id_user,
        'nama_produk' => 'Produk Tersedia',
        'kategori' => 'Makanan',
        'harga' => 25000,
        'stok' => 2,
    ]);
    Product::create([
        'id_user' => $seller->id_user,
        'nama_produk' => 'Produk Habis',
        'kategori' => 'Makanan',
        'harga' => 25000,
        'stok' => 0,
    ]);

    $this->actingAs($seller)
        ->get(route('seller.dashboard'))
        ->assertOk()
        ->assertSee('Produk Tersedia')
        ->assertSee('Produk Habis')
        ->assertSee('Total Produk');
});

test('homepage shows products published by sellers from the database', function () {
    $seller = marketplaceUser('landing-seller');
    Product::create([
        'id_user' => $seller->id_user,
        'nama_produk' => 'Kopi Lokal',
        'kategori' => 'Minuman',
        'harga' => 18000,
        'stok' => 5,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Kopi Lokal')
        ->assertSee('landing-seller');
});

test('buyers can manage their cart and place an order', function () {
    $buyer = marketplaceUser('checkout-buyer', 'buyer');
    $seller = marketplaceUser('checkout-seller');
    $product = Product::create([
        'id_user' => $seller->id_user,
        'nama_produk' => 'Kopi Checkout',
        'kategori' => 'Minuman',
        'harga' => 18000,
        'stok' => 4,
    ]);

    $this->actingAs($buyer)
        ->post(route('cart.items.store'), [
            'product_id' => $product->id_produk,
            'quantity' => 2,
        ])
        ->assertRedirect(route('cart.index'));

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee('Kopi Checkout');

    $this->get(route('checkout.create'))
        ->assertOk()
        ->assertSee('Alamat pengiriman');

    $this->post(route('checkout.store'), [
        'nama_penerima' => 'Pembeli Checkout',
        'telepon' => '08123456789',
        'alamat' => 'Jl. Contoh No. 1',
    ])->assertRedirect();

    $order = Order::with('items')->firstOrFail();
    $this->assertSame($buyer->id_user, $order->id_user);
    $this->assertSame('36000.00', $order->total);
    $this->assertSame(1, $order->items->count());
    $this->assertSame(2, $order->items->first()->kuantitas);
    $this->assertDatabaseHas('products', [
        'id_produk' => $product->id_produk,
        'stok' => 2,
    ]);

    $this->get(route('buyer.orders.show', $order))
        ->assertOk()
        ->assertSee('Pesanan berhasil dibuat')
        ->assertSee('Kopi Checkout');
});

test('only buyers can checkout and product stock cannot be oversold', function () {
    $buyer = marketplaceUser('stock-buyer', 'buyer');
    $seller = marketplaceUser('stock-seller');
    $product = Product::create([
        'id_user' => $seller->id_user,
        'nama_produk' => 'Produk Stok Terbatas',
        'kategori' => 'Kerajinan',
        'harga' => 10000,
        'stok' => 1,
    ]);

    $this->actingAs($seller)
        ->get(route('checkout.create'))
        ->assertForbidden();

    $this->actingAs($buyer)
        ->from(route('marketplace.index'))
        ->post(route('cart.items.store'), [
            'product_id' => $product->id_produk,
            'quantity' => 2,
        ])
        ->assertSessionHasErrors('quantity');
});

test('login sends sellers and buyers to their own home pages', function () {
    $seller = marketplaceUser('login-seller');
    $buyer = marketplaceUser('login-buyer', 'buyer');

    $this->post(route('login'), [
        'username' => $seller->nama_user,
        'account_type' => 'seller',
        'password' => 'password',
    ])->assertRedirect(route('seller.dashboard', absolute: false));

    auth()->logout();

    $this->post(route('login'), [
        'username' => $buyer->nama_user,
        'account_type' => 'buyer',
        'password' => 'password',
    ])->assertRedirect(route('buyer.dashboard', absolute: false));
});

test('login rejects an account type that does not match the username', function () {
    $seller = marketplaceUser('type-check-seller');

    $this->post(route('login'), [
        'username' => $seller->nama_user,
        'account_type' => 'buyer',
        'password' => 'password',
    ])->assertSessionHasErrors('username');

    $this->assertGuest();
});
