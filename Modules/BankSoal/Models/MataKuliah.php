<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'bs_mata_kuliah';

    protected $fillable = [
        'kode',
        'nama',
        'sks',
    ];

    protected $casts = [
        'sks' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function cpl(): BelongsToMany
    {
        return $this->belongsToMany(Cpl::class, 'bs_mata_kuliah_cpl', 'mk_id', 'cpl_id');
    }

    public function dosenPengampu(): HasMany
    {
        return $this->hasMany(DosenPengampuMk::class, 'mk_id');
    }

    public function pertanyaan(): HasMany
    {
        return $this->hasMany(Pertanyaan::class, 'mk_id');
    }

    public function rps(): HasMany
    {
        return $this->hasMany(Rps::class, 'mk_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeSearch($query, string $keyword)
    {
        return $query->where('nama', 'like', "%{$keyword}%")
                     ->orWhere('kode', 'like', "%{$keyword}%");
    }
}