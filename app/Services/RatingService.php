<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Review;
use App\Models\Store;

class RatingService
{
    public static function recalculate(int $productId, int $storeId): void
    {
        $p = Review::where('product_id', $productId)->where('moderation_status', 'published');
        Product::whereKey($productId)->update(['rating_avg' => round((float) $p->avg('rating'), 2), 'rating_count' => $p->count()]);

        $s = Review::where('store_id', $storeId)->where('moderation_status', 'published');
        Store::whereKey($storeId)->update(['rating_avg' => round((float) $s->avg('rating'), 2), 'rating_count' => $s->count()]);
    }
}
