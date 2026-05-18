<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class KpDokumen extends Model
{
    protected $table = 'eo_kp_dokumen';

    protected $fillable = [
        'kp_id',
        'jenis_dokumen',
        'file_path',
        'status_validasi',
        'tanggal_upload',
    ];

    public function kerjaPraktik()
    {
        return $this->belongsTo(KerjaPraktik::class, 'kp_id');
    }
}
