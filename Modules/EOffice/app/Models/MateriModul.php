<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Materi / file yang diunggah asprak ke modul.
 *
 * Tabel: materi_modul
 */
class MateriModul extends Model
{
    protected $table = 'materi_modul';

    protected $fillable = [
        'modul_id',
        'user_id',
        'judul',
        'deskripsi',
        'file_path',
        'tipe_file',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
