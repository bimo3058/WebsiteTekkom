<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Modules\BankSoal\Enums\RpsStatus;

class RpsDetail extends Model
{
    protected $table    = 'bs_rps_detail';
    protected $fillable = ['mk_id', 'semester', 'tahun_ajaran', 'dokumen', 'status', 'catatan'];

    protected $casts = [
        'status' => RpsStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }
    
    public function cpls()
    {
        return $this->belongsToMany(
            Cpl::class,
            'bs_rps_cpl',
            'rps_id',
            'cpl_id'
        );
    }

    public function cpmks()
    {
        return $this->belongsToMany(
            Cpmk::class,
            'bs_rps_cpmk',
            'rps_id',
            'cpmk_id'
        );
    }

    public function dosens()
    {
        return $this->belongsToMany(
            User::class,
            'bs_rps_dosen',
            'rps_id',
            'dosen_id'
        );
    }
}
