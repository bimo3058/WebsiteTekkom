<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\BankSoal\Models\Komprehensif\KompreJawaban;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
use Modules\BankSoal\Services\CbtSessionService;

class AdminCbtController extends Controller
{
    public function __construct(private CbtSessionService $cbtService) {}
    /**
     * Fitur Live Proctoring untuk memantau mahasiswa yang sedang ujian.
     */
    public function liveProctoring()
    {
        $this->authorize('viewAny', KompreSession::class);

        $sessions = KompreSession::with('user')
            ->withCount([
                // Total soal dalam sesi
                'jawabans',
                // Soal yang sudah dijawab (tidak null)
                'jawabans as terjawab_count' => fn($q) => $q->whereNotNull('jawaban_dipilih'),
                // Jumlah pelanggaran
                'cheatLogs',
            ])
            ->where('status', 'ongoing')
            ->orderBy('started_at', 'desc')
            ->get();

        return view('banksoal::admin.cbt.live-proctoring', compact('sessions'));
    }

    /**
     * Laporan Riwayat Hasil Ujian yang sudah selesai.
     */
    public function riwayat(Request $request)
    {
        $this->authorize('viewAny', KompreSession::class);

        // Subquery correlated untuk menghitung "Ujian Ke-" per mahasiswa
        // Compatible dengan MySQL 5.7+ (tanpa window function)
        $ujianKeSubquery = "
            (SELECT COUNT(*) FROM bs_kompre_session s2
             WHERE s2.user_id = bs_kompre_session.user_id
               AND s2.status = 'finished'
               AND (s2.finished_at < bs_kompre_session.finished_at
                    OR (s2.finished_at = bs_kompre_session.finished_at AND s2.id <= bs_kompre_session.id))
            ) AS ujian_ke
        ";

        $query = KompreSession::with(['user.student', 'jadwal.periode'])
            ->selectRaw("bs_kompre_session.*, {$ujianKeSubquery}")
            ->where('bs_kompre_session.status', 'finished');

        // Filter periode
        if ($request->filled('periode_id')) {
            $query->whereHas('jadwal', fn($q) => $q->where('periode_id', $request->periode_id));
        }

        // Filter keterangan (LULUS / MENGULANG)
        if ($request->filled('keterangan')) {
            if ($request->keterangan === 'lulus') {
                $query->where('bs_kompre_session.score', '>=', 60);
            } elseif ($request->keterangan === 'mengulang') {
                $query->where('bs_kompre_session.score', '<', 60);
            }
        }

        // Pencarian NIM atau Nama Mahasiswa
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('user', function ($subQ) use ($q) {
                $subQ->where('name', 'like', "%{$q}%")
                    ->orWhereHas('student', fn($sq) => $sq->where('student_number', 'like', "%{$q}%"));
            });
        }

        $perPage = $request->input('per_page', 5);
        $sessions = $query->orderBy('bs_kompre_session.finished_at', 'desc')->paginate($perPage)->withQueryString();
        $periodes  = PeriodeUjian::orderBy('created_at', 'desc')->get();

