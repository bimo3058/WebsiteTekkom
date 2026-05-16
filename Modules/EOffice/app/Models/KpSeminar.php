<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KpSeminar extends Model
{
    use HasFactory;

    protected $table = 'eo_kp_seminar';

    protected $fillable = [
        'kp_id',
        'tanggal_seminar',
        'waktu_seminar',
        'ruangan',
        'status_validasi_syarat',
        'path_undangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_seminar' => 'date',
        ];
    }

    /**
     * Relasi ke tabel kerja praktik
     */
    public function kerjaPraktik()
    {
        return $this->belongsTo(KerjaPraktik::class, 'kp_id');
    }
}
