<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id_user', 'id_user');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_user', 'id_user');
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
