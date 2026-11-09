<?php

namespace Modules\EOffice\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\EOffice\Models\MrBlacklist;
use Modules\EOffice\Models\MrJadwalInternal;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\Ruangan;
use Modules\EOffice\Models\TanggalLibur;
use Modules\EOffice\Models\Pengaturan;

class RoomBookingService
{
    // Konstanta sumber modul eksternal
    public const SOURCE_KP = Peminjaman::SOURCE_KP;
    public const SOURCE_CAPSTONE = Peminjaman::SOURCE_CAPSTONE;
    public const SOURCE_PRAKTIKUM = Peminjaman::SOURCE_PRAKTIKUM;

    /**
     * Memeriksa ketersediaan seluruh ruangan pada tanggal dan rentang jam tertentu.
     * Mengembalikan daftar ruangan lengkap dengan status tersedia dan alasannya jika terisi.
     *
     * @param string $tanggal Format: Y-m-d
     * @param string $jamMulai Format: H:i (e.g. '08:00')
     * @param string $jamSelesai Format: H:i (e.g. '10:00')
     * @param int|null $kapasitasMin Filter minimal kapasitas ruangan
     * @param string|null $kategori Filter kategori ruangan ('Kelas', 'Laboratorium', 'Sidang')
     * @param bool $bypassHMin Lewati batasan H-min (default true untuk pemesanan agenda resmi/modul eksternal)
     * @param string|null $source Sumber modul pemanggil ('kp', 'capstone', 'praktikum')
     * @return array
     */
    public function getAvailableRooms(
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        ?int $kapasitasMin = null,
        ?string $kategori = null,
        bool $bypassHMin = true,
        ?string $source = null
    ): array {
        $parsedDate = Carbon::parse($tanggal);
        $formattedDate = $parsedDate->format('Y-m-d');
        $dayOfWeekIso = (int) $parsedDate->format('N'); // 1 = Senin .. 7 = Minggu

        $jamMulaiNorm = substr($jamMulai, 0, 5);
        $jamSelesaiNorm = substr($jamSelesai, 0, 5);

        // Normalisasi kategori: jika pemanggil mengirim 'Ruang Sidang', petakan ke 'Sidang'
        if ($kategori) {
            $catLower = strtolower(trim($kategori));
            if ($catLower === 'ruang sidang' || $catLower === 'sidang') {
                $kategori = Ruangan::KATEGORI_SIDANG;
            } elseif ($catLower === 'kelas' || $catLower === 'ruang kelas') {
                $kategori = Ruangan::KATEGORI_KELAS;
            } elseif ($catLower === 'laboratorium' || $catLower === 'lab') {
                $kategori = Ruangan::KATEGORI_LAB;
            }
        }

        // Ambil master ruangan yang aktif
        $ruanganQuery = Ruangan::where('is_active', true)->orderBy('nama', 'asc');

        // Aturan Pembatasan Modul Eksternal:
        // Modul KP dan Praktikum DILARANG meminjam Ruang Sidang (khusus Capstone & Admin)
        $normalizedSource = $source ? strtolower(trim((string) $source)) : null;
        if (in_array($normalizedSource, ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])) {
            $normalizedSource = 'kp';
        }
        if (in_array($normalizedSource, ['kp', 'praktikum'])) {
            $ruanganQuery->where('kategori', '!=', Ruangan::KATEGORI_SIDANG);
        }

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
        $jamBuka = substr($settings['jam_buka'] ?? '08:00', 0, 5);
        $jamTutup = substr($settings['jam_tutup'] ?? '16:00', 0, 5);
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
                $roomPendings = $peminjamanPendingConflicts->get($room->id);
                $callerPriority = Peminjaman::getPriorityLevel($source);

                $blockingPending = $roomPendings->first(function ($p) use ($callerPriority) {
                    return $p->priority >= $callerPriority;
                });

