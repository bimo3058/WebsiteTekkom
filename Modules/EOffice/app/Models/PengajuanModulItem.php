<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanModulItem extends Model
{
    protected $table = 'pengajuan_modul_item';

    protected $fillable = [
        'pengajuan_modul_id',
        'modul_sumber_id',
        'aksi',
        'nama_lama',
        'urutan_lama',
        'nama_baru',
        'urutan_baru',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanModul::class, 'pengajuan_modul_id');
    }

    public function modulSumber()
    {
        return $this->belongsTo(Modul::class, 'modul_sumber_id');
    }
}
