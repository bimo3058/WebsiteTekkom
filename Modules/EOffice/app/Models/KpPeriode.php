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
        'kelas_dibuka'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'pra_kp_mulai' => 'date',
        'pra_kp_akhir' => 'date',
        'pra_kp_pengingat' => 'date',
        'saat_kp_mulai' => 'date',
        'saat_kp_akhir' => 'date',
        'saat_kp_pengingat' => 'date',
        'pasca_kp_mulai' => 'date',
        'pasca_kp_akhir' => 'date',
        'pasca_kp_pengingat' => 'date',
        'kelas_dibuka' => 'array',
    ];

    public function templates()
    {
        return $this->hasMany(\Modules\EOffice\Models\KpTemplate::class, 'periode_id', 'id');
    }

    public function komponenNilai()
    {
        return $this->hasMany(\Modules\EOffice\Models\KpKomponenNilai::class, 'periode_id', 'id');
    }
}
