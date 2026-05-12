<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bidang extends Model
{
    use HasFactory;

    protected $table = 'mk_bidang';

    protected $fillable = [
        'nama_bidang',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'bidang_id');
    }
}