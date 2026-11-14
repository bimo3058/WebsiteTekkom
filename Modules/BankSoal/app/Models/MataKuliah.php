<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataKuliah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bs_mata_kuliah';

    protected $fillable = [
        'kode',
        'nama',
        'sks',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'sks' => 'integer',
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Validasi Rules
    // -------------------------------------------------------------------------

    public static function validationRules(?int $id = null): array
    {
        // Identitas data master ditentukan oleh keseluruhan field (lihat
        // Modules\BankSoal\Support\MasterDataUpsert), bukan unik per kolom.
        // Jadi kode/nama yang sama dengan 1 field berbeda tetap dianggap data baru.
        return [
            'kode'     => 'required|string|max:50',
            'nama'     => 'required|string|max:255',
            'sks'      => 'required|integer|min:1|max:3',
            'semester' => 'required|integer|min:1|max:8',
            'is_active'=> 'boolean',
        ];
    }

    public static function validationMessages(): array
    {
        return [
            'kode.required'     => 'Kode mata kuliah wajib diisi.',
            'nama.required'     => 'Nama mata kuliah wajib diisi.',
            'sks.required'      => 'SKS wajib diisi.',
            'semester.required' => 'Semester wajib diisi.',
        ];
    }

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

    public function cpmk(): HasMany
    {
        return $this->hasMany(Cpmk::class, 'mk_id');
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
