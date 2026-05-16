<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Pendaftaran mahasiswa sebagai praktikan (verifikasi Cetak IRS oleh koor).
 *
 * Tabel: pendaftaran_praktikan
 * status: pending | approved | rejected
 */
class PendaftaranPraktikan extends Model
{
    public const STATUS_PENDING  = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /** Pesan standar penolakan otomatis / cepat */
    public const PESAN_TOLAK_IRS_DEFAULT = 'Anda belum mengambil IRS praktikum';

    protected $table = 'pendaftaran_praktikan';

    protected $fillable = [
        'user_id',
        'praktikum_id',
        'periode_id',
        'irs_path',
        'status',
        'alasan_penolakan',
        'direview_oleh',
        'direview_pada',
        'catatan_koor',
        'validasi_mode',
    ];

    protected $casts = [
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

    public function periode()
    {
        return $this->belongsTo(PeriodePendaftaran::class, 'periode_id');
    }

    public function direviewOleh()
    {
        return $this->belongsTo(User::class, 'direview_oleh');
    }
}
