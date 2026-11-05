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
        Peminjaman::autoExpirePending();
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
            'mata_kuliah' => 'nullable|string|max:255',
            'kode_mk' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:50',
            'sks' => 'nullable|integer',
            'kuota' => 'nullable|integer',
            'pengampu' => 'nullable|string|max:255'
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
                'mata_kuliah' => $request->mata_kuliah,
                'kode_mk' => $request->kode_mk,
                'kelas' => $request->kelas,
                'sks' => $request->sks,
                'kuota' => $request->kuota,
                'pengampu' => $request->pengampu
            ]);

            // Eksekusi "Hukum Mengalah": Batalkan peminjaman mahasiswa yang tertimpa secara tertib
            $overlappingBookings = Peminjaman::where('ruangan_id', $request->ruangan_id)
                ->where('tanggal_pinjam', $request->tanggal)
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->where(function ($q) use ($request) {
                    $q->where('jam_mulai', '<', $request->jam_selesai)
                        ->where('jam_selesai', '>', $request->jam_mulai);
                })
                ->get();

            $canceledCount = 0;
            foreach ($overlappingBookings as $b) {
                $b->update([
                    'status' => 'dibatalkan',
                    'alasan_penolakan' => 'Dibatalkan oleh Sistem: Ruangan diblokir untuk Jadwal Internal/Pemeliharaan (' . $request->keterangan . ')',
                    'waktu_approval' => now(),
                ]);

                if ($b->user) {
                    $b->user->notify(new \Modules\EOffice\Notifications\PeminjamanStatusUpdated($b, true));
                }
                $canceledCount++;
            }

            $msg = 'Jadwal Internal Express berhasil disimpan.';
            if ($canceledCount > 0) {
                $msg .= " Sebanyak {$canceledCount} peminjaman mahasiswa yang bertabrakan telah dibatalkan otomatis dan ternotifikasi.";
            }

            return redirect()->back()->with('success', $msg);
        } else {
            // Path B: Peminjaman Manual (Hard Collision Guard)
            if ($collisionData['conflict']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['Bentrok' => 'Gagal menyimpan peminjaman manual! Terdapat jadwal yang beririsan di ruangan dan waktu yang dipilih.']);
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
                'status' => 'disetujui',
                'waktu_approval' => now(),
                'created_by' => auth()->id()
            ]);

            // Kirim notifikasi persetujuan instan kepada user
            $targetUser->notify(new \Modules\EOffice\Notifications\PeminjamanStatusUpdated($peminjaman, false));

            return redirect()->back()->with('success', 'Booking Ekspres berhasil dimasukkan atas nama ' . $targetUser->name . ' dan otomatis Disetujui.');
        }
    }

    /**
     * Check collisions against routine/specific internal schedules and approved bookings.
     */
    public function getCollisions($ruanganId, $tanggal, $jamMulai, $jamSelesai)
    {
        if (!$ruanganId || !$tanggal || !$jamMulai || !$jamSelesai) {
            return ['conflict' => false, 'conflicts_list' => []];
        }

        $conflictsList = [];

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
                'nama' => $nama,
                'waktu' => $range,
            ];
        }

        // 2. Cek bentrok dengan Peminjaman lain yang telah disetujui
        $peminjamanConflicts = Peminjaman::with('user')
            ->where('ruangan_id', $ruanganId)
            ->where('tanggal_pinjam', $tanggal)
            ->where('status', 'disetujui')
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai);
            })
            ->orderBy('jam_mulai', 'asc')
            ->get();

        foreach ($peminjamanConflicts as $p) {
            $userPart = $p->user ? " (Oleh: {$p->user->name})" : "";
            $range = \Carbon\Carbon::parse($p->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($p->jam_selesai)->format('H:i') . ' WIB';
            $conflictsList[] = [
                'tipe' => 'Peminjaman',
                'nama' => ($p->tujuan ?: 'Peminjaman Ruangan') . $userPart,
                'waktu' => $range,
            ];
        }

        return [
            'conflict' => count($conflictsList) > 0,
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
