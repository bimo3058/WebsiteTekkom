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
use Modules\EOffice\Models\NilaiJenisTugas;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $moduls = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->with(['modul.praktikum', 'modul.asprak.user'])->get()->pluck('modul')->filter()->values()
            : collect();

        if ($asprak && $moduls->isEmpty()) {
            session()->now('error', 'Akses terbatas: Anda belum di-assign sebagai pengampu pada modul manapun di praktikum ini.');
        }

        $praktikum = $asprak ? $asprak->praktikum : null;

        $daftarPraktikan = collect();
        $nilaiJenisMap = [];

        if ($praktikum) {
            $daftarPraktikan = DaftarPraktikan::where('praktikum_id', $praktikum->id)
                ->with(['user', 'user.student', 'nilai', 'absensi'])
                ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
                ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
                ->orderBy('created_at')
                ->get();

            $modulIds = $moduls->pluck('id')->toArray();
            $nilaiJenisAll = NilaiJenisTugas::whereIn('modul_id', $modulIds)->get();

            foreach ($nilaiJenisAll as $nj) {
                $nilaiJenisMap[$nj->modul_id][$nj->daftar_praktikan_id][$nj->jenis_tugas] = $nj->nilai;
            }
        }

        return view('eoffice::manajemen-praktikum.asprak.absensi', [
            'moduls' => $moduls,
            'modulDiampu' => $moduls,
            'praktikum' => $praktikum,
            'daftarPraktikan' => $daftarPraktikan,
            'nilaiJenisMap' => $nilaiJenisMap,
        ]);
    }

    public function show(Request $request, int $modulId)
    {
        $modul = Modul::with('praktikum')->findOrFail($modulId);

        $praktikans = DaftarPraktikan::where('praktikum_id', $modul->praktikum_id)
            ->with(['user', 'user.student'])
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at')
            ->get();

        $absensi = Absensi::where('modul_id', $modulId)->get()->keyBy('daftar_praktikan_id');

        // Ambil nilai per jenis tugas untuk modul ini, di-index per daftar_praktikan_id
        $nilaiJenis = NilaiJenisTugas::where('modul_id', $modulId)
            ->get()
            ->groupBy('daftar_praktikan_id')
            ->map(fn($rows) => $rows->keyBy('jenis_tugas'));

        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $moduls = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->with(['modul.praktikum', 'modul.asprak.user'])->get()->pluck('modul')->filter()->values()
            : collect();

        $praktikum = $asprak ? $asprak->praktikum : null;

        return view(
            'eoffice::manajemen-praktikum.asprak.absensi-show',
            compact('modul', 'praktikans', 'absensi', 'moduls', 'nilaiJenis', 'praktikum')
        );
    }

    public function store(Request $request, int $modulId)
    {
        $request->validate([
            'absensi' => 'required|array',
            'absensi.*.status' => 'required|in:hadir,terlambat,alpa',
            'absensi.*.keterangan' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'nilai' => 'nullable|array',
            'nilai.*.tugas_pendahuluan' => 'nullable|numeric|min:0|max:100',
            'nilai.*.laporan' => 'nullable|numeric|min:0|max:100',
            'nilai.*.responsi' => 'nullable|numeric|min:0|max:100',
            'nilai.*.tugas_pengganti' => 'nullable|numeric|min:0|max:100',
        ]);

        $jenisList = ['tugas_pendahuluan', 'laporan', 'responsi', 'tugas_pengganti'];

        foreach ($request->absensi as $daftarPraktikanId => $item) {
            $status = $item['status'];

            Absensi::updateOrCreate(
                ['modul_id' => $modulId, 'daftar_praktikan_id' => $daftarPraktikanId],
                [
                    'tanggal' => $request->tanggal,
                    'status' => $status,
                    'keterangan' => $item['keterangan'] ?? null,
                ]
            );

            // Jika ada data nilai, update nilai_jenis_tugas & pengumpulan_tugas
            if ($request->has("nilai.{$daftarPraktikanId}") && in_array($status, ['hadir', 'terlambat'])) {
                $nilaiData = $request->nilai[$daftarPraktikanId];

                foreach ($jenisList as $jenis) {
                    $nilaiValue = isset($nilaiData[$jenis]) && $nilaiData[$jenis] !== ''
                        ? (float) $nilaiData[$jenis]
                        : null;

                    // 1. Update source of truth (nilai_jenis_tugas)
                    NilaiJenisTugas::updateOrCreate(
                        [
                            'daftar_praktikan_id' => $daftarPraktikanId,
                            'modul_id' => $modulId,
                            'jenis_tugas' => $jenis,
                        ],
                        ['nilai' => $nilaiValue]
                    );

                    // 2. Sync to pengumpulan_tugas if Tugas exists
                    $tugas = \Modules\EOffice\Models\Tugas::where('modul_id', $modulId)
                        ->where('jenis_tugas', $jenis)
                        ->first();

                    if ($tugas) {
                        $pengumpulan = \Modules\EOffice\Models\PengumpulanTugas::firstOrCreate(
                            [
                                'tugas_id' => $tugas->id,
                                'daftar_praktikan_id' => $daftarPraktikanId,
                            ]
                        );
                        $pengumpulan->update([
                            'nilai' => $nilaiValue,
                            'status_pengumpulan' => $nilaiValue !== null ? 'acc' : 'belum_dicek',
                        ]);
                    }
                }
            }
        }

        // Auto-sync persentase kehadiran ke nilai_praktikum
        $modul = Modul::findOrFail($modulId);
        $this->syncNilaiAbsensi($modul->praktikum_id);

        return back()->with('success', 'Absensi dan Nilai berhasil disimpan.');
    }

    public function update(Request $request, int $absensiId)
    {
        $request->validate([
            'status' => 'required|in:hadir,terlambat,alpa',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $absensi = Absensi::findOrFail($absensiId);
        if (!$this->ownsModul((int) $absensi->modul_id)) {
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
        if (!$this->ownsModul((int) $absensi->modul_id)) {
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
     */
    private function syncNilaiAbsensi(string $praktikumId): void
    {
        try {
            $daftarList = DaftarPraktikan::where('praktikum_id', $praktikumId)->get();

            foreach ($daftarList as $dp) {
                $totalAbsensi = Absensi::where('daftar_praktikan_id', $dp->id)->count();
                $jumlahHadir = Absensi::where('daftar_praktikan_id', $dp->id)->where('status', 'hadir')->count();
                $nilaiAbsensi = $totalAbsensi > 0 ? round(($jumlahHadir / $totalAbsensi) * 100, 2) : 0;

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
        return ModulAsprak::whereHas(
            'asprak',
            fn($q) => $q
                ->where('user_id', auth()->id())
                ->where('role', 'asprak')
                ->whereNull('deleted_at')
        )->where('modul_id', $modulId)->exists();
    }

    public function exportCsv(Request $request)
    {
        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $moduls = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->with(['modul.praktikum', 'modul.asprak.user'])->get()->pluck('modul')->filter()->values()
            : collect();

        $praktikum = $asprak ? $asprak->praktikum : null;
        if (!$praktikum) abort(404);

        $daftarPraktikan = DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->with(['user', 'user.student', 'absensi'])
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at')
            ->get();

        $modulIds = $moduls->pluck('id')->toArray();
        $nilaiJenisAll = NilaiJenisTugas::whereIn('modul_id', $modulIds)->get();
        $nilaiJenisMap = [];
        foreach ($nilaiJenisAll as $nj) {
            $nilaiJenisMap[$nj->modul_id][$nj->daftar_praktikan_id][$nj->jenis_tugas] = $nj->nilai;
        }

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Rekap_Nilai_{$praktikum->kode}.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($daftarPraktikan, $moduls, $nilaiJenisMap) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Praktikan', 'NIM', 'Kelompok', 'Shift', 'Modul', 'Kehadiran', 'Tugas Pendahuluan', 'Laporan', 'Responsi', 'Tugas Pengganti', 'Keterangan']);

            $no = 1;
            foreach ($daftarPraktikan as $dp) {
                foreach ($moduls as $m) {
                    $absensi = $dp->absensi->firstWhere('modul_id', $m->id);
                    $njMap = $nilaiJenisMap[$m->id][$dp->id] ?? [];
                    $row = [
                        $no,
                        $dp->user?->name ?? '-',
                        $dp->user?->student?->student_number ?? $dp->user?->email ?? '-',
                        $dp->kelompok ?? '-',
                        $dp->shift ?? '-',
                        $m->nama,
                        $absensi ? ucfirst($absensi->status) : '-',
                        $njMap['tugas_pendahuluan'] ?? '-',
                        $njMap['laporan'] ?? '-',
                        $njMap['responsi'] ?? '-',
                        $njMap['tugas_pengganti'] ?? '-',
                        $absensi?->keterangan ?? '-',
                    ];
                    fputcsv($file, $row);
                }
                $no++;
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
