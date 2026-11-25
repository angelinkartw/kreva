<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductConversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerProductChatController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = ProductConversation::query()
            ->where('buyer_id', $request->user()->getAuthIdentifier())
            ->with(['product', 'seller'])
            ->latest('updated_at')
            ->paginate(20);

        return view('chats.index', [
            'conversations' => $conversations,
            'chatRole' => 'buyer',
            'chatRoutePrefix' => 'buyer.chats',
        ]);
    }

    public function start(Request $request, string $slug): RedirectResponse
    {
        $product = Product::query()
            ->visible()
            ->with('store:id,user_id')
            ->where('products.slug', $slug)
            ->firstOrFail();

        $conversation = ProductConversation::query()->firstOrCreate(
            [
                'buyer_id' => $request->user()->getAuthIdentifier(),
                'product_id' => $product->id,
            ],
            ['seller_id' => $product->store->user_id],
        );

        return redirect()->route('buyer.chats.show', $conversation);
    }

    public function show(Request $request, int $conversation): View
    {
        $conversation = $this->buyerConversation($request, $conversation);
        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->getAuthIdentifier())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        $conversation->load(['product.images', 'seller', 'messages.sender']);

        return view('chats.show', [
            'conversation' => $conversation,
            'chatRole' => 'buyer',
            'chatRoutePrefix' => 'buyer.chats',
        ]);
    }

    public function send(Request $request, int $conversation): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);
        $conversation = $this->buyerConversation($request, $conversation);
        $conversation->messages()->create([
            'sender_id' => $request->user()->getAuthIdentifier(),
            'body' => trim($data['message']),
        ]);
        $conversation->touch();

        return redirect()->route('buyer.chats.show', $conversation)->with('status', 'Pesan terkirim.');
    }

    private function buyerConversation(Request $request, int $conversation): ProductConversation
    {
        return ProductConversation::query()
            ->where('buyer_id', $request->user()->getAuthIdentifier())
            ->findOrFail($conversation);
    }
}
