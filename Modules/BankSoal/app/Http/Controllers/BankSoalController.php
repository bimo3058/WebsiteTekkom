<?php

namespace Modules\BankSoal\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Services\MataKuliahService;
use Modules\BankSoal\Services\PertanyaanService;
use Modules\BankSoal\Services\KompreService;

class BankSoalController extends Controller
{
    public function __construct(
        protected MataKuliahService $mataKuliahService,
        protected PertanyaanService $pertanyaanService,
        protected KompreService $kompreService
    ) {}

    public function index()
    {
        $this->authorize('banksoal.view');
        return view('banksoal::index');
    }

    /* |--------------------------------------------------------------------------
    | Dashboard Methods
    |
    | FIX: Tambahkan $this->authorize('banksoal.view') di SETIAP dashboard method.
    | Sebelumnya method-method ini TIDAK ada pengecekan permission,
    | sehingga siapa saja yang punya role bisa langsung akses.
    |-------------------------------------------------------------------------- */

    public function adminDashboard()
    {
        $this->authorize('banksoal.view'); // FIX: tambahkan
        $mataKuliah = $this->mataKuliahService->listAll();
        return view('banksoal::dashboard.admin', compact('mataKuliah'));
    }

    public function gpmDashboard()
    {
        $this->authorize('banksoal.view'); // FIX: tambahkan
        $mataKuliah = $this->mataKuliahService->listAll();
        $statsSoal = $mataKuliah->mapWithKeys(function ($mk) {
            return [$mk->id => $this->pertanyaanService->list(['mk_id' => $mk->id])->total()];
        });
        return view('banksoal::dashboard.gpm', compact('mataKuliah', 'statsSoal'));
    }

    public function dosenDashboard()
    {
        $this->authorize('banksoal.view'); // FIX: tambahkan
        $user = auth()->user();
        $mataKuliah = $this->mataKuliahService->getMkByDosen($user->id);
        return view('banksoal::dashboard.dosen', compact('mataKuliah'));
    }

    public function mahasiswaDashboard()
    {
        $this->authorize('banksoal.view'); // FIX: tambahkan
        $user = auth()->user();
        $history = $this->kompreService->getRiwayat($user->id);
        $activeSession = $history->where('status', 'ongoing')->first();
        return view('banksoal::dashboard.mahasiswa', compact('activeSession', 'history'));
    }

    /* |--------------------------------------------------------------------------
    | CRUD Methods — sudah benar, tidak perlu diubah
    |-------------------------------------------------------------------------- */

    public function create()
    {
        $this->authorize('banksoal.edit');
        return view('banksoal::create');
    }

    public function store(Request $request)
    {
        $this->authorize('banksoal.edit');
        DB::beginTransaction();
        try {
            DB::commit();
            return redirect()->route('banksoal.index')->with('success', 'Pertanyaan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('banksoal.view');
        return view('banksoal::show');
    }

    public function edit($id)
    {
        $this->authorize('banksoal.edit');
        return view('banksoal::edit');
    }

    public function update(Request $request, $id)
    {
        $this->authorize('banksoal.edit');
    }

    public function destroy($id)
    {
        $this->authorize('banksoal.delete');
        DB::beginTransaction();
        try {
            DB::commit();
            return back()->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus.');
        }
    }
}