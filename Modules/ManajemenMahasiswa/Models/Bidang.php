<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bidang extends Model
{
    use HasFactory;

    public const JENIS_BIDANG = 'bidang';
    public const JENIS_UNIT   = 'unit';

    protected $table = 'mk_bidang';

    protected $fillable = [
        'nama_bidang',
        'jenis',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'bidang_id');
    }

    public function pengurus(): HasMany
    {
        return $this->hasMany(PengurusBidang::class, 'bidang_id');
    }

    // -------------------------------------------------------------------------
    // Scopes & helpers
    // -------------------------------------------------------------------------

    /** Hanya baris berjenis Bidang (PSDM, RISTEK, ...). */
    public function scopeBidang(Builder $query): Builder
    {
        return $query->where('jenis', self::JENIS_BIDANG);
    }

    /** Hanya baris berjenis Unit (Infokom, PMO). */
    public function scopeUnit(Builder $query): Builder
    {
        return $query->where('jenis', self::JENIS_UNIT);
    }

    /** Apakah baris ini sebuah Unit (bukan Bidang). */
    public function isUnit(): bool
    {
        return $this->jenis === self::JENIS_UNIT;
    }
}
