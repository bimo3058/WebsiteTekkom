<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'eo_mr_ruangans';

    protected $fillable = [
        'nama',
        'lokasi',
        'lantai',
        'kapasitas',
        'fasilitas',
        'is_active',
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
}
