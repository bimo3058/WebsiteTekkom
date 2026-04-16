<?php

namespace Modules\BankSoal\Http\Controllers\BS\GPM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Services\ValidasiBankSoalService;

class ValidasiBankSoalController extends Controller
{
    protected $validasiService;

    public function __construct(ValidasiBankSoalService $validasiService)
    {
        $this->validasiService = $validasiService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $all_paket_soal = $this->validasiService->getDaftarAntreanMataKuliah();
        $paket_soal = $search ? $this->validasiService->getDaftarAntreanMataKuliah($search) : $all_paket_soal;
        $counts = $this->validasiService->getCounts();
        return view('banksoal::gpm.validasi-bank-soal', compact('paket_soal', 'counts', 'all_paket_soal'));
    }

    public function review(Request $request)
    {
        $mkId = $request->query('mk_id');
        $soal = $this->validasiService->getSoalReview($mkId);

        if (!$soal) {
            return redirect()->route('banksoal.soal.gpm.validasi-bank-soal')->with('success', 'Mantap! Semua soal pada mata kuliah ini telah selesai divalidasi.');
        }

        $opsi_jawaban = $this->validasiService->getOpsiJawaban($soal->id);
        $review = \Illuminate\Support\Facades\DB::table('bs_review')->where('pertanyaan_id', $soal->id)->orderBy('id', 'desc')->first();
        return view('banksoal::gpm.validasi-bank-soal-review', compact('soal', 'opsi_jawaban', 'review'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pertanyaan_id' => 'required|integer',
            'status_review' => 'required|string',
            'catatan'       => 'required|string'
        ]);

        $this->validasiService->simpanReview($validated);
        
        $mkId = $request->query('mk_id');

        return redirect()->route('banksoal.soal.gpm.validasi-bank-soal.review', ['mk_id' => $mkId])->with('success', 'Hasil review berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status_review' => 'required|string',
            'catatan'       => 'required|string'
        ]);

        $this->validasiService->updateReview($id, $validated);

        return back()->with('success', 'Hasil review berhasil diupdate!');
    }
}