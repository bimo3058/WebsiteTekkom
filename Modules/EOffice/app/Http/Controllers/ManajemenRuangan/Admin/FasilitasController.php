<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Fasilitas;

class FasilitasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Fasilitas::query();

        if (!empty($search)) {
            $query->whereRaw('LOWER(nama_fasilitas) LIKE ?', ['%' . strtolower($search) . '%']);
        }

        $fasilitas = $query->orderBy('nama_fasilitas', 'asc')->paginate($request->query('per_page', 10))->withQueryString();

        return view('eoffice::manajemen-ruangan.admin.fasilitas.index', compact('fasilitas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_fasilitas' => 'required|string'
        ]);

        $lines = preg_split('/\r\n|\r|\n/', $request->nama_fasilitas);
        $addedCount = 0;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (!empty($trimmed)) {
                // Hindari duplikat (Case-Insensitive)
                $exists = Fasilitas::whereRaw('LOWER(nama_fasilitas) = ?', [strtolower($trimmed)])->exists();
                if (!$exists) {
                    Fasilitas::create([
                        'nama_fasilitas' => $trimmed
                    ]);
                    $addedCount++;
                }
            }
        }

        return redirect()->back()->with('success', $addedCount . ' fasilitas baru berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $fasilitas = Fasilitas::findOrFail($id);
        $fasilitas->delete();

        return redirect()->back()->with('success', 'Fasilitas berhasil dihapus dari Master.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|string'
        ]);

        $idsArray = explode(',', $request->ids);
        Fasilitas::whereIn('id', $idsArray)->delete();

        return redirect()->back()->with('success', count($idsArray) . ' fasilitas berhasil dihapus dari Master secara bersamaan.');
    }
}
