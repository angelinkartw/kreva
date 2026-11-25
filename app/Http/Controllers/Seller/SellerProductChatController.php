<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ProductConversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerProductChatController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = ProductConversation::query()
            ->where('seller_id', $request->user()->getAuthIdentifier())
            ->with(['product', 'buyer'])
            ->latest('updated_at')
            ->paginate(20);

        return view('chats.index', [
            'conversations' => $conversations,
            'chatRole' => 'seller',
            'chatRoutePrefix' => 'seller.chats',
        ]);
    }

    public function show(Request $request, int $conversation): View
    {
        $conversation = $this->sellerConversation($request, $conversation);
        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->getAuthIdentifier())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        $conversation->load(['product.images', 'buyer', 'messages.sender']);

        return view('chats.show', [
            'conversation' => $conversation,
            'chatRole' => 'seller',
            'chatRoutePrefix' => 'seller.chats',
        ]);
    }

    public function send(Request $request, int $conversation): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);
        $conversation = $this->sellerConversation($request, $conversation);
        $conversation->messages()->create([
            'sender_id' => $request->user()->getAuthIdentifier(),
            'body' => trim($data['message']),
        ]);
        $conversation->touch();

        return redirect()->route('seller.chats.show', $conversation)->with('status', 'Pesan terkirim.');
    }

    private function sellerConversation(Request $request, int $conversation): ProductConversation
    {
        return ProductConversation::query()
            ->where('seller_id', $request->user()->getAuthIdentifier())
            ->findOrFail($conversation);
    }
}
