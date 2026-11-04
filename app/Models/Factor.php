<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kode_factor', 'nama_factor'])]
class Factor extends Model
{
    protected $table = 'factors';
    protected $primaryKey = 'id_factor';
    public $timestamps = true;
    const UPDATED_AT = null;
}
