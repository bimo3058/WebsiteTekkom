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
        'semester',
    ];

    protected $casts = [
        'sks' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Validasi Rules
    // -------------------------------------------------------------------------

    public static function validationRules(?int $id = null): array
    {
        $uniqueKode = $id ? "unique:bs_mata_kuliah,kode,{$id}" : 'unique:bs_mata_kuliah,kode';
        $uniqueNama = $id ? "unique:bs_mata_kuliah,nama,{$id}" : 'unique:bs_mata_kuliah,nama';

        return [
            'kode'     => "required|string|max:50|{$uniqueKode}",
            'nama'     => "required|string|max:255|{$uniqueNama}",
            'sks'      => 'required|integer|min:1|max:3',
            'semester' => 'required|integer|min:1|max:8',
        ];
    }

    public static function validationMessages(): array
    {
        return [
            'kode.unique' => 'Kode mata kuliah sudah terdaftar. Gunakan kode lain.',
            'nama.unique' => 'Nama mata kuliah sudah terdaftar. Gunakan nama lain.',
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