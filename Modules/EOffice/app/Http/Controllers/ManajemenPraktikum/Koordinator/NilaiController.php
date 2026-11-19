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
                'praktikum' => null,
                'daftarPraktikan' => collect(),
                'moduls' => collect(),
                'allModuls' => collect(),
                'modulFilter' => null,
                'nilaiJenisMap' => [],
            ]);
        }

        $modulFilter = $request->input('modul_id');

        $modulsQuery = Modul::with(['asprak.user'])->where('praktikum_id', $praktikum->id)
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

        $allModuls = Modul::with(['asprak.user'])->where('praktikum_id', $praktikum->id)->orderBy('urutan')->get();

        return view('eoffice::manajemen-praktikum.koordinator.nilai', compact(
            'praktikum',
            'daftarPraktikan',
            'moduls',
            'allModuls',
            'modulFilter',
            'nilaiJenisMap'
        ));
    }

    public function approve(Request $request)
    {
        $praktikum = DashboardController::resolvePraktikum() ?? abort(404);
        app(\Modules\EOffice\Services\ManajemenPraktikum\GradePublication::class)->approve($request,$praktikum,'koor');
        return back()->with('success', 'Seluruh nilai modul dan tugas akhir telah diajukan untuk persetujuan dosen.');
    }

    public function exportCsv(Request $request)
    {
        $praktikum = DashboardController::resolvePraktikum();
        if (!$praktikum)
            abort(404);

        if ($request->has('cards')) return app(\Modules\EOffice\Services\ManajemenPraktikum\NilaiCsvExport::class)->download($request,$praktikum);

        $modulFilter = $request->input('modul_id');
        $modulsQuery = Modul::with(['asprak.user'])->where('praktikum_id', $praktikum->id)->orderBy('urutan');
        if ($modulFilter)
            $modulsQuery->where('id', $modulFilter);
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
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Rekap_Nilai_{$praktikum->kode}.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $callback = function () use ($daftarPraktikan, $moduls, $nilaiJenisMap, $praktikum) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['No', 'Nama Praktikan', 'NIM', 'Kelompok', 'Shift', 'Modul', 'Kehadiran', 'Tugas Pendahuluan', $praktikum->laporanLabel(), 'Responsi', 'Tugas Pengganti', 'Keterangan']);

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

    public function unapprove(Request $request)
    {
        $praktikum=DashboardController::resolvePraktikum() ?? abort(404);
        app(\Modules\EOffice\Services\ManajemenPraktikum\GradePublication::class)->cancel($praktikum);
        return back()->with('success','Persetujuan dan publikasi nilai dibatalkan. Nilai perlu diajukan kembali setelah perubahan.');
    }

    public function updateBobot(Request $request, $id)
    {
        $request->validate([
            'bobot_tp' => 'required|integer|min:0|max:100',
            'bobot_praktikum' => 'required|integer|min:0|max:100',
            'bobot_laporan' => 'required|integer|min:0|max:100',
            'bobot_responsi' => 'required|integer|min:0|max:100',
            'module_version' => 'required|integer|min:0',
            'extra_weights' => 'nullable|array',
            'extra_weights.*' => 'required|integer|min:1|max:100',
            'final_weights' => 'sometimes|array:version,modules,ta',
            'final_weights.version' => 'required_with:final_weights|integer|min:0',
            'final_weights.modules' => 'required_with:final_weights|integer|min:0|max:100',
            'final_weights.ta' => 'required_with:final_weights|integer|min:0|max:100',
            'final_payload'=>'sometimes|json',
        ]);

        $praktikum = DashboardController::resolvePraktikum() ?? abort(404);
        abort_unless((string)$praktikum->id === (string)$id,403);
        
        // Ensure total is 100
        $total = $request->bobot_tp + $request->bobot_praktikum + $request->bobot_laporan + $request->bobot_responsi + array_sum($request->input('extra_weights',[]));
        if ($total !== 100) {
            return redirect()->back()->with('error', 'Total bobot harus 100%');
        }
        \Illuminate\Support\Facades\DB::transaction(function () use ($request,$praktikum) {
            $extraWeights=$request->input('extra_weights',[]);
            if (array_diff(array_keys($extraWeights),array_column($praktikum->komponen_modul ?? [],'id')) || count($extraWeights)!==count($praktikum->komponen_modul ?? [])) throw \Illuminate\Validation\ValidationException::withMessages(['bobot'=>'Pengaturan komponen telah berubah. Muat ulang halaman.']);
            $data=$request->only(['bobot_tp','bobot_praktikum','bobot_laporan','bobot_responsi']);
            $data['version']=$request->module_version;
            $data['tambahan']=[];
            foreach ($praktikum->komponen_modul ?? [] as $component) $data['tambahan'][substr($component['id'],-1)]=['nama'=>$component['nama'],'bobot'=>$extraWeights[$component['id']]];
            $changed=collect($request->only(['bobot_tp','bobot_praktikum','bobot_laporan','bobot_responsi']))->contains(fn ($value,$key)=>(int)$value !== (int)$praktikum->$key)
                || collect($praktikum->komponen_modul ?? [])->contains(fn ($c)=>(int)$extraWeights[$c['id']] !== (int)$c['bobot']);
            if ($changed) app(\Modules\EOffice\Services\ManajemenPraktikum\ModuleAssessment::class)->save($praktikum,$data);
            if ($request->has('ta_bobot')) app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\TugasAkhirController::class)->fromPracticumPage($request,$praktikum,'koor','weights');
            if ($request->has('final_weights')) app(FinalGradeController::class)->saveWeights($praktikum,$request->input('final_weights'));
            if($request->filled('final_payload')){
                $payload=json_decode($request->input('final_payload'),true);
                if(!is_array($payload))throw \Illuminate\Validation\ValidationException::withMessages(['final_payload'=>'Data nilai keseluruhan tidak valid.']);
                app(\Modules\EOffice\Services\ManajemenPraktikum\FinalAssessment::class)->save($praktikum,$payload,'koor');
            }
        });

        return redirect()->back()->with('success', 'Bobot nilai berhasil diperbarui.');
    }
}
