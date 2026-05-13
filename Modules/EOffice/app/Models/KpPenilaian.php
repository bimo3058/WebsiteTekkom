<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class KpPenilaian extends Model
{
    protected $table = 'eo_kp_penilaian';

    protected $fillable = [
        'kp_id',
        'nilai_lapangan',
        'nilai_seminar_pembimbing',
        'nilai_akhir',
    ];

    public function kerjaPraktik()
    {
        return $this->belongsTo(KerjaPraktik::class, 'kp_id');
    }
}
