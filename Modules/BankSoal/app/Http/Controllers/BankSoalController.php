<?php

namespace Modules\BankSoal\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\BankSoal\KompreSessionService;
use App\Services\BankSoal\MataKuliahService;
use App\Services\BankSoal\PertanyaanService;
use App\Services\BankSoal\RpsService;
use Illuminate\Http\Request;

class BankSoalController extends Controller
{
    public function __construct(
        private MataKuliahService    $mataKuliahService,
        private PertanyaanService    $pertanyaanService,
        private KompreSessionService $kompreSessionService,
        private RpsService           $rpsService,
    ) {}

    public function index()
    {
        return view('banksoal::index');
    }

    public function dashboard()
    {
        $user  = auth()->user();
        $roles = $user->roles->pluck('name'); // load sekali, tidak query ulang

        if ($roles->intersect(['superadmin', 'admin'])->isNotEmpty()) {
            return $this->adminDashboard();
        }

        if ($roles->contains('gpm')) {
            return $this->gpmDashboard();
        }

        if ($roles->contains('dosen')) {
            return $this->dosenDashboard();
        }

        return $this->mahasiswaDashboard();
    }

    private function adminDashboard()
    {
        $mataKuliah = $this->mataKuliahService->getAll();

        return view('banksoal::dashboard.admin', compact('mataKuliah'));
    }

    private function gpmDashboard()
    {
        $user       = auth()->user();
        $mataKuliah = $this->mataKuliahService->getAll();

        // Stats soal per MK untuk overview GPM
        $statsSoal = $mataKuliah->mapWithKeys(function ($mk) {
            return [$mk->id => $this->pertanyaanService->getStatsByMataKuliah($mk->id)];
        });

        return view('banksoal::dashboard.gpm', compact('mataKuliah', 'statsSoal'));
    }

    private function dosenDashboard()
    {
        $user       = auth()->user();
        $mataKuliah = $this->mataKuliahService->getByDosen($user->id);

        return view('banksoal::dashboard.dosen', compact('mataKuliah'));
    }

    private function mahasiswaDashboard()
    {
        $user          = auth()->user();
        $activeSession = $this->kompreSessionService->getActiveSession($user->id);
        $history       = $this->kompreSessionService->getHistoryByUser($user->id);

        return view('banksoal::dashboard.mahasiswa', compact('activeSession', 'history'));
    }

    public function create()
    {
        return view('banksoal::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('banksoal::show');
    }

    public function edit($id)
    {
        return view('banksoal::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}