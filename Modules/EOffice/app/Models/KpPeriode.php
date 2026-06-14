<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\EOffice\Database\Factories\KpPeriodeFactory;

class KpPeriode extends Model
{
    use HasFactory;

    protected $table = 'eo_kp_periode';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'is_active',
        'tanggal_buka',
        'tanggal_tutup',
        'pra_kp_mulai',
        'pra_kp_akhir',
        'pra_kp_pengingat',
        'saat_kp_mulai',
        'saat_kp_akhir',
        'saat_kp_pengingat',
        'pasca_kp_mulai',
        'pasca_kp_akhir',
        'pasca_kp_pengingat',
    ];

    // protected static function newFactory(): KpPeriodeFactory
    // {
    //     // return KpPeriodeFactory::new();
    // }
}
