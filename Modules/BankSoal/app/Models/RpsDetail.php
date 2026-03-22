<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;

class RpsDetail extends Model
{
    protected $table    = 'bs_rps_detail';
    protected $fillable = ['mk_id', 'dosenpengampu_id', 'semester', 'tahun_ajaran', 'cpl_id', 'cpmk_id', 'dokumen'];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }
}
