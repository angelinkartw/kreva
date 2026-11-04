<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_order', 'id_produk', 'nama_produk', 'harga', 'kuantitas', 'subtotal'])]
class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $primaryKey = 'id_order_item';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'kuantitas' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }
}
