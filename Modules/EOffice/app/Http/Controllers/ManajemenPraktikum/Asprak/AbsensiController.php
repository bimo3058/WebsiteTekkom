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
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\PengajuanModul;

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
            session()->now('warning', 'Belum ada modul yang ditugaskan kepada Anda. Tugas akhir yang sudah dipublikasikan tetap dapat dikelola.');
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
        $modul=Modul::with('praktikum')->findOrFail($modulId);
        abort_unless($this->ownsModul($modulId),403,'Anda tidak berhak mengubah absensi modul ini.');
        $types=array_keys($modul->praktikum->moduleTaskTypes());
        $rules=['absensi'=>'present|array','absensi.*.status'=>'nullable|in:hadir,terlambat,alpa,tidak_hadir','absensi.*.keterangan'=>'nullable|string|max:255','tanggal'=>'required|date','nilai'=>'nullable|array','nilai.*'=>'array'];
        foreach($types as $type) $rules['nilai.*.'.$type]='nullable|numeric|min:0|max:100';
        $data=$request->validate($rules);
        $ids=array_unique(array_merge(array_keys($data['absensi']),array_keys($data['nilai'] ?? [])));
        $allowed=DaftarPraktikan::where('praktikum_id',$modul->praktikum_id)->whereIn('id',$ids)->pluck('id')->map(fn($id)=>(string)$id)->all();
        if(array_diff($ids,$allowed)) throw \Illuminate\Validation\ValidationException::withMessages(['nilai'=>'Mahasiswa tidak terdaftar pada praktikum ini.']);
        foreach($data['nilai'] ?? [] as $values) if(array_diff(array_keys($values),$types)) throw \Illuminate\Validation\ValidationException::withMessages(['nilai'=>'Kolom penilaian telah berubah. Muat ulang halaman.']);
        \Illuminate\Support\Facades\DB::transaction(function() use($modul,$modulId,$data){
            $praktikum=Praktikum::whereKey($modul->praktikum_id)->lockForUpdate()->firstOrFail();
            if(!$praktikum->is_active) throw \Illuminate\Validation\ValidationException::withMessages(['nilai'=>'Praktikum telah diarsipkan.']);
            if(PengajuanModul::where('praktikum_id',$modul->praktikum_id)->whereIn('status',['draft','diajukan','perlu_revisi'])->exists()) throw \Illuminate\Validation\ValidationException::withMessages(['nilai'=>'Rancangan modul harus diselesaikan sebelum menyimpan absensi dan nilai.']);
            foreach($data['absensi'] as $id=>$item){
                if(empty($item['status'])){
                    if(filled($item['keterangan'] ?? null)) throw \Illuminate\Validation\ValidationException::withMessages(['absensi'=>'Pilih kehadiran sebelum mengisi keterangan.']);
                    Absensi::where('modul_id',$modulId)->where('daftar_praktikan_id',$id)->delete();
                }else Absensi::updateOrCreate(['modul_id'=>$modulId,'daftar_praktikan_id'=>$id],['tanggal'=>$data['tanggal'],'status'=>$item['status'],'keterangan'=>$item['keterangan'] ?? null]);
            }
            // Attendance is informational: a grade can be saved without an attendance selection.
            foreach($data['nilai'] ?? [] as $id=>$values) foreach($values as $type=>$score){
                $value=$score===null || $score==='' ? null : (float)$score;
                NilaiJenisTugas::updateOrCreate(['daftar_praktikan_id'=>$id,'modul_id'=>$modulId,'jenis_tugas'=>$type],['nilai'=>$value]);
                $task=\Modules\EOffice\Models\Tugas::where('modul_id',$modulId)->where('jenis_tugas',$type)->first();
                if($task){
                    $submission=\Modules\EOffice\Models\PengumpulanTugas::firstOrCreate(['tugas_id'=>$task->id,'daftar_praktikan_id'=>$id]);
                    $submission->update(['nilai'=>$value,'status_pengumpulan'=>$submission->status_pengumpulan==='revisi'?'revisi':($value!==null?'acc':'belum_dicek')]);
                }
            }
            if(collect($data['absensi'])->contains(fn($item)=>filled($item['status'] ?? null))) Praktikum::whereKey($modul->praktikum_id)->whereNull('started_at')->update(['status_pelaksanaan'=>'berjalan','started_at'=>now()]);
            $this->syncNilaiAbsensi($modul->praktikum_id);
        });
        return $request->expectsJson() ? response()->json(['message'=>'Absensi dan nilai berhasil disimpan.']) : back()->with('success','Absensi dan nilai berhasil disimpan.');
    }

    public function update(Request $request, int $absensiId)
    {
        $request->validate([
            'status' => 'nullable|in:hadir,terlambat,alpa,tidak_hadir',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $absensi = Absensi::findOrFail($absensiId);
        if (!$this->ownsModul((int) $absensi->modul_id)) {
            abort(403, 'Anda tidak berhak mengubah absensi ini.');
        }

        $modul = Modul::findOrFail($absensi->modul_id);
        $masihAdaRancangan = PengajuanModul::where('praktikum_id', $modul->praktikum_id)
            ->whereIn('status', ['draft', 'diajukan', 'perlu_revisi'])
            ->exists();
        if ($masihAdaRancangan) {
            return back()->with('error', 'Absensi belum dapat diisi karena rancangan modul masih perlu diselesaikan atau dibatalkan oleh koordinator.');
        }

        $absensi->update($request->only(['status', 'keterangan']));

        if (filled($absensi->status)) {
            Praktikum::whereKey($modul->praktikum_id)
                ->whereNull('started_at')
                ->update([
                    'status_pelaksanaan' => 'berjalan',
                    'started_at' => now(),
                ]);
        }
        $this->syncNilaiAbsensi($modul->praktikum_id);

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

        $selected=$moduls->map(fn($modul)=>(string)$modul->getRawOriginal('id'))->all();
        if ($request->filled('modul_id')) $selected=array_values(array_intersect($selected,[(string)$request->input('modul_id')]));
        $allowed=$selected;
        if (\Modules\EOffice\Models\TugasAkhir::where('praktikum_id',$praktikum->id)->where('published',true)->exists()) $allowed[]='tugas_akhir';
        if (\Illuminate\Support\Facades\Schema::hasTable('eo_prak_final_results')) $allowed[]='nilai_akhir';
        if ($request->has('cards')) {
            $data=$request->validate(['cards'=>'required|array|min:1','cards.*'=>'required|string|distinct']);
            abort_if((bool)array_diff($data['cards'],$allowed),403);
            $selected=$data['cards'];
        }
        if (!$selected) return back()->with('error','Tidak ada rekap yang dapat diekspor.');
        $request->merge(['cards'=>$selected]);
        return app(\Modules\EOffice\Services\ManajemenPraktikum\NilaiCsvExport::class)->download($request,$praktikum);
    }
}
