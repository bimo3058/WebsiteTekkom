<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Tugas;
use Modules\EOffice\Models\Modul;

class TugasController extends Controller
{
    public function __construct(private SupabaseStorage $supabase) {}

    public function index(Request $request)
    {
        $user = auth()->user();

        $semuaPraktikan = DaftarPraktikan::with('praktikum')
            ->where('user_id', $user->id)
            ->whereHas('praktikum', fn($q) => $q->whereIn('status', ['aktif','nonaktif']))
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
                'modulList'      => collect(),
                'praktikum'      => null,
                'daftarPraktikan' => null,
                'semuaPraktikan'  => collect(),
            ]);
        }

        $praktikum = $daftarPraktikan->praktikum;

        // --- Optimasi: Load semua pengumpulan dalam 1 query, bukan per-tugas (fix N+1) ---
        // Kumpulkan semua tugas_id terlebih dahulu
        $modulListRaw = Modul::with([
                'tugas' => fn($q) => $q->where('is_published', true)->orderBy('deadline'),
                'modulAsprak.asprak.user',
            ])
            ->where('praktikum_id', $daftarPraktikan->praktikum_id)
            ->orderBy('urutan')
            ->get();

        $allTugasIds = $modulListRaw->flatMap(fn($m) => $m->tugas->pluck('id'));

        // 1 query untuk semua pengumpulan sekaligus
        $allPengumpulan = PengumpulanTugas::whereIn('tugas_id', $allTugasIds)
            ->where('daftar_praktikan_id', $daftarPraktikan->id)
            ->with('riwayat')
            ->get()
            ->keyBy('tugas_id');

        $modulList = $modulListRaw->map(function ($modul) use ($allPengumpulan) {
            $tugasList = $modul->tugas->map(function ($tugas) use ($allPengumpulan) {
                $tugas->pengumpulan = $allPengumpulan->get($tugas->id);
                return $tugas;
            });
            return collect([
                'modul'  => $modul,
                'tugas'  => $tugasList,
                'asprak' => $modul->modulAsprak->map(fn($ma) => $ma->asprak?->user?->name)->filter()->values(),
            ]);
        });

        return view('eoffice::manajemen-praktikum.mahasiswa.tugas', compact(
            'modulList',
            'praktikum',
            'daftarPraktikan',
            'semuaPraktikan'
        ));
    }

    public function kumpul(Request $request, string $tugasId)
    {
        $request->validate([
            'file'    => 'required|array|max:3',
            'file.*'  => 'required|file|max:5120',
        ]);

        $user  = auth()->user();
        $tugas = Tugas::with('modul')->findOrFail($tugasId);

        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)
            ->where('praktikum_id', $tugas->modul->praktikum_id)
            ->firstOrFail();

        // BATAS MUTLAK: deadline_acc jika ada, jika tidak ada fallback ke deadline AC
        $tenggatMutlak = $tugas->deadline_acc ?? $tugas->deadline;
        if ($tenggatMutlak && now()->gt($tenggatMutlak)) {
            return back()->with('error', 'Tenggat waktu pengumpulan (Tenggat ACC) sudah lewat.');
        }

        $existing = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('daftar_praktikan_id', $daftarPraktikan->id)
            ->first();

        if ($existing && $existing->status_pengumpulan !== PengumpulanTugas::STATUS_REVISI) {
            return back()->with('error', 'Tugas sudah dikumpulkan dan tidak dalam status revisi.');
        }

        $paths = [];
        $failedCount = 0;
        foreach ($request->file('file') as $f) {
            $uploaded = $this->supabase->upload(
                $f,
                'tugas/' . $tugas->modul->praktikum_id . '/' . $tugas->id,
                'eoffice'
            );
            if ($uploaded) {
                $paths[] = [
                    'path' => $uploaded,
                    'original_name' => $f->getClientOriginalName()
                ];
            } else {
                $failedCount++;
            }
        }
        $path = json_encode($paths);

        $pengumpulan = PengumpulanTugas::updateOrCreate(
            [
                'tugas_id'            => $tugas->id,
                'daftar_praktikan_id' => $daftarPraktikan->id,
            ],
            [
                'file_path'          => $path,
                'catatan'            => null,
                'nilai'              => null,
                'catatan_revisi'     => null,
                'is_revision'        => false,
                'status_pengumpulan' => PengumpulanTugas::STATUS_BELUM_DICEK,
            ]
        );

        // Catat ke RiwayatPengumpulan
        $pengumpulan->riwayat()->create([
            'file_path'   => $path,
            'is_revision' => false,
        ]);

        if ($failedCount > 0) {
            return back()->with('warning', 'Tugas dikumpulkan, namun ' . $failedCount . ' file gagal diunggah karena format tidak didukung.');
        }
        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }

    public function kirimUlang(Request $request, string $tugasId)
    {
        $request->validate([
            'file'    => 'nullable|array|max:3',
            'file.*'  => 'required|file|max:5120',
            'retained_files' => 'nullable|array|max:3',
            'retained_files.*' => 'required|string',
        ]);

        $user  = auth()->user();
        $tugas = Tugas::with('modul')->findOrFail($tugasId);

        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)
            ->where('praktikum_id', $tugas->modul->praktikum_id)
            ->firstOrFail();

        // BATAS MUTLAK: deadline_acc jika ada, jika tidak ada fallback ke deadline AC
        $tenggatMutlak = $tugas->deadline_acc ?? $tugas->deadline;
        if ($tenggatMutlak && now()->gt($tenggatMutlak)) {
            return back()->with('error', 'Tenggat waktu pengumpulan (Tenggat ACC) sudah lewat.');
        }

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugasId)
            ->where('daftar_praktikan_id', $daftarPraktikan->id)
            ->whereIn('status_pengumpulan', [
                PengumpulanTugas::STATUS_REVISI,
                PengumpulanTugas::STATUS_DRAFT,
            ])
            ->firstOrFail();

        $existingFiles = collect($pengumpulan->files)
            ->mapWithKeys(function ($file) {
                $path = is_array($file) ? ($file['path'] ?? null) : $file;
                return $path ? [$path => $file] : [];
            });

        // Never trust paths posted by the browser: only retain files already owned by this submission.
        $paths = collect($request->input('retained_files', []))
            ->unique()
            ->map(fn ($path) => $existingFiles->get($path))
            ->filter()
            ->values()
            ->all();

        $newFiles = $request->file('file', []);
        if (count($paths) + count($newFiles) === 0) {
            return back()->withErrors(['file' => 'Pilih minimal satu berkas untuk dikirim kembali.']);
        }
        if (count($paths) + count($newFiles) > 3) {
            return back()->withErrors(['file' => 'Maksimal 3 berkas per pengumpulan.']);
        }

        $failedCount = 0;
        foreach ($newFiles as $f) {
            $uploaded = $this->supabase->upload(
                $f,
                'tugas/' . $tugas->modul->praktikum_id . '/' . $tugas->id,
                'eoffice'
            );
            if ($uploaded) {
                $paths[] = [
                    'path' => $uploaded,
                    'original_name' => $f->getClientOriginalName()
                ];
            } else {
                $failedCount++;
            }
        }
        if (count($paths) === 0) {
            return back()->withErrors(['file' => 'Semua berkas baru gagal diunggah. Silakan coba kembali.']);
        }

        $path = json_encode($paths);

        $isFromRevisi = $pengumpulan->status_pengumpulan === PengumpulanTugas::STATUS_REVISI || $pengumpulan->is_revision;

        $pengumpulan->update([
            'file_path'          => $path,
            'catatan'            => null,
            'catatan_revisi'     => null,
            'is_revision'        => $isFromRevisi,
            'status_pengumpulan' => PengumpulanTugas::STATUS_BELUM_DICEK,
        ]);

        // Catat ke RiwayatPengumpulan
        $pengumpulan->riwayat()->create([
            'file_path'   => $path,
            'is_revision' => $isFromRevisi,
        ]);

        if ($failedCount > 0) {
            return back()->with('warning', 'Tugas dikirim kembali, namun ' . $failedCount . ' file baru gagal diunggah.');
        }

        return back()->with('success', 'Tugas berhasil dikirim kembali.');
    }

    public function batalkan(Request $request, string $tugasId)
    {
        $user = auth()->user();
        $tugas = Tugas::with('modul')->findOrFail($tugasId);

        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)
            ->where('praktikum_id', $tugas->modul->praktikum_id)
            ->firstOrFail();

        $tenggatMutlak = $tugas->deadline_acc ?? $tugas->deadline;
        if ($tenggatMutlak && now()->gt($tenggatMutlak)) {
            return back()->with('error', 'Tidak dapat membatalkan pengiriman setelah tenggat waktu.');
        }

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugasId)
            ->where('daftar_praktikan_id', $daftarPraktikan->id)
            ->where('status_pengumpulan', PengumpulanTugas::STATUS_BELUM_DICEK)
            ->firstOrFail();

        $pengumpulan->update(['status_pengumpulan' => PengumpulanTugas::STATUS_DRAFT]);

        return back()->with('success', 'Pengiriman dibatalkan. Berkas tetap tersimpan dan dapat diperbarui.');
    }

    public function hapus(Request $request, string $tugasId)
    {
        $user  = auth()->user();
        $tugas = Tugas::with('modul')->findOrFail($tugasId);

        $daftarPraktikan = DaftarPraktikan::where('user_id', $user->id)
            ->where('praktikum_id', $tugas->modul->praktikum_id)
            ->firstOrFail();

        $tenggatMutlak = $tugas->deadline_acc ?? $tugas->deadline;
        if ($tenggatMutlak && now()->gt($tenggatMutlak)) {
            return back()->with('error', 'Tidak dapat menghapus pengiriman setelah tenggat waktu.');
        }

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugasId)
            ->where('daftar_praktikan_id', $daftarPraktikan->id)
            ->first();

        if (!$pengumpulan) {
            return back()->with('error', 'Tidak ada pengiriman yang ditemukan.');
        }

        // Hanya bisa hapus jika belum di-ACC
        if (in_array($pengumpulan->status_pengumpulan, [PengumpulanTugas::STATUS_ACC])) {
            return back()->with('error', 'Pengiriman yang sudah di-ACC tidak dapat dihapus.');
        }

        // Hapus semua riwayat dan record utama
        $pengumpulan->riwayat()->delete();
        $pengumpulan->delete();

        return back()->with('success', 'Pengiriman tugas berhasil dihapus.');
    }
}
