<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'eo_mr_fasilitas';

    protected $fillable = [
        'nama_fasilitas'
    ];

    public function ruangans()
    {
        return $this->belongsToMany(Ruangan::class, 'eo_mr_ruangan_fasilitas', 'fasilitas_id', 'ruangan_id');
    }
}
