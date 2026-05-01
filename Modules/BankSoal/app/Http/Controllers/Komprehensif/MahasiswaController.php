<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\PeriodeUjian;
use Modules\BankSoal\Models\PendaftarUjian;

class MahasiswaController extends Controller
{
    private function getStudentSemester()
    {
        $student = auth()->user()->student;
        $semester = 1;
        if ($student && $student->cohort_year) {
            $currentYear = date('Y');
            $currentMonth = date('n');
            $yearsPassed = $currentYear - $student->cohort_year;
            $semester = ($yearsPassed * 2) + ($currentMonth >= 8 ? 1 : 0);
            if ($semester < 1) $semester = 1;
        }
        return $semester;
    }

    public function dashboard()
    {
        $activePeriode = PeriodeUjian::where('status', 'aktif')->latest()->first();
        $pendaftar = null;

        if ($activePeriode) {
            $pendaftar = PendaftarUjian::where('periode_ujian_id', $activePeriode->id)
                ->where('mahasiswa_id', auth()->id())
                ->first();
        }

        $semester = $this->getStudentSemester();
        $isEligible = $semester >= 7;

        return view('banksoal::mahasiswa.dashboard', compact('activePeriode', 'pendaftar', 'semester', 'isEligible'));
    }

    public function pendaftaran()
    {
        $activePeriode = PeriodeUjian::where('status', 'aktif')->latest()->first();
        $pendaftar = null;

        if ($activePeriode) {
            $pendaftar = PendaftarUjian::withTrashed()
                ->where('periode_ujian_id', $activePeriode->id)
                ->where('mahasiswa_id', auth()->id())
                ->first();
        }

        return view('banksoal::mahasiswa.pendaftaran', compact('activePeriode', 'pendaftar'));
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

        $dosens = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))->orderBy('name')->get(['id', 'name']);
        
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
}
