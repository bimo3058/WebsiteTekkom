<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Prestasi extends Model
{
    use HasFactory;

    protected $table = 'mk_prestasi';

    protected $fillable = [
        'kemahasiswaan_id',
        'nama_prestasi',
        'tingkat',
        'tahun',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const TINGKAT_INTERNASIONAL = 'internasional';
    const TINGKAT_NASIONAL      = 'nasional';
    const TINGKAT_REGIONAL      = 'regional';
    const TINGKAT_UNIVERSITAS   = 'universitas';
    const TINGKAT_PRODI         = 'prodi';

    const TINGKAT_LIST = [
        self::TINGKAT_INTERNASIONAL,
        self::TINGKAT_NASIONAL,
        self::TINGKAT_REGIONAL,
        self::TINGKAT_UNIVERSITAS,
        self::TINGKAT_PRODI,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function kemahasiswaan(): BelongsTo
    {
        return $this->belongsTo(Kemahasiswaan::class, 'kemahasiswaan_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByTingkat(Builder $query, string $tingkat): Builder
    {
        return $query->where('tingkat', $tingkat);
    }

    public function scopeByTahun(Builder $query, int $tahun): Builder
    {
        return $query->where('tahun', $tahun);
    }
}