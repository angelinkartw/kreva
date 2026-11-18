<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $guarded = ['*'];

    protected $casts = ['photos' => 'array'];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id')->select('id', 'name');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
