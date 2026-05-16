<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Absensi;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\Nilai;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $moduls = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->with('modul.praktikum')->get()->pluck('modul')->filter()->values()
            : collect();

        return view('eoffice::manajemen-praktikum.asprak.absensi', [
            'moduls'      => $moduls,
            'modulDiampu' => $moduls,
        ]);
    }

    public function show(Request $request, int $modulId)
    {
        $modul = Modul::with('praktikum')->findOrFail($modulId);
        $praktikans = DaftarPraktikan::where('praktikum_id', $modul->praktikum_id)->with('user')->get();
        $absensi = Absensi::where('modul_id', $modulId)->get()->keyBy('daftar_praktikan_id');

        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $moduls = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->with('modul.praktikum')->get()->pluck('modul')->filter()->values()
            : collect();

        return view('eoffice::manajemen-praktikum.asprak.absensi-show', compact('modul', 'praktikans', 'absensi', 'moduls'));
    }

    public function store(Request $request, int $modulId)
    {
        $request->validate([
            'absensi'           => 'required|array',
            'absensi.*.status'  => 'required|in:hadir,izin,tidak_hadir',
            'absensi.*.keterangan' => 'nullable|string|max:255',
            'tanggal'           => 'required|date',
        ]);

        foreach ($request->absensi as $daftarPraktikanId => $item) {
            Absensi::updateOrCreate(
                ['modul_id' => $modulId, 'daftar_praktikan_id' => $daftarPraktikanId],
                [
                    'tanggal' => $request->tanggal,
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]
            );
        }

        // Auto-sync persentase kehadiran ke nilai_praktikum
        $modul = Modul::findOrFail($modulId);
        $this->syncNilaiAbsensi($modul->praktikum_id);

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function update(Request $request, int $absensiId)
    {
        $request->validate([
            'status'     => 'required|in:hadir,izin,tidak_hadir',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $absensi = Absensi::findOrFail($absensiId);
        if (! $this->ownsModul((int) $absensi->modul_id)) {
            abort(403, 'Anda tidak berhak mengubah absensi ini.');
        }

        $absensi->update($request->only(['status', 'keterangan']));

        $modul = Modul::find($absensi->modul_id);
        if ($modul) {
            $this->syncNilaiAbsensi($modul->praktikum_id);
        }

        return back()->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy(int $absensiId)
    {
        $absensi = Absensi::findOrFail($absensiId);
        if (! $this->ownsModul((int) $absensi->modul_id)) {
            abort(403, 'Anda tidak berhak menghapus absensi ini.');
        }

        $modulId = $absensi->modul_id;
        $absensi->delete();

        $modul = Modul::find($modulId);
        if ($modul) {
            $this->syncNilaiAbsensi($modul->praktikum_id);
        }

        return back()->with('success', 'Absensi dihapus.');
    }

    /**
     * Hitung persen kehadiran per praktikan dan sync ke nilai_praktikum.nilai_absensi.
     * Skala 0-100 berdasarkan persentase hadir dari total pertemuan.
     */
    private function syncNilaiAbsensi(string $praktikumId): void
    {
        try {
            $daftarList = DaftarPraktikan::where('praktikum_id', $praktikumId)->get();

            foreach ($daftarList as $dp) {
                $totalAbsensi  = Absensi::where('daftar_praktikan_id', $dp->id)->count();
                $jumlahHadir   = Absensi::where('daftar_praktikan_id', $dp->id)->where('status', 'hadir')->count();
                $nilaiAbsensi  = $totalAbsensi > 0 ? round(($jumlahHadir / $totalAbsensi) * 100, 2) : 0;

                Nilai::updateOrCreate(
                    ['daftar_praktikan_id' => $dp->id],
                    ['nilai_absensi' => $nilaiAbsensi]
                );
            }
        } catch (\Throwable) {
            // Jangan crash
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
}
