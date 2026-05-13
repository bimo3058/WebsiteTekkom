<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class KpPengumuman extends Model
{
    protected $table = 'eo_kp_pengumuman';
    
    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_published',
        'user_id',
    ];

    // Relasi ke tabel users (untuk mengetahui siapa yang membuat pengumuman)
    public function pembuat()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