        return view('banksoal::admin.cbt.riwayat', compact('sessions', 'periodes'));
    }

    /**
     * Detail hasil jawaban per sesi mahasiswa.
     */
    public function detailHasil($id)
    {
        $session = KompreSession::with([
            'user.student',
            'jadwal',
            'jawabans.pertanyaan.jawabans',
            'jawabans.pertanyaan.cpl',
            'jawabans.opsiTerpilih',
        ])->findOrFail($id);

        $this->authorize('view', $session);

        return view('banksoal::admin.cbt.detail-hasil', compact('session'));
    }

    /**
     * Aksi untuk Force Submit secara sepihak oleh admin jika perlu.
     */
    public function forceSubmit($id)
    {
        $session = KompreSession::findOrFail($id);

        // Policy memverifikasi role admin DAN status === 'ongoing' sekaligus.
        // Jika sesi sudah selesai, authorize() akan throw AuthorizationException (403).
        $this->authorize('forceSubmit', $session);

        $this->cbtService->finishSession($session);

        return back()->with('success', 'Sesi ujian berhasil diakhiri paksa. Skor: ' . $session->fresh()->score);
    }

    public function analytics()
    {
        $this->authorize('viewAny', KompreSession::class);

        // 1. Matriks Umum — query agregasi langsung dari DB, tidak memuat ke RAM
        $stats = KompreSession::where('status', 'finished')
            ->selectRaw('COUNT(*) as total, AVG(score) as rata_rata, MAX(score) as tertinggi, MIN(score) as terendah, SUM(CASE WHEN score >= 60 THEN 1 ELSE 0 END) as lulus')
            ->first();

        $totalPeserta = (int) $stats->total;
        $rataRata     = $totalPeserta > 0 ? round($stats->rata_rata, 2) : 0;
        $tertinggi    = $totalPeserta > 0 ? $stats->tertinggi : 0;
        $terendah     = $totalPeserta > 0 ? $stats->terendah : 0;
        $lulus        = (int) $stats->lulus;
        $tidakLulus   = $totalPeserta - $lulus;

        // 2. Soal Tersulit — agregasi di DB, bukan di PHP collection
        $kesalahanPerSoal = KompreJawaban::whereHas('session', fn($q) => $q->where('status', 'finished'))
            ->whereDoesntHave('opsiTerpilih', fn($q) => $q->where('is_benar', true))
            ->with('pertanyaan.cpl')  // eager-load cpl agar view tidak lazy-load
            ->selectRaw('pertanyaan_id, COUNT(*) as salah_count')
            ->groupBy('pertanyaan_id')
            ->orderByDesc('salah_count')
            ->limit(10)
            ->get()
            ->map(fn($row) => [
                'pertanyaan'  => $row->pertanyaan,
                'salah_count' => $row->salah_count,
            ]);

        // 3. Pemetaan Capaian CPL — agregasi di DB
        $cplStats = KompreJawaban::whereHas('session', fn($q) => $q->where('status', 'finished'))
            ->join('bs_pertanyaan', 'bs_kompre_jawaban.pertanyaan_id', '=', 'bs_pertanyaan.id')
            ->join('bs_cpl', 'bs_pertanyaan.cpl_id', '=', 'bs_cpl.id')
            ->leftJoin('bs_jawaban', 'bs_kompre_jawaban.jawaban_dipilih', '=', 'bs_jawaban.id')
            ->selectRaw('bs_cpl.id, bs_cpl.kode as cpl_kode, bs_cpl.deskripsi, COUNT(*) as total, SUM(CASE WHEN bs_jawaban.is_benar = true THEN 1 ELSE 0 END) as benar')
            ->groupBy('bs_cpl.id', 'bs_cpl.kode', 'bs_cpl.deskripsi')
            ->orderBy('bs_cpl.kode')
            ->get()
            ->map(fn($row) => [
                'cpl_kode'   => $row->cpl_kode,
                'deskripsi'  => $row->deskripsi,
                'persentase' => $row->total > 0 ? round(($row->benar / $row->total) * 100, 2) : 0,
            ]);

        return view('banksoal::admin.cbt.analitik', compact(
            'totalPeserta', 'rataRata', 'tertinggi', 'terendah',
            'lulus', 'tidakLulus', 'kesalahanPerSoal', 'cplStats'
        ));
    }

    /**
     * Reset semua data ujian komprehensif (Periode, Jadwal, Pendaftar, Sesi, Jawaban, Log).
     * Memerlukan konfirmasi password admin.
     */
    public function resetSemua(Request $request)
    {
        // Hanya admin_banksoal (super admin modul) yang boleh reset.
        // Dibedakan dari 'admin' biasa karena operasi ini bersifat destruktif.
        $this->authorize('resetAll', KompreSession::class);

        $request->validate([
            'konfirmasi_password' => 'required|string',
        ]);

        // Verifikasi password admin yang sedang login
        if (! Hash::check($request->konfirmasi_password, auth()->user()->password)) {
            return redirect()->back()->withErrors([
                'konfirmasi_password' => 'Password yang Anda masukkan salah. Reset dibatalkan.',
            ])->withFragment('reset-section');
        }

        // Pastikan tidak ada sesi ujian yang sedang berlangsung
        $ongoingSessions = KompreSession::where('status', 'ongoing')->count();
        if ($ongoingSessions > 0) {
            return redirect()->back()->with(
                'error',
                "Tidak dapat mereset! Masih ada {$ongoingSessions} sesi ujian yang sedang berlangsung. Tunggu hingga semua selesai atau gunakan Force Submit terlebih dahulu."
            )->withFragment('reset-section');
        }

        // Hapus urut dari tabel turunan ke tabel induk
        DB::table('bs_cheat_logs')->delete();
        DB::table('bs_kompre_jawaban')->delete();
        DB::table('bs_kompre_session')->delete();
        DB::table('bs_pendaftar_ujians')->delete();
        DB::table('bs_jadwal_ujians')->delete();
        DB::table('bs_periode_ujians')->delete();

        return redirect()->route('banksoal.admin.cbt.riwayat')
            ->with('success', '✅ Semua data ujian komprehensif berhasil direset. Sistem siap digunakan dari awal.');
    }
}
