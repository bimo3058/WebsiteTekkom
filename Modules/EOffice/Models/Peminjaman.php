<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Peminjaman extends Model
{
    use HasFactory, SoftDeletes;

    // Konstanta sumber peminjaman eksternal
    public const SOURCE_KP = 'kp';
    public const SOURCE_CAPSTONE = 'capstone';
    public const SOURCE_PRAKTIKUM = 'praktikum';

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
        'created_by',
        'is_hidden_by_user',
        'source',
        'source_id',
        'replaces_peminjaman_id',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'waktu_approval' => 'datetime',
        'source_id' => 'string',
    ];

    public function getSourceLabelAttribute(): string
    {
        $src = strtolower(trim((string) $this->source));
        if ($src === 'capstone' || str_contains((string) $this->tujuan, '[CAPSTONE')) {
            return 'Sidang Capstone';
        }

        if (in_array($src, ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik']) || str_contains((string) $this->tujuan, 'Seminar KP') || str_contains((string) $this->tujuan, 'Kerja Praktik')) {
            return 'Seminar KP';
        }

        if ($src === 'praktikum' || str_contains((string) $this->tujuan, 'Praktikum')) {
            return 'Praktikum';
        }

        return 'Peminjaman Langsung';
    }

    public function getIsPastEventAttribute(): bool
    {
        if (!$this->tanggal_pinjam) {
            return false;
        }

        $tglStr = $this->tanggal_pinjam instanceof \Carbon\CarbonInterface
            ? $this->tanggal_pinjam->format('Y-m-d')
            : \Carbon\Carbon::parse($this->tanggal_pinjam)->format('Y-m-d');

        $jamStr = $this->jam_selesai ? substr((string) $this->jam_selesai, 0, 8) : '23:59:59';

        return \Carbon\Carbon::parse("{$tglStr} {$jamStr}")->isPast();
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->trashed() && strtolower((string) $this->status) === 'disetujui' && $this->is_past_event) {
            return 'disetujui';
        }

        if ($this->trashed() && strtolower((string) $this->status) !== 'dibatalkan') {
            return 'dibatalkan';
        }

        return (string) $this->status;
    }

    public function getAlasanPenolakanAttribute($value)
    {
        // Secara dinamis mengubah teks usang di baris database lama saat dirender ke layar
        if ($value === 'Sistem (Kadaluarsa otomatis - Waktu peminjaman sudah terlewat)') {
            return 'Dibatalkan Sistem: Kedaluwarsa';
        }

        // Jika status dibatalkan dan alasan kosong, berarti dibatalkan oleh user sendiri
        if (strtolower($this->status) === 'dibatalkan' && empty($value)) {
            return 'Dibatalkan oleh pengguna secara mandiri';
        }

        return $value;
    }

    public function getBerkasUrlAttribute(): ?string
    {
        if (empty($this->berkas_pendukung)) {
            return null;
        }

        return app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->berkas_pendukung, 'eoffice');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function replacesPeminjaman()
    {
        return $this->belongsTo(self::class, 'replaces_peminjaman_id');
    }

    public function replacementBookings()
    {
        return $this->hasMany(self::class, 'replaces_peminjaman_id');
    }

    public static function autoExpirePending()
    {
        $now = \Carbon\Carbon::now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        // 1. Peminjaman menunggu reguler yang waktu pelaksanaannya sudah lewat
        $expired = self::with(['replacesPeminjaman'])->where('status', 'menunggu')
            ->where(function ($q) use ($today, $currentTime) {
                $q->where('tanggal_pinjam', '<', $today)
                    ->orWhere(function ($subQ) use ($today, $currentTime) {
                        $subQ->where('tanggal_pinjam', '=', $today)
                            ->where('jam_mulai', '<=', $currentTime);
                    });
            })->get();

        // 2. Peminjaman reschedule yang waktu jadwal lamanya sudah tiba/terlewat
        $expiredReschedules = self::with(['replacesPeminjaman'])
            ->where('status', 'menunggu')
            ->whereNotNull('replaces_peminjaman_id')
            ->whereHas('replacesPeminjaman', function ($rq) use ($today, $currentTime) {
                $rq->where('tanggal_pinjam', '<', $today)
                    ->orWhere(function ($subQ) use ($today, $currentTime) {
                        $subQ->where('tanggal_pinjam', '=', $today)
                            ->where('jam_mulai', '<=', $currentTime);
                    });
            })->get();

        $allExpired = $expired->merge($expiredReschedules)->unique('id');

        foreach ($allExpired as $pinjam) {
            $prevStatus = $pinjam->status;
            $alasan = 'Dibatalkan Sistem: Melewati batas waktu persetujuan Admin Ruang';

            $pinjam->update([
                'status' => 'ditolak',
                'alasan_penolakan' => $alasan,
                'waktu_approval' => now(),
            ]);

            event(new \Modules\EOffice\Events\RoomBookingStatusChanged($pinjam, 'ditolak', $prevStatus, $alasan));
        }
    }
}
