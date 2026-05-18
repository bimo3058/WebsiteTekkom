<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class KpDokumen extends Model
{
    protected $table = 'eo_kp_dokumen';

    protected $fillable = [
        'kp_id',
        'jenis_dokumen',
        'phase',
        'file_name',
        'file_path',
        'status_validasi',
        'approval_status',
        'approved_by',
        'approved_at',
        'revision_note',
        'nilai_input_mahasiswa',
        'nilai_validasi_koordinator',
        'nilai_status',
        'tanggal_upload',
    ];

    public function kerjaPraktik()
    {
        return $this->belongsTo(KerjaPraktik::class, 'kp_id');
    }
}
