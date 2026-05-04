<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\PeriodeUjian;
use Modules\BankSoal\Models\JadwalUjian;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $periodes = PeriodeUjian::orderBy('created_at', 'desc')->get();
        $selectedPeriodeId = $request->periode_id ?? ($periodes->first()->id ?? null);
        
        $jadwals = collect();
        $selectedPeriode = null;

        if ($selectedPeriodeId) {
            $jadwals = JadwalUjian::where('periode_ujian_id', $selectedPeriodeId)
                ->orderBy('waktu_mulai')
                ->get();
            $selectedPeriode = PeriodeUjian::find($selectedPeriodeId);
        }

        return view('banksoal::jadwal.index', compact('periodes', 'selectedPeriodeId', 'jadwals', 'selectedPeriode'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'periode_ujian_id' => 'required|exists:bs_periode_ujians,id',
            'nama_sesi' => [
                'required',
                'numeric',
                'min:1',
                \Illuminate\Validation\Rule::unique('bs_jadwal_ujians', 'nama_sesi')->where(function ($query) use ($request) {
                    return $query->where('periode_ujian_id', $request->periode_ujian_id)
                                 ->where('tanggal_ujian', $request->tanggal_ujian)
                                 ->whereNull('deleted_at');
                })
            ],
            'tanggal_ujian' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'kuota' => 'required|integer|min:1',
            'ruangan' => 'nullable|string|max:255',
        ], [
            'nama_sesi.unique' => 'Sesi ke-' . $request->nama_sesi . ' sudah ada pada tanggal ini.',
        ]);

        JadwalUjian::create($validatedData);

        return redirect()->route('banksoal.pendaftaran.alokasi-sesi.index', ['periode_id' => $request->periode_ujian_id])
            ->with('success', 'Sesi ujian berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalUjian::findOrFail($id);
        $periodeId = $jadwal->periode_ujian_id;

        // Reset status mahasiswa yang sebelumnya dialokasikan ke sesi ini
        $jadwal->pendaftars()->update(['jadwal_ujian_id' => null]);

        $jadwal->delete();

        return redirect()->route('banksoal.pendaftaran.alokasi-sesi.index', ['periode_id' => $periodeId])
            ->with('success', 'Sesi ujian berhasil dihapus dan peserta telah dikembalikan ke daftar belum dialokasi.');
    }
}
