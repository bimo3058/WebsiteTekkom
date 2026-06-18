<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\NilaiJenisTugas;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Tugas;

class TugasController extends Controller
{
    public function __construct(private SupabaseStorage $supabase) {}

    public function index(Request $request)
    {
        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $modulIds = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->pluck('modul_id')
            : collect();

        $tugasList = Tugas::whereIn('modul_id', $modulIds)
            ->with(['modul.praktikum', 'pengumpulan.daftarPraktikan.user', 'pengumpulan.riwayat'])
            ->withCount([
                'pengumpulan',
                'pengumpulan as pengumpulan_acc_count'    => fn ($q) => $q->where('status_pengumpulan', 'acc'),
                'pengumpulan as pengumpulan_revisi_count' => fn ($q) => $q->where('status_pengumpulan', 'revisi'),
            ])
            ->orderByDesc('created_at')
            ->get();

        $praktikumIds = $tugasList->map(fn($t) => $t->modul?->praktikum_id)->filter()->unique();
        $allPraktikans = \Modules\EOffice\Models\DaftarPraktikan::whereIn('praktikum_id', $praktikumIds)
            ->with(['user', 'user.student'])
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at')
            ->get()
            ->groupBy('praktikum_id');

        foreach ($tugasList as $tugas) {
            $praktikumId = $tugas->modul?->praktikum_id;
            $tugas->praktikans = $praktikumId ? ($allPraktikans->get($praktikumId) ?? collect()) : collect();
            $tugas->pengumpulan_mapped = $tugas->pengumpulan->keyBy('daftar_praktikan_id');
        }

        return view('eoffice::manajemen-praktikum.asprak.tugas', compact('tugasList'));
    }

