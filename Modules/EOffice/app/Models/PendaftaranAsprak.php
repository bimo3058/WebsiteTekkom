<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Pendaftaran calon Asisten Praktikum oleh mahasiswa.
 * Tabel: pendaftaran_asprak
 *
 * Flow: Mahasiswa submit → Koor review → auto assign role asprak (jika approve)
 * status: pending | approved | rejected
 */
class PendaftaranAsprak extends Model
{
    protected $table = 'pendaftaran_asprak';

    protected $fillable = [
        'user_id',
        'praktikum_id',
        'ipk',
        'motivasi',
        'cv_path',
        'transkrip_path',
        'jadwal',
        'status',
        'status_koor',
        'alasan_penolakan',
        'direview_oleh',
        'direview_pada',
        'catatan_koor',
    ];

    protected $casts = [
        'ipk'           => 'float',
        'jadwal'        => 'array',
        'direview_pada'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }

    public function direviewOleh()
    {
        return $this->belongsTo(User::class, 'direview_oleh');
    }
}
