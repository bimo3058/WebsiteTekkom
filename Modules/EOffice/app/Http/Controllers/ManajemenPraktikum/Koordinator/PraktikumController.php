<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\Pengumuman;

class PraktikumController extends Controller
{
    /**
     * Menampilkan daftar semua praktikum yang dikoordinasikan.
     */
    public function index()
    {
        $user = auth()->user();

        $praktikums = Praktikum::with(['dosens', 'modul'])
            ->where('koor_id', $user->id)
            ->withCount(['daftarPraktikan', 'modul', 'asprakPraktikum'])
            ->orderByDesc('status') // 'aktif' di atas
            ->orderByDesc('created_at')
            ->get();

        $currentYear = now()->year;
        $currentSemester = now()->month <= 6 ? 'Genap' : 'Ganjil';
        $defaultTahunAjaran = $currentSemester === 'Genap' ? $currentYear - 1 : $currentYear;
        $semesterLabel = "Semester {$currentSemester} {$defaultTahunAjaran}/" . ($defaultTahunAjaran + 1);

        return view('eoffice::manajemen-praktikum.koordinator.praktikum', compact(
            'praktikums',
            'semesterLabel'
        ));
    }

    /**
     * Menampilkan halaman detail drill-down untuk satu praktikum tertentu.
     */
    public function show($id)
    {
        $user = auth()->user();

        // Pastikan praktikum ini memang diampu oleh koor yang sedang login
        $praktikum = Praktikum::with(['dosens'])
            ->where('id', $id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        // Menyimpan context session agar menu-menu koor yang lain (Pendaftaran, Modul, dll) membaca praktikum ini
        session(['koor_praktikum_id' => $praktikum->id]);

        // Redirect langsung ke halaman Seleksi Asisten
        return redirect()->route('eoffice.manprak.koor.pendaftaran-asprak.index');
    }

    /**
     * Update cover image praktikum
     */
    public function updateCover(\Illuminate\Http\Request $request, $id)
    {
        $user = auth()->user();
        
        $praktikum = Praktikum::where('id', $id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        $request->validate([
            'cover' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $file = $request->file('cover');
        $supabase = app(\App\Services\SupabaseStorage::class);
        
        // Delete old cover if exists
        if ($praktikum->cover_path) {
            try {
                $supabase->delete($praktikum->cover_path, 'eoffice');
            } catch (\Exception $e) {
                // ignore if not found
            }
        }

        $path = $supabase->upload($file, 'praktikum/cover', 'eoffice');

        $praktikum->update(['cover_path' => $path]);

        return back()->with('success', 'Cover praktikum berhasil diperbarui.');
    }

    /**
     * Hapus cover image praktikum
     */
    public function deleteCover($id)
    {
        $user = auth()->user();

        $praktikum = Praktikum::where('id', $id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        if ($praktikum->cover_path) {
            try {
                $supabase = app(\App\Services\SupabaseStorage::class);
                $supabase->delete($praktikum->cover_path, 'eoffice');
            } catch (\Exception $e) {
                // ignore if not found
            }
            $praktikum->update(['cover_path' => null]);
        }

        return back()->with('success', 'Gambar sampul berhasil dihapus.');
    }
}
