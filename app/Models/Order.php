<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_user', 'nama_penerima', 'telepon', 'alamat', 'total', 'status'])]
class Order extends Model
{
    protected $table = 'orders';

    protected $primaryKey = 'id_order';

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_order', 'id_order');
    }
}
