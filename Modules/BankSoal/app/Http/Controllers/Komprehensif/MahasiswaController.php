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

    public function riwayat()
    {
        $sessions = KompreSession::where('user_id', auth()->id())
            ->where('status', 'finished')
            ->with(['jadwal.periode', 'jawabans'])
            ->orderBy('finished_at', 'desc')
            ->get();

        $totalUjian     = $sessions->count();
        $nilaiTertinggi = $sessions->max('score') ?? 0;
        $nilaiRataRata  = $totalUjian ? round($sessions->avg('score'), 1) : 0;
        $jumlahLulus    = $sessions->where('score', '>=', 60)->count();

        return view('banksoal::mahasiswa.riwayat', compact(
            'sessions', 'totalUjian', 'nilaiTertinggi', 'nilaiRataRata', 'jumlahLulus'
        ));
    }
}
