<?php

namespace Modules\EOffice\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\KpMasterRubrik;

class MasterRubrikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KpMasterRubrik::query();

        if ($request->has('search')) {
            $query->where('kode', 'like', '%' . $request->search . '%')
                ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
        }

        $rubriks = $query->orderBy('id', 'desc')->paginate($request->get('limit', 10));

        return view('eoffice::koordinator.rubrik.index', compact('rubriks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('eoffice::koordinator.rubrik.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:20',
            'deskripsi' => 'required|string',
            'bobot' => 'required|integer|min:0|max:100',
            'role_penilai' => 'required|in:dosen_pembimbing,koordinator',
            'is_active' => 'boolean'
        ]);

        KpMasterRubrik::create([
            'kode' => $request->kode,
            'deskripsi' => $request->deskripsi,
            'bobot' => $request->bobot,
            'role_penilai' => $request->role_penilai,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('eoffice.kp.koordinator.master-rubrik.index')->with('success', 'Master rubrik berhasil ditambahkan.');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        // Not used
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rubrik = KpMasterRubrik::findOrFail($id);
        return view('eoffice::koordinator.rubrik.edit', compact('rubrik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|max:20',
            'deskripsi' => 'required|string',
            'bobot' => 'required|integer|min:0|max:100',
            'role_penilai' => 'required|in:dosen_pembimbing,koordinator',
            'is_active' => 'boolean'
        ]);

        $rubrik = KpMasterRubrik::findOrFail($id);
        $rubrik->update([
            'kode' => $request->kode,
            'deskripsi' => $request->deskripsi,
            'bobot' => $request->bobot,
            'role_penilai' => $request->role_penilai,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('eoffice.kp.koordinator.master-rubrik.index')->with('success', 'Master rubrik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rubrik = KpMasterRubrik::findOrFail($id);
        $rubrik->delete();

        return redirect()->route('eoffice.kp.koordinator.master-rubrik.index')->with('success', 'Master rubrik berhasil dihapus.');
    }
}
