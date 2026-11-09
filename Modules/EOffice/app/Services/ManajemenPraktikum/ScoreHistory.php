<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Facades\{DB,Schema};

class ScoreHistory
{
    private array $cache=[];
    public function formatted(string $scope,string $id): array
    {
        $result=$this->entries($scope,$id);
        foreach($result as &$columns)foreach($columns as $key=>&$entries)foreach($entries as &$e){
            $format=fn($v)=>$v===null?'—':str_replace('.',',',rtrim(rtrim(number_format((float)$v,4,'.',''),'0'),'.'));
            $e=['editor'=>$e['oleh'],'date'=>\Illuminate\Support\Carbon::parse($e['waktu'])->translatedFormat('d F Y, H:i').' WIB','before'=>$format($e['sebelum'][$key]),'after'=>$format($e['sesudah'][$key]),'type'=>'penilaian'];
        }
        return $result;
    }

    public function record(string $practicum,string $scope,?string $module,string $student,string $key,$before,$after): void
    {
        if(!Schema::hasTable('eo_prak_score_histories') || ($before===null && $after===null) || ($before!==null && $after!==null && (string)\Brick\Math\BigRational::of((string)$before)->simplified()===(string)\Brick\Math\BigRational::of((string)$after)->simplified()))return;
        DB::table('eo_prak_score_histories')->insert(['praktikum_id'=>$practicum,'scope'=>$scope,'module_id'=>$module,'praktikan_id'=>$student,'score_key'=>$key,'editor_id'=>auth()->id(),'editor_name'=>auth()->user()?->name??'Sistem','before_value'=>$before,'after_value'=>$after,'created_at'=>now()]);
        $this->cache=[];
    }

    public function entries(string $scope,string $id): array
    {
        $cacheKey=$scope.'|'.$id;
        if(isset($this->cache[$cacheKey]))return $this->cache[$cacheKey];
        if(!Schema::hasTable('eo_prak_score_histories'))return [];
        $rows=DB::table('eo_prak_score_histories')->where('scope',$scope)->where($scope==='module'?'module_id':'praktikum_id',$id)->orderBy('id')->get();
        $result=[];
        foreach($rows as $row)$result[$row->praktikan_id][$row->score_key][]=['oleh'=>$row->editor_name,'editor_id'=>$row->editor_id,'waktu'=>$row->created_at,'jenis'=>'penilaian','sebelum'=>[$row->score_key=>$row->before_value],'sesudah'=>[$row->score_key=>$row->after_value]];
        return $this->cache[$cacheKey]=$result;
    }
}
