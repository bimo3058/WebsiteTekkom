<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengaduanDelegasi extends Model
{
    public $timestamps = false;

    protected $table = 'mk_pengaduan_delegasi';

    protected $fillable = [
        'pengaduan_id',
        'delegated_by',
        'delegated_to',
        'notes_admin',
        'status',
        'tanggapan',
        'notes_balik',
        'alasan_tolak',
        'delegated_at',
        'responded_at',
    ];

    protected $casts = [
        'delegated_at'  => 'datetime',
        'responded_at'  => 'datetime',
    ];

    public const STATUS_AKTIF       = 'aktif';
    public const STATUS_DITANGGAPI  = 'ditanggapi';
    public const STATUS_DITOLAK     = 'ditolak';

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

    public function delegatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'delegated_by');
    }

    public function delegatedTo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'delegated_to');
    }
}
