<?php

namespace Modules\BankSoal\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class BankSoalController extends Controller
{
    public function index()
    {
        return view('banksoal::index');
    }

    public function dashboard()
    {
        $user  = auth()->user();
        $roles = $user->roles->pluck('name');

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
        $mataKuliah = $this->mataKuliahService->getAll();
        $statsSoal  = $mataKuliah->mapWithKeys(function ($mk) {
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

    public function store(Request $request)
    {
        // TODO: implementasi
        // Setelah store berhasil:
        // AuditLogger::create('bank_soal', "Menambah pertanyaan: {$pertanyaan->soal}", $pertanyaan, $pertanyaan->toArray());
    }

    public function show($id)
    {
        // TODO: implementasi
        // AuditLogger::view('bank_soal', "Melihat pertanyaan ID {$id}");
        return view('banksoal::show');
    }

    public function edit($id)
    {
        return view('banksoal::edit');
    }

    public function update(Request $request, $id)
    {
        // TODO: implementasi
        // $oldData = $pertanyaan->toArray();
        // $pertanyaan->update($validated);
        // AuditLogger::update('bank_soal', "Mengubah pertanyaan: {$pertanyaan->soal}", $pertanyaan, $oldData, $pertanyaan->fresh()->toArray());
    }

    public function destroy($id)
    {
        // TODO: implementasi
        // AuditLogger::delete('bank_soal', "Menghapus pertanyaan ID {$id}", $pertanyaan, $pertanyaan->toArray());
    }
}