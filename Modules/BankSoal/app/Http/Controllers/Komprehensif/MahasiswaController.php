<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Komprehensif\PendaftarUjian;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;

class MahasiswaController extends Controller
{
    private function getStudentSemester()
    {
        $student = auth()->user()->student;
        $semester = 1;

        if ($student && $student->cohort_year) {
            $currentYear  = (int) date('Y');
            $currentMonth = (int) date('n');

            // Kalender akademik Indonesia:
            //   Semester Ganjil  : Agustus/September  → Januari
            //   Semester Genap   : Februari/Maret     → Juli
            //
            // Cara hitung semester saat ini:
            //   1 Jan – 31 Jul  → masih semester genap dari tahun ajaran yang sama
            //   1 Agt – 31 Des  → masuk semester ganjil tahun ajaran baru
            //
            // Contoh angkatan 2023, Mei 2026 (bulan 5):
            //   effectiveYear = 2026 - 1 = 2025  (belum melewati Agustus)
            //   academicYears = 2025 - 2023 = 2   (sudah melewati 2 tahun ajaran penuh)
            //   baseSemester  = 2 * 2 = 4
            //   semester genap aktif → +2  → total = 6  [s6 = Feb-Jul 2026] ← SALAH
            //
            // Perbaikan: hitung semester AKTIF yang sedang dijalani, bukan yang sudah lewat.
            // Setiap tahun ajaran = 2 semester. Angkatan masuk semester 1 di Agustus.
            // Total semester aktif = (tahun berjalan × 2) + offset bulan.

            if ($currentMonth >= 8) {
                // Semester ganjil: Ags–Jan
                // Tahun ajaran baru sudah mulai
                $academicYears = $currentYear - $student->cohort_year;
                $semester = ($academicYears * 2) + 1;
            } else {
                // Semester genap: Feb–Jul
                // Masih dalam tahun ajaran yang dimulai tahun sebelumnya
                $academicYears = ($currentYear - 1) - $student->cohort_year;
                $semester = ($academicYears * 2) + 2;
            }

            if ($semester < 1) $semester = 1;
        }

        return $semester;
    }

