<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class KpKomponenNilai extends Model
{
    protected $table = 'eo_kp_komponen_nilai';

    protected $fillable = [
        'periode_id',
        'nama_komponen',
        'bobot',
        'role_penilai',
    ];

    public function periode()
    {
        return $this->belongsTo(KpPeriode::class, 'periode_id');
    }

    public function detailNilai()
    {
        return $this->hasMany(KpNilaiDetail::class, 'komponen_id');
    }
}
