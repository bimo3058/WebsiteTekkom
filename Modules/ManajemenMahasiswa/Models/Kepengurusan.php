<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kepengurusan extends Model
{
    use HasFactory;

    protected $table = 'mk_kepengurusan';

    protected $fillable = [
        'tahun_periode',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function pengurusHimaskom(): HasMany
    {
        return $this->hasMany(PengurusHimaskom::class, 'kepengurusan_id');
    }

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'kepengurusan_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByPeriode($query, string $periode)
    {
        return $query->where('tahun_periode', $periode);
    }
}