    public function dashboard()
    {
        $activePeriode = PeriodeUjian::where('status', 'aktif')->latest()->first();
        $pendaftar = null;
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

                // Auto-grade no-show: jika sesi sudah habis & mahasiswa tidak pernah masuk ujian
                if (! $finishedSession) {
                    $jadwal = $pendaftar->jadwal;
                    if ($jadwal->tanggal_ujian && $jadwal->waktu_selesai) {
                        $waktuSelesai = Carbon::parse(
                            $jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_selesai
                        );

                        $sudahPunyaSesi = KompreSession::where('user_id', auth()->id())
                            ->where('jadwal_id', $jadwal->id)
                            ->exists();

                        if (now()->gte($waktuSelesai) && ! $sudahPunyaSesi) {
                            $waktuMulaiSesi = Carbon::parse(
                                $jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_mulai
                            );

                            $finishedSession = KompreSession::create([
                                'user_id'     => auth()->id(),
                                'jadwal_id'   => $jadwal->id,
                                'title'       => 'Tidak Mengerjakan',
                                'status'      => KompreSessionStatus::Finished,
                                'score'       => 0,
                                'started_at'  => $waktuMulaiSesi,
                                'finished_at' => $waktuSelesai,
                            ]);
                        }
                    }
                }
            }
        }

        $semester = $this->getStudentSemester();
        $isEligible = $semester >= 7;

        // Cek apakah kuota pendaftaran periode sudah penuh
        $kuotaPenuh = false;
        if ($activePeriode && $activePeriode->kuota_peserta && !$pendaftar) {
            $jumlahPendaftar = PendaftarUjian::where('periode_ujian_id', $activePeriode->id)->count();
            $kuotaPenuh = $jumlahPendaftar >= $activePeriode->kuota_peserta;
        }

        return view('banksoal::mahasiswa.dashboard', compact('activePeriode', 'pendaftar', 'semester', 'isEligible', 'finishedSession', 'kuotaPenuh'));
    }

    public function createPendaftaran()
    {
        // Gunakan scope date-driven; auto-update draft→aktif jika mahasiswa akses sebelum admin refresh
        $activePeriode = PeriodeUjian::currentlyActive()->latest()->first();
        if ($activePeriode && $activePeriode->status === 'draft') {
            $activePeriode->update(['status' => 'aktif']);
            $activePeriode->refresh();
        }

        // Cek eligibility semester minimal
        $semester = $this->getStudentSemester();
        if ($semester < 7) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('error', 'Akses ditolak: Anda belum memenuhi syarat minimal Semester 7.');
        }

        // Satu gate terpadu: periode aktif, belum ditutup paksa, dan masih dalam rentang tanggal
        if (!$activePeriode || !$activePeriode->pendaftaran_terbuka) {
            $msg = 'Pendaftaran tidak tersedia saat ini.';
            if ($activePeriode && $activePeriode->pendaftaran_ditutup_paksa) {
                $msg = 'Pendaftaran telah ditutup oleh admin sebelum tanggal berakhir.';
            } elseif (!$activePeriode) {
                $msg = 'Tidak ada periode pendaftaran aktif.';
            }
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('error', $msg);
        }

        // Hindari jika sudah mendaftar (termasuk yang pernah ditolak / soft-deleted)
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

        $dosens = User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))->orderBy('name')->get(['id', 'name']);
        
        return view('banksoal::mahasiswa.pendaftaran-form', compact('activePeriode', 'dosens'));
    }

    public function storePendaftaran(Request $request)
    {
        $activePeriode = PeriodeUjian::currentlyActive()->latest()->first();
        if ($activePeriode && $activePeriode->status === 'draft') {
            $activePeriode->update(['status' => 'aktif']);
            $activePeriode->refresh();
        }

        if (!$activePeriode) {
            return redirect()->route('komprehensif.mahasiswa.dashboard');
        }

        $semester = $this->getStudentSemester();
        if ($semester < 7) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('error', 'Akses ditolak: Anda belum memenuhi syarat minimal Semester 7.');
        }

        // Gate terpadu — konsisten dengan createPendaftaran()
        if (!$activePeriode->pendaftaran_terbuka) {
            $msg = $activePeriode->pendaftaran_ditutup_paksa
                ? 'Aksi ditolak: Pendaftaran telah ditutup oleh admin.'
                : 'Aksi ditolak: Pendaftaran sedang ditutup.';
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('error', $msg);
        }

        $request->validate([
            'nim' => 'required|string',
            'nama' => 'required|string',
            'kontak_wa' => 'required|string|max:20',
            'semester' => 'required|integer|min:7',
            'target_wisuda' => 'required|string',
            'dosen_pembimbing_1_id' => 'required|exists:users,id',
            'dosen_pembimbing_2_id' => 'nullable|exists:users,id|different:dosen_pembimbing_1_id',
        ], [
            'dosen_pembimbing_2_id.different' => 'Dosen Pembimbing 2 tidak boleh sama dengan Dosen Pembimbing 1',
            'semester.min' => 'Mahasiswa minimal semester 7'
        ]);

        PendaftarUjian::create([
            'periode_ujian_id' => $activePeriode->id,
            'mahasiswa_id' => auth()->id(),
            'nim' => $request->nim,
            'nama_lengkap' => $request->nama,
            'kontak_wa' => $request->kontak_wa,
            'semester_aktif' => $request->semester,
            'target_wisuda' => $request->target_wisuda,
            'dosen_pembimbing_1_id' => $request->dosen_pembimbing_1_id, 
            'dosen_pembimbing_2_id' => $request->dosen_pembimbing_2_id,
            'status_pendaftaran' => 'pending',
        ]);

        return redirect()->route('komprehensif.mahasiswa.dashboard')->with('success', 'Berhasil! Pengajuan pendaftaran telah sukses terkirim ke sistem program studi.');
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
