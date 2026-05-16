<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Tugas;

class TugasController extends Controller
{
    /**
     * Daftar tugas praktikum mahasiswa.
     */
    public function index()
    {
        $user = auth()->user();

        // Cari daftar_praktikan milik user ini
        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)
            ->whereHas('praktikum', fn($q) => $q->where('status', 'aktif'))
            ->first();

        if (!$daftarPraktikan) {
            return view('eoffice::manajemen-praktikum.mahasiswa.tugas', [
                'tugasList'      => collect(),
                'daftarPraktikan'=> null,
            ]);
        }

        // Semua tugas dari modul di praktikum yang diikuti
        $tugasList = Tugas::whereHas('modul', fn($q) => $q->where('praktikum_id', $daftarPraktikan->praktikum_id))
            ->where('is_published', true)
            ->with(['modul'])
            ->orderBy('deadline')
            ->get()
            ->map(function ($tugas) use ($daftarPraktikan) {
                // Cek apakah sudah dikumpul
                $pengumpulan = PengumpulanTugas::where('tugas_id', $tugas->id)
                    ->where('daftar_praktikan_id', $daftarPraktikan->id)
                    ->first();

                $tugas->pengumpulan     = $pengumpulan;
                $tugas->sudah_kumpul    = !is_null($pengumpulan);
                return $tugas;
            });

        return view('eoffice::manajemen-praktikum.mahasiswa.tugas', compact(
            'tugasList',
            'daftarPraktikan'
        ));
    }

    /**
     * Kumpul / upload tugas.
     */
    public function kumpul(Request $request, string $tugasId)
    {
        $request->validate([
            'file'    => 'required|file|max:10240|mimes:pdf,docx,doc,zip,rar',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)->firstOrFail();

        $tugas = Tugas::findOrFail($tugasId);

        // Cek deadline
        if (now()->gt($tugas->deadline)) {
            return back()->with('error', 'Deadline sudah lewat, pengumpulan tidak dapat diterima.');
        }

        // Cek sudah dikumpul
        $existing = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('daftar_praktikan_id', $daftarPraktikan->id)
            ->whereNull('catatan_revisi')   // belum diminta revisi
            ->first();

        if ($existing && !$existing->is_revision) {
            return back()->with('error', 'Tugas sudah dikumpulkan sebelumnya.');
        }

        // Simpan file
        $path = $request->file('file')->store(
            'tugas/' . $tugas->modul->praktikum_id . '/' . $tugas->id,
            'local'
        );

        PengumpulanTugas::updateOrCreate(
            [
                'tugas_id'            => $tugas->id,
                'daftar_praktikan_id' => $daftarPraktikan->id,
            ],
            [
                'file_path'      => $path,
                'catatan'        => $request->catatan,
                'nilai'          => null,
                'catatan_revisi' => null,
                'is_revision'    => false,
            ]
        );

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}
