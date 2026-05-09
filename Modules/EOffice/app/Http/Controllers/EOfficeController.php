<?php

namespace Modules\EOffice\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EOfficeController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        $this->authorize('eoffice.view'); // FIX
        return view('eoffice::index');
    }

    /* |--------------------------------------------------------------------------
    | Dashboard Methods
    |-------------------------------------------------------------------------- */

    public function adminDashboard()
    {
        $this->authorize('eoffice.view'); // FIX
        return view('eoffice::dashboard.admin');
    }

    public function dosenDashboard()
    {
        $this->authorize('eoffice.view'); // FIX
        return view('eoffice::dashboard.dosen');
    }

    public function mahasiswaDashboard()
    {
        $this->authorize('eoffice.view'); // FIX
        return view('eoffice::dashboard.mahasiswa');
    }

    /* |--------------------------------------------------------------------------
    | CRUD Methods
    |-------------------------------------------------------------------------- */

    public function create()
    {
        $this->authorize('eoffice.edit'); // FIX
        return view('eoffice::create');
    }

    public function store(Request $request)
    {
        $this->authorize('eoffice.edit'); // FIX
        DB::beginTransaction();
        try {
            DB::commit();
            return redirect()->back()->with('success', 'Dokumen berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('eoffice.view'); // FIX
        return view('eoffice::show');
    }

    public function edit($id)
    {
        $this->authorize('eoffice.edit'); // FIX
        return view('eoffice::edit');
    }

    public function update(Request $request, $id)
    {
        $this->authorize('eoffice.edit'); // FIX
    }

    public function destroy($id)
    {
        $this->authorize('eoffice.delete'); // FIX
    }
}