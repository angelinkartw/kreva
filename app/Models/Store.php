<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'slug', 'description', 'story', 'logo', 'banner', 'province', 'city', 'district',
        'postal_code', 'origin_address', 'ktp_number', 'npwp_number', 'verification_status', 'verified_at',
        'operating_hours', 'enabled_couriers',
    ];

    // Data identitas tidak pernah ikut keluar ke API publik.
    protected $hidden = ['ktp_number', 'npwp_number', 'verification_note', 'deleted_at'];

    protected function casts(): array
    {
        return [
            'ktp_number' => 'encrypted',
            'npwp_number' => 'encrypted',
            'operating_hours' => 'array',
            'enabled_couriers' => 'array',
            'verified_at' => 'datetime',
            'rating_avg' => 'float',
        ];
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function bankAccount(): HasOne
    {
        return $this->hasOne(SellerBankAccount::class)->latestOfMany();
    }

    public function storeOrders(): HasMany
    {
        return $this->hasMany(StoreOrder::class);
    }

    public function scopeVerified($q)
    {
        return $q->where('verification_status', 'verified');
    }
}
