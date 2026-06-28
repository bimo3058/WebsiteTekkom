<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MrJadwalInternal extends Model
{
    use HasUuids;

    protected $table = 'eo_mr_jadwal_internal';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'ruangan_id',
        'tipe_jadwal',
        'kategori',
        'hari',
        'tanggal_spesifik',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
    ];

    /**
     * Relationship to Ruangan
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}