                if ($blockingPending) {
                    $isAvailable = false;
                    $conflictType = 'menunggu';
                    $conflictReason = 'Sedang Dalam Pengajuan: ' . ($blockingPending->source_label ?: 'Peminjaman') . ' (' . ($blockingPending->tujuan ?: 'Menunggu Persetujuan') . ')';
                    $hasPendingWarning = true;
                    $pendingReason = $conflictReason;
                } else {
                    $firstLower = $roomPendings->first();
                    $isAvailable = true;
                    $hasPendingWarning = true;
                    $pendingReason = 'Terdapat antrean peminjaman (' . ($firstLower->source_label ?: 'Peminjaman') . ') yang masih menunggu persetujuan. Agenda resmi Anda akan masuk sebagai prioritas akademik di meja Admin Ruang.';
                }
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
            $source = isset($payload['source']) ? strtolower(trim((string) $payload['source'])) : null;
            if (in_array($source, ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])) {
                $source = 'kp';
            }
            if ($source && !in_array($source, ['kp', 'capstone', 'praktikum'])) {
                throw new \Exception("Pemesanan gagal: Sumber modul '{$source}' tidak dikenali oleh sistem manajemen ruangan.");
            }

            $sourceId = $payload['source_id'] ?? null;
            $userId = $payload['user_id'] ?? auth()->id();
            if (!$userId) {
                throw new \Exception('Pemesanan gagal: Identitas pemohon (user_id) tidak valid.');
            }

            // 0. Pengecekan Sanksi / Blacklist Peminjaman Ruangan
            $isBlacklisted = MrBlacklist::where('user_id', $userId)->first();
            if ($isBlacklisted) {
                $alasan = $isBlacklisted->alasan ? " (Alasan: {$isBlacklisted->alasan})" : '';
                throw new \Exception("Pemesanan gagal: Akun pemohon sedang ditangguhkan dari layanan peminjaman ruangan{$alasan}.");
            }
            $tujuan = $payload['tujuan'] ?? $payload['keperluan'] ?? 'Kegiatan Akademik';
            $targetUser = \App\Models\User::find($userId);
            $nomorTelepon = $payload['nomor_telepon'] ?? $targetUser?->whatsapp ?? null;
            if (!empty($payload['nomor_telepon']) && $targetUser && empty($targetUser->whatsapp)) {
                $targetUser->update(['whatsapp' => $payload['nomor_telepon']]);
            }
            $berkasPendukung = $payload['berkas_pendukung'] ?? null;

            // 1. Validasi Urutan Jam
            if ($jamSelesai <= $jamMulai) {
                throw new \Exception('Pemesanan gagal: Jam selesai harus lebih besar dari jam mulai.');
            }

            // 2. Ambil Pengaturan Operasional Admin
            $settings = Pengaturan::pluck('value', 'key')->toArray();
            $jamBuka = substr($settings['jam_buka'] ?? '08:00', 0, 5);
            $jamTutup = substr($settings['jam_tutup'] ?? '16:00', 0, 5);
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

            // 8. Validasi Pembatasan Kategori Ruangan per Modul:
            // Ruangan berkategori 'Sidang' hanya boleh dipesan oleh modul Capstone (Sidang TA/Capstone).
            // Modul KP, Praktikum, atau modul lain DITOLAK meminjam Ruang Sidang.
            if ($ruangan->kategori === Ruangan::KATEGORI_SIDANG && $source !== 'capstone') {
                $sourceLabel = match ($source) {
                    'kp' => 'Kerja Praktik (Seminar KP)',
                    'praktikum' => 'Praktikum',
                    default => 'Modul ' . ($source ?: 'Umum'),
                };
                throw new \Exception("Pemesanan gagal: {$sourceLabel} tidak diizinkan meminjam Ruangan berkategori 'Sidang' ({$ruangan->nama}). Ruang Sidang khusus diperuntukkan bagi agenda Sidang Capstone/Tugas Akhir.");
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

            // 2. Cek bentrok dengan Peminjaman Resmi yang sudah Disetujui (Hard Lock)
            $bentrokApproved = Peminjaman::where('ruangan_id', $ruanganId)
                ->where('tanggal_pinjam', $tanggal)
                ->where('status', 'disetujui')
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                })->first();

            if ($bentrokApproved) {
                $label = $bentrokApproved->source_label ?: 'Peminjaman Lain';
                throw new \Exception("Ruangan {$ruangan->nama} tidak dapat dipesan karena sudah dipesan dan disetujui resmi ({$label}: {$bentrokApproved->tujuan}). Silakan pilih jadwal/ruangan lain atau hubungi Admin Ruang untuk koordinasi.");
            }

            // 3. Cek bentrok dengan Peminjaman yang masih Menunggu Persetujuan (Soft Lock dengan Hierarki Prioritas)
            $incomingPriority = Peminjaman::getPriorityLevel($source, $tujuan);

            $bentrokMenunggu = Peminjaman::where('ruangan_id', $ruanganId)
                ->where('tanggal_pinjam', $tanggal)
                ->where('status', 'menunggu')
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                })->get();

            if ($bentrokMenunggu->isNotEmpty()) {
                // Cari apakah ada antrean menunggu yang memiliki prioritas sama atau lebih tinggi
                $blockingPending = $bentrokMenunggu->first(function ($item) use ($incomingPriority) {
                    return $item->priority >= $incomingPriority;
                });

                if ($blockingPending) {
                    $label = $blockingPending->source_label ?: 'Peminjaman Lain';
                    throw new \Exception("Ruangan {$ruangan->nama} tidak dapat dipesan karena sedang dalam proses pengajuan persetujuan ({$label}: {$blockingPending->tujuan}).");
                }

                // Jika seluruh antrean menunggu memiliki prioritas lebih rendah daripada $incomingPriority,
                // pemesanan akademik prioritas ini diizinkan masuk berdampingan ke meja Admin Ruang.
            }

            // 3. Status Peminjaman dari Modul (Selalu 'menunggu' untuk diverifikasi & disetujui Admin Ruangan)
            $status = 'menunggu';
            $waktuApproval = null;

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
        if (empty($sourceId)) {
            return false;
        }

        $normalizedSource = strtolower(trim((string) $source));
        if (in_array($normalizedSource, ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])) {
            $normalizedSource = 'kp';
        }

        $sourceQuery = ($normalizedSource === 'kp') ? ['kp', 'kerja_praktik', 'kerja praktik'] : [$normalizedSource];

        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $bookings = Peminjaman::with(['user', 'ruangan'])
            ->whereIn('source', $sourceQuery)
            ->where('source_id', (string) $sourceId)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->where(function ($q) use ($date, $time) {
                // Hanya membatalkan jadwal mendatang / yang belum selesai terlaksana
                $q->where('tanggal_pinjam', '>', $date)
                    ->orWhere(function ($sub) use ($date, $time) {
                        $sub->where('tanggal_pinjam', '=', $date)
                            ->where('jam_selesai', '>', $time);
                    });
            })
            ->get();

        if ($bookings->isEmpty()) {
            return false;
        }

        $alasanTeks = $alasan ?: 'Dibatalkan oleh sistem ' . strtoupper($normalizedSource);

        DB::transaction(function () use ($bookings, $alasanTeks) {
            foreach ($bookings as $peminjaman) {
                $prevStatus = $peminjaman->status;
                $peminjaman->update([
                    'status' => 'dibatalkan',
                    'alasan_penolakan' => $alasanTeks,
                    'waktu_approval' => now(),
                ]);

                if ($peminjaman->user) {
                    $peminjaman->user->notify(new \Modules\EOffice\Notifications\PeminjamanStatusUpdated($peminjaman, true));
                }

                event(new \Modules\EOffice\Events\RoomBookingStatusChanged($peminjaman, 'dibatalkan', $prevStatus, $alasanTeks));
            }
        });

        return true;
    }

    /**
     * Menjadwalkan ulang (Reschedule) peminjaman ruangan dari modul eksternal (KP, Capstone, Praktikum).
     * Menerapkan audit trail lengkap: Jadwal lama dibatalkan secara tertib, jadwal baru dibuat berstatus 'menunggu'.
     *
     * @param string $source
     * @param int|string $sourceId
     * @param array $payload
     * @return Peminjaman
     * @throws \Exception
     */
    public function rescheduleBooking(string $source, $sourceId, array $payload): Peminjaman
    {
        if (empty($sourceId)) {
            throw new \Exception('Reschedule gagal: Parameter source_id tidak boleh kosong.');
        }

        $normalizedSource = strtolower(trim((string) $source));
        if (in_array($normalizedSource, ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])) {
            $normalizedSource = 'kp';
        }

        return DB::transaction(function () use ($normalizedSource, $sourceId, $payload) {
            $sourceQuery = ($normalizedSource === 'kp') ? ['kp', 'kerja_praktik', 'kerja praktik'] : [$normalizedSource];

            // 1. Cari peminjaman asli yang disetujui untuk di-reschedule (utamakan status 'disetujui' dan yang merupakan root booking)
            $oldBooking = Peminjaman::whereIn('source', $sourceQuery)
                ->where('source_id', (string) $sourceId)
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->orderByRaw("CASE WHEN status = 'disetujui' THEN 1 ELSE 2 END")
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$oldBooking) {
                throw new \Exception("Reschedule gagal: Data peminjaman aktif untuk modul {$normalizedSource} dengan ID {$sourceId} tidak ditemukan.");
            }

            // Dapatkan ID peminjaman asal yang sah (jadwal disetujui yang sedang digantikan)
            $rootBookingId = ($oldBooking->status === 'disetujui')
                ? $oldBooking->id
                : ($oldBooking->replaces_peminjaman_id ?: $oldBooking->id);

            // 2. Siapkan parameter baru (ambil dari payload baru, atau fallback ke data lama jika tidak diubah)
            $newRuanganId = $payload['ruangan_id'] ?? $oldBooking->ruangan_id;
            $newTanggal = isset($payload['tanggal_pinjam']) ? $payload['tanggal_pinjam'] : ($payload['tanggal'] ?? $oldBooking->tanggal_pinjam);
            $parsedDate = Carbon::parse($newTanggal);
            $formattedTanggal = $parsedDate->format('Y-m-d');
            $newJamMulai = substr($payload['jam_mulai'] ?? $oldBooking->jam_mulai, 0, 5);
            $newJamSelesai = substr($payload['jam_selesai'] ?? $oldBooking->jam_selesai, 0, 5);
            $userId = $payload['user_id'] ?? $oldBooking->user_id;
            $tujuan = $payload['tujuan'] ?? $payload['keperluan'] ?? $oldBooking->tujuan;

            // Resolusi Nomor Telepon yang cerdas (Graceful Fallback)
            $targetUser = \App\Models\User::find($userId);
            $nomorTelepon = $payload['nomor_telepon'] ?? $oldBooking->nomor_telepon ?? $targetUser?->whatsapp ?? null;

            // Jika ada nomor telepon baru dan user belum punya nomor WA, update profil user
            if (!empty($payload['nomor_telepon']) && $targetUser && empty($targetUser->whatsapp)) {
                $targetUser->update(['whatsapp' => $payload['nomor_telepon']]);
            }

            // 3. Validasi Urutan Jam
            if ($newJamSelesai <= $newJamMulai) {
                throw new \Exception('Reschedule gagal: Jam selesai harus lebih besar dari jam mulai.');
            }

            // 4. Pengecekan Sanksi / Blacklist
            $isBlacklisted = MrBlacklist::where('user_id', $userId)->first();
            if ($isBlacklisted) {
                $alasan = $isBlacklisted->alasan ? " (Alasan: {$isBlacklisted->alasan})" : '';
                throw new \Exception("Reschedule gagal: Akun pemohon sedang ditangguhkan dari layanan peminjaman ruangan{$alasan}.");
            }

            // 5. Validasi Operasional Kampus
            $settings = Pengaturan::pluck('value', 'key')->toArray();
            $jamBuka = substr($settings['jam_buka'] ?? '08:00', 0, 5);
            $jamTutup = substr($settings['jam_tutup'] ?? '16:00', 0, 5);
            $bukaAkhirPekan = filter_var($settings['buka_akhir_pekan'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $now = Carbon::now();
            if ($parsedDate->isPast() && !$parsedDate->isToday()) {
                throw new \Exception('Reschedule gagal: Tanggal baru yang dipilih sudah lewat.');
            }
            if ($parsedDate->isToday() && $newJamMulai < $now->format('H:i')) {
                throw new \Exception('Reschedule gagal: Jam mulai baru yang dipilih sudah lewat.');
            }
            if (!$bukaAkhirPekan && $parsedDate->isWeekend()) {
                throw new \Exception('Reschedule gagal: Ruangan kampus tutup pada akhir pekan (Sabtu/Minggu).');
            }
            $holiday = TanggalLibur::where('tanggal', $formattedTanggal)->first();
            if ($holiday) {
                throw new \Exception("Reschedule gagal: Tanggal {$formattedTanggal} adalah Hari Libur Nasional (" . ($holiday->keterangan ?: 'Tutup') . ').');
            }
            if ($newJamMulai < $jamBuka || $newJamSelesai > $jamTutup) {
                throw new \Exception("Reschedule gagal: Waktu peminjaman baru ({$newJamMulai} - {$newJamSelesai}) berada di luar jam operasional ruangan ({$jamBuka} - {$jamTutup}).");
            }

            // 6. Lock Ruangan Baru
            $ruangan = Ruangan::where('id', $newRuanganId)->lockForUpdate()->first();
            if (!$ruangan || !$ruangan->is_active) {
                throw new \Exception('Ruangan baru yang dipilih tidak ditemukan atau sedang tidak aktif.');
            }

            $dayOfWeekIso = (int) $parsedDate->format('N');

            // 7. Cek Bentrok dengan Jadwal Internal (Kuliah Rutin / Blokir)
            $bentrokInternal = MrJadwalInternal::where('ruangan_id', $newRuanganId)
                ->where(function ($q) use ($newJamMulai, $newJamSelesai) {
                    $q->where('jam_mulai', '<', $newJamSelesai)
                        ->where('jam_selesai', '>', $newJamMulai);
                })->where(function ($q) use ($dayOfWeekIso, $formattedTanggal) {
                    $q->where(function ($sub) use ($dayOfWeekIso, $formattedTanggal) {
                        $sub->where('tipe_jadwal', 'rutin')
                            ->where('hari', $dayOfWeekIso)
                            ->where(function ($eff) use ($formattedTanggal) {
                                $eff->whereNull('tgl_mulai_efektif')
                                    ->orWhere('tgl_mulai_efektif', '<=', $formattedTanggal);
                            })
                            ->where(function ($eff) use ($formattedTanggal) {
                                $eff->whereNull('tgl_selesai_efektif')
                                    ->orWhere('tgl_selesai_efektif', '>=', $formattedTanggal);
                            });
                    })->orWhere(function ($sub) use ($formattedTanggal) {
                        $sub->where('tipe_jadwal', 'spesifik')
                            ->where('tanggal_spesifik', $formattedTanggal);
                    });
                })->first();

            if ($bentrokInternal) {
                $namaBentrok = $bentrokInternal->kategori === 'Jadwal Akademik (Kuliah)'
                    ? ($bentrokInternal->mata_kuliah . ' (' . $bentrokInternal->kelas . ')')
                    : $bentrokInternal->keterangan;
                throw new \Exception("Ruangan {$ruangan->nama} tidak dapat dipesan pada jam tersebut karena terisi: {$namaBentrok}.");
            }

            // 8. IDEMPOTENSI: Batalkan draf pengajuan reschedule sebelumnya yang masih 'menunggu' untuk jadwal lama ini
            Peminjaman::where(function ($q) use ($rootBookingId, $oldBooking) {
                    $q->where('replaces_peminjaman_id', $rootBookingId)
                        ->orWhere('replaces_peminjaman_id', $oldBooking->id);
                })
                ->where('status', 'menunggu')
                ->update([
                    'status' => 'dibatalkan',
                    'alasan_penolakan' => 'Dibatalkan sistem: Digantikan oleh pengajuan reschedule terbaru',
                    'waktu_approval' => now(),
                ]);

            // 9. Cek Bentrok dengan Peminjaman Lain (KECUALIKAN diri sendiri: id != $oldBooking->id dan draf penggantinya)
            $bentrokPeminjaman = Peminjaman::where('ruangan_id', $newRuanganId)
                ->where('tanggal_pinjam', $formattedTanggal)
                ->where('id', '!=', $oldBooking->id)
                ->where('id', '!=', $rootBookingId)
                ->where(function ($q) use ($oldBooking, $rootBookingId) {
                    $q->whereNull('replaces_peminjaman_id')
                        ->orWhere(function ($sub) use ($oldBooking, $rootBookingId) {
                            $sub->where('replaces_peminjaman_id', '!=', $oldBooking->id)
                                ->where('replaces_peminjaman_id', '!=', $rootBookingId);
                        });
                })
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->where(function ($q) use ($newJamMulai, $newJamSelesai) {
                    $q->where('jam_mulai', '<', $newJamSelesai)
                        ->where('jam_selesai', '>', $newJamMulai);
                })->first();

            if ($bentrokPeminjaman) {
                $label = $bentrokPeminjaman->source_label ?: 'Peminjaman Lain';
                $statusText = $bentrokPeminjaman->status === 'menunggu'
                    ? 'sedang dalam proses pengajuan persetujuan'
                    : 'sudah dipesan resmi';
                throw new \Exception("Ruangan {$ruangan->nama} tidak dapat dipesan karena {$statusText} ({$label}: {$bentrokPeminjaman->tujuan}).");
            }

            // 10. TWO-PHASE SWAP: Buat record peminjaman baru berstatus 'menunggu' dengan relasi replaces_peminjaman_id
            // Peminjaman lama ($oldBooking) TETAP berstatus 'disetujui' agar slot lama tidak hilang jika pengajuan baru ditolak.
            // Peminjaman lama baru akan otomatis dibatalkan saat Admin Ruang menyetujui pengajuan baru ini.
            $newBooking = Peminjaman::create([
                'user_id' => $userId,
                'ruangan_id' => $newRuanganId,
                'nomor_telepon' => $nomorTelepon,
                'tujuan' => $tujuan,
                'tanggal_pinjam' => $formattedTanggal,
                'jam_mulai' => $newJamMulai,
                'jam_selesai' => $newJamSelesai,
                'status' => 'menunggu',
                'waktu_approval' => null,
                'source' => $normalizedSource,
                'source_id' => (string) $sourceId,
                'replaces_peminjaman_id' => $rootBookingId,
                'berkas_pendukung' => $payload['berkas_pendukung'] ?? $oldBooking->berkas_pendukung,
                'created_by' => auth()->id() ?? $userId,
            ]);

            // Kirim notifikasi update ke user
            $newBooking->load(['user', 'ruangan']);
            $newBooking->load('user');
            if ($newBooking->user) {
                $newBooking->user->notify(new \Modules\EOffice\Notifications\PeminjamanStatusUpdated($newBooking, false));
            }

            // Dispatch event bahwa permohonan reschedule diajukan
            event(new \Modules\EOffice\Events\RoomBookingStatusChanged($newBooking, 'menunggu', null, 'Pengajuan reschedule diajukan'));

            return $newBooking;
        });
    }

    /**
     * Mengambil data peminjaman aktif berdasarkan source dan source_id.
     */
    public function getBooking(string $source, $sourceId): ?Peminjaman
    {
        $normalizedSource = strtolower(trim((string) $source));
        if (in_array($normalizedSource, ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])) {
            $normalizedSource = 'kp';
        }

        $sourceQuery = ($normalizedSource === 'kp') ? ['kp', 'kerja_praktik', 'kerja praktik'] : [$normalizedSource];

        return Peminjaman::whereIn('source', $sourceQuery)
            ->where('source_id', (string) $sourceId)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->latest('id')
            ->first();
    }

    /**
     * Mengambil status peminjaman terkini ('menunggu', 'disetujui', null) untuk modul luar.
     */
    public function getBookingStatus(string $source, $sourceId): ?string
    {
        return $this->getBooking($source, $sourceId)?->status;
    }
}
