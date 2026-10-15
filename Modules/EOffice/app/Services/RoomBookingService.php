<?php

namespace Modules\EOffice\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\EOffice\Models\MrJadwalInternal;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\Ruangan;
use Modules\EOffice\Models\TanggalLibur;
use Modules\EOffice\Models\Pengaturan;

class RoomBookingService
{
    /**
     * Memeriksa ketersediaan seluruh ruangan pada tanggal dan rentang jam tertentu.
     * Mengembalikan daftar ruangan lengkap dengan status tersedia dan alasannya jika terisi.
     *
     * @param string $tanggal Format: Y-m-d
     * @param string $jamMulai Format: H:i (e.g. '08:00')
     * @param string $jamSelesai Format: H:i (e.g. '10:00')
     * @param int|null $kapasitasMin Filter minimal kapasitas ruangan
     * @param string|null $kategori Filter kategori ruangan (misal: 'Laboratorium', 'Ruang Sidang', dll)
     * @param bool $bypassHMin Lewati batasan H-min (default true untuk pemesanan agenda resmi/modul eksternal)
     * @return array
     */
    public function getAvailableRooms(
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        ?int $kapasitasMin = null,
        ?string $kategori = null,
        bool $bypassHMin = true
    ): array {
        $parsedDate = Carbon::parse($tanggal);
        $formattedDate = $parsedDate->format('Y-m-d');
        $dayOfWeekIso = (int) $parsedDate->format('N'); // 1 = Senin .. 7 = Minggu

        $jamMulaiNorm = substr($jamMulai, 0, 5);
        $jamSelesaiNorm = substr($jamSelesai, 0, 5);

        // Ambil master ruangan yang aktif
        $ruanganQuery = Ruangan::where('is_active', true)->orderBy('nama', 'asc');

        if ($kapasitasMin && $kapasitasMin > 0) {
            $ruanganQuery->where('kapasitas', '>=', $kapasitasMin);
        }

        if ($kategori && $kategori !== 'Semua' && $kategori !== 'Semua Kategori') {
            $ruanganQuery->where('kategori', $kategori);
        }

        $allRuangans = $ruanganQuery->get();

        // 1. Ambil bentrok dari Jadwal Internal & Jadwal Kuliah Rutin
        $internalConflicts = MrJadwalInternal::where(function ($q) use ($jamMulaiNorm, $jamSelesaiNorm) {
            $q->where('jam_mulai', '<', $jamSelesaiNorm)
                ->where('jam_selesai', '>', $jamMulaiNorm);
        })->where(function ($q) use ($dayOfWeekIso, $formattedDate) {
            $q->where(function ($sub) use ($dayOfWeekIso, $formattedDate) {
                $sub->where('tipe_jadwal', 'rutin')
                    ->where('hari', $dayOfWeekIso)
                    ->where(function ($eff) use ($formattedDate) {
                        $eff->whereNull('tgl_mulai_efektif')
                            ->orWhere('tgl_mulai_efektif', '<=', $formattedDate);
                    })
                    ->where(function ($eff) use ($formattedDate) {
                        $eff->whereNull('tgl_selesai_efektif')
                            ->orWhere('tgl_selesai_efektif', '>=', $formattedDate);
                    });
            })->orWhere(function ($sub) use ($formattedDate) {
                $sub->where('tipe_jadwal', 'spesifik')
                    ->where('tanggal_spesifik', $formattedDate);
            });
        })->get()->groupBy('ruangan_id');

        // 2. Ambil bentrok dari Peminjaman Resmi yang sudah Disetujui
        $peminjamanApprovedConflicts = Peminjaman::where('tanggal_pinjam', $formattedDate)
            ->where('status', 'disetujui')
            ->where(function ($q) use ($jamMulaiNorm, $jamSelesaiNorm) {
                $q->where('jam_mulai', '<', $jamSelesaiNorm)
                    ->where('jam_selesai', '>', $jamMulaiNorm);
            })->get()->groupBy('ruangan_id');

        // 3. Ambil data peminjaman yang masih 'menunggu' (sebagai informasi tentatif)
        $peminjamanPendingConflicts = Peminjaman::where('tanggal_pinjam', $formattedDate)
            ->where('status', 'menunggu')
            ->where(function ($q) use ($jamMulaiNorm, $jamSelesaiNorm) {
                $q->where('jam_mulai', '<', $jamSelesaiNorm)
                    ->where('jam_selesai', '>', $jamMulaiNorm);
            })->get()->groupBy('ruangan_id');

        // Ambil pengaturan operasional admin
        $settings = Pengaturan::pluck('value', 'key')->toArray();
        $jamBuka = $settings['jam_buka'] ?? '08:00';
        $jamTutup = $settings['jam_tutup'] ?? '16:00';
        $bukaAkhirPekan = filter_var($settings['buka_akhir_pekan'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $batasHMinBooking = (int) ($settings['batas_h_min_booking'] ?? 0);

        // 4. Periksa apakah tanggal libur nasional
        $isHoliday = TanggalLibur::where('tanggal', $formattedDate)->first();

        // 5. Evaluasi pemblokiran operasional global
        $globalBlockReason = null;
        $globalBlockType = null;

        $now = Carbon::now();
        $isPastDate = $parsedDate->isPast() && !$parsedDate->isToday();
        $isPastTimeToday = $parsedDate->isToday() && ($jamMulaiNorm < $now->format('H:i'));

        $minAllowedDate = Carbon::today()->addDays($batasHMinBooking);
        $isTooEarly = !$bypassHMin && ($batasHMinBooking > 0) && $parsedDate->copy()->startOfDay()->lt($minAllowedDate);

        $isClosedWeekend = !$bukaAkhirPekan && $parsedDate->isWeekend();

        if ($jamSelesaiNorm <= $jamMulaiNorm) {
            $globalBlockType = 'invalid_time';
            $globalBlockReason = 'Jam selesai harus lebih besar dari jam mulai.';
        } elseif ($isPastDate || $isPastTimeToday) {
            $globalBlockType = 'expired';
            $globalBlockReason = 'Waktu yang dipilih sudah lewat.';
        } elseif ($isTooEarly) {
            $globalBlockType = 'h_min';
            $globalBlockReason = "Pemesanan minimal H-{$batasHMinBooking} hari sebelumnya.";
        } elseif ($isClosedWeekend) {
            $globalBlockType = 'weekend';
            $globalBlockReason = 'Ruangan kampus tutup pada akhir pekan (Sabtu/Minggu).';
        } elseif ($isHoliday) {
            $globalBlockType = 'holiday';
            $globalBlockReason = 'Hari Libur Nasional: ' . ($isHoliday->keterangan ?: 'Tutup');
        } elseif ($jamMulaiNorm < $jamBuka || $jamSelesaiNorm > $jamTutup) {
            $globalBlockType = 'operating_hours';
            $globalBlockReason = "Di luar jam operasional kampus ({$jamBuka} - {$jamTutup}).";
        }

        $results = [];

        foreach ($allRuangans as $room) {
            $isAvailable = true;
            $conflictType = 'none';
            $conflictReason = null;
            $hasPendingWarning = false;
            $pendingReason = null;

            if ($globalBlockReason !== null) {
                $isAvailable = false;
                $conflictType = $globalBlockType;
                $conflictReason = $globalBlockReason;
            } elseif ($internalConflicts->has($room->id)) {
                $firstConflict = $internalConflicts->get($room->id)->first();
                $isAvailable = false;
                $conflictType = $firstConflict->kategori === 'Jadwal Akademik (Kuliah)' ? 'akademik' : 'maintenance';
                $conflictReason = $firstConflict->kategori === 'Jadwal Akademik (Kuliah)'
                    ? 'Jadwal Kuliah: ' . ($firstConflict->mata_kuliah ?: 'Mata Kuliah') . ' (' . $firstConflict->kelas . ')'
                    : 'Blokir: ' . ($firstConflict->keterangan ?: $firstConflict->kategori);
            } elseif ($peminjamanApprovedConflicts->has($room->id)) {
                $firstPeminjaman = $peminjamanApprovedConflicts->get($room->id)->first();
                $isAvailable = false;
                $conflictType = 'peminjaman_sah';
                $conflictReason = 'Terpakai: ' . ($firstPeminjaman->source_label ?: 'Peminjaman') . ' - ' . ($firstPeminjaman->tujuan ?: 'Kegiatan Kampus');
            } elseif ($peminjamanPendingConflicts->has($room->id)) {
                $firstPending = $peminjamanPendingConflicts->get($room->id)->first();
                $isAvailable = false;
                $conflictType = 'menunggu';
                $conflictReason = 'Sedang Dalam Pengajuan: ' . ($firstPending->source_label ?: 'Peminjaman') . ' (' . ($firstPending->tujuan ?: 'Menunggu Persetujuan') . ')';
                $hasPendingWarning = true;
                $pendingReason = $conflictReason;
            }

            $results[] = [
                'id' => $room->id,
                'nama' => $room->nama,
                'lokasi' => $room->lokasi,
                'lantai' => $room->lantai,
                'kapasitas' => $room->kapasitas,
                'kategori' => $room->kategori,
                'fasilitas' => $room->fasilitas,
                'is_available' => $isAvailable,
                'conflict_type' => $conflictType,
                'conflict_reason' => $conflictReason,
                'has_pending_warning' => $hasPendingWarning,
                'pending_reason' => $pendingReason,
                'is_holiday' => (bool) $isHoliday,
                'holiday_keterangan' => $isHoliday?->keterangan,
            ];
        }

        return $results;
    }

    /**
     * Menyimpan peminjaman ruangan dari modul eksternal (KP, Capstone, Praktikum, atau Umum).
     * Menerapkan Database Locking untuk mencegah Race Condition.
     *
     * @param array $payload
     * @return Peminjaman
     * @throws \Exception
     */
    public function bookRoom(array $payload): Peminjaman
    {
        return DB::transaction(function () use ($payload) {
            $ruanganId = $payload['ruangan_id'];
            $parsedDate = Carbon::parse($payload['tanggal_pinjam'] ?? $payload['tanggal']);
            $tanggal = $parsedDate->format('Y-m-d');
            $jamMulai = substr($payload['jam_mulai'], 0, 5);
            $jamSelesai = substr($payload['jam_selesai'], 0, 5);
            $source = isset($payload['source']) ? strtolower((string) $payload['source']) : null;
            $sourceId = $payload['source_id'] ?? null;
            $userId = $payload['user_id'] ?? auth()->id();
            $tujuan = $payload['tujuan'] ?? $payload['keperluan'] ?? 'Kegiatan Akademik';
            $nomorTelepon = $payload['nomor_telepon'] ?? null;
            $berkasPendukung = $payload['berkas_pendukung'] ?? null;

            // 1. Validasi Urutan Jam
            if ($jamSelesai <= $jamMulai) {
                throw new \Exception('Pemesanan gagal: Jam selesai harus lebih besar dari jam mulai.');
            }

            // 2. Ambil Pengaturan Operasional Admin
            $settings = Pengaturan::pluck('value', 'key')->toArray();
            $jamBuka = $settings['jam_buka'] ?? '08:00';
            $jamTutup = $settings['jam_tutup'] ?? '16:00';
            $bukaAkhirPekan = filter_var($settings['buka_akhir_pekan'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $batasHMinBooking = (int) ($settings['batas_h_min_booking'] ?? 0);

            // 3. Validasi Waktu Masa Lalu
            $now = Carbon::now();
            if ($parsedDate->isPast() && !$parsedDate->isToday()) {
                throw new \Exception('Pemesanan gagal: Tanggal yang dipilih sudah lewat.');
            }
            if ($parsedDate->isToday() && $jamMulai < $now->format('H:i')) {
                throw new \Exception('Pemesanan gagal: Jam mulai yang dipilih sudah lewat.');
            }

            // 4. Validasi Akhir Pekan (Weekend)
            if (!$bukaAkhirPekan && $parsedDate->isWeekend()) {
                throw new \Exception('Pemesanan gagal: Ruangan kampus tutup pada akhir pekan (Sabtu/Minggu).');
            }

            // 5. Validasi Hari Libur Nasional
            $holiday = TanggalLibur::where('tanggal', $tanggal)->first();
            if ($holiday) {
                throw new \Exception("Pemesanan gagal: Tanggal {$tanggal} adalah Hari Libur Nasional (" . ($holiday->keterangan ?: 'Tutup') . ').');
            }

            // 6. Validasi Jam Operasional Kampus
            if ($jamMulai < $jamBuka || $jamSelesai > $jamTutup) {
                throw new \Exception("Pemesanan gagal: Waktu peminjaman ({$jamMulai} - {$jamSelesai}) berada di luar jam operasional ruangan ({$jamBuka} - {$jamTutup}).");
            }

            // 7. Lock baris ruangan untuk mencegah balapan submit di milidetik yang sama
            $ruangan = Ruangan::where('id', $ruanganId)->lockForUpdate()->first();
            if (!$ruangan || !$ruangan->is_active) {
                throw new \Exception('Ruangan yang dipilih tidak ditemukan atau sedang tidak aktif.');
            }

            $dayOfWeekIso = (int) $parsedDate->format('N');

            // 1. Cek bentrok dengan Jadwal Kuliah / Blokir Internal
            $bentrokInternal = MrJadwalInternal::where('ruangan_id', $ruanganId)
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                })->where(function ($q) use ($dayOfWeekIso, $tanggal) {
                    $q->where(function ($sub) use ($dayOfWeekIso, $tanggal) {
                        $sub->where('tipe_jadwal', 'rutin')
                            ->where('hari', $dayOfWeekIso)
                            ->where(function ($eff) use ($tanggal) {
                                $eff->whereNull('tgl_mulai_efektif')
                                    ->orWhere('tgl_mulai_efektif', '<=', $tanggal);
                            })
                            ->where(function ($eff) use ($tanggal) {
                                $eff->whereNull('tgl_selesai_efektif')
                                    ->orWhere('tgl_selesai_efektif', '>=', $tanggal);
                            });
                    })->orWhere(function ($sub) use ($tanggal) {
                        $sub->where('tipe_jadwal', 'spesifik')
                            ->where('tanggal_spesifik', $tanggal);
                    });
                })->first();

            if ($bentrokInternal) {
                $namaBentrok = $bentrokInternal->kategori === 'Jadwal Akademik (Kuliah)'
                    ? ($bentrokInternal->mata_kuliah . ' (' . $bentrokInternal->kelas . ')')
                    : $bentrokInternal->keterangan;
                throw new \Exception("Ruangan {$ruangan->nama} tidak dapat dipesan karena terisi: {$namaBentrok}.");
            }

            // 2. Cek bentrok dengan Peminjaman (yang sudah Disetujui atau Sedang Dalam Pengajuan)
            $bentrokPeminjaman = Peminjaman::where('ruangan_id', $ruanganId)
                ->where('tanggal_pinjam', $tanggal)
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                })->first();

            if ($bentrokPeminjaman) {
                $label = $bentrokPeminjaman->source_label ?: 'Peminjaman Lain';
                $statusText = $bentrokPeminjaman->status === 'menunggu'
                    ? 'sedang dalam proses pengajuan persetujuan'
                    : 'sudah dipesan resmi';
                throw new \Exception("Ruangan {$ruangan->nama} tidak dapat dipesan karena {$statusText} ({$label}: {$bentrokPeminjaman->tujuan}).");
            }

            // 3. Tentukan Status Berdasarkan Kebijakan Modul (Dual-Pipeline)
            $isAutoApprove = in_array($source, ['kp', 'kerja_praktik', 'capstone']);
            $status = $isAutoApprove ? 'disetujui' : 'menunggu';
            $waktuApproval = $isAutoApprove ? now() : null;

            // 4. Simpan ke database
            $peminjaman = Peminjaman::create([
                'user_id' => $userId,
                'ruangan_id' => $ruanganId,
                'nomor_telepon' => $nomorTelepon,
                'tujuan' => $tujuan,
                'tanggal_pinjam' => $tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'status' => $status,
                'waktu_approval' => $waktuApproval,
                'source' => $source,
                'source_id' => $sourceId,
                'berkas_pendukung' => $berkasPendukung,
                'created_by' => auth()->id() ?? $userId,
            ]);

            return $peminjaman;
        });
    }

    /**
     * Membatalkan peminjaman berdasarkan sumber modul dan ID referensinya.
     * Digunakan jika seminar/praktikum di-reschedule atau dibatalkan dari modul asal.
     *
     * @param string $source
     * @param int|string $sourceId
     * @param string|null $alasan
     * @return bool
     */
    public function cancelBooking(string $source, $sourceId, ?string $alasan = null): bool
    {
        $normalizedSource = strtolower((string) $source);

        $peminjaman = Peminjaman::where('source', $normalizedSource)
            ->where('source_id', $sourceId)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->first();

        if (!$peminjaman) {
            return false;
        }

        $peminjaman->update([
            'status' => 'dibatalkan',
            'alasan_penolakan' => $alasan ?: 'Dibatalkan oleh sistem ' . strtoupper($normalizedSource),
            'waktu_approval' => now(),
        ]);

        return true;
    }
}
