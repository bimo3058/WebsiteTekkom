<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{Praktikum,Modul,DaftarPraktikan,Nilai,TugasAkhir,TaNilai};
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\TugasAkhirController;

class GradePublication
{
    public function approve(Request $request,Praktikum $praktikum,string $role): void
    {
        abort_unless(in_array($role,['koor','dosen']),403);
        DB::transaction(function() use($request,$praktikum,$role){
            $current=Praktikum::whereKey($praktikum->id)->lockForUpdate()->firstOrFail();
            abort_unless($role==='koor' ? (string)$current->koor_id===(string)auth()->id() : $current->dosens()->where('users.id',auth()->id())->exists(),403);
            $this->check((bool)$current->is_active,'Praktikum telah diarsipkan dan tidak dapat diubah.');
            $ids=DaftarPraktikan::where('praktikum_id',$current->id)->pluck('id');
            $modules=Modul::where('praktikum_id',$current->id)->exists();
            $grades=Nilai::whereIn('daftar_praktikan_id',$ids)->lockForUpdate()->get();
            if($modules){
                $this->check($current->moduleWeightsValid(),'Lengkapi bobot seluruh kolom modul dengan total 100%.');
                $this->check($ids->isNotEmpty() && $ids->diff($grades->pluck('daftar_praktikan_id'))->isEmpty(),'Nilai modul belum tersedia untuk seluruh mahasiswa. Lengkapi penilaian sebelum mengajukan persetujuan.');
            }
            $ta=TugasAkhir::where('praktikum_id',$current->id)->lockForUpdate()->first();
            $taGrades=$ta && $ta->published ? TaNilai::where('tugas_akhir_id',$ta->id)->whereIn('praktikan_id',$ids)->lockForUpdate()->get() : collect();
            if($ta && $ta->published) $this->check($ids->isNotEmpty() && $ids->diff($taGrades->pluck('praktikan_id'))->isEmpty(),'Nilai tugas akhir belum tersedia untuk seluruh mahasiswa. Lengkapi penilaian sebelum mengajukan persetujuan.');
            $this->check($modules || ($ta && $ta->published),'Belum ada nilai yang dapat diajukan.');
            if($role==='dosen') $this->check($grades->every(fn($g)=>$g->disetujui_koor) && $taGrades->every(fn($g)=>$g->disetujui_koor),'Koordinator harus menyetujui seluruh nilai modul dan tugas akhir terlebih dahulu.');
            if(\Illuminate\Support\Facades\Schema::hasTable('eo_prak_grade_batches')) {
                $this->check($modules && $ta && $ta->published,'Nilai akhir memerlukan modul dan tugas akhir yang sudah dipublikasikan.');
                $book=app(FinalGradeBook::class);
                if($role==='koor') $book->propose($current); else $book->publish($current);
            }
            if($role==='koor') {
                Nilai::whereIn('id',$grades->pluck('id'))->update(['disetujui_dosen'=>false,'dipublikasikan'=>false]);
                TaNilai::whereIn('id',$taGrades->pluck('id'))->update(['disetujui_dosen'=>false]);
            }
            app(TugasAkhirController::class)->fromPracticumPage($request,$current,$role,'approve');
            Nilai::whereIn('id',$grades->pluck('id'))->update($role==='koor'
                ? ['disetujui_koor'=>true,'disetujui_dosen'=>false,'dipublikasikan'=>false]
                : ['disetujui_dosen'=>true,'dipublikasikan'=>true]);
        });
    }

    public function cancel(Praktikum $praktikum): void
    {
        DB::transaction(function() use($praktikum){
            $current=Praktikum::whereKey($praktikum->id)->lockForUpdate()->firstOrFail();
            abort_unless((string)$current->koor_id===(string)auth()->id() || $current->dosens()->where('users.id',auth()->id())->exists(),403);
            $this->check((bool)$current->is_active,'Praktikum telah diarsipkan dan tidak dapat diubah.');
            $ids=DaftarPraktikan::where('praktikum_id',$praktikum->id)->pluck('id');
            Nilai::whereIn('daftar_praktikan_id',$ids)->update(['disetujui_koor'=>false,'disetujui_dosen'=>false,'dipublikasikan'=>false]);
            $ta=TugasAkhir::where('praktikum_id',$praktikum->id)->first();
            if($ta) TaNilai::where('tugas_akhir_id',$ta->id)->update(['disetujui_koor'=>false,'disetujui_dosen'=>false]);
            if(\Illuminate\Support\Facades\Schema::hasTable('eo_prak_grade_batches')) DB::table('eo_prak_grade_batches')->where('praktikum_id',$praktikum->id)->where('status','pending')->update(['status'=>'withdrawn']);
        });
    }

    private function check(bool $condition,string $message): void
    {
        if(!$condition) throw ValidationException::withMessages(['publikasi_nilai'=>$message]);
    }
}
