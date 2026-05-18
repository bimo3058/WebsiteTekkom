<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengaduanLog extends Model
{
    public $timestamps = false;

    protected $table = 'mk_pengaduan_log';

    protected $fillable = [
        'pengaduan_id',
        'actor_user_id',
        'action',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public const ACTION_DIBUAT           = 'dibuat';
    public const ACTION_DIBACA           = 'dibaca';
    public const ACTION_DIDELEGASIKAN    = 'didelegasikan';
    public const ACTION_DITANGGAPI_DOSEN = 'ditanggapi_dosen';
    public const ACTION_DITOLAK_DOSEN    = 'ditolak_dosen';
    public const ACTION_DIJAWAB          = 'dijawab';
    public const ACTION_DIAJUKAN_ULANG   = 'diajukan_ulang';
    public const ACTION_DITUTUP_MAHASISWA = 'ditutup_mahasiswa';
    public const ACTION_DITUTUP_ADMIN    = 'ditutup_admin';
    public const ACTION_DITUTUP_OTOMATIS = 'ditutup_otomatis';

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'actor_user_id');
    }
}
