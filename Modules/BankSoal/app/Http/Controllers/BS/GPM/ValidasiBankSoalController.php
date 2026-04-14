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

    public function review()
    {
        $soal = $this->validasiService->getSoalReview();

        if (!$soal) {
            return redirect()->route('banksoal.soal.gpm.validasi-bank-soal')->with('success', 'Mantap! Semua soal telah selesai divalidasi.');
        }

        $opsi_jawaban = $this->validasiService->getOpsiJawaban($soal->id);
        return view('banksoal::gpm.validasi-bank-soal-review', compact('soal', 'opsi_jawaban'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pertanyaan_id' => 'required|integer',
            'status_review' => 'required|string',
            'catatan'       => 'required|string'
        ]);

        $this->validasiService->simpanReview($validated);

        return redirect()->route('banksoal.soal.gpm.validasi-bank-soal.review')->with('success', 'Hasil review berhasil disimpan!');
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