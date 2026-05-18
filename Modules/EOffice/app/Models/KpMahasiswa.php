<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KpMahasiswa extends Model
{
    use HasFactory;
    protected $table = 'eo_kp_mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'nama_lengkap',
        'prodi',
    ];

    /**
     * Relasi ke tabel users global (READ ONLY)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke data kerja praktik milik mahasiswa ini
     */
    public function kerjaPraktik()
    {
        return $this->hasMany(KerjaPraktik::class, 'mahasiswa_id');
    }
    /**
     * Ambil atau buat record KpMahasiswa berdasarkan user yang login.
     * Otomatis mengisi NIM dan nama dari tabel students & users global.
     */
    public static function getOrCreateFromAuth(): self
    {
        $user = auth()->user();

        return static::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nim'           => optional($user->student)->student_number ?? '-',
                'nama_lengkap'  => $user->name,
                'prodi'         => 'Teknik Komputer',
            ]
        );
    }
}
