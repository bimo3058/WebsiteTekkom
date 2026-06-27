<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Praktikum;
use App\Services\SupabaseStorage;

/**
 * Asprak: Pengumuman (semua asprak bisa upload/hapus).
 * Mendukung dropdown filter per praktikum yang di-assign ke asprak.
 */
class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Semua assignment asprak milik user ini
        $asprakList = AsistenPraktikum::where('user_id', $user->id)
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
            ->with('praktikum')
            ->get();

        $praktikumList = $asprakList->map(fn($a) => $a->praktikum)->filter();

        // Praktikum yang dipilih (default ke yang pertama)
        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $asprak      = $asprakList->firstWhere('praktikum_id', $praktikumId);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        // Pengumuman hanya untuk praktikum yang dipilih
        $pengumumans = $praktikumId
            ? Pengumuman::where('praktikum_id', $praktikumId)
                ->with('user')
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.asprak.pengumuman', compact(
            'pengumumans',
            'asprak',
            'praktikumList',
            'praktikum'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'judul'        => 'required|string|max:255',
            'konten'       => 'required|string',
            'is_published' => 'boolean',
            'lampiran'     => 'nullable|array|max:3',
            'lampiran.*'   => 'file|max:5120',
        ]);

        $user   = auth()->user();
        $asprak = AsistenPraktikum::where('user_id', $user->id)
            ->where('role', 'asprak')
            ->where('praktikum_id', $request->praktikum_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$asprak) {
            return back()->with('error', 'Anda tidak terdaftar sebagai asprak di praktikum ini.');
        }

        $lampiranPaths = [];
        if ($request->hasFile('lampiran')) {
            $storage = app(SupabaseStorage::class);
            foreach ($request->file('lampiran') as $file) {
                $path = $storage->upload($file, 'pengumuman', 'eoffice');
                if ($path) {
                    $lampiranPaths[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                    ];
                }
            }
        }

        Pengumuman::create([
            'praktikum_id' => $request->praktikum_id,
            'user_id'      => $user->id,
            'judul'        => $request->judul,
            'konten'       => $request->konten,
            'is_published' => $request->boolean('is_published'),
            'lampiran'     => empty($lampiranPaths) ? null : $lampiranPaths,
        ]);

        return redirect()->route('eoffice.manprak.asprak.pengumuman.index', [
            'praktikum_id' => $request->praktikum_id,
        ])->with('success', 'Pengumuman berhasil diunggah.');
    }

    public function destroy(int $id)
    {
        $user       = auth()->user();
        $pengumuman = Pengumuman::findOrFail($id);

        if ($pengumuman->user_id !== $user->id && !$user->hasAnyRole(['koor_prak', 'admin_eoffice', 'superadmin'])) {
            return back()->with('error', 'Anda hanya bisa menghapus pengumuman milik sendiri.');
        }

        $praktikumId = $pengumuman->praktikum_id;
        $pengumuman->delete();

        return redirect()->route('eoffice.manprak.asprak.pengumuman.index', [
            'praktikum_id' => $praktikumId,
        ])->with('success', 'Pengumuman berhasil dihapus.');
    }
}
