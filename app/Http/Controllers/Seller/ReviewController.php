<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $store = $request->user()->store ?? abort(404);

        return Review::where('store_id', $store->id)->where('moderation_status', 'published')
            ->with('buyer', 'product:id,name,slug')->latest('id')->cursorPaginate(30);
    }

    public function reply(Request $request, int $id)
    {
        $store = $request->user()->store ?? abort(404);
        $data = $request->validate(['reply' => ['required', 'string', 'max:1000']]);

        $review = Review::where('store_id', $store->id)->findOrFail($id);
        $review->forceFill(['seller_reply' => strip_tags($data['reply'])])->save();

        return $review;
    }
}
