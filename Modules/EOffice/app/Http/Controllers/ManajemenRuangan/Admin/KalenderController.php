<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\MrJadwalInternal;
use Modules\EOffice\Models\Ruangan;
use Modules\EOffice\Models\TanggalLibur;
use Modules\EOffice\Models\Pengaturan;

class KalenderController extends Controller
{
    /**
     * Display the Global Calendar merging both Student and Internal Bookings.
     */
    public function index(Request $request)
    {
        $allRuangansDaftar = Ruangan::where('is_active', true)->orderBy('nama', 'asc')->get();
        $selectedRoomId = $request->get('ruangan_id');
        $selectedKategori = $request->get('kategori');
        $kategoriList = $allRuangansDaftar->pluck('kategori')->filter()->unique()->values();

        if ($selectedRoomId) {
            $ruangans = $allRuangansDaftar->where('id', $selectedRoomId)->values();
        } elseif ($selectedKategori && $selectedKategori !== 'Semua Kategori') {
            $ruangans = $allRuangansDaftar->where('kategori', $selectedKategori)->values();
        } else {
            $ruangans = $allRuangansDaftar;
        }

        $mode = $request->get('mode', 'week');
        $today = \Carbon\Carbon::today();

        $weekStart = $request->get('week_start')
            ? \Carbon\Carbon::parse($request->get('week_start'))
            : $today->copy();
        $weekEnd = $weekStart->copy()->addDays(6);

        $monthDate = $request->get('month')
            ? \Carbon\Carbon::parse($request->get('month') . '-01')
            : $today->copy()->startOfMonth();

        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd = $monthDate->copy()->endOfMonth();

        $bookingsRaw = Peminjaman::with(['user', 'ruangan'])
            ->whereBetween('tanggal_pinjam', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->get();

        $monthBookings = $mode === 'month'
            ? Peminjaman::whereBetween('tanggal_pinjam', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->selectRaw('tanggal_pinjam, count(*) as total')
                ->groupBy('tanggal_pinjam')
                ->pluck('total', 'tanggal_pinjam')
            : collect();

        $holidayStart = $mode === 'month' ? $monthStart->format('Y-m-d') : $weekStart->format('Y-m-d');
        $holidayEnd = $mode === 'month' ? $monthEnd->format('Y-m-d') : $weekEnd->format('Y-m-d');
        $holidays = TanggalLibur::whereBetween('tanggal', [$holidayStart, $holidayEnd])
            ->pluck('keterangan', 'tanggal')->toArray();
        $jamBuka = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';
        $bukaAkhirPekan = filter_var(Pengaturan::where('key', 'buka_akhir_pekan')->value('value') ?? false, FILTER_VALIDATE_BOOLEAN);

        $internalSchedules = MrJadwalInternal::with('ruangan')->get();

        return view('eoffice::manajemen-ruangan.admin.kalender.index', compact(
            'ruangans',
            'allRuangansDaftar',
            'selectedRoomId',
            'kategoriList',
            'bookingsRaw',
            'internalSchedules',
            'weekStart',
            'weekEnd',
            'mode',
            'today',
            'monthDate',
            'monthStart',
            'monthEnd',
            'monthBookings',
            'holidays',
            'jamBuka',
            'jamTutup',
            'bukaAkhirPekan'
        ));
    }

    /**
     * Jalur Tol: Express Booking Bypass 
     * Injects either a direct 'Disetujui' booking or a 'Jadwal Internal Spesifik' schedule.
     */
    public function expressBooking(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'tipe_aksi' => 'required|in:internal,dosen',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required|string',
            'nim' => 'required_if:tipe_aksi,dosen',
            'kategori' => 'required_if:tipe_aksi,internal|string',
        ]);

        $collisionData = $this->getCollisions($request->ruangan_id, $request->tanggal, $request->jam_mulai, $request->jam_selesai);

        if ($request->tipe_aksi === 'internal') {
            // Path A: Register an Ad-Hoc block (Maintenance / Internal Academic)
            MrJadwalInternal::create([
                'ruangan_id' => $request->ruangan_id,
                'tipe_jadwal' => 'spesifik',
                'kategori' => $request->kategori,
                'tanggal_spesifik' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'keterangan' => $request->keterangan,
            ]);

            // Eksekusi "Hukum Mengalah": Batalkan peminjaman mahasiswa yang tertimpa secara tertib
            $overlappingBookings = Peminjaman::with('ruangan')->where('ruangan_id', $request->ruangan_id)
                ->where('tanggal_pinjam', $request->tanggal)
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->where(function ($q) use ($request) {
                    $q->where('jam_mulai', '<', $request->jam_selesai)
                        ->where('jam_selesai', '>', $request->jam_mulai);
                })
                ->get();

            $canceledCount = 0;
            $kategori = $request->kategori ?: 'Pemeliharaan / Penutupan';
            foreach ($overlappingBookings as $b) {
                $prevStatus = $b->status;
                $isApprovedBefore = ($prevStatus === 'disetujui');
                $newStatus = $isApprovedBefore ? 'dibatalkan' : 'ditolak';

                $ruanganNama = $b->ruangan ? $b->ruangan->nama : 'Ruangan';
                $tglFormatted = \Carbon\Carbon::parse($b->tanggal_pinjam)->translatedFormat('d M Y');
                $jamMulai = substr($b->jam_mulai, 0, 5);
                $jamSelesai = $b->jam_selesai ? substr($b->jam_selesai, 0, 5) : null;
                $waktuFormatted = $jamSelesai ? "{$jamMulai} - {$jamSelesai} WIB" : "{$jamMulai} WIB";

                if ($isApprovedBefore) {
                    $alasan = "Dibatalkan oleh Admin: Terjadi perubahan alokasi ruangan mendesak untuk {$kategori}. Hak penggunaan ruangan pada jadwal ini dicabut. Silakan mengajukan jadwal pengganti atau hubungi Admin.";
                    $notifMsg = "Izin peminjaman {$ruanganNama} pada {$tglFormatted} pukul {$waktuFormatted} yang sebelumnya telah disetujui, terpaksa dibatalkan oleh Admin karena {$kategori}.";
                } else {
                    $alasan = "Ditolak oleh Admin: Ruangan dialokasikan untuk {$kategori}. Silakan mengajukan permohonan di ruangan atau waktu lain.";
                    $notifMsg = "Pengajuan peminjaman {$ruanganNama} pada {$tglFormatted} pukul {$waktuFormatted} telah ditolak oleh Admin karena {$kategori}.";
                }

                $b->update([
                    'status' => $newStatus,
                    'alasan_penolakan' => $alasan,
                    'waktu_approval' => now(),
                ]);

                if ($b->user) {
                    $b->user->notify(new \Modules\EOffice\Notifications\PeminjamanStatusUpdated($b, $isApprovedBefore, $notifMsg));
                }

                // Dispatch Event perubahan status peminjaman untuk modul luar (Praktikum, KP, Capstone)
                event(new \Modules\EOffice\Events\RoomBookingStatusChanged($b, $newStatus, $prevStatus, $alasan));

                $canceledCount++;
            }

            $msg = 'Jadwal Internal Express berhasil disimpan.';
            if ($canceledCount > 0) {
                $msg .= " Sebanyak {$canceledCount} peminjaman mahasiswa yang bertabrakan telah dibatalkan otomatis dan ternotifikasi.";
            }

            return redirect()->back()->with('success', $msg);
        } else {
            // Path B: Peminjaman Manual (Hard Collision Guard terhadap Kuliah/Pemeliharaan/Peminjaman Disetujui)
            if ($collisionData['has_hard_conflict'] ?? $collisionData['conflict']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['Bentrok' => 'Gagal menyimpan peminjaman manual! Terdapat jadwal perkuliahan, pemeliharaan, atau peminjaman yang sudah disetujui di ruangan dan waktu yang dipilih.']);
            }

            // Resolve User by NIM/NIP or Email first
            $targetUser = \App\Models\User::where('external_id', $request->nim)
                ->orWhere('email', $request->nim)
                ->first();

            if (!$targetUser) {
                return redirect()->back()->withInput()->with('error', 'Peminjam dengan identitas tersebut tidak ditemukan di sistem.');
            }

            // Interceptor: Cek status blacklist / Banned account
            $blacklist = \Modules\EOffice\Models\MrBlacklist::where('user_id', $targetUser->id)->first();
            if ($blacklist) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['Bentrok' => "Peminjam atas nama {$targetUser->name} ({$request->nim}) sedang dalam status Blacklist dan tidak dapat meminjam ruangan. Alasan: " . ($blacklist->alasan ?? 'Pelanggaran ketentuan')]);
            }

            $peminjaman = Peminjaman::create([
                'user_id' => $targetUser->id,
                'ruangan_id' => $request->ruangan_id,
                'tanggal_pinjam' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'tujuan' => $request->keterangan,
                'nomor_telepon' => $targetUser->whatsapp ?? null,
                'status' => 'disetujui',
                'waktu_approval' => now(),
                'created_by' => auth()->id()
            ]);

            // Kirim notifikasi persetujuan instan kepada user
            $targetUser->notify(new \Modules\EOffice\Notifications\PeminjamanStatusUpdated($peminjaman, false));

            // Dispatch Event perubahan status peminjaman untuk modul luar (Praktikum, KP, Capstone)
            event(new \Modules\EOffice\Events\RoomBookingStatusChanged($peminjaman, 'disetujui', null, 'Booking ekspres disetujui instan oleh Admin Ruang'));

            // AUTO-REJECT CASCADE: Tolak otomatis antrean 'menunggu' lain yang bertabrakan di ruangan & jam yang sama
            $collidingPendings = Peminjaman::with(['user', 'ruangan'])->where('ruangan_id', $peminjaman->ruangan_id)
                ->where('tanggal_pinjam', $peminjaman->tanggal_pinjam)
                ->where('status', 'menunggu')
                ->where('id', '!=', $peminjaman->id)
                ->where(function ($q) use ($peminjaman) {
                    $q->where('jam_mulai', '<', $peminjaman->jam_selesai)
                        ->where('jam_selesai', '>', $peminjaman->jam_mulai);
                })->get();

            $autoRejectedCount = 0;
            $kategoriPrioritas = 'kegiatan prioritas Admin/Dosen';
            foreach ($collidingPendings as $cp) {
                $ruanganNama = $cp->ruangan ? $cp->ruangan->nama : 'Ruangan';
                $tglFormatted = \Carbon\Carbon::parse($cp->tanggal_pinjam)->translatedFormat('d M Y');
                $jamMulai = substr($cp->jam_mulai, 0, 5);
                $jamSelesai = $cp->jam_selesai ? substr($cp->jam_selesai, 0, 5) : null;
                $waktuFormatted = $jamSelesai ? "{$jamMulai} - {$jamSelesai} WIB" : "{$jamMulai} WIB";

                $autoReason = "Ditolak oleh Admin: Ruangan dialokasikan untuk {$kategoriPrioritas}. Silakan mengajukan permohonan di ruangan atau waktu lain.";
                $notifMsg = "Pengajuan peminjaman {$ruanganNama} pada {$tglFormatted} pukul {$waktuFormatted} telah ditolak oleh Admin karena {$kategoriPrioritas}.";

                $cp->update([
                    'status' => 'ditolak',
                    'alasan_penolakan' => $autoReason,
                    'waktu_approval' => now(),
                ]);

                event(new \Modules\EOffice\Events\RoomBookingStatusChanged($cp, 'ditolak', 'menunggu', $autoReason));

                if ($cp->user) {
                    $cp->user->notify(new \Modules\EOffice\Notifications\PeminjamanStatusUpdated($cp, false, $notifMsg));
                }
                $autoRejectedCount++;
            }

            $successMsg = 'Booking Ekspres berhasil dimasukkan atas nama ' . $targetUser->name . ' dan otomatis Disetujui.';
            if ($autoRejectedCount > 0) {
                $successMsg .= " Sebanyak {$autoRejectedCount} permohonan mahasiswa berstatus menunggu yang bertabrakan telah ditolak otomatis.";
            }

            return redirect()->back()->with('success', $successMsg);
        }
    }

    /**
     * Check collisions against routine/specific internal schedules and approved bookings.
     */
    public function getCollisions($ruanganId, $tanggal, $jamMulai, $jamSelesai)
    {
        if (!$ruanganId || !$tanggal || !$jamMulai || !$jamSelesai) {
            return [
                'conflict' => false,
                'has_hard_conflict' => false,
                'has_pending_conflict' => false,
                'conflicts_list' => []
            ];
        }

        $conflictsList = [];
        $hasHardConflict = false;
        $hasPendingConflict = false;

        // 1. Cek bentrok dengan Jadwal Internal (Akademik & Pemeliharaan / Blokir)
        $dayOfWeek = \Carbon\Carbon::parse($tanggal)->format('N');
        $isHoliday = TanggalLibur::where('tanggal', $tanggal)->exists();

        $internalConflicts = MrJadwalInternal::where('ruangan_id', $ruanganId)
            ->where(function ($query) use ($tanggal, $dayOfWeek, $isHoliday) {
                if (!$isHoliday) {
                    $query->where(function ($q) use ($dayOfWeek, $tanggal) {
                        $q->where('tipe_jadwal', 'rutin')
                            ->where('hari', $dayOfWeek)
                            ->where(function ($tq) use ($tanggal) {
                                $tq->whereNull('tgl_mulai_efektif')
                                    ->orWhere('tgl_mulai_efektif', '<=', $tanggal);
                            })
                            ->where(function ($tq) use ($tanggal) {
                                $tq->whereNull('tgl_selesai_efektif')
                                    ->orWhere('tgl_selesai_efektif', '>=', $tanggal);
                            });
                    })->orWhere(function ($q) use ($tanggal) {
                        $q->where('tipe_jadwal', 'spesifik')->where('tanggal_spesifik', $tanggal);
                    });
                } else {
                    // Pada hari libur nasional, kuliah rutin ditiadakan; hanya periksa blokir spesifik
                    $query->where('tipe_jadwal', 'spesifik')->where('tanggal_spesifik', $tanggal);
                }
            })
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai);
            })
            ->orderBy('jam_mulai', 'asc')
            ->get();

        foreach ($internalConflicts as $c) {
            $range = \Carbon\Carbon::parse($c->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($c->jam_selesai)->format('H:i') . ' WIB';
            $isAkademik = ($c->kategori === 'Jadwal Akademik (Kuliah)' || !empty($c->mata_kuliah));
            $tipe = $isAkademik ? 'Akademik' : 'Pemeliharaan';

            if ($isAkademik) {
                $nama = $c->mata_kuliah ? ($c->mata_kuliah . ($c->kelas ? ' - Kelas ' . $c->kelas : '')) : ($c->keterangan ?: 'Jadwal Perkuliahan');
            } else {
                $nama = $c->keterangan ?: 'Pemeliharaan / Blokir Ruangan';
            }

            $conflictsList[] = [
                'tipe' => $tipe,
                'status' => 'internal',
                'nama' => $nama,
                'waktu' => $range,
            ];
            $hasHardConflict = true;
        }

        // 2. Cek bentrok dengan Peminjaman (Disetujui maupun Antrean Menunggu)
        $peminjamanConflicts = Peminjaman::with('user')
            ->where('ruangan_id', $ruanganId)
            ->where('tanggal_pinjam', $tanggal)
            ->whereIn('status', ['disetujui', 'menunggu'])
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai);
            })
            ->orderBy('jam_mulai', 'asc')
            ->get();

        foreach ($peminjamanConflicts as $p) {
            $userPart = $p->user ? " (Oleh: {$p->user->name})" : "";
            $range = \Carbon\Carbon::parse($p->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($p->jam_selesai)->format('H:i') . ' WIB';
            $isPending = ($p->status === 'menunggu');

            if ($isPending) {
                $hasPendingConflict = true;
            } else {
                $hasHardConflict = true;
            }

            $conflictsList[] = [
                'tipe' => 'Peminjaman' . ($isPending ? ' (Menunggu)' : ' (Disetujui)'),
                'status' => $p->status,
                'nama' => ($p->tujuan ?: 'Peminjaman Ruangan') . $userPart,
                'waktu' => $range,
            ];
        }

        return [
            'conflict' => count($conflictsList) > 0,
            'has_hard_conflict' => $hasHardConflict,
            'has_pending_conflict' => $hasPendingConflict,
            'conflicts_list' => $conflictsList,
        ];
    }

    /**
     * API Endpoint for Realtime AJAX Collision Check on Global Calendar.
     */
    public function checkCollision(Request $request)
    {
        $ruanganId = $request->query('ruangan_id');
        $tanggal = $request->query('tanggal');
        $jamMulai = $request->query('jam_mulai');
        $jamSelesai = $request->query('jam_selesai');

        $result = $this->getCollisions($ruanganId, $tanggal, $jamMulai, $jamSelesai);
        return response()->json($result);
    }

    /**
     * API Endpoint for Autocomplete JSON search.
     */
    public function searchUsers(Request $request)
    {
        $query = $request->query('q');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $searchTerm = '%' . strtolower($query) . '%';
        $blacklistedUserIds = \Modules\EOffice\Models\MrBlacklist::pluck('user_id')->toArray();

        $users = \App\Models\User::where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(external_id) LIKE ?', [$searchTerm]);
            })
            ->take(5)
            ->get(['id', 'name', 'email', 'external_id'])
            ->map(function ($u) use ($blacklistedUserIds) {
                $u->is_blacklisted = in_array($u->id, $blacklistedUserIds);
                return $u;
            });

        return response()->json($users);
    }
}
