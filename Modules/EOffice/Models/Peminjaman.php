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

    // Prioritas Hierarki Peminjaman
    public const PRIORITY_CAPSTONE = 30;
    public const PRIORITY_PRAKTIKUM = 30;
    public const PRIORITY_KP = 20;
    public const PRIORITY_UMUM = 10;

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

    public static function getPriorityLevel(?string $source, ?string $tujuan = null): int
    {
        $src = strtolower(trim((string) $source));
        if ($src === 'capstone' || str_contains((string) $tujuan, '[CAPSTONE')) {
            return self::PRIORITY_CAPSTONE;
        }

        if ($src === 'praktikum' || str_contains((string) $tujuan, 'Praktikum')) {
            return self::PRIORITY_PRAKTIKUM;
        }

        if (in_array($src, ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik']) || str_contains((string) $tujuan, 'Seminar KP') || str_contains((string) $tujuan, 'Kerja Praktik')) {
            return self::PRIORITY_KP;
        }

        return self::PRIORITY_UMUM;
    }

    public function getPriorityAttribute(): int
    {
        return self::getPriorityLevel($this->source, $this->tujuan);
    }

    public function getConflictingPendingsAttribute()
    {
        if ($this->status !== 'menunggu') {
            return collect();
        }

        $dateStr = $this->tanggal_pinjam instanceof \Carbon\CarbonInterface
            ? $this->tanggal_pinjam->format('Y-m-d')
            : (string) $this->tanggal_pinjam;

        $jamMulai = substr((string) $this->jam_mulai, 0, 5);
        $jamSelesai = substr((string) $this->jam_selesai, 0, 5);

        return self::with(['user'])->where('ruangan_id', $this->ruangan_id)
            ->where('tanggal_pinjam', $dateStr)
            ->where('status', 'menunggu')
            ->where('id', '!=', $this->id)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai);
            })
            ->get();
    }

    public function getConflictGroupInfo(): array
    {
        if ($this->status !== 'menunggu') {
            return [];
        }

        $conflicts = $this->conflicting_pendings;
        if ($conflicts->isEmpty()) {
            return [];
        }

        // Gabungkan peminjaman ini dengan seluruh peminjaman yang bentrok
        $all = collect([$this])->merge($conflicts)->sortByDesc(function ($p) {
            return sprintf('%03d_%010d', $p->priority, 9999999999 - $p->id);
        })->values();

        $total = $all->count();
        $items = [];

        foreach ($all as $index => $item) {
            $rankNum = $index + 1;
            $category = ($item->priority >= self::PRIORITY_KP) ? 'Akademik' : 'Umum';
            $peminjamName = $item->user->name ?? 'User';
            $label = $item->source_label;
            $tujuan = $item->tujuan;

            $cleanTujuan = trim(preg_replace('/^\[CAPSTONE[^\]]*\]/i', '', (string) $tujuan));
            $detailSuffix = !empty($cleanTujuan) && $cleanTujuan !== $label ? " ({$cleanTujuan})" : '';

            $line = "[Prioritas {$rankNum} - {$category}] {$label} - {$peminjamName}{$detailSuffix}";

            // Peringkat 1 otomatis ditandai (Rekomendasi Disetujui) jika memiliki prioritas lebih tinggi dari kandidat lain
            if ($index === 0 && $total > 1) {
                $line .= ' (Rekomendasi Disetujui)';
            }

            $items[] = [
                'id' => $item->id,
                'text' => $line,
                'is_current' => ($item->id === $this->id),
            ];
        }

        return [
            'total' => $total,
            'header' => "PERHATIAN ADMIN RUANG: SLOT INI DIPEREBUTKAN {$total} PENGAJUAN",
            'items' => $items,
            'footer' => "Jika Anda menyetujui pengajuan ini, sistem akan otomatis menolak pengajuan lainnya dan memberi notifikasi ke pemohon.",
        ];
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCreatorRoleLabelAttribute(): ?string
    {
        if (!$this->created_by || $this->created_by === $this->user_id) {
            return null;
        }

        if (!$this->relationLoaded('creator')) {
            $this->load('creator.roles');
        }

        if (!$this->creator || !$this->creator->roles || $this->creator->roles->isEmpty()) {
            return null;
        }

        return $this->creator->roles
            ->pluck('name')
            ->map(fn($name) => ucwords(str_replace('_', ' ', (string) $name)))
            ->implode(', ');
    }

    public function getTeleponAttribute(): ?string
    {
        if (!empty($this->nomor_telepon)) {
            return $this->nomor_telepon;
        }

        return $this->user?->whatsapp ?? null;
    }

    public function getCreatorTeleponAttribute(): ?string
    {
        if (!$this->created_by || $this->created_by === $this->user_id) {
            return null;
        }

        return $this->creator?->whatsapp ?? null;
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
