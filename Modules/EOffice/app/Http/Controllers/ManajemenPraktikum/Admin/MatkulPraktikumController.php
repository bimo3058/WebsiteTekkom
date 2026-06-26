<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\MatkulPraktikum;

class MatkulPraktikumController extends Controller
{
    public function index(Request $request)
    {
        $query = MatkulPraktikum::orderBy('semester')->orderBy('kode');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($semester = $request->input('semester')) {
            $query->where('semester', $semester);
        }

        $matkulList = $query->paginate(50)->withQueryString();

        return view('eoffice::manajemen-praktikum.admin.matkul-praktikum', compact('matkulList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'     => 'required|string|max:20|unique:eo_matkul_praktikum,kode',
            'nama'     => 'required|string|max:255',
            'sks'      => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        MatkulPraktikum::create($request->only(['kode', 'nama', 'sks', 'semester']));

        return back()->with('success', "Mata kuliah {$request->nama} berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $mk = MatkulPraktikum::findOrFail($id);

        $request->validate([
            'kode'     => "required|string|max:20|unique:eo_matkul_praktikum,kode,{$id}",
            'nama'     => 'required|string|max:255',
            'sks'      => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        $mk->update($request->only(['kode', 'nama', 'sks', 'semester']));

        return back()->with('success', "Mata kuliah {$mk->nama} berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $mk = MatkulPraktikum::findOrFail($id);
        $mk->delete();

        return back()->with('success', "Mata kuliah {$mk->nama} berhasil dihapus.");
    }
}
