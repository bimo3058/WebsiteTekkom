<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AktivasiSesiController extends Controller
{
    /**
     * Tampilkan halaman aktivitas sesi CBT Admin.
     */
    public function index(Request $request)
    {
        $periodes = \Modules\BankSoal\Models\PeriodeUjian::orderBy('tanggal_mulai', 'desc')->get();
        $selectedPeriodeId = $request->query('periode_id');

        $selectedPeriode = null;
        if ($selectedPeriodeId) {
            $selectedPeriode = $periodes->firstWhere('id', $selectedPeriodeId);
        } else {
            $selectedPeriode = $periodes->firstWhere('status', 'aktif') ?? $periodes->first();
            $selectedPeriodeId = $selectedPeriode?->id;
        }

        $jadwals = [];
        if ($selectedPeriode) {
            $jadwals = \Modules\BankSoal\Models\JadwalUjian::withCount('pendaftars')
                ->where('periode_ujian_id', $selectedPeriodeId)
                ->orderBy('tanggal_ujian', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->get();
        }

        return view('banksoal::aktivasi.index', compact('periodes', 'selectedPeriode', 'selectedPeriodeId', 'jadwals'));
    }

    /**
     * Ubah status dari sebuah JadwalUjian dan generate/hapus token secara otomatis.
     */
    public function toggle(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu_jadwal,aktif,selesai',
        ]);

        $jadwal = \Modules\BankSoal\Models\JadwalUjian::findOrFail($id);

        $token = null;
        if ($request->status === 'aktif') {
            $token = Str::upper(Str::random(6));
        }

        $jadwal->update([
            'status' => $request->status,
            'token'  => $token,
        ]);

        return redirect()->back()->with('success', 'Status Sesi "' . $jadwal->nama_sesi . '" berhasil diubah menjadi ' . strtoupper($request->status) . '.');
    }
}
