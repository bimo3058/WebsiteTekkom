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
     * Daftar tugas praktikum mahasiswa + status pengumpulan.
     */
    public function index()
    {
        $user = auth()->user();

        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)
            ->whereHas('praktikum', fn($q) => $q->where('status', 'aktif'))
            ->first();

        if (!$daftarPraktikan) {
            return view('eoffice::manajemen-praktikum.mahasiswa.tugas', [
                'tugasList'       => collect(),
                'daftarPraktikan' => null,
            ]);
        }

        $tugasList = Tugas::whereHas('modul', fn($q) => $q->where('praktikum_id', $daftarPraktikan->praktikum_id))
            ->where('is_published', true)
            ->with(['modul'])
            ->orderBy('deadline')
            ->get()
            ->map(function ($tugas) use ($daftarPraktikan) {
                $pengumpulan = PengumpulanTugas::where('tugas_id', $tugas->id)
                    ->where('daftar_praktikan_id', $daftarPraktikan->id)
                    ->first();

                $tugas->pengumpulan  = $pengumpulan;
                $tugas->sudah_kumpul = !is_null($pengumpulan);
                // status: belum_dikumpul | belum_dicek | revisi | acc
                $tugas->status_tugas = $pengumpulan?->status_pengumpulan ?? 'belum_dikumpul';
                return $tugas;
            });

        return view('eoffice::manajemen-praktikum.mahasiswa.tugas', compact(
            'tugasList',
            'daftarPraktikan'
        ));
    }

    /**
     * Kumpulkan tugas (pertama kali).
     */
    public function kumpul(Request $request, string $tugasId)
    {
        $request->validate([
            'file'    => 'required|file|max:10240|mimes:pdf,docx,doc,zip,rar',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user            = auth()->user();
        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)->firstOrFail();
        $tugas           = Tugas::findOrFail($tugasId);

        if ($tugas->deadline && now()->gt($tugas->deadline)) {
            return back()->with('error', 'Deadline sudah lewat, pengumpulan tidak dapat diterima.');
        }

        $existing = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('daftar_praktikan_id', $daftarPraktikan->id)
            ->first();

        // Hanya boleh kumpul ulang jika status revisi
        if ($existing && $existing->status_pengumpulan !== PengumpulanTugas::STATUS_REVISI) {
            return back()->with('error', 'Tugas sudah dikumpulkan dan tidak dalam status revisi.');
        }

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
                'file_path'          => $path,
                'catatan'            => $request->catatan,
                'nilai'              => null,
                'catatan_revisi'     => null,
                'is_revision'        => false,
                'status_pengumpulan' => PengumpulanTugas::STATUS_BELUM_DICEK,
            ]
        );

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }

    /**
     * Kirim ulang tugas setelah mendapat revisi dari asprak.
     * Sesuai docx: "dapat kirim kembali hasil setelah revisi".
     */
    public function kirimUlang(Request $request, string $tugasId)
    {
        $request->validate([
            'file'    => 'required|file|max:10240|mimes:pdf,docx,doc,zip,rar',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user            = auth()->user();
        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)->firstOrFail();

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugasId)
            ->where('daftar_praktikan_id', $daftarPraktikan->id)
            ->where('status_pengumpulan', PengumpulanTugas::STATUS_REVISI)
            ->firstOrFail();

        $tugas = Tugas::findOrFail($tugasId);
        $path  = $request->file('file')->store(
            'tugas/' . $tugas->modul->praktikum_id . '/' . $tugas->id,
            'local'
        );

        $pengumpulan->update([
            'file_path'          => $path,
            'catatan'            => $request->catatan,
            'catatan_revisi'     => null,
            'is_revision'        => true,
            'status_pengumpulan' => PengumpulanTugas::STATUS_BELUM_DICEK,
        ]);

        return back()->with('success', 'Tugas revisi berhasil dikirim ulang.');
    }
}
