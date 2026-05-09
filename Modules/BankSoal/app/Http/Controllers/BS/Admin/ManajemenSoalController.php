<?php

namespace Modules\BankSoal\Http\Controllers\BS\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\Pertanyaan;
use Modules\BankSoal\Models\MataKuliah;

class ManajemenSoalController extends Controller
{
    public function index(Request $request)
    {
        $mataKuliahAll = MataKuliah::with(['cpl', 'pertanyaan.cpmk'])->get();

        $query = Pertanyaan::with(['mataKuliah', 'cpl'])
            ->join('bs_mata_kuliah', 'bs_pertanyaan.mk_id', '=', 'bs_mata_kuliah.id')
            ->leftJoin('bs_cpl', 'bs_pertanyaan.cpl_id', '=', 'bs_cpl.id')
            ->select('bs_pertanyaan.*');

        if ($request->searchSoal) {
            $search = $request->searchSoal;
            $query->where(function($q) use ($search) {
                $q->where('bs_pertanyaan.soal', 'like', "%{$search}%")
                  ->orWhere('bs_mata_kuliah.nama', 'like', "%{$search}%")
                  ->orWhere('bs_mata_kuliah.kode', 'like', "%{$search}%")
                  ->orWhere('bs_cpl.kode', 'like', "%{$search}%");
            });
        }

        if ($request->filterMK) {
            $query->where('bs_pertanyaan.mk_id', $request->filterMK);
        }

        if ($request->filterTipe) {
            $query->where('bs_pertanyaan.tipe_soal', $request->filterTipe);
        }

        if ($request->filterStatus) {
            $query->where('bs_pertanyaan.status', $request->filterStatus);
        }

        $soals = $query->orderBy('bs_pertanyaan.created_at', 'desc')->paginate(10);
        $soals->appends($request->query());

        $mataKuliahAll->transform(function ($mk) {
            $soalCpmks = $mk->pertanyaan ? $mk->pertanyaan->pluck('cpmk')->filter() : collect();
            $mk->all_cpmks = $soalCpmks->unique('id')->sortBy('kode')->values();
            return $mk;
        });

        return view('banksoal::pages.admin.kontrol-banksoal.soal', compact('soals', 'mataKuliahAll'));
    }

    public function ekstrak(Request $request)
    {
        $request->validate([
            'mk_id' => 'required',
            'jenis_soal' => 'nullable|array',
            'cpl_id' => 'nullable',
            'cpmk_id' => 'nullable',
            'bobot_total' => 'nullable|numeric'
        ]);

        $query = Pertanyaan::with(['mataKuliah', 'cpl', 'jawaban'])
            ->where('mk_id', $request->mk_id);

        if ($request->filled('jenis_soal')) {
            $tipe_soal_map = collect($request->jenis_soal)->map(function ($tipe) {
                return $tipe === 'Pilihan Ganda' ? 'pilihan_ganda' : 'essay';
            })->toArray();
            $query->whereIn('tipe_soal', $tipe_soal_map);
        }

        if ($request->filled('cpl_id')) {
            $query->where('cpl_id', $request->cpl_id);
        }

        $soals = $query->inRandomOrder()->get();

        if($soals->isEmpty()){
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada soal yang sesuai dengan kriteria ekstraksi.']);
            }
            return back()->with('error', 'Tidak ada soal yang sesuai dengan kriteria ekstraksi.');
        }

        $mataKuliah = MataKuliah::find($request->mk_id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'mataKuliah' => $mataKuliah,
                'soals' => $soals->map(function ($soal) {
                    return [
                        'id' => $soal->id,
                        'soal' => $soal->soal,
                        'cpl' => $soal->cpl ? $soal->cpl->kode : null,
                        'cpmk' => $soal->cpmk ? $soal->cpmk->kode : null,
                    ];
                }),
                'request' => $request->all()
            ]);
        }

        return view('banksoal::pages.admin.kontrol-banksoal.ekstrak-result', compact('soals', 'mataKuliah', 'request'));
    }

    public function cetakUjian(Request $request)
    {
        $request->validate([
            'soal_ids' => 'required|array',
            'mk_id' => 'required',
            'agenda' => 'nullable',
            'tahun_ajaran' => 'nullable',
            'semester' => 'nullable'
        ]);

        $soals = Pertanyaan::with(['cpl', 'cpmk', 'jawaban'])
            ->whereIn('id', $request->soal_ids)
            ->get();
            
        // Urutkan soal sesuai soal_ids
        $soals = $soals->sortBy(function($model) use ($request) {
            return array_search($model->id, $request->soal_ids);
        });

        $mataKuliah = MataKuliah::find($request->mk_id);

        return view('banksoal::pages.bank-soal.Dosen.print-ujian', compact('soals', 'mataKuliah', 'request'));
    }

    public function cetakSemua(Request $request)
    {
        $request->validate([
            'mk_id' => 'required',
        ]);

        $mataKuliah = MataKuliah::find($request->mk_id);
        $soals = Pertanyaan::with(['cpl', 'cpmk', 'jawaban'])
            ->where('mk_id', $request->mk_id)
            ->get();
            
        if ($soals->isEmpty()) {
            return back()->with('error', 'Tidak ada soal untuk dicetak pada Mata Kuliah ini.');
        }

        return view('banksoal::pages.bank-soal.Dosen.print-ujian', compact('soals', 'mataKuliah', 'request'));
    }
}
