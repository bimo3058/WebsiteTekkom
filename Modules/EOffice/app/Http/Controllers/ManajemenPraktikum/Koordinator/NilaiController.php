<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\Nilai;
use Modules\EOffice\Models\NilaiJenisTugas;
use Modules\EOffice\Models\Praktikum;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $praktikum = DashboardController::resolvePraktikum();

        if (!$praktikum) {
            return view('eoffice::manajemen-praktikum.koordinator.nilai', [
                'praktikum'       => null,
                'daftarPraktikan' => collect(),
                'moduls'          => collect(),
                'allModuls'       => collect(),
                'modulFilter'     => null,
                'nilaiJenisMap'   => [],
            ]);
        }

        $modulFilter = $request->input('modul_id');

        $modulsQuery = Modul::where('praktikum_id', $praktikum->id)
            ->orderBy('urutan');

        if ($modulFilter) {
            $modulsQuery->where('id', $modulFilter);
        }

        $moduls = $modulsQuery->get();

        $daftarPraktikan = DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->with(['user', 'user.student', 'nilai', 'absensi'])
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at')
            ->get();

        // Ambil semua nilai_jenis_tugas untuk modul-modul yang tampil
        $modulIds = $moduls->pluck('id')->toArray();
        $nilaiJenisAll = NilaiJenisTugas::whereIn('modul_id', $modulIds)->get();

        // Susun index: nilaiJenisMap[modul_id][daftar_praktikan_id][jenis_tugas] => nilai
        $nilaiJenisMap = [];
        foreach ($nilaiJenisAll as $nj) {
            $nilaiJenisMap[$nj->modul_id][$nj->daftar_praktikan_id][$nj->jenis_tugas] = $nj->nilai;
        }

        $allModuls = Modul::where('praktikum_id', $praktikum->id)->orderBy('urutan')->get();

        return view('eoffice::manajemen-praktikum.koordinator.nilai', compact(
            'praktikum', 'daftarPraktikan', 'moduls', 'allModuls', 'modulFilter', 'nilaiJenisMap'
        ));
    }

    public function approve(Request $request)
    {
        $praktikum = DashboardController::resolvePraktikum() ?? abort(404);

        $daftarIds = DaftarPraktikan::where('praktikum_id', $praktikum->id)->pluck('id');

        $nilais = Nilai::whereIn('daftar_praktikan_id', $daftarIds)->get();

        foreach ($nilais as $nilai) {
            $nilai->disetujui_koor = true;
            if ($nilai->disetujui_dosen && $nilai->disetujui_koor) {
                $nilai->dipublikasikan = true;
            }
            $nilai->save();
        }

        return back()->with('success', 'Nilai telah disetujui oleh koordinator.');
    }

    public function exportCsv(Request $request)
    {
        $praktikum = DashboardController::resolvePraktikum();
        if (!$praktikum) abort(404);

        $modulFilter = $request->input('modul_id');
        $modulsQuery = Modul::where('praktikum_id', $praktikum->id)->orderBy('urutan');
        if ($modulFilter) $modulsQuery->where('id', $modulFilter);
        $moduls = $modulsQuery->get();

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
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=Rekap_Nilai_{$praktikum->kode}.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $callback = function() use($daftarPraktikan, $moduls, $nilaiJenisMap) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['No', 'Nama Praktikan', 'NIM', 'Kelompok', 'Shift', 'Modul', 'Kehadiran', 'Tugas Pendahuluan', 'Praktikum', 'Laporan', 'Responsi', 'Keterangan']);

            $no = 1;
            foreach ($daftarPraktikan as $dp) {
                foreach ($moduls as $m) {
                    $absensi = $dp->absensi->firstWhere('modul_id', $m->id);
                    $njMap   = $nilaiJenisMap[$m->id][$dp->id] ?? [];
                    $row = [
                        $no,
                        $dp->user?->name ?? '-',
                        $dp->user?->student?->student_number ?? $dp->user?->email ?? '-',
                        $dp->kelompok ?? '-',
                        $dp->shift ?? '-',
                        $m->nama,
                        $absensi ? ucfirst($absensi->status) : '-',
                        $njMap['tugas_pendahuluan'] ?? '-',
                        $njMap['praktikum'] ?? '-',
                        $njMap['laporan'] ?? '-',
                        $njMap['responsi'] ?? '-',
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