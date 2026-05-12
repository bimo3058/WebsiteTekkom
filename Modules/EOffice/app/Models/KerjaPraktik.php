<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\EOffice\Database\Factories\KerjaPraktikFactory;

class KerjaPraktik extends Model
{
    use HasFactory;

    protected $table = 'eo_kerja_praktik';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nim',
        'mahasiswa_id',
        'dosen_pembimbing_id',
        'rencana_judul',
        'rencana_tempat',
        'tempat_fix',
        'judul_fix',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_kp',
        'is_acc_admin',
    ];

    /**
     * Relasi ke tabel dokumen KP
     */
    public function dokumen()
    {
        return $this->hasMany(KpDokumen::class, 'kp_id');
    }

    /**
     * Relasi ke tabel penilaian KP (hasOne karena 1 KP punya 1 nilai)
     */
    public function penilaian()
    {
        return $this->hasOne(KpPenilaian::class, 'kp_id');
    }

    // protected static function newFactory(): KerjaPraktikFactory
    // {
    //     // return KerjaPraktikFactory::new();
    // }
}
