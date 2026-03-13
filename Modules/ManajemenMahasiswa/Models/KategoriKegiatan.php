<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriKegiatan extends Model
{
    use HasFactory;

    protected $table = 'mk_kategori_kegiatan';

    protected $fillable = [
        'nama_kategori',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'kategori_kegiatan_id');
    }
}