<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Pendaftaran calon Koordinator Praktikum oleh mahasiswa.
 * Tabel: pendaftaran_koordinator
 *
 * Flow: Mahasiswa submit → Dosen review (status_dosen) → Admin final approve (status)
 * status_dosen: menunggu | disetujui | ditolak
 * status: pending | approved | rejected
 */
class PendaftaranKoordinator extends Model
{
    protected $table = 'pendaftaran_koordinator';

    protected $fillable = [
        'user_id',
        'praktikum_id',
        'ipk',
        'motivasi',
        'status',
        'status_dosen',
        'catatan_dosen',
        'alasan_penolakan',
        'transkrip_path',
        'direview_oleh',
        'direview_pada',
    ];

    protected $casts = [
        'ipk'          => 'float',
        'direview_pada' => 'datetime',
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
