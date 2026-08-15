<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'eo_mr_peminjamans';

    protected $fillable = [
        'user_id',
        'ruangan_id',
        'nomor_telepon',
        'tujuan',
        'tanggal_pinjam',
        'jam_mulai',
        'jam_selesai',
        'berkas_pendukung',
        'status',
        'alasan_penolakan',
        'waktu_approval',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'waktu_approval' => 'datetime',
    ];

    public function getAlasanPenolakanAttribute($value)
    {
        // Secara dinamis mengubah teks usang di baris database lama saat dirender ke layar
        if ($value === 'Sistem (Kadaluarsa otomatis - Waktu peminjaman sudah terlewat)') {
            return 'Dibatalkan Sistem: Kedaluwarsa';
        }
        return $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public static function autoExpirePending()
    {
        $now = \Carbon\Carbon::now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        $expired = self::where('status', 'menunggu')
            ->where(function ($q) use ($today, $currentTime) {
                $q->where('tanggal_pinjam', '<', $today)
                    ->orWhere(function ($subQ) use ($today, $currentTime) {
                        $subQ->where('tanggal_pinjam', '=', $today)
                            ->where('jam_mulai', '<=', $currentTime);
                    });
            })->get();

        foreach ($expired as $pinjam) {
            $pinjam->update([
                'status' => 'ditolak',
                'alasan_penolakan' => 'Dibatalkan Sistem: Kedaluwarsa',
                'waktu_approval' => now()
            ]);
        }
    }
}
