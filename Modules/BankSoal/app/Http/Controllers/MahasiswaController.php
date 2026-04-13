<?php

namespace Modules\BankSoal\Http\Controllers;

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

        // Optional: Jika ingin mengambil dosen bisa dilakukan di sini.
        // Berdasarkan request: "untuk dropdown dosen gunakan yang dummy dulu", kita tidak perlu query.
        
        return view('banksoal::mahasiswa.pendaftaran-form', compact('activePeriode'));
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
            'semester' => 'required|integer|min:7',
            'target_wisuda' => 'required|string',
            // Kita abaikan dummy dropdown input di Request ini agar app tidak melempar constraint error
        ]);

        PendaftarUjian::create([
            'periode_ujian_id' => $activePeriode->id,
            'mahasiswa_id' => auth()->id(),
            'nim' => $request->nim,
            'nama_lengkap' => $request->nama,
            'semester_aktif' => $request->semester,
            'target_wisuda' => $request->target_wisuda,
            // Isi foreign id pembimbing ke null dulu supaya bisa insert meskipun menggunakan dummy input
            'dosen_pembimbing_1_id' => null, 
            'dosen_pembimbing_2_id' => null,
            'status_pendaftaran' => 'pending',
        ]);

        return redirect()->route('komprehensif.mahasiswa.pendaftaran')->with('success', 'Berhasil! Pengajuan pendaftaran telah sukses terkirim ke sistem program studi.');
    }
}
