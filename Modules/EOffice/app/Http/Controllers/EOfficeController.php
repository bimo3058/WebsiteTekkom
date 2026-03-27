<?php

namespace Modules\EOffice\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class EOfficeController extends Controller
{
    public function index()
    {
        return view('eoffice::index');
    }

    public function dashboard()
    {
        $user = auth()->user();

        if ($user->roles()->whereIn('name', ['superadmin', 'admin_eoffice'])->exists()) {
            return view('eoffice::dashboard.admin');
        }

        if ($user->roles()->where('name', 'dosen')->exists()) {
            return view('eoffice::dashboard.dosen');
        }

        return view('eoffice::dashboard.mahasiswa');
    }

    public function create()
    {
        return view('eoffice::create');
    }

    public function store(Request $request)
    {
        // TODO: implementasi
        // AuditLogger::create('eoffice', "Membuat dokumen: {$dokumen->judul}", $dokumen, $dokumen->toArray());
    }

    public function show($id)
    {
        // TODO: implementasi
        // AuditLogger::view('eoffice', "Melihat dokumen ID {$id}");
        return view('eoffice::show');
    }

    public function edit($id)
    {
        return view('eoffice::edit');
    }

    public function update(Request $request, $id)
    {
        // TODO: implementasi
        // AuditLogger::update('eoffice', "Mengubah dokumen ID {$id}", $model, $oldData, $newData);
    }

    public function destroy($id)
    {
        // TODO: implementasi
        // AuditLogger::delete('eoffice', "Menghapus dokumen ID {$id}", $model, $oldData);
    }
}