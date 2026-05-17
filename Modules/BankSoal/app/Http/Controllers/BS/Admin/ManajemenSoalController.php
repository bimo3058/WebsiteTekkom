<?php

namespace Modules\BankSoal\Http\Controllers\BS\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\PenarikanSoal;
use Modules\BankSoal\Models\MataKuliah;
use Modules\BankSoal\Models\Pertanyaan;

class ManajemenSoalController extends Controller
{
    public function index(Request $request)
    {
        $query = PenarikanSoal::with(['mataKuliah', 'dosen'])
            ->where('metode_ujian', 'offline')
            ->orderByRaw("CASE status_cetak WHEN 'pending' THEN 1 WHEN 'diproses' THEN 2 WHEN 'selesai' THEN 3 ELSE 4 END ASC")
            ->orderBy('created_at', 'desc');

        if ($request->searchSoal) {
            $search = $request->searchSoal;
            $query->where(function($q) use ($search) {
                $q->where('nama_ekstraksi', 'like', "%{$search}%")
                  ->orWhereHas('mataKuliah', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filterStatus) {
            $query->where('status_cetak', $request->filterStatus);
        }

        $antreanCetak = $query->paginate(10);
        $antreanCetak->appends($request->query());

        return view('banksoal::pages.admin.kontrol-banksoal.soal', compact('antreanCetak'));
    }

    public function cetakDokumen(Request $request, $id)
    {
        $penarikan = PenarikanSoal::where('metode_ujian', 'offline')->findOrFail($id);
        
        // Ubah status ke diproses jika masih pending
        if ($penarikan->status_cetak === 'pending') {
            $penarikan->update(['status_cetak' => 'diproses']);
        }

        $soalArray = $penarikan->getSoalArray();
        $soalIds = collect($soalArray)->pluck('id')->toArray();

        $soals = collect();
        if (!empty($soalIds)) {
            $soals = Pertanyaan::with(['cpl', 'cpmk', 'jawaban'])
                ->whereIn('id', $soalIds)
                ->get()
                ->sortBy(function($model) use ($soalIds) {
                    return array_search($model->id, $soalIds);
                })
                ->values();
        }

        $mataKuliah = MataKuliah::find($penarikan->mk_id);
        $request->merge([
            'agenda' => explode(' - ', $penarikan->nama_ekstraksi)[1] ?? 'Ujian',
            'tahun_ajaran' => $penarikan->tahun_akademik,
            'semester' => $penarikan->semester
        ]);

        return view('banksoal::pages.bank-soal.Dosen.print-ujian', compact('soals', 'mataKuliah', 'request'));
    }

    public function tandaiSelesai(Request $request, $id)
    {
        $penarikan = PenarikanSoal::where('metode_ujian', 'offline')->findOrFail($id);
        $penarikan->update(['status_cetak' => 'selesai']);

        return back()->with('success', 'Status cetakan ujian telah ditandai Selesai.');
    }
}
