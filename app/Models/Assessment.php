<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class Assessment extends Model
{
    protected $table = 'assessments';
    protected $primaryKey = 'id_assessment';
    public $timestamps = true;

    protected $fillable = [
        'id_user', 
        'id_umkm', 
        'owner_finished',
        'employee_finished',
        'id_owner',
        'id_employee',
        'answers',
        'status', 
        'total_score', 
        'score_ins', 
        'score_ldi', 
        'score_weq', 
        'score_ops', 
        'score_ect', 
        'score_ov', 
        'kategori_kuartil', 
        'started_at', 
        'finished_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'total_score' => 'decimal:2',
    ];
}
