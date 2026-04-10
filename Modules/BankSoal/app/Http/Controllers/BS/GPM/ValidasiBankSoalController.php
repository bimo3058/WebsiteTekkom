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

    public function index()
    {
        $paket_soal = $this->validasiService->getDaftarAntreanMataKuliah();
        return view('banksoal::gpm.validasi-bank-soal', compact('paket_soal'));
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
}