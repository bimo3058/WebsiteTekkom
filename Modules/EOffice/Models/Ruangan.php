<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    public const KATEGORI_KELAS = 'Kelas';
    public const KATEGORI_LAB = 'Laboratorium';
    public const KATEGORI_SIDANG = 'Sidang';

    public const ALL_KATEGORI = [
        self::KATEGORI_KELAS,
        self::KATEGORI_LAB,
        self::KATEGORI_SIDANG,
    ];

    public const KATEGORI_MAHASISWA = [
        self::KATEGORI_KELAS,
        self::KATEGORI_LAB,
    ];

    protected $table = 'eo_mr_ruangans';

    protected $fillable = [
        'nama',
        'lokasi',
        'lantai',
        'kapasitas',
        'fasilitas',
        'is_active',
        'kategori',
    ];

    protected $casts = [
        'fasilitas' => 'array',
        'is_active' => 'boolean',
    ];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'ruangan_id');
    }

    public function fotos()
    {
        return $this->hasMany(RuanganFoto::class, 'ruangan_id')->orderBy('urutan', 'asc');
    }

    public function getCoverUrlAttribute(): ?string
    {
        $firstFoto = $this->fotos->first();
        return $firstFoto ? $firstFoto->foto_url : null;
    }
}
