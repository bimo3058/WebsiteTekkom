<?php
namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{Praktikum,Modul,NilaiJenisTugas,TugasAkhir,TaNilai,TaAbsensi};

class NilaiCsvExport
{
    public function download(Request $request, Praktikum $praktikum, bool $publishedOnly = false)
    {
        if ($publishedOnly) {
            $enrolled = $praktikum->daftarPraktikan()->pluck('id');
            $published = \Modules\EOffice\Models\Nilai::whereIn('daftar_praktikan_id', $enrolled)->where('dipublikasikan', true)->pluck('daftar_praktikan_id');
            abort_unless($enrolled->isNotEmpty() && $enrolled->diff($published)->isEmpty(), 403, 'Nilai belum dipublikasikan untuk seluruh mahasiswa.');
        }
        $data=$request->validate(['cards'=>'required|array|min:1|max:100','cards.*'=>'required|string|distinct']);
        $ta=TugasAkhir::where('praktikum_id',$praktikum->id)->where('published',true)->first();
        $available=Modul::where('praktikum_id',$praktikum->id)->orderBy('urutan')->get();
        $allowed=$available->map(fn($m)=>(string)$m->getRawOriginal('id'))->all(); if($ta) $allowed[]='tugas_akhir';
        if(\Illuminate\Support\Facades\Schema::hasTable('eo_prak_final_results')) $allowed[]='nilai_akhir';
        if(array_diff($data['cards'],$allowed)) throw ValidationException::withMessages(['cards'=>'Pilihan card tidak tersedia pada praktikum ini.']);
        $moduls=$available->filter(fn($m)=>in_array((string)$m->getRawOriginal('id'),$data['cards'],true));
        $includeTa=in_array('tugas_akhir',$data['cards'],true);
        $finalReport=in_array('nilai_akhir',$data['cards'],true) ? app(FinalGradeBook::class)->report($praktikum,null,$publishedOnly) : null;
        if ($publishedOnly && $finalReport && !$finalReport['published']) abort(403, 'Nilai keseluruhan belum dipublikasikan.');
        if($finalReport && ($finalReport['errors'] || collect($finalReport['rows'])->contains(fn($r)=>!empty($r['errors'])))) throw ValidationException::withMessages(['nilai_akhir'=>'Rekap nilai keseluruhan belum dapat diekspor. Lengkapi bobot dan verifikasi angkatan pada halaman Absensi & Nilai.']);
        $students=$praktikum->daftarPraktikan()->with(['user.student','absensi'])->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")->orderBy('created_at')->get();
        $scores=NilaiJenisTugas::whereIn('modul_id',$moduls->map(fn($m)=>$m->getRawOriginal('id')))->get()->groupBy(fn($n)=>$n->modul_id.'|'.$n->daftar_praktikan_id);
        $grades=$includeTa ? TaNilai::where('tugas_akhir_id',$ta->id)->get()->keyBy('praktikan_id') : collect();
        if ($publishedOnly && $includeTa) {
            abort_unless($students->every(fn($student) => $grades->get($student->id)?->disetujui_koor && $grades->get($student->id)?->disetujui_dosen), 403, 'Nilai tugas akhir belum dipublikasikan.');
        }
        $attendance=$includeTa ? TaAbsensi::where('tugas_akhir_id',$ta->id)->get()->keyBy('praktikan_id') : collect();
        $documents=[];
        $filenamePart=fn($name)=>trim(preg_replace('/[\s<>:"\/\\\\|?*\x00-\x1F]+/u','_', $name), '._') ?: 'Praktikum';
        $practicumName=$filenamePart($praktikum->nama);
        foreach($moduls as $modul) $documents[]=['name'=>'Rekap_Nilai_'.$filenamePart($modul->nama).'_'.$practicumName.'.csv','modules'=>collect([$modul]),'ta'=>false];
        if($includeTa) $documents[]=['name'=>'Rekap_Nilai_Tugas_Akhir_'.$practicumName.'.csv','modules'=>collect(),'ta'=>true];
        if($finalReport) $documents[]=['name'=>'Rekap_Nilai_Nilai_Akhir_'.$practicumName.'.csv','modules'=>collect(),'ta'=>false,'final'=>true];
        $render=function($document) use($students,$scores,$ta,$grades,$attendance,$praktikum,$finalReport){
            $moduls=$document['modules']; $includeTa=$document['ta'];
            $file=fopen('php://temp','w+'); fwrite($file,"\xEF\xBB\xBF");
            if($document['final']??false){
                $extraColumns=$finalReport['components']??[];
                $safeHeader=fn($v)=>preg_match('/^[=+@\-\t\r]/',$v)?"'".$v:$v;
                fputcsv($file,array_map($safeHeader,array_merge(['NO','NAMA MAHASISWA','NIM','KELOMPOK','RATA-RATA MODUL ('.$finalReport['weights']['modules'].'%)','TUGAS AKHIR ('.$finalReport['weights']['ta'].'%)'],array_map(fn($c)=>mb_strtoupper($c['nama']).' ('.$c['bobot'].'%)',$extraColumns),['TOTAL','PREDIKAT'])));
                $number=0;
                foreach($finalReport['rows'] as $row) fputcsv($file,array_map(fn($v)=>is_string($v)&&preg_match('/^[=+@\-\t\r]/',$v)?"'".$v:$v,array_merge([++$number,$row['name'],$row['nim'],$row['group'],$row['module'],$row['ta']],array_map(fn($c)=>$row['extras'][$c['id']]??null,$extraColumns),[$row['final'],$row['letter']])));
                rewind($file);$contents=stream_get_contents($file);fclose($file);return $contents;
            }
            $components=$includeTa ? $ta->komponen : [];
            $header=['NO','NAMA MAHASISWA','NIM','KELOMPOK','SHIFT','KEHADIRAN'];
            if(!$includeTa) $header=array_merge($header,['TUGAS PENDAHULUAN','PRAKTIKUM',mb_strtoupper($praktikum->laporanLabel()),'RESPONSI']);
            if(!$includeTa) foreach($praktikum->komponen_modul ?? [] as $extra) $header[]=mb_strtoupper($extra['nama']);
            foreach($components as $c) $header[]=mb_strtoupper($c['nama']);
            fputcsv($file,array_merge($header,['KETERANGAN','RATA-RATA']));
            $safe=fn($v)=>is_string($v)&&preg_match('/^[=+@\-\t\r]/',$v) ? "'".$v : $v;
            foreach($students as $i=>$student){
                $base=[$i+1,$student->user?->name,$student->user?->student?->student_number ?? $student->user?->email,$student->kelompok,$student->shift];
                foreach($moduls as $modul){
                    $values=($scores->get($modul->getRawOriginal('id').'|'.$student->id) ?? collect())->pluck('nilai','jenis_tugas');
                    $presence=$student->absensi->firstWhere('modul_id',$modul->getRawOriginal('id'));
                    $average=$praktikum->moduleAverage($values->all()) ?? 'Bobot belum valid';
                    $extraValues=collect($praktikum->komponen_modul ?? [])->map(fn($c)=>$values[$c['id']]??0)->all();
                    $row=array_merge($base,[$presence?->status,$values['tugas_pendahuluan']??0,$values['tugas_pengganti']??0,$values['laporan']??0,$values['responsi']??0],$extraValues,[$presence?->keterangan,$average]);
                    fputcsv($file,array_map($safe,$row));
                }
                if($includeTa){
                    $grade=$grades->get($student->id); $presence=$attendance->get($student->id);
                    $values=collect($components)->map(fn($c)=>$grade?->skor[$c['id']]??0)->all();
                    $average=$ta->average($grade?->skor ?? []) ?? 'Bobot belum valid';
                    fputcsv($file,array_map($safe,array_merge($base,[$presence?->status],$values,[$presence?->keterangan,$average])));
                }
            } rewind($file);$contents=stream_get_contents($file);fclose($file);return $contents;
        };
        if(count($documents)===1) return response()->streamDownload(fn()=>print($render($documents[0])),$documents[0]['name'],['Content-Type'=>'text/csv; charset=UTF-8']);
        return response()->streamDownload(function() use($documents,$render){
            $path=tempnam(sys_get_temp_dir(),'nilai_csv_');
            try {
                $zip=new \ZipArchive;
                if($zip->open($path,\ZipArchive::CREATE|\ZipArchive::OVERWRITE)!==true) throw new \RuntimeException('Arsip unduhan tidak dapat dibuat.');
                foreach($documents as $document) { if(!$zip->addFromString($document['name'],$render($document))) throw new \RuntimeException('CSV tidak dapat ditambahkan ke arsip.'); }
                $zip->close();unset($zip);readfile($path);
            } finally { if(is_file($path)) @unlink($path); }
        },'Rekap_Nilai_'.$practicumName.'.zip',['Content-Type'=>'application/zip']);
    }
}