    public function create(Request $request)
    {
        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $moduls = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->with('modul.praktikum')->get()->pluck('modul')->filter()->values()
            : collect();

        return view('eoffice::manajemen-praktikum.asprak.tugas-create', compact('moduls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'modul_id'     => 'required|exists:modul_praktikum,id',
            'jenis_tugas'  => 'required|in:tugas_pendahuluan,praktikum,laporan,responsi',
            'judul'        => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'deadline'     => 'nullable|date',
            'deadline_acc' => 'nullable|date|after_or_equal:deadline',
            'is_published' => 'boolean',
            'file'         => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if (! $this->ownsModul((int) $request->modul_id)) {
            abort(403, 'Anda tidak di-assign ke modul ini.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $this->supabase->upload($request->file('file'), 'tugas-praktikum', 'eoffice');
        }

        $tugas = Tugas::create([
            ...$request->only(['modul_id', 'jenis_tugas', 'judul', 'deskripsi', 'deadline', 'deadline_acc', 'is_published']),
            'file_path' => $filePath,
        ]);

        // Auto-create pengumpulan_tugas if there are already scores in nilai_jenis_tugas
        $this->syncExistingNilaiJenisToPengumpulan($tugas);

        return redirect()->route('eoffice.manprak.asprak.tugas.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $tugas  = $this->findOwnedTugas($id, ['modul.praktikum']);
        $asprak = AsistenPraktikum::where('user_id', auth()->id())
            ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $moduls = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->with('modul.praktikum')->get()->pluck('modul')->filter()->values()
            : collect();

        return view('eoffice::manajemen-praktikum.asprak.tugas-edit', compact('tugas', 'moduls'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'jenis_tugas'  => 'required|in:tugas_pendahuluan,praktikum,laporan,responsi',
            'judul'        => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'deadline'     => 'nullable|date',
            'deadline_acc' => 'nullable|date|after_or_equal:deadline',
            'is_published' => 'boolean',
            'file'         => 'nullable|file|mimes:pdf|max:10240',
            'hapus_file'   => 'nullable|boolean',
        ]);

        $tugas = $this->findOwnedTugas($id);

        $filePath = $tugas->file_path;

        if ($request->boolean('hapus_file') || $request->hasFile('file')) {
            if ($tugas->file_path) {
                $this->supabase->delete($tugas->file_path, 'eoffice');
            }
            $filePath = null;
        }

        if ($request->hasFile('file')) {
            $filePath = $this->supabase->upload($request->file('file'), 'tugas-praktikum', 'eoffice');
        }

        $tugas->update([
            ...$request->only(['jenis_tugas', 'judul', 'deskripsi', 'deadline', 'deadline_acc', 'is_published']),
            'file_path' => $filePath,
        ]);

        // Auto-create/update pengumpulan_tugas if jenis_tugas changed or scores exist in nilai_jenis_tugas
        $this->syncExistingNilaiJenisToPengumpulan($tugas);

        return redirect()->route('eoffice.manprak.asprak.tugas.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $tugas = $this->findOwnedTugas($id);

        // Hapus file dari storage jika ada
        if ($tugas->file_path) {
            $this->supabase->delete($tugas->file_path, 'eoffice');
        }

        $tugas->delete();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function pengumpulan(int $id)
    {
        $tugas = $this->findOwnedTugas($id, ['modul.praktikum']);

        // Ambil semua praktikan terdaftar di praktikum tugas ini
        $praktikans = DaftarPraktikan::where('praktikum_id', $tugas->modul?->praktikum_id)
            ->with(['user', 'user.student'])
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at')
            ->get();

        // Ambil data pengumpulan tugas untuk tugas_id ini
        $pengumpulan = PengumpulanTugas::where('tugas_id', $id)
            ->with(['riwayat'])
            ->get()
            ->keyBy('daftar_praktikan_id');

        // Ambil nilai jenis tugas sesuai jenis_tugas tugas ini (dari tabel nilai_jenis_tugas)
        $nilaiJenis = NilaiJenisTugas::where('modul_id', $tugas->modul_id)
            ->where('jenis_tugas', $tugas->jenis_tugas)
            ->get()
            ->keyBy('daftar_praktikan_id');

        return view('eoffice::manajemen-praktikum.asprak.tugas-pengumpulan',
            compact('tugas', 'praktikans', 'pengumpulan', 'nilaiJenis')
        );
    }

    /**
     * Beri nilai sekaligus set status ACC.
     * Nilai tugas langsung masuk ke tabel nilai_praktikum (auto-sync).
     */
    public function beriNilai(Request $request, int $id)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $pengumpulan = PengumpulanTugas::with(['daftarPraktikan', 'tugas.modul.modulAsprak'])->findOrFail($id);
        if (! $this->ownsModul((int) $pengumpulan->tugas?->modul_id)) {
            abort(403, 'Anda tidak berhak menilai pengumpulan ini.');
        }

        $pengumpulan->update([
            'nilai'              => $request->nilai,
            'status_pengumpulan' => PengumpulanTugas::STATUS_ACC,
            'catatan_revisi'     => null,
            'is_revision'        => false,
        ]);

        // Sync nilai ke tabel nilai_jenis_tugas (two-way sync)
        $tugas = $pengumpulan->tugas;
        if ($tugas && $tugas->jenis_tugas) {
            NilaiJenisTugas::updateOrCreate(
                [
                    'daftar_praktikan_id' => $pengumpulan->daftar_praktikan_id,
                    'modul_id'            => $tugas->modul_id,
                    'jenis_tugas'         => $tugas->jenis_tugas,
                ],
                ['nilai' => $request->nilai]
            );
        }

        // Auto-sync nilai ke tabel nilai_praktikum
        $this->syncNilaiTugas($pengumpulan);

        return back()->with('success', 'Nilai berhasil disimpan dan status diubah ke ACC.');
    }

    /**
     * Beri revisi — kirim catatan, set status revisi, boleh lampirkan file balik.
     */
    public function beriRevisi(Request $request, int $id)
    {
        $request->validate([
            'catatan_revisi' => 'required|string',
            'file_revisi'    => 'nullable|file|mimes:pdf,docx,doc,zip,rar|max:10240',
        ]);

        $pengumpulan = PengumpulanTugas::with('tugas.modul')->findOrFail($id);
        if (! $this->ownsModul((int) $pengumpulan->tugas?->modul_id)) {
            abort(403, 'Anda tidak berhak merevisi pengumpulan ini.');
        }

        $filePath = $pengumpulan->file_revisi_asprak;
        if ($request->hasFile('file_revisi')) {
            // Hapus file revisi lama jika ada
            if ($pengumpulan->file_revisi_asprak) {
                try {
                    $this->supabase->delete($pengumpulan->file_revisi_asprak, 'eoffice');
                } catch (\Throwable $e) {
                    // Ignore storage deletion errors
                }
            }

            $filePath = $this->supabase->upload(
                $request->file('file_revisi'),
                'tugas-revisi/' . ($pengumpulan->tugas?->modul?->praktikum_id ?? 'default') . '/' . $pengumpulan->tugas_id,
                'eoffice'
            );
        }

        $pengumpulan->update([
            'catatan_revisi'     => $request->catatan_revisi,
            'file_revisi_asprak' => $filePath,
            'is_revision'        => true,
            'status_pengumpulan' => PengumpulanTugas::STATUS_REVISI,
        ]);

        return back()->with('success', 'Revisi berhasil dikirim ke mahasiswa.');
    }

    /**
     * Update nilai jenis tugas dari halaman Lihat Pengumpulan (two-way sync).
     * Menyimpan ke nilai_jenis_tugas — sumber kebenaran tunggal.
     */
    public function updateNilaiJenis(Request $request, int $id)
    {
        $request->validate([
            'nilai'                => 'required|array',
            'nilai.*.nilai_jenis'  => 'nullable|numeric|min:0|max:100',
        ]);

        $tugas = $this->findOwnedTugas($id, ['modul']);

        if (! $tugas->jenis_tugas) {
            return back()->with('error', 'Tugas ini belum memiliki jenis tugas.');
        }

        foreach ($request->nilai as $daftarPraktikanId => $item) {
            $nilaiValue = isset($item['nilai_jenis']) && $item['nilai_jenis'] !== ''
                ? (float) $item['nilai_jenis']
                : null;

            NilaiJenisTugas::updateOrCreate(
                [
                    'daftar_praktikan_id' => $daftarPraktikanId,
                    'modul_id'            => $tugas->modul_id,
                    'jenis_tugas'         => $tugas->jenis_tugas,
                ],
                ['nilai' => $nilaiValue]
            );

            // Sync ke pengumpulan_tugas
            $pengumpulan = PengumpulanTugas::firstOrCreate([
                'tugas_id' => $tugas->id,
                'daftar_praktikan_id' => $daftarPraktikanId,
            ]);
            $pengumpulan->update([
                'nilai' => $nilaiValue,
                'status_pengumpulan' => $nilaiValue !== null ? 'acc' : 'belum_dicek',
            ]);
        }

        // Hitung ulang rata-rata nilai tugas untuk di-sync ke tabel Nilai (koor)
        foreach ($request->nilai as $daftarPraktikanId => $item) {
            $pengumpulan = PengumpulanTugas::where('tugas_id', $tugas->id)
                ->where('daftar_praktikan_id', $daftarPraktikanId)
                ->first();
            if ($pengumpulan) {
                $this->syncNilaiTugas($pengumpulan);
            }
        }

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    /**
     * Helper: Jika form tugas baru dibuat, ambil semua nilai di nilai_jenis_tugas yang sudah ada
     * dan buatkan form pengumpulan_tugas otomatis dengan nilai tersebut.
     */
    private function syncExistingNilaiJenisToPengumpulan(Tugas $tugas): void
    {
        if (! $tugas->jenis_tugas) return;

        $existingNilai = NilaiJenisTugas::where('modul_id', $tugas->modul_id)
            ->where('jenis_tugas', $tugas->jenis_tugas)
            ->whereNotNull('nilai')
            ->get();

        foreach ($existingNilai as $nj) {
            $pengumpulan = PengumpulanTugas::firstOrCreate([
                'tugas_id'            => $tugas->id,
                'daftar_praktikan_id' => $nj->daftar_praktikan_id,
            ]);

            // Jika belum ada nilainya di pengumpulan_tugas, update
            if ($pengumpulan->nilai === null) {
                $pengumpulan->update([
                    'nilai'              => $nj->nilai,
                    'status_pengumpulan' => 'acc',
                ]);
            }

            $this->syncNilaiTugas($pengumpulan);
        }
    }

    /**
     * Sync nilai tugas rata-rata ke tabel nilai_praktikum.
     * Nilai akhir dihitung dari rata-rata nilai semua tugas pada praktikum.
     */
    private function syncNilaiTugas(PengumpulanTugas $pengumpulan): void
    {
        try {
            $daftarPraktikanId = $pengumpulan->daftar_praktikan_id;

            // Rata-rata nilai seluruh tugas yang sudah dinilai untuk praktikan ini
            $rataRataNilaiTugas = PengumpulanTugas::where('daftar_praktikan_id', $daftarPraktikanId)
                ->whereNotNull('nilai')
                ->avg('nilai');

            \Modules\EOffice\Models\Nilai::updateOrCreate(
                ['daftar_praktikan_id' => $daftarPraktikanId],
                ['nilai_tugas' => round($rataRataNilaiTugas, 2)]
            );
        } catch (\Throwable) {
            // Jangan crash karena gagal sync
        }
    }

    private function ownsModul(int $modulId): bool
    {
        return ModulAsprak::whereHas('asprak', fn($q) => $q
            ->where('user_id', auth()->id())
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
        )->where('modul_id', $modulId)->exists();
    }

    private function findOwnedTugas(int $id, array $with = []): Tugas
    {
        $query = Tugas::with($with)->where('id', $id)
            ->whereHas('modul.modulAsprak.asprak', function ($q) {
                $q->where('user_id', auth()->id())
                    ->where('role', 'asprak')
                    ->whereNull('deleted_at');
            });

        return $query->firstOrFail();
    }
}
