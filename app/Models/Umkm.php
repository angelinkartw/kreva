<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class Umkm extends Model
{
    protected $table = 'umkm';
    protected $primaryKey = 'id_umkm';
    protected $fillable = ['nama_umkm', 'industry', 'usia_usaha', 'id_user'];
    public $timestamps = true;
    const UPDATED_AT = null;
}
