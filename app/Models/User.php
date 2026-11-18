<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['nama_user', 'password', 'id_umkm', 'role', 'account_type', 'gender', 'age'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    const UPDATED_AT = null;

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, Store::class, 'user_id', 'store_id', 'id_user', 'id');
    }

    public function store(): HasOne
    {
        return $this->hasOne(Store::class, 'user_id', 'id_user');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id', 'id_user');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'user_id', 'id_user');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class, 'user_id', 'id_user');
    }

    /**
     * Accessor untuk nama agar tetap kompatibel dengan views yang menggunakan ->name
     */
    public function getNameAttribute()
    {
        return $this->nama_user;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
