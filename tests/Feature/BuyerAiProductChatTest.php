<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductConversation;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BuyerAiProductChatTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;

    private User $buyer;

    private Store $store;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.openai.api_key' => 'test-api-key']);

        $this->seller = User::forceCreate([
            'nama_user' => 'penjual-test',
            'email' => 'penjual-test@example.test',
            'password' => Hash::make('Password#12345'),
            'role' => 'owner',
            'account_type' => 'seller',
        ]);
        $this->store = Store::create([
            'user_id' => $this->seller->id_user,
            'name' => 'Toko Test',
            'slug' => 'toko-test',
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
        $this->buyer = User::forceCreate([
            'nama_user' => 'pembeli-test',
            'email' => 'pembeli-test@example.test',
            'password' => Hash::make('Password#12345'),
            'role' => 'owner',
            'account_type' => 'buyer',
        ]);
        $category = Category::create(['name' => 'Kopi', 'slug' => 'kopi']);
        $this->product = $this->store->products()->create([
            'category_id' => $category->id,
            'name' => 'Kopi Lereng',
            'slug' => 'kopi-lereng',
            'description' => 'Kopi arabika dari lereng Gunung Ijen.',
            'craftsman_story' => 'Dipetik dan diolah oleh petani lokal.',
            'status' => 'active',
            'weight_gram' => 250,
        ]);
        $this->product->variants()->create([
            'label' => 'Biji 250 gram',
            'price' => 85000,
            'stock' => 7,
        ]);
    }

    public function test_buyer_opens_product_details_before_starting_ai_chat(): void
    {
        $this->actingAs($this->buyer)
            ->get(route('buyer.dashboard'))
            ->assertOk()
            ->assertDontSee('Tanya AI tentang produk')
            ->assertSee(route('products.show', ['slug' => $this->product->slug]), false);

        $this->get(route('products.show', ['slug' => $this->product->slug]))
            ->assertOk()
            ->assertSee('Kopi Lereng')
            ->assertSee('Masukkan ke troli')
            ->assertSee('Beli sekarang')
            ->assertSee('Chat Seller')
            ->assertSee('Tanya AI')
            ->assertSee('id="product-ai-chat" class="mt-8 hidden', false);

        $this->actingAs($this->seller)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertDontSee('Asisten AI');
    }

    public function test_seller_cannot_use_the_buyer_ai_chat_endpoint(): void
    {
        $this->actingAs($this->seller)
            ->postJson(route('buyer.products.ai-chat.reply', ['slug' => $this->product->slug]), [
                'message' => 'Apa keunikan produknya?',
            ])
            ->assertStatus(403);

        Http::assertNothingSent();
    }

    public function test_buyer_receives_an_answer_grounded_in_public_product_data(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'Kopi arabika ini berasal dari lereng Gunung Ijen.']]],
            ]),
        ]);

        $this->actingAs($this->buyer)
            ->postJson(route('buyer.products.ai-chat.reply', ['slug' => $this->product->slug]), [
                'message' => 'Dari mana asal kopinya?',
            ])
            ->assertOk()
            ->assertJson(['answer' => 'Kopi arabika ini berasal dari lereng Gunung Ijen.']);

        Http::assertSent(fn (ClientRequest $request) => $request->url() === 'https://api.openai.com/v1/chat/completions'
            && $request->hasHeader('Authorization', 'Bearer test-api-key')
            && $request->data()['model'] === 'gpt-4o-mini'
            && str_contains($request->data()['messages'][0]['content'], 'Kopi arabika dari lereng Gunung Ijen.')
            && str_contains($request->data()['messages'][0]['content'], 'Dipetik dan diolah oleh petani lokal.')
            && str_contains($request->data()['messages'][0]['content'], '85000'));
    }

    public function test_buyer_chat_history_is_used_and_can_be_cleared(): void
    {
        Http::fakeSequence('api.openai.com/v1/chat/completions')
            ->push(['choices' => [['message' => ['content' => 'Asalnya dari Gunung Ijen.']]]])
            ->push(['choices' => [['message' => ['content' => 'Ya, tersedia 7 stok.']]]])
            ->push(['choices' => [['message' => ['content' => 'Harga Rp85.000.']]]]);

        $this->actingAs($this->buyer)
            ->postJson(route('buyer.products.ai-chat.reply', ['slug' => $this->product->slug]), [
                'message' => 'Dari mana asalnya?',
            ])
            ->assertOk();

        $this->postJson(route('buyer.products.ai-chat.reply', ['slug' => $this->product->slug]), [
            'message' => 'Masih ada stok?',
        ])->assertOk();

        Http::assertSent(fn (ClientRequest $request) => collect($request->data()['messages'])
            ->contains(fn (array $message) => $message === ['role' => 'assistant', 'content' => 'Asalnya dari Gunung Ijen.']));

        $this->deleteJson(route('buyer.products.ai-chat.clear', ['slug' => $this->product->slug]))->assertOk();
        $this->postJson(route('buyer.products.ai-chat.reply', ['slug' => $this->product->slug]), [
            'message' => 'Berapa harganya?',
        ])->assertOk();

        Http::assertSent(fn (ClientRequest $request) => collect($request->data()['messages'])
            ->contains(fn (array $message) => $message === ['role' => 'user', 'content' => 'Berapa harganya?'])
            && ! collect($request->data()['messages'])
                ->contains(fn (array $message) => $message === ['role' => 'user', 'content' => 'Dari mana asalnya?']));
    }

    public function test_buyer_cannot_ask_about_a_product_that_is_not_visible(): void
    {
        $unlistedProduct = $this->store->products()->create([
            'category_id' => $this->product->category_id,
            'name' => 'Produk Draft',
            'slug' => 'produk-draft',
            'description' => 'Produk yang belum ditayangkan.',
            'status' => 'draft',
            'weight_gram' => 100,
        ]);

        $this->actingAs($this->buyer)
            ->postJson(route('buyer.products.ai-chat.reply', ['slug' => $unlistedProduct->slug]), [
                'message' => 'Apa deskripsinya?',
            ])
            ->assertNotFound();

        Http::assertNothingSent();
    }

    public function test_chat_returns_clear_error_when_openai_is_not_configured(): void
    {
        config(['services.openai.api_key' => '']);

        $this->actingAs($this->buyer)
            ->postJson(route('buyer.products.ai-chat.reply', ['slug' => $this->product->slug]), [
                'message' => 'Apa nama produknya?',
            ])
            ->assertStatus(503)
            ->assertJsonPath('message', 'Asisten AI belum dikonfigurasi. Tambahkan OPENAI_API_KEY pada environment aplikasi.');

        Http::assertNothingSent();
    }

    public function test_buyer_and_seller_can_message_only_their_product_conversation(): void
    {
        $this->actingAs($this->buyer)
            ->post(route('buyer.products.chats.store', ['slug' => $this->product->slug]))
            ->assertRedirect();

        $conversation = ProductConversation::query()->firstOrFail();
        $this->post(route('buyer.chats.messages.store', $conversation), [
            'message' => 'Apakah produk ini tersedia?',
        ])->assertRedirect(route('buyer.chats.show', $conversation));

        $this->actingAs($this->seller)
            ->get(route('seller.chats.index'))
            ->assertOk()
            ->assertSee('Kopi Lereng')
            ->assertSee('pembeli-test');

        $this->post(route('seller.chats.messages.store', $conversation), [
            'message' => 'Ya, stok masih tersedia.',
        ])->assertRedirect(route('seller.chats.show', $conversation));

        $this->actingAs($this->buyer)
            ->get(route('buyer.chats.show', $conversation))
            ->assertOk()
            ->assertSee('Apakah produk ini tersedia?')
            ->assertSee('Ya, stok masih tersedia.');
    }

    public function test_another_seller_cannot_open_a_product_conversation(): void
    {
        $this->actingAs($this->buyer)
            ->post(route('buyer.products.chats.store', ['slug' => $this->product->slug]));
        $conversation = ProductConversation::query()->firstOrFail();

        $otherSeller = User::forceCreate([
            'nama_user' => 'seller-lain',
            'email' => 'seller-lain@example.test',
            'password' => Hash::make('Password#12345'),
            'role' => 'owner',
            'account_type' => 'seller',
        ]);

        $this->actingAs($otherSeller)
            ->get(route('seller.chats.show', $conversation))
            ->assertNotFound();
    }
}
