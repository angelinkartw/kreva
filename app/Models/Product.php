<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'craftsman_story',
        'status',
        'weight_gram',
        'price_min',
    ];

    protected function casts(): array
    {
        return [
            'price_min' => 'integer',
            'weight_gram' => 'integer',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(ProductConversation::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('products.status', 'active')
            ->whereHas('store', fn (Builder $store) => $store->where('verification_status', 'verified'));
    }

    public function refreshPriceMin(): void
    {
        $price = $this->variants()->min('price');

        $this->forceFill(['price_min' => $price ?? 0])->save();
    }
}
