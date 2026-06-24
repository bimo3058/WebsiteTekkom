<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Modules\EOffice\Models\Ruangan;
use Modules\EOffice\Models\RuanganFoto;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Ruangan::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('lokasi', 'like', "%{$search}%");
        }

        $ruangans = $query->orderBy('nama')->paginate(15);
        return view('eoffice::manajemen-ruangan.admin.ruangan.index', compact('ruangans'));
    }

    public function create()
    {
        return view('eoffice::manajemen-ruangan.admin.ruangan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'lantai' => 'nullable|integer',
            'kapasitas' => 'required|integer|min:0',
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'string',
            'fotos' => 'nullable|array|max:10',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $data = [
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'lantai' => $request->lantai,
            'kapasitas' => $request->kapasitas,
            'fasilitas' => $request->fasilitas ?? [],
            'is_active' => $request->has('is_active') ? true : false,
        ];

        // Ensure we don't pass 'foto' to Ruangan anymore, since it's removed.
        $ruangan = Ruangan::create($data);

        if ($request->hasFile('fotos')) {
            $urutan = 0;
            $supabase = app(\App\Services\SupabaseStorage::class);
            foreach ($request->file('fotos') as $file) {
                $path = $supabase->upload($file, 'mr-foto-ruangan');
                if ($path) {
                    RuanganFoto::create([
                        'ruangan_id' => $ruangan->id,
                        'path_foto' => $path,
                        'urutan' => $urutan++
                    ]);
                }
            }
        }

        return redirect()->route('eoffice.peminjaman.admin.ruangan.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('eoffice::manajemen-ruangan.admin.ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'lantai' => 'nullable|integer',
            'kapasitas' => 'required|integer|min:0',
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'string',
            'fotos' => 'nullable|array|max:10',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'foto_order' => 'nullable|string'
        ]);

        $data = [
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'lantai' => $request->lantai,
            'kapasitas' => $request->kapasitas,
            'fasilitas' => $request->fasilitas ?? [],
            'is_active' => $request->has('is_active') ? true : false,
        ];

        $ruangan->update($data);

        // Update Sorting Order of Existing Photos
        if ($request->has('foto_order') && $request->foto_order) {
            $orderArray = json_decode($request->foto_order, true);
            if (is_array($orderArray)) {
                foreach ($orderArray as $index => $fotoId) {
                    RuanganFoto::where('id', $fotoId)->where('ruangan_id', $ruangan->id)->update(['urutan' => $index]);
                }
            }
        }

        // Add New Photos
        if ($request->hasFile('fotos')) {
            $maxUrutan = RuanganFoto::where('ruangan_id', $ruangan->id)->max('urutan');
            $urutan = $maxUrutan !== null ? $maxUrutan + 1 : 0;
            $supabase = app(\App\Services\SupabaseStorage::class);

            foreach ($request->file('fotos') as $file) {
                $path = $supabase->upload($file, 'mr-foto-ruangan');
                if ($path) {
                    RuanganFoto::create([
                        'ruangan_id' => $ruangan->id,
                        'path_foto' => $path,
                        'urutan' => $urutan++
                    ]);
                }
            }
        }

        return redirect()->route('eoffice.peminjaman.admin.ruangan.index')->with('success', 'Detail ruangan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return redirect()->route('eoffice.peminjaman.admin.ruangan.index')->with('success', 'Ruangan berhasil dihapus.');
    }

    public function destroyFoto($id)
    {
        $foto = RuanganFoto::findOrFail($id);
        app(\App\Services\SupabaseStorage::class)->delete($foto->path_foto);
        $foto->delete();
        return redirect()->back()->with('success', 'Foto ruangan berhasil dihapus.');
    }
}
