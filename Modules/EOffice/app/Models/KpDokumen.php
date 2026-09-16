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

    public function getFileUrlAttribute()
    {
        if (empty($this->file_path)) {
            return '#';
        }

        if (\Illuminate\Support\Str::startsWith($this->file_path, 'kp-uploads/')) {
            return app(\App\Services\SupabaseStorage::class)->publicUrl($this->file_path, 'eoffice');
        }

        return asset('storage/' . $this->file_path);
    }
}
