<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerBankAccount extends Model
{
    protected $fillable = ['bank', 'account_number', 'account_holder', 'verified', 'changed_at'];

    protected $hidden = ['account_number'];

    protected $casts = ['account_number' => 'encrypted', 'verified' => 'boolean', 'changed_at' => 'datetime'];

    public function masked(): string
    {
        $n = $this->account_number;

        return str_repeat('*', max(strlen($n) - 4, 0)).substr($n, -4);
    }
}
