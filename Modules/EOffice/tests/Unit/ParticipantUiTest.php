<?php

namespace Modules\EOffice\Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Blade,DB,Schema};
use Modules\EOffice\Models\{DaftarPraktikan,Praktikum,TaNilai,TaPengumpulan,TugasAkhir};
use Modules\EOffice\Services\ManajemenPraktikum\ParticipantListFilters;
use Tests\TestCase;

class ParticipantUiTest extends TestCase
{
    public function test_filters_apply_before_pagination_and_preserve_practicum_scope(): void
    {
        config(['database.connections.participant_test'=>['driver'=>'sqlite','database'=>':memory:']]);
        DB::setDefaultConnection('participant_test');
        Schema::create('users',function(Blueprint $t){$t->id();$t->string('name');$t->string('email');$t->softDeletes();});
        Schema::create('students',function(Blueprint $t){$t->id();$t->integer('user_id');$t->string('student_number');});
        Schema::create('daftar_praktikan',function(Blueprint $t){$t->id();$t->string('praktikum_id');$t->integer('user_id');$t->string('kelompok');$t->string('shift');});
        foreach(range(1,25) as $i){
            DB::table('users')->insert(['id'=>$i,'name'=>'Mahasiswa '.$i,'email'=>'student'.$i.'@test.local']);
            DB::table('students')->insert(['user_id'=>$i,'student_number'=>'21120123'.str_pad((string)$i,6,'0',STR_PAD_LEFT)]);
            DB::table('daftar_praktikan')->insert(['praktikum_id'=>$i===25?'other':'active','user_id'=>$i,'kelompok'=>$i>20?'12':'1','shift'=>$i>20?'3':'1']);
        }
        $query=fn($params)=>ParticipantListFilters::apply(DaftarPraktikan::where('praktikum_id','active'),Request::create('/praktikan','GET',$params));
        $page=$query(['kelompok'=>'12','shift'=>'3'])->paginate(2);
        $this->assertSame(4,$page->total());
        $this->assertCount(2,$page->items());
        $this->assertSame(24,$query(['search'=>'000024'])->first()->user_id);
        $this->assertSame(0,$query(['search'=>'000025'])->count());
        $this->assertSame(1,$query(['search'=>'MAHASISWA 24','kelompok'=>'12','shift'=>'3'])->count());
        $this->assertSame(0,$query(['kelompok'=>'missing'])->count());
        $this->assertSame(24,$query(['kelompok'=>'','shift'=>''])->count());
    }

    public function test_student_stage_badges_match_submission_states_without_exposing_unpublished_scores(): void
    {
        $stage=['id'=>'stage','deadline'=>now()->addDays(5)->toIso8601String()];
        $ta=new TugasAkhir(['tahap'=>[$stage],'komponen'=>[['id'=>'score','nama'=>'Laporan','bobot'=>100,'tahap_id'=>'stage']]]);
        $submission=new TaPengumpulan(['status'=>'dikumpulkan','riwayat'=>[['jenis'=>'pengumpulan','kategori'=>'awal']]]);
        $grade=new TaNilai(['skor'=>['score'=>84.5],'riwayat'=>[],'disetujui_koor'=>false,'disetujui_dosen'=>false]);
        $render=fn($submission=null,$grade=null)=>Blade::render('<x-eoffice::manajemen-praktikum.ui.student-stage-status :ta="$ta" :stage="$stage" :submission="$submission" :grade="$grade" />',['ta'=>$ta,'stage'=>$stage,'submission'=>$submission,'grade'=>$grade]);
        $this->assertStringContainsString('Belum Dikumpul',$render());
        $this->assertStringContainsString('Menunggu Penilaian',$render($submission));
        $this->assertStringContainsString('ACC',$render($submission,$grade));
        $this->assertStringNotContainsString('Nilai:',$render($submission,$grade));
        $grade->disetujui_koor=$grade->disetujui_dosen=true;
        $this->assertStringContainsString('84,50',$render($submission,$grade));
        $grade->riwayat=[['belum_ditinjau'=>['stage']]];
        $this->assertStringContainsString('Menunggu Penilaian',$render($submission,$grade));
        foreach(['dibatalkan'=>'Pengiriman Dibatalkan','perlu_perbaikan'=>'Perlu Revisi'] as $status=>$label){$submission->status=$status;$this->assertStringContainsString($label,$render($submission,$grade));}
    }

    public function test_participant_headers_are_shared_and_table_headings_are_uppercase(): void
    {
        foreach(['koordinator','asprak','mahasiswa','dosen'] as $role){
            $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$role.'/daftar-praktikan.blade.php'));
            $this->assertStringContainsString('ui.participants-header',$source);
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source),TOKEN_PARSE));
            preg_match_all('~<th\b[^>]*>(.*?)</th>~s',$source,$headers);
            foreach($headers[1] as $heading)$this->assertSame(mb_strtoupper(trim($heading)),trim($heading));
        }
        $html=Blade::render('<x-eoffice::manajemen-praktikum.ui.participants-header export-url="/export" />');
        foreach(['Praktikan Terdaftar','Ekspor CSV','Cari nama / NIM...','Semua Kelompok','Semua Shift','name="kelompok"','name="shift"'] as $text)$this->assertStringContainsString($text,$html);
        $this->assertStringNotContainsString('Export CSV',$html);
    }

    public function test_header_and_empty_state_visual_fixture(): void
    {
        $praktikum=new Praktikum(['nama'=>'Praktikum Uji']);$praktikum->id='prak';
        $html='';
        foreach(['koor','asprak','dosen','mhs'] as $role){
            $header=Blade::render('<x-eoffice::manajemen-praktikum.'.$role.'-header :praktikum="$praktikum" />',compact('praktikum'));
            $this->assertStringContainsString('border-bottom:',$header);
            $html.='<section class="mp-box-body mp-praktikum-content" data-role="'.$role.'" style="height:400px;flex:none;margin-bottom:24px">'.$header.'<div class="mp-card mp-unpublished-grades" style="padding:48px 24px;min-height:280px;display:flex;align-items:center;justify-content:center;flex-direction:column"><div>Nilai Belum Dipublikasikan</div><p>Nilai akan tampil setelah disetujui.</p></div></section>';
        }
        if(getenv('PRACTICUM_UI_PREVIEW')==='1'){
            $css=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/partials/_styles.blade.php')).file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/partials/_shell-styles.blade.php'));
            $alpine=str_replace('\\','/',base_path('node_modules/alpinejs/dist/cdn.min.js'));
            $assets=glob(public_path('build/assets/*.css'));$built=$assets?'<link rel="stylesheet" href="file:///'.str_replace('\\','/',$assets[0]).'">':'';
            file_put_contents(storage_path('framework/testing/participant-ui-fixture.html'),'<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'.$built.$css.'<style>*{box-sizing:border-box}.sticky{position:sticky}.mp-app{width:100%}body{margin:0} [x-cloak]{display:none!important}</style><script defer src="file:///'.$alpine.'"></script><main class="mp-app">'.$html.'</main>');
        }
    }
}
