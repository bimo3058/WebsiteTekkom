<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalUjian extends Model
{
    use SoftDeletes;

    protected $table = 'bs_jadwal_ujians';

    protected $fillable = [
        'periode_ujian_id',
        'nama_sesi',
        'tanggal_ujian',
        'kuota',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan',
    ];

    public function periode()
    {
        return $this->belongsTo(PeriodeUjian::class, 'periode_ujian_id');
    }

    protected $casts = [
        'tanggal_ujian' => 'date',
    ];
}
