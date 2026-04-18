<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\PeriodeUjian;
use Modules\BankSoal\Models\PendaftarUjian;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $activePeriode = PeriodeUjian::where('status', 'aktif')->latest()->first();
        $pendaftar = null;

        if ($activePeriode) {
            $pendaftar = PendaftarUjian::where('periode_ujian_id', $activePeriode->id)
                ->where('mahasiswa_id', auth()->id())
                ->first();
        }

        return view('banksoal::mahasiswa.dashboard', compact('activePeriode', 'pendaftar'));
    }

    public function pendaftaran()
    {
        $activePeriode = PeriodeUjian::where('status', 'aktif')->latest()->first();
        $pendaftar = null;

        if ($activePeriode) {
            $pendaftar = PendaftarUjian::where('periode_ujian_id', $activePeriode->id)
                ->where('mahasiswa_id', auth()->id())
                ->first();
        }

        return view('banksoal::mahasiswa.pendaftaran', compact('activePeriode', 'pendaftar'));
    }

    public function createPendaftaran()
    {
        $activePeriode = PeriodeUjian::where('status', 'aktif')->latest()->first();

        // Hindari jika tidak ada periode aktif (dipingpong kembali)
        if (!$activePeriode) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('error', 'Tidak ada periode pendaftaran aktif.');
        }

        // Hindari jika sudah mendaftar
        $pendaftar = PendaftarUjian::where('periode_ujian_id', $activePeriode->id)
            ->where('mahasiswa_id', auth()->id())
            ->first();

        if ($pendaftar) {
            return redirect()->route('komprehensif.mahasiswa.pendaftaran')->with('error', 'Anda sudah terdaftar pada periode ini.');
        }

        $dosens = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))->orderBy('name')->get(['id', 'name']);
        
        return view('banksoal::mahasiswa.pendaftaran-form', compact('activePeriode', 'dosens'));
    }

    public function storePendaftaran(Request $request)
    {
        $activePeriode = PeriodeUjian::where('status', 'aktif')->latest()->first();

        if (!$activePeriode) {
            return redirect()->route('komprehensif.mahasiswa.dashboard');
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

        return redirect()->route('komprehensif.mahasiswa.pendaftaran')->with('success', 'Berhasil! Pengajuan pendaftaran telah sukses terkirim ke sistem program studi.');
    }
}
