<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'mk_pengaduan';

    protected $fillable = [
        'user_id',
        'kategori',
        'is_anonim',
        'data_template',
        'status',
        'read_at',
        'read_by',
        'jawaban',
        'answered_at',
        'answered_by',
    ];

    protected $casts = [
        'is_anonim' => 'bool',
        'data_template' => 'array',
        'read_at' => 'datetime',
        'answered_at' => 'datetime',
    ];

    public const STATUS_BARU = 'baru';
    public const STATUS_DIBACA = 'dibaca';
    public const STATUS_DIJAWAB = 'dijawab';

    public const KATEGORI_AKADEMIK = 'akademik';
    public const KATEGORI_PEMBELAJARAN = 'pembelajaran';
    public const KATEGORI_TENDIK = 'tendik';
    public const KATEGORI_TUGAS_BEBAN = 'tugas_beban';
    public const KATEGORI_LAINNYA = 'lainnya';

    public const KATEGORI_LIST = [
        self::KATEGORI_AKADEMIK,
        self::KATEGORI_PEMBELAJARAN,
        self::KATEGORI_TENDIK,
        self::KATEGORI_TUGAS_BEBAN,
        self::KATEGORI_LAINNYA,
    ];

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function dibacaOleh(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'read_by');
    }

    public function dijawabOleh(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'answered_by');
    }
}
