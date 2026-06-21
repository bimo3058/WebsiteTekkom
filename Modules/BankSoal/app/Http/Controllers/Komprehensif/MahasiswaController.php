<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\BankSoal\Http\Requests\Komprehensif\StorePendaftaranRequest;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Komprehensif\PendaftarUjian;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
use Modules\BankSoal\Services\Komprehensif\PendaftaranService;
use Modules\BankSoal\Support\SemesterCalculator;

class MahasiswaController extends Controller
{
    public function __construct(private PendaftaranService $pendaftaranService) {}

    public function dashboard()
    {
        $activePeriode   = PeriodeUjian::where('status', 'aktif')->latest()->first();
        $pendaftar       = null;
        $finishedSession = null;

        if ($activePeriode) {
            $pendaftar = PendaftarUjian::withTrashed()
                ->where('periode_ujian_id', $activePeriode->id)
                ->where('mahasiswa_id', auth()->id())
                ->first();

            if ($pendaftar && $pendaftar->jadwal) {
                $finishedSession = KompreSession::where('user_id', auth()->id())
                    ->where('jadwal_id', $pendaftar->jadwal->id)
                    ->where('status', 'finished')
                    ->first();

                // Auto-grade no-show: delegasikan logika ke service
                if (!$finishedSession) {
                    $finishedSession = $this->pendaftaranService->autoGradeNoShow(
                        $pendaftar->jadwal,
                        auth()->id()
                    );
                }
            }
        }

        $semester   = SemesterCalculator::fromCohortYear(auth()->user()->student?->cohort_year);
        $isEligible = $semester >= 7;

        $kuotaPenuh = $activePeriode && !$pendaftar
            ? $this->pendaftaranService->isKuotaPenuh($activePeriode)
            : false;

        return view('banksoal::mahasiswa.dashboard', compact(
            'activePeriode', 'pendaftar', 'semester', 'isEligible', 'finishedSession', 'kuotaPenuh'
        ));
    }

    public function createPendaftaran()
    {
        $activePeriode = $this->pendaftaranService->getActivePeriode();

        $semester = SemesterCalculator::fromCohortYear(auth()->user()->student?->cohort_year);
        if ($semester < 7) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')
                ->with('error', 'Akses ditolak: Anda belum memenuhi syarat minimal Semester 7.');
        }

        if (!$activePeriode || !$activePeriode->pendaftaran_terbuka) {
            $msg = 'Pendaftaran tidak tersedia saat ini.';
            if ($activePeriode && $activePeriode->pendaftaran_ditutup_paksa) {
                $msg = 'Pendaftaran telah ditutup oleh admin sebelum tanggal berakhir.';
            } elseif (!$activePeriode) {
                $msg = 'Tidak ada periode pendaftaran aktif.';
            }
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('error', $msg);
        }

        $pendaftar = PendaftarUjian::withTrashed()
            ->where('periode_ujian_id', $activePeriode->id)
            ->where('mahasiswa_id', auth()->id())
            ->first();

        if ($pendaftar) {
            $msg = $pendaftar->trashed()
                ? 'Pendaftaran Anda pada periode ini telah ditolak. Anda tidak dapat mendaftar ulang.'
                : 'Anda sudah terdaftar pada periode ini.';
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('info', $msg);
        }

        $dosens = User::role('dosen')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('banksoal::mahasiswa.pendaftaran-form', compact('activePeriode', 'dosens'));
    }

    public function storePendaftaran(StorePendaftaranRequest $request): RedirectResponse
    {
        $activePeriode = $this->pendaftaranService->getActivePeriode();

        if (!$activePeriode) {
            return redirect()->route('komprehensif.mahasiswa.dashboard');
        }

        $semester = SemesterCalculator::fromCohortYear(auth()->user()->student?->cohort_year);
        if ($semester < 7) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')
                ->with('error', 'Akses ditolak: Anda belum memenuhi syarat minimal Semester 7.');
        }

        if (!$activePeriode->pendaftaran_terbuka) {
            $msg = $activePeriode->pendaftaran_ditutup_paksa
                ? 'Aksi ditolak: Pendaftaran telah ditutup oleh admin.'
                : 'Aksi ditolak: Pendaftaran sedang ditutup.';
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('error', $msg);
        }

        try {
            $this->pendaftaranService->daftar($activePeriode, auth()->id(), $request->validated());
        } catch (\RuntimeException $e) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('komprehensif.mahasiswa.dashboard')
            ->with('success', 'Berhasil! Pengajuan pendaftaran telah sukses terkirim ke sistem program studi.');
    }

    public function riwayat(Request $request)
    {
        $query = KompreSession::where('user_id', auth()->id())
            ->where('status', 'finished')
            ->with(['jadwal.periode', 'jawabans']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('jadwal.periode', function($q) use ($search) {
                $q->where('nama_periode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sort_by') && $request->filled('sort_dir')) {
            $sortBy = $request->sort_by;
            $sortDir = strtolower($request->sort_dir) === 'asc' ? 'asc' : 'desc';
            
            if ($sortBy === 'score') {
                $query->orderBy('score', $sortDir);
            } else {
                $query->orderBy('finished_at', $sortDir);
            }
        } else {
            $query->orderBy('finished_at', 'desc');
        }

        $perPage = $request->input('per_page', 10);
        $sessions = $query->paginate($perPage);

        $allSessions = KompreSession::where('user_id', auth()->id())->where('status', 'finished')->get();
        $totalUjian     = $allSessions->count();
        $nilaiTertinggi = $allSessions->max('score') ?? 0;
        $nilaiRataRata  = $totalUjian ? round($allSessions->avg('score'), 1) : 0;
        $jumlahLulus    = $allSessions->where('score', '>=', 60)->count();

        return view('banksoal::mahasiswa.riwayat', compact(
            'sessions', 'totalUjian', 'nilaiTertinggi', 'nilaiRataRata', 'jumlahLulus'
        ));
    }

    /**
     * Detail hasil ujian mahasiswa per CPL.
     */
    public function detailRiwayat($id)
    {
        $session = KompreSession::with([
            'jadwal.periode',
            'jawabans.pertanyaan.jawaban',
            'jawabans.pertanyaan.cpl',
            'jawabans.opsiTerpilih',
        ])->findOrFail($id);

        // Pastikan mahasiswa hanya bisa melihat sesi ujian miliknya sendiri
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan melihat detail hasil ujian ini.');
        }

        // Kelompokkan jawaban per CPL, urutkan berdasarkan kode CPL
        $jawabansPerCpl = $session->jawabans
            ->sortBy('urutan_soal')
            ->groupBy(fn($j) => optional($j->pertanyaan?->cpl)->kode ?? 'Tanpa CPL')
            ->sortKeys();

        // Hitung statistik per CPL untuk ditampilkan di view
        $cplStats = $jawabansPerCpl->map(function ($jawabans, $kode) {
            $total  = $jawabans->count();
            $benar  = $jawabans->filter(fn($j) => $j->opsiTerpilih?->is_benar)->count();
            $salah  = $jawabans->filter(fn($j) => $j->jawaban_dipilih && !$j->opsiTerpilih?->is_benar)->count();

            return [
                'kode'      => $kode,
                'deskripsi' => $jawabans->first()->pertanyaan?->cpl?->deskripsi ?? '',
                'total'     => $total,
                'benar'     => $benar,
                'salah'     => $salah,
                'kosong'    => $total - $benar - $salah,
                'pct_benar' => $total > 0 ? round($benar / $total * 100) : 0,
            ];
        })->values();

        return view('banksoal::mahasiswa.detail-riwayat', compact('session', 'cplStats'));
    }
}
