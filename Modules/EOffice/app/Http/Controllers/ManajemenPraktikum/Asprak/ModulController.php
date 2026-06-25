<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\Praktikum;

class ModulController extends Controller
{
    /**
     * Daftar semua modul di praktikum asprak ini + form create.
     */
    public function index(Request $request)
    {
        $user   = auth()->user();
        $asprak = $request->attributes->get('asprak')
            ?? AsprakPraktikum::where('user_id', $user->id)
                ->where('role', 'asprak')
                ->whereNull('deleted_at')
                ->first();

        $praktikum = $asprak ? Praktikum::find($asprak->praktikum_id) : null;

        $moduls = $praktikum
            ? Modul::with(['modulAsprak.asprak.user', 'materi', 'tugas'])
                ->where('praktikum_id', $praktikum->id)
                ->orderBy('urutan')
                ->get()
            : collect();

        // Tandai modul mana yang diampu asprak ini
        $assignedModulIds = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->pluck('modul_id')
            : collect();

        if ($asprak && $assignedModulIds->isEmpty()) {
            return redirect()->route('eoffice.manprak.asprak.dashboard')->with('error', 'Akses ditolak: Anda belum di-assign ke modul manapun di praktikum ini.');
        }

        return view('eoffice::manajemen-praktikum.asprak.modul', compact(
            'praktikum',
            'moduls',
            'asprak',
            'assignedModulIds'
        ));
    }

    /**
     * Buat modul baru untuk praktikum yang di-assign ke asprak ini.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'urutan'        => 'required|integer|min:1',
            'jadwal_minggu' => 'nullable|string|max:100',
        ]);

        $user   = auth()->user();
        $asprak = $request->attributes->get('asprak')
            ?? AsprakPraktikum::where('user_id', $user->id)
                ->where('role', 'asprak')
                ->whereNull('deleted_at')
                ->firstOrFail();

        $praktikum = Praktikum::where('id', $asprak->praktikum_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $modul = Modul::create([
            'praktikum_id'  => $praktikum->id,
            'nama'          => $request->nama,
            'deskripsi'     => $request->deskripsi,
            'urutan'        => $request->urutan,
            'jadwal_minggu' => $request->jadwal_minggu,
        ]);

        // Otomatis assign asprak pembuat ke modul ini
        ModulAsprak::firstOrCreate([
            'modul_id'  => $modul->id,
            'asprak_id' => $asprak->id,
        ]);

        return back()->with('success', 'Modul berhasil dibuat dan Anda otomatis ditambahkan sebagai pengampu.');
    }

    /**
     * Detail modul: info lengkap + materi, tugas, absensi, daftar praktikan.
     */
    public function show(Request $request, int $id)
    {
        $user   = auth()->user();
        $asprak = $request->attributes->get('asprak')
            ?? AsprakPraktikum::where('user_id', $user->id)
                ->where('role', 'asprak')
                ->whereNull('deleted_at')
                ->first();

        $modul = Modul::with([
            'praktikum',
            'materi',
            'tugas.pengumpulan',
            'modulAsprak.asprak.user',
            'absensi.daftarPraktikan.user',
        ])->where('praktikum_id', $asprak?->praktikum_id)
            ->findOrFail($id);

        $isAssigned = $asprak
            ? ModulAsprak::where('modul_id', $modul->id)
                ->where('asprak_id', $asprak->id)
                ->exists()
            : false;

        if (!$isAssigned) {
            return back()->with('error', 'Gagal memuat: Anda belum di-assign sebagai pengampu pada modul ini.');
        }

        $daftarPraktikan = DaftarPraktikan::with(['user', 'nilai'])
            ->where('praktikum_id', $modul->praktikum_id)
            ->get();

        return view('eoffice::manajemen-praktikum.asprak.modul-detail', compact(
            'modul',
            'daftarPraktikan',
            'asprak',
            'isAssigned'
        ));
    }

    /**
     * Update modul (hanya modul di praktikum asprak ini).
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'urutan'        => 'required|integer|min:1',
            'jadwal_minggu' => 'nullable|string|max:100',
        ]);

        $user   = auth()->user();
        $asprak = $request->attributes->get('asprak')
            ?? AsprakPraktikum::where('user_id', $user->id)
                ->where('role', 'asprak')
                ->whereNull('deleted_at')
                ->firstOrFail();

        $modul = Modul::where('praktikum_id', $asprak->praktikum_id)->findOrFail($id);

        $isAssigned = ModulAsprak::where('modul_id', $modul->id)
            ->where('asprak_id', $asprak->id)
            ->exists();
        
        if (!$isAssigned) {
            return back()->with('error', 'Gagal menyimpan: Anda belum di-assign sebagai pengampu pada modul ini.');
        }

        $modul->update($request->only(['nama', 'deskripsi', 'urutan', 'jadwal_minggu']));

        return back()->with('success', 'Modul berhasil diperbarui.');
    }

    /**
     * Hapus modul (hanya modul di praktikum asprak ini).
     */
    public function destroy(Request $request, int $id)
    {
        $user   = auth()->user();
        $asprak = $request->attributes->get('asprak')
            ?? AsprakPraktikum::where('user_id', $user->id)
                ->where('role', 'asprak')
                ->whereNull('deleted_at')
                ->firstOrFail();

        $modul = Modul::where('praktikum_id', $asprak->praktikum_id)->findOrFail($id);

        $isAssigned = ModulAsprak::where('modul_id', $modul->id)
            ->where('asprak_id', $asprak->id)
            ->exists();
        
        if (!$isAssigned) {
            return back()->with('error', 'Gagal menghapus: Anda belum di-assign sebagai pengampu pada modul ini.');
        }

        $modul->delete();

        return back()->with('success', 'Modul berhasil dihapus.');
    }
}
