<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Tugas;

class TugasController extends Controller
{
    public function __construct(private SupabaseStorage $supabase) {}

    public function index(Request $request)
    {
        $user = auth()->user();

        $semuaPraktikan = DaftarPraktikan::with('praktikum')
            ->where('user_id', $user->id)
            ->whereHas('praktikum', fn($q) => $q->where('status', 'aktif'))
            ->get();

        // Sinkronisasi dengan session
        $praktikumAktifId = $request->input('praktikum_id') 
            ?? session('mhs_praktikum_id') 
            ?? $semuaPraktikan->first()?->praktikum_id;

        if ($praktikumAktifId) {
            session(['mhs_praktikum_id' => $praktikumAktifId]);
        }

        $daftarPraktikan = $semuaPraktikan->firstWhere('praktikum_id', $praktikumAktifId);

        // Fallback
        if (!$daftarPraktikan && $semuaPraktikan->isNotEmpty()) {
            $daftarPraktikan = $semuaPraktikan->first();
            session(['mhs_praktikum_id' => $daftarPraktikan->praktikum_id]);
        }

        if (!$daftarPraktikan) {
            return view('eoffice::manajemen-praktikum.mahasiswa.tugas', [
                'tugasList'      => collect(),
                'daftarPraktikan' => null,
                'semuaPraktikan'  => collect(),
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
                $tugas->status_tugas = $pengumpulan?->status_pengumpulan ?? 'belum_dikumpul';
                return $tugas;
            });

        return view('eoffice::manajemen-praktikum.mahasiswa.tugas', compact(
            'tugasList',
            'daftarPraktikan',
            'semuaPraktikan'
        ));
    }

    public function kumpul(Request $request, string $tugasId)
    {
        $request->validate([
            'file'    => 'required|file|max:10240|mimes:pdf,docx,doc,zip,rar',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user  = auth()->user();
        $tugas = Tugas::with('modul')->findOrFail($tugasId);

        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)
            ->where('praktikum_id', $tugas->modul->praktikum_id)
            ->firstOrFail();

        if ($tugas->deadline && now()->gt($tugas->deadline)) {
            return back()->with('error', 'Deadline sudah lewat, pengumpulan tidak dapat diterima.');
        }

        $existing = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('daftar_praktikan_id', $daftarPraktikan->id)
            ->first();

        if ($existing && $existing->status_pengumpulan !== PengumpulanTugas::STATUS_REVISI) {
            return back()->with('error', 'Tugas sudah dikumpulkan dan tidak dalam status revisi.');
        }

        $path = $this->supabase->upload(
            $request->file('file'),
            'tugas/' . $tugas->modul->praktikum_id . '/' . $tugas->id,
            'eoffice'
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

    public function kirimUlang(Request $request, string $tugasId)
    {
        $request->validate([
            'file'    => 'required|file|max:10240|mimes:pdf,docx,doc,zip,rar',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user  = auth()->user();
        $tugas = Tugas::with('modul')->findOrFail($tugasId);

        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)
            ->where('praktikum_id', $tugas->modul->praktikum_id)
            ->firstOrFail();

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugasId)
            ->where('daftar_praktikan_id', $daftarPraktikan->id)
            ->where('status_pengumpulan', PengumpulanTugas::STATUS_REVISI)
            ->firstOrFail();
        $path  = $this->supabase->upload(
            $request->file('file'),
            'tugas/' . $tugas->modul->praktikum_id . '/' . $tugas->id,
            'eoffice'
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