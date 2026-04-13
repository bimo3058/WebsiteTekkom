<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeriodeUjian extends Model
{
    use SoftDeletes;

    protected $table = 'bs_periode_ujians';

    protected $fillable = [
        'nama_periode',
        'slug',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_mulai_ujian',
        'tanggal_selesai_ujian',
        'status',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_mulai_ujian' => 'date',
        'tanggal_selesai_ujian' => 'date',
    ];
}
