<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Facades\{DB,Validator};
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{Praktikum,DaftarPraktikan,TaNilai,TugasAkhir};

class FinalAssessment
{
    public function locked(Praktikum $p): bool
    {
        return $p->moduleWeightsLocked() || TaNilai::whereIn('tugas_akhir_id',TugasAkhir::where('praktikum_id',$p->id)->select('id'))->where(fn($q)=>$q->where('disetujui_koor',true)->orWhere('disetujui_dosen',true))->exists();
    }

    public function save(Praktikum $praktikum,array $payload,string $role): void
    {
        $data=Validator::make($payload,[
            'version'=>'required|integer|min:0','settings'=>'nullable|array:modules,ta,components',
            'settings.modules'=>'required_with:settings|integer|min:0|max:100','settings.ta'=>'required_with:settings|integer|min:0|max:100',
            'settings.components'=>'present_with:settings|array|max:3','settings.components.*'=>'array:id,nama,bobot',
            'settings.components.*.id'=>'required|uuid|distinct','settings.components.*.nama'=>'required|string|max:100',
            'settings.components.*.bobot'=>'required|integer|min:1|max:100',
            'students'=>'present|array','students.*'=>'array:version,scores','students.*.version'=>'required|integer|min:0',
            'students.*.scores'=>'required|array','students.*.scores.*'=>['nullable','regex:/^\d{1,3}(\.\d{1,2})?$/','numeric','between:0,100'],
        ],[
            'students.*.scores.*.between'=>'Nilai harus berada antara 0 dan 100.',
            'students.*.scores.*.numeric'=>'Nilai harus berupa angka.',
            'students.*.scores.*.regex'=>'Masukkan nilai 0–100 dengan maksimal dua angka desimal.',
        ])->validate();
        DB::transaction(function()use($praktikum,$data,$role){
            $p=Praktikum::whereKey($praktikum->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($role,['koor','dosen']) && ($role==='koor'?(string)$p->koor_id===(string)auth()->id():$p->dosens()->where('users.id',auth()->id())->exists()),403);
            $this->check($p->is_active,'Praktikum telah diarsipkan.');
            $this->check(!$this->locked($p),'Batalkan persetujuan atau publikasi terlebih dahulu sebelum mengubah nilai keseluruhan.');
            $this->check((int)$data['version']===(int)$p->bobot_gabungan_version,'Pengaturan kolom telah berubah. Muat ulang halaman.');
            if(isset($data['settings'])){
                abort_unless($role==='koor',403);
                $settings=$data['settings'];$components=$settings['components'];
                foreach($components as &$c)$c['nama']=trim($c['nama']);unset($c);
                $names=array_map(fn($c)=>mb_strtolower($c['nama']),$components);
                $this->check(!in_array('',$names,true) && count(array_unique($names))===count($names),'Nama kolom wajib diisi dan tidak boleh sama.');
                $this->check((int)$settings['modules']+(int)$settings['ta']+array_sum(array_column($components,'bobot'))===100,'Total bobot modul, tugas akhir, dan kolom tambahan harus 100%.');
                $p->update(['final_components'=>$components,'bobot_gabungan_modul'=>$settings['modules'],'bobot_gabungan_ta'=>$settings['ta'],'bobot_gabungan_version'=>$p->bobot_gabungan_version+1]);
            }
            $keys=array_column($p->final_components??[],'id');
            $students=DaftarPraktikan::where('praktikum_id',$p->id)->pluck('id')->map(fn($id)=>(string)$id)->all();
            foreach($data['students'] as $student=>$input){
                $this->check(in_array((string)$student,$students,true),'Mahasiswa tidak terdaftar pada praktikum ini.');
                $this->check(!array_diff(array_keys($input['scores']),$keys),'Kolom penilaian tidak dikenal atau sudah dihapus.');
                $record=DB::table('eo_prak_extra_scores')->where('praktikum_id',$p->id)->where('praktikan_id',$student)->lockForUpdate()->first();
                $this->check((int)($record->version??0)===(int)$input['version'],'Nilai telah diubah pengguna lain. Muat ulang halaman.');
                $scores=$record?json_decode($record->scores,true):[];$changed=false;
                foreach($input['scores'] as $key=>$value){
                    $before=$scores[$key]??null;$after=$value===null?null:(string)$value;
                    if(($before===null && $after===null)||($before!==null && $after!==null && \Brick\Math\BigRational::of((string)$before)->isEqualTo(\Brick\Math\BigRational::of($after))))continue;
                    app(ScoreHistory::class)->record((string)$p->id,'final',null,(string)$student,$key,$before,$after);
                    $scores[$key]=$after;$changed=true;
                }
                if($changed)DB::table('eo_prak_extra_scores')->updateOrInsert(['praktikum_id'=>$p->id,'praktikan_id'=>$student],['scores'=>json_encode($scores),'version'=>($record->version??0)+1,'created_at'=>$record->created_at??now(),'updated_at'=>now()]);
            }
        });
    }

    private function check(bool $condition,string $message): void
    {
        if(!$condition)throw ValidationException::withMessages(['nilai_keseluruhan'=>$message]);
    }
}
