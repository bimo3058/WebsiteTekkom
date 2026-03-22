<?php

namespace Modules\BankSoal\Models\Shared;

use Illuminate\Database\Eloquent\Model;

/**
 * [Shared - Model] Detail Rencana Pembelajaran Semester
 * 
 * Model ini digunakan oleh semua role
 */
class RpsDetail extends Model
{
    protected $table    = 'bs_rps_detail';
    protected $fillable = ['mk_id', 'dosenpengampu_id', 'semester', 'tahun_ajaran', 'cpl_id', 'cpmk_id', 'dokumen'];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }
}
