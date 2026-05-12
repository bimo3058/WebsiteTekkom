<?php

namespace Modules\BankSoal\Models\Komprehensif;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\BankSoal\Enums\JadwalStatus;

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
        'status',
        'token',
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
        'status'        => JadwalStatus::class,
    ];

    public function periode()
    {
        return $this->belongsTo(PeriodeUjian::class, 'periode_ujian_id');
    }

    public function pendaftars()
    {
        return $this->hasMany(PendaftarUjian::class, 'jadwal_ujian_id');
    }
}
