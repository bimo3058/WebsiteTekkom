<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class KpNilaiDetail extends Model
{
    protected $table = 'eo_kp_nilai_detail';

    protected $fillable = [
        'kp_id',
        'komponen_id',
        'nilai_angka',
    ];

    public function kp()
    {
        return $this->belongsTo(KerjaPraktik::class, 'kp_id');
    }

    public function komponen()
    {
        return $this->belongsTo(KpKomponenNilai::class, 'komponen_id');
    }
}
