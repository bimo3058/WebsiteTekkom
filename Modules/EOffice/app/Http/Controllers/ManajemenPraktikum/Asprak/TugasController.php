<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Tugas;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $modulIds = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->pluck('modul_id')
            : collect();

        $tugasList = Tugas::whereIn('modul_id', $modulIds)
            ->with(['modul.praktikum', 'pengumpulan.daftarPraktikan.user'])
            ->withCount([
                'pengumpulan',
                'pengumpulan as pengumpulan_acc_count'    => fn ($q) => $q->where('status_pengumpulan', 'acc'),
                'pengumpulan as pengumpulan_revisi_count' => fn ($q) => $q->where('status_pengumpulan', 'revisi'),
            ])
            ->orderByDesc('created_at')
            ->get();

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
            'judul'        => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'deadline'     => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        if (! $this->ownsModul((int) $request->modul_id)) {
            abort(403, 'Anda tidak di-assign ke modul ini.');
        }

        Tugas::create($request->only(['modul_id', 'judul', 'deskripsi', 'deadline', 'is_published']));

        return redirect()->route('eoffice.manprak.asprak.tugas.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'judul'        => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'deadline'     => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        $tugas = $this->findOwnedTugas($id);
        $tugas->update($request->only(['judul', 'deskripsi', 'deadline', 'is_published']));

        return back()->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->findOwnedTugas($id)->delete();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function pengumpulan(int $id)
    {
        $tugas = $this->findOwnedTugas($id, ['modul.praktikum']);
        $pengumpulan = PengumpulanTugas::where('tugas_id', $id)
            ->with('daftarPraktikan.user')
            ->orderByDesc('created_at')
            ->get();

        return view('eoffice::manajemen-praktikum.asprak.tugas-pengumpulan', compact('tugas', 'pengumpulan'));
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
        ]);

        $pengumpulan = PengumpulanTugas::with('tugas')->findOrFail($id);
        if (! $this->ownsModul((int) $pengumpulan->tugas?->modul_id)) {
            abort(403, 'Anda tidak berhak merevisi pengumpulan ini.');
        }

        $pengumpulan->update([
            'catatan_revisi'     => $request->catatan_revisi,
            'is_revision'        => true,
            'status_pengumpulan' => PengumpulanTugas::STATUS_REVISI,
        ]);

        return back()->with('success', 'Revisi berhasil dikirim ke mahasiswa.');
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
