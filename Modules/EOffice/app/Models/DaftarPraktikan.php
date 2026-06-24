<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Mahasiswa yang terdaftar di praktikum.
 *
 * Tabel: daftar_praktikan
 */
class DaftarPraktikan extends Model
{
    use HasUuids;

    protected $table = 'daftar_praktikan';

    protected $fillable = [
        'praktikum_id',
        'user_id',
        'status',
        'kelompok',
        'shift',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }

    public function nilai()
    {
        return $this->hasOne(Nilai::class, 'daftar_praktikan_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'daftar_praktikan_id');
    }

    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class, 'daftar_praktikan_id');
    }

    public function nilaiJenisTugas()
    {
        return $this->hasMany(NilaiJenisTugas::class, 'daftar_praktikan_id');
    }
}
