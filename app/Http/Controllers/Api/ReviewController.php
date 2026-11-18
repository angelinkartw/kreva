<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Review;
use App\Services\RatingService;
use App\Support\Html;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /** Kata kasar sederhana untuk moderasi otomatis awal; ganti dengan layanan moderasi bila perlu. */
    private const BLOCKLIST = ['anjing', 'bangsat', 'tai', 'kontol', 'memek'];

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_item_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['string', 'max:300'],
        ]);

        $item = OrderItem::with('storeOrder.order')->findOrFail($data['order_item_id']);
        $so = $item->storeOrder;

        // Hanya pembeli yang pesanannya SELESAI
        if ($so->order->buyer_id !== $request->user()->id) {
            abort(404);
        }
        if ($so->status !== OrderStatus::Completed) {
            throw new DomainException('Ulasan hanya dapat dibuat setelah pesanan selesai.', 'not_completed');
        }
        if (Review::where('order_item_id', $item->id)->exists()) {
            throw new DomainException('Anda sudah mengulas produk ini.', 'already_reviewed');
        }

        $comment = Html::clean(strip_tags((string) ($data['comment'] ?? '')));
        $flagged = collect(self::BLOCKLIST)->contains(fn ($w) => str_contains(mb_strtolower($comment), $w));

        $review = Review::forceCreate([
            'order_item_id' => $item->id, 'buyer_id' => $request->user()->id,
            'product_id' => $item->product_id, 'store_id' => $so->store_id,
            'rating' => $data['rating'], 'comment' => $comment, 'photos' => $data['photos'] ?? null,
            'moderation_status' => $flagged ? 'flagged' : 'published',
        ]);

        RatingService::recalculate($item->product_id, $so->store_id);

        return response()->json($review, 201);
    }

    public function update(Request $request, int $id)
    {
        $review = Review::where('buyer_id', $request->user()->id)->findOrFail($id);
        $data = $request->validate(['rating' => ['sometimes', 'integer', 'between:1,5'], 'comment' => ['sometimes', 'nullable', 'string', 'max:2000']]);

        if (isset($data['comment'])) {
            $data['comment'] = strip_tags($data['comment']);
        }
        $review->forceFill($data)->save();
        RatingService::recalculate($review->product_id, $review->store_id);

        return $review;
    }
}
