<?php

namespace Modules\EOffice\Tests\Unit;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\TugasAkhirController;
use Modules\EOffice\Models\{TugasAkhir, TaNilai, TaPengumpulan, TaAbsensi, Praktikum, DaftarPraktikan};
use Modules\EOffice\Services\NotifikasiService;
use Tests\TestCase;

class TugasAkhirTest extends TestCase
{
    private const PRAK = '0199f101-0000-7000-8000-000000000001';
    private const STUDENT = '0199f101-0000-7000-8000-000000000002';
    private const STAGE = '0199f101-0000-7000-8000-000000000003';
    private const COMPONENT = '0199f101-0000-7000-8000-000000000004';

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.ta_test' => ['driver' => 'sqlite', 'database' => ':memory:']]);
        DB::setDefaultConnection('ta_test');
        Schema::create('users', function (Blueprint $t) { $t->id(); $t->string('name'); $t->softDeletes(); });
        Schema::create('students', function (Blueprint $t) { $t->id(); $t->unsignedBigInteger('user_id'); $t->string('student_number')->nullable(); });
        Schema::create('eo_praktikum', function (Blueprint $t) { $t->uuid('id')->primary(); $t->string('nama'); $t->unsignedBigInteger('koor_id'); $t->boolean('is_active')->default(true); $t->softDeletes(); });
        Schema::create('daftar_praktikan', function (Blueprint $t) { $t->uuid('id')->primary(); $t->uuid('praktikum_id'); $t->unsignedBigInteger('user_id'); });
        Schema::create('asprak_praktikum', function (Blueprint $t) { $t->id(); $t->uuid('praktikum_id'); $t->unsignedBigInteger('user_id'); $t->string('role'); $t->softDeletes(); });
        Schema::create('eo_praktikum_dosen', function (Blueprint $t) { $t->uuid('praktikum_id'); $t->unsignedBigInteger('dosen_id'); });
        Schema::create('nilai_praktikum',function(Blueprint $t){$t->id();$t->uuid('daftar_praktikan_id');$t->boolean('disetujui_koor')->default(false);$t->boolean('disetujui_dosen')->default(false);$t->boolean('dipublikasikan')->default(false);$t->timestamps();});
        (require base_path('Modules/EOffice/database/migrations/2026_10_07_000001_create_tugas_akhir_tables.php'))->up();
        (require base_path('Modules/EOffice/database/migrations/2026_10_07_000002_create_ta_absensi_table.php'))->up();
        (require base_path('Modules/EOffice/database/migrations/2026_10_07_000003_add_tipe_to_tugas_akhir.php'))->up();
        DB::table('users')->insert([['id' => 1, 'name' => 'Koor'], ['id' => 2, 'name' => 'Mahasiswa'], ['id' => 3, 'name' => 'Asisten'], ['id' => 4, 'name' => 'Dosen'], ['id' => 5, 'name' => 'Orang lain']]);
        DB::table('eo_praktikum')->insert(['id' => self::PRAK, 'nama' => 'Praktikum Test', 'koor_id' => 1]);
        DB::table('daftar_praktikan')->insert(['id' => self::STUDENT, 'praktikum_id' => self::PRAK, 'user_id' => 2]);
        DB::table('asprak_praktikum')->insert(['praktikum_id' => self::PRAK, 'user_id' => 3, 'role' => 'asprak']);
        DB::table('eo_praktikum_dosen')->insert(['praktikum_id' => self::PRAK, 'dosen_id' => 4]);
        $this->mock(NotifikasiService::class, function ($mock) { $mock->shouldReceive('kirimBulk')->zeroOrMoreTimes(); $mock->shouldReceive('kirim')->zeroOrMoreTimes(); });
        Storage::fake('local');
    }

    private function request(string $role, int $user, array $data = [], array $files = []): Request
    {
        $this->actingAs(User::findOrFail($user));
        $request = Request::create('/test', 'POST', $data, [], $files);
        $route = new Route('POST', 'test/{praktikum}', fn () => null);
        $route->bind(Request::create('/test/'.self::PRAK));
        $route->setParameter('praktikum', self::PRAK);
        $route->setParameter('taRole', $role);
        $request->setRouteResolver(fn () => $route);
        return $request;
    }

    private function task(): TugasAkhir
    {
        return TugasAkhir::create(['praktikum_id' => self::PRAK, 'ketentuan' => 'Ketentuan', 'published' => true, 'tahap' => [['id' => self::STAGE, 'nama' => 'Proposal', 'ketentuan' => 'Dokumen', 'deadline' => now()->addDay()->format('Y-m-d\TH:i')]], 'komponen' => [['id' => self::COMPONENT, 'nama' => 'Laporan', 'bobot' => 100]]]);
    }

    public function test_staff_stage_opens_collection_table_and_student_keeps_submission_form(): void
    {
        $this->task();
        foreach (['asprak' => 3, 'dosen' => 4, 'mahasiswa' => 2] as $role => $user) {
            $request = $this->request($role, $user);
            $request->setMethod('GET');
            $request->query->set('tahap_id', self::STAGE);
            $request->route()->setParameter('taSection', 'tugas');
            $view = (new TugasAkhirController)->index($request);
            $this->assertSame('eoffice::manajemen-praktikum.tugas-akhir.'.($role === 'mahasiswa' ? 'index' : 'pengumpulan'), $view->name());
            $this->assertSame(self::STAGE, $view->getData()['selectedStage']);
        }
    }

    public function test_student_stage_submission_matches_module_sections_and_revision_state(): void
    {
        $ta=$this->task();
        $this->actingAs(User::findOrFail(2));
        $data=['praktikum'=>Praktikum::findOrFail(self::PRAK),'ta'=>$ta,'stage'=>$ta->tahap[0],'student'=>DaftarPraktikan::findOrFail(self::STUDENT),'submissions'=>collect(),'stageGrades'=>collect()];
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.student-submission',$data)->render();
        foreach(['Pengumpulan Tugas','Tambah Berkas','name="files[]"','name="link"','border-top:1px dashed #DFE1E7'] as $text)$this->assertStringContainsString($text,$html);
        $submission=TaPengumpulan::create(['tugas_akhir_id'=>$ta->id,'praktikan_id'=>self::STUDENT,'tahap_id'=>self::STAGE,'status'=>'dikumpulkan','riwayat'=>[['jenis'=>'pengumpulan','kategori'=>'awal','waktu'=>now()->toIso8601String(),'oleh'=>'Mahasiswa','files'=>[['path'=>'test/hasil.pdf','nama'=>'Hasil.pdf']]]]]);
        $data['submissions']=collect([$submission]);
        $data['stage']['revisi_aktif']=true;
        $data['stage']['revisi_deadline']=now()->addDays(2)->toIso8601String();
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.student-submission',$data)->render();
        foreach(['Pengumpulan Revisi','Kumpulkan Revisi','Hasil.pdf','target="_blank"','Batalkan Pengumpulan'] as $text)$this->assertStringContainsString($text,$html);
        $data['stage']['revisi_deadline']=now()->subMinute()->toIso8601String();
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.student-submission',$data)->render();
        $this->assertStringNotContainsString('Kumpulkan Revisi',$html);
        $this->assertStringContainsString('Hasil.pdf',$html);
        $card=\Illuminate\Support\Facades\Blade::render('<x-eoffice::manajemen-praktikum.ui.tugas-akhir-card :praktikum="$praktikum" role="mahasiswa" section="tugas" />',$data);
        $this->assertStringContainsString('Pengumpulan Tugas',$card);
        $this->assertStringContainsString('Hasil.pdf',$card);
        $this->assertStringNotContainsString('>Lihat Tugas</a>',$card);
    }

    public function test_task_attachment_previews_and_downloads_use_the_displayed_filename(): void
    {
        $ta=$this->task();
        $path='ta/random.pdf';
        Storage::disk('local')->put($path,"%PDF-1.4\nTest");
        $ta->update(['lampiran'=>[['path'=>$path,'nama'=>'Proposal Mahasiswa.pdf']]]);
        $request=$this->request('asprak',3);
        $request->setMethod('GET');$request->query->set('path',$path);
        $response=(new TugasAkhirController)->download($request);
        $this->assertSame('application/pdf',$response->headers->get('Content-Type'));
        $this->assertStringStartsWith('inline;',$response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Proposal Mahasiswa.pdf',$response->headers->get('Content-Disposition'));
        $request->query->set('download',1);
        $response=(new TugasAkhirController)->download($request);
        $this->assertStringStartsWith('attachment;',$response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Proposal Mahasiswa.pdf',$response->headers->get('Content-Disposition'));
    }

    public function test_stage_batch_grading_is_atomic_and_rejects_stale_versions(): void
    {
        $ta=$this->task();
        $other='0199f101-0000-7000-8000-000000000006';
        DB::table('daftar_praktikan')->insert(['id'=>$other,'praktikum_id'=>self::PRAK,'user_id'=>5]);
        $students=[self::STUDENT=>['version'=>0,'skor'=>[self::COMPONENT=>80]],$other=>['version'=>0,'skor'=>[self::COMPONENT=>90]]];
        $controller=new TugasAkhirController;
        $controller->gradeBatch($this->request('asprak',3,['tahap_id'=>self::STAGE,'students'=>$students]));
        $this->assertSame(2,TaNilai::count());
        $first=TaNilai::where('praktikan_id',self::STUDENT)->firstOrFail();
        $this->assertEquals(80,$first->skor[self::COMPONENT]);
        $students[self::STUDENT]=['version'=>$first->version,'skor'=>[self::COMPONENT=>85]];
        try {
            $controller->gradeBatch($this->request('asprak',3,['tahap_id'=>self::STAGE,'students'=>$students]));
            $this->fail('Stale row accepted');
        } catch(ValidationException $e) { $this->assertNotEmpty($e->errors()); }
        $this->assertEquals(80,$first->fresh()->skor[self::COMPONENT]);
        $this->assertSame($first->version,$first->fresh()->version);
    }

    public function test_stage_collection_renders_shared_table_and_single_save_button(): void
    {
        $this->task();
        Schema::table('users',fn(Blueprint $t)=>$t->string('email')->nullable());
        Schema::table('daftar_praktikan',function(Blueprint $t){$t->string('kelompok')->default('1');$t->string('shift')->default('1');});
        DB::table('users')->where('id',2)->update(['email'=>'student@example.test']);
        DB::table('students')->insert(['user_id'=>2,'student_number'=>'21120123140136']);
        $request=$this->request('asprak',3);$request->setMethod('GET');$request->query->set('tahap_id',self::STAGE);$request->route()->setParameter('taSection','tugas');
        $data=(new TugasAkhirController)->index($request)->getData();
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/pengumpulan.blade.php'));
        $source=preg_replace('/^<x-eoffice::manajemen-praktikum.layout.*$/m','',$source);
        $source=str_replace(['</x-eoffice::manajemen-praktikum.layout>','<x-slot name="header">','</x-slot>'],['','<div>','</div>'],$source);
        $html=\Illuminate\Support\Facades\Blade::render($source,$data);
        foreach (['>NO</th>','>NAMA MAHASISWA</th>','>SHIFT</th>','id="taStageGrades"','form="taStageGrades"','Cari nama / NIM...','Semua Kelompok','Semua Shift','student@example.test'] as $text) $this->assertStringContainsString($text,$html);
        $this->assertSame(1,substr_count($html,'>Simpan</button>'));
        foreach (['background:#EEF2FF;color:#4F46E5','>RATA-RATA</th>','mp-badge sm ta-collection-tag'] as $style) $this->assertStringContainsString($style,$html);
        $this->assertStringNotContainsString('>CATATAN</th>',$html);
        $this->assertStringNotContainsString('Rata-rata tahap',$html);
        $this->assertStringNotContainsString('ta-collection-grade-',$html);
        if (getenv('PRACTICUM_TA_COLLECTION_PREVIEW')==='1') {
            $css=view('eoffice::manajemen-praktikum.partials._styles')->render();
            $alpine=str_replace('\\','/',base_path('node_modules/alpinejs/dist/cdn.min.js'));
            file_put_contents(storage_path('framework/testing/ta-collection-fixture.html'),'<!doctype html><meta charset="utf-8">'.$css.'<style>[x-cloak]{display:none!important}body{padding:24px;font-family:Arial}</style><script defer src="file:///'.$alpine.'"></script>'.$html);
        }
    }

    public function test_json_recap_and_stage_batch_use_each_student_payload(): void
    {
        $this->task();$controller=new TugasAkhirController;
        $payload=['students'=>[self::STUDENT=>['grade'=>['version'=>0,'skor'=>[self::COMPONENT=>'80.25']],'attendance'=>['version'=>0,'status'=>'hadir']]]];
        $request=$this->request('asprak',3);$request->headers->set('Content-Type','application/json');$request->setJson(new \Symfony\Component\HttpFoundation\InputBag($payload));
        $this->assertSame(200,$controller->saveRecap($request)->status());
        $this->assertEquals(80.25,TaNilai::firstOrFail()->skor[self::COMPONENT]);
        $this->assertSame('hadir',TaAbsensi::firstOrFail()->status);
        $request->setJson(new \Symfony\Component\HttpFoundation\InputBag(['tahap_id'=>self::STAGE,'students'=>[self::STUDENT=>['version'=>1,'skor'=>[self::COMPONENT=>'88.25']]]]));
        $controller->gradeBatch($request);
        $this->assertEquals(88.25,TaNilai::firstOrFail()->skor[self::COMPONENT]);
        $this->assertSame(2,TaNilai::firstOrFail()->version);
    }

    public function test_json_revision_only_can_use_revision_grade_when_initial_is_empty(): void
    {
        $ta=$this->task();$stages=$ta->tahap;$stages[0]['revisi_metode']='ganti';$ta->update(['tahap'=>$stages]);
        TaPengumpulan::create(['tugas_akhir_id'=>$ta->id,'praktikan_id'=>self::STUDENT,'tahap_id'=>self::STAGE,'status'=>'dikumpulkan','riwayat'=>[
            ['jenis'=>'pengumpulan','waktu'=>now()->subDay()->toIso8601String(),'files'=>[]],['jenis'=>'pengumpulan','waktu'=>now()->toIso8601String(),'files'=>[]],
        ]]);
        $request=$this->request('asprak',3);$request->headers->set('Content-Type','application/json');
        $request->setJson(new \Symfony\Component\HttpFoundation\InputBag(['students'=>[self::STUDENT=>['grade'=>['version'=>0,'skor'=>[self::COMPONENT=>null],'revisi'=>[self::COMPONENT=>'90.25']]]]]));
        (new TugasAkhirController)->saveRecap($request);
        $this->assertEquals(90.25,TaNilai::firstOrFail()->skor[self::COMPONENT]);
        $this->assertArrayNotHasKey(self::COMPONENT,TaNilai::firstOrFail()->initialScores());
    }

    public function test_assistant_recap_saves_attendance_and_grades_atomically(): void
    {
        $ta=$this->task();$controller=new TugasAkhirController;
        $payload=['students'=>[self::STUDENT=>['attendance'=>['version'=>0,'status'=>'hadir','keterangan'=>'Tes'],'grade'=>['version'=>0,'skor'=>[self::COMPONENT=>80]]]]];
        $response=$controller->saveRecap($this->request('asprak',3,$payload));
        $this->assertSame(200,$response->status());
        $this->assertSame('hadir',TaAbsensi::firstOrFail()->status);
        $this->assertEquals(80,TaNilai::firstOrFail()->skor[self::COMPONENT]);
        $payload['students'][self::STUDENT]['attendance']=['version'=>1,'status'=>'alpa'];
        try {$controller->saveRecap($this->request('asprak',3,$payload));$this->fail('Stale grade accepted');}
        catch(ValidationException $error){$this->assertNotEmpty($error->errors());}
        $this->assertSame('hadir',TaAbsensi::firstOrFail()->status);
        $this->assertSame(1,TaAbsensi::firstOrFail()->version);
        $controller->saveRecap($this->request('asprak',3,['students'=>[self::STUDENT=>['grade'=>['version'=>1,'skor'=>[self::COMPONENT=>null],'kosongkan'=>[self::COMPONENT=>true]]]]]));
        $this->assertArrayNotHasKey(self::COMPONENT,TaNilai::firstOrFail()->skor);
        try {$controller->saveRecap($this->request('dosen',4,$payload));$this->fail('Lecturer may not edit');}
        catch(\Symfony\Component\HttpKernel\Exception\HttpException $error){$this->assertSame(403,$error->getStatusCode());}
    }

    public function test_assistant_recap_renders_module_table_format_without_action_column(): void
    {
        $ta=$this->task();
        $stages=$ta->tahap;$stages[0]['revisi_aktif']=true;$ta->update(['tahap'=>$stages]);
        TaNilai::create(['tugas_akhir_id'=>$ta->id,'praktikan_id'=>self::STUDENT,'skor'=>[self::COMPONENT=>90],'version'=>2,'riwayat'=>[
            ['oleh'=>'Asisten A','waktu'=>'2026-10-30T10:00:00+07:00','jenis'=>'penilaian','tahap_id'=>self::STAGE,'awal'=>[self::COMPONENT=>80],'sebelum'=>[],'sesudah'=>[self::COMPONENT=>80]],
            ['oleh'=>'Asisten B','waktu'=>'2026-10-31T10:00:00+07:00','jenis'=>'penilaian','tahap_id'=>self::STAGE,'awal'=>[self::COMPONENT=>85],'revisi'=>[self::COMPONENT=>95],'sebelum'=>[self::COMPONENT=>80],'sesudah'=>[self::COMPONENT=>90]],
        ]]);
        TaPengumpulan::create(['tugas_akhir_id'=>$ta->id,'praktikan_id'=>self::STUDENT,'tahap_id'=>self::STAGE,'status'=>'dikumpulkan','riwayat'=>[
            ['jenis'=>'pengumpulan','waktu'=>now()->subDay()->toIso8601String(),'files'=>[]],['jenis'=>'pengumpulan','waktu'=>now()->toIso8601String(),'files'=>[]],
        ]]);
        Schema::table('users',fn(Blueprint $t)=>$t->string('email')->nullable());
        Schema::table('daftar_praktikan',function(Blueprint $t){$t->string('kelompok')->default('1');$t->string('shift')->default('1');});
        $request=$this->request('asprak',3);$request->setMethod('GET');
        DB::table('daftar_praktikan')->insert(['id'=>'0199f101-0000-7000-8000-000000000006','praktikum_id'=>self::PRAK,'user_id'=>5]);
        $data=(new TugasAkhirController)->index($request)->getData();
        $data['base']='eoffice.manprak.asprak.tugas-akhir.';$data['routeArgs']=['praktikum'=>self::PRAK];
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.rekap-asprak',$data)->render();
        foreach(['NAMA MAHASISWA','KELOMPOK','SHIFT','KEHADIRAN','KETERANGAN','RATA-RATA','registerExtraSave','mp-grade-history-trigger'] as $text)$this->assertStringContainsString($text,$html);
        foreach(['>AKSI</th>','Simpan Absensi','Simpan Nilai','Riwayat Absensi'] as $text)$this->assertStringNotContainsString($text,$html);
        foreach(['Riwayat Proposal','Asisten A','Asisten B','Riwayat lebih lawas','Riwayat lebih baru','Opsional...','mp-grade-score'] as $text)$this->assertStringContainsString($text,$html);
        if(getenv('PRACTICUM_TA_COLLECTION_PREVIEW')==='1'){
            $css=view('eoffice::manajemen-praktikum.partials._styles')->render();
            $alpine=str_replace('\\','/',base_path('node_modules/alpinejs/dist/cdn.min.js'));
            preg_match('~<script>[\s\S]*?</script>~',file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php')),$manager);
            $manager=\Illuminate\Support\Facades\Blade::render($manager[0],$data);
            $data['modulDiampu']=collect();
            $data['praktikum']->setAttribute('cover_path',null);
            $header=\Illuminate\Support\Facades\Blade::render('<x-eoffice::manajemen-praktikum.asprak-header :praktikum="$praktikum" activeTab="absensi">@include("eoffice::manajemen-praktikum.asprak.absensi-toolbar")</x-eoffice::manajemen-praktikum.asprak-header>',$data);
            $stages=$ta->tahap;$stages[0]['revisi_deadline']='2026-11-15 20:00:00';$ta->update(['tahap'=>$stages]);
            $stageCards=\Illuminate\Support\Facades\Blade::render('<x-eoffice::manajemen-praktikum.ui.tugas-akhir-card :praktikum="$praktikum" role="asprak" section="tugas" />',$data);
            $accordion=\Illuminate\Support\Facades\Blade::render('<div x-data="{alwaysOpen:true}"><button class="mp-grade-card-header" type="button" @click="alwaysOpen=!alwaysOpen">Tugas Akhir</button><x-eoffice::manajemen-praktikum.ui.grade-accordion state="alwaysOpen">{!! $table !!}</x-eoffice::manajemen-praktikum.ui.grade-accordion></div>',['table'=>$html]);
            $assets=glob(public_path('build/assets/*.css'));$built=$assets?'<link rel="stylesheet" href="file:///'.str_replace('\\','/',$assets[0]).'">':'';
            file_put_contents(storage_path('framework/testing/ta-recap-fixture.html'),'<!doctype html><meta charset="utf-8">'.$built.$css.'<style>[x-cloak]{display:none!important}body{padding:24px;font-family:Arial}</style>'.$manager.'<script defer src="file:///'.$alpine.'"></script><div class="mp-box-body" style="height:calc(100vh - 48px);display:block;overflow:auto"><div x-data="absensiManager()" data-absensi-manager>'.$header.$accordion.$stageCards.'<div style="height:1200px"></div></div></div>');
            $data['selectedStage']=self::STAGE;
            $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/pengumpulan.blade.php'));
            $source=preg_replace('/^<x-eoffice::manajemen-praktikum.layout.*$/m','',$source);
            $source=str_replace(['</x-eoffice::manajemen-praktikum.layout>','<x-slot name="header">','</x-slot>'],['','<div>','</div>'],$source);
            $collection=\Illuminate\Support\Facades\Blade::render($source,$data);
            file_put_contents(storage_path('framework/testing/ta-collection-fixture.html'),'<!doctype html><meta charset="utf-8">'.$built.$css.'<style>[x-cloak]{display:none!important}body{padding:24px}</style><script defer src="file:///'.$alpine.'"></script>'.$collection);
        }
    }

    public function test_unpublished_stage_is_not_accessible_to_assistant(): void
    {
        $this->task()->update(['published' => false]);
        $request = $this->request('asprak', 3);
        $request->setMethod('GET');
        $request->query->set('tahap_id', self::STAGE);
        $request->route()->setParameter('taSection', 'tugas');
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new TugasAkhirController)->index($request);
    }

    public function test_student_submission_does_not_accept_comments_or_display_historical_comments(): void
    {
        $this->task();
        (new TugasAkhirController)->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com','catatan'=>'Komentar mahasiswa']));
        $entry=TaPengumpulan::firstOrFail()->entries()[0];
        $this->assertArrayNotHasKey('catatan',$entry);
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.submission-documents',[
            'entry'=>['catatan'=>'Komentar lama mahasiswa'],'original'=>true,
        ])->render();
        $this->assertStringNotContainsString('Komentar lama mahasiswa',$html);
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/content.blade.php'));
        $this->assertStringNotContainsString('name="catatan"',$source);
    }

    public function test_grade_updates_from_both_pages_use_one_shared_record(): void
    {
        $this->task();
        $controller = new TugasAkhirController;
        foreach ([['tugas', 70, 0], ['nilai', 90, 1], ['tugas', 85, 2]] as [$section, $score, $version]) {
            $request = $this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => $version, 'skor' => [self::COMPONENT => $score]]);
            $request->route()->setParameter('taSection', $section);
            $controller->grade($request);
            $this->assertSame(1, TaNilai::count());
            foreach (['nilai', 'tugas'] as $readSection) {
                $read = $this->request('asprak', 3);
                $read->setMethod('GET');
                $read->route()->setParameter('taSection', $readSection);
                if ($readSection === 'tugas') $read->query->set('tahap_id', self::STAGE);
                $grade = $controller->index($read)->getData()['grades']->get(self::STUDENT);
                $this->assertEquals($score, $grade->skor[self::COMPONENT]);
                $this->assertSame($version + 1, $grade->version);
            }
        }
    }

    public function test_coordinator_creates_stage_parameters_without_upload_or_weights(): void
    {
        $controller = new TugasAkhirController;
        $controller->save($this->request('koor', 1, ['version'=>0, 'ketentuan'=>'Ketentuan', 'publish'=>1, 'tahap'=>[
            ['nama'=>'Asistensi', 'ketentuan'=>'Offline', 'pengumpulan'=>0, 'parameter'=>[['nama'=>'Progres']]],
            ['nama'=>'Responsi', 'ketentuan'=>'Presentasi', 'pengumpulan'=>0, 'parameter'=>[['nama'=>'Pemahaman']]],
        ]]));
        $ta = TugasAkhir::firstOrFail();
        $this->assertFalse($ta->tahap[0]['pengumpulan']);
        $this->assertCount(1, $ta->componentsFor($ta->tahap[0]['id']));
        $this->assertEquals(0, collect($ta->komponen)->sum('bobot'));
        foreach ($ta->komponen as $component) $this->assertNull($component['bobot']);
        $controller->weights($this->request('koor', 1, ['version'=>$ta->version,'bobot'=>collect($ta->komponen)->mapWithKeys(fn($c)=>[$c['id']=>50])->all()]));
        $this->assertEquals(100, collect($ta->fresh()->komponen)->sum('bobot'));
    }

    public function test_grading_one_stage_preserves_other_stage_scores(): void
    {
        $ta = $this->task();
        $otherStage = '0199f101-0000-7000-8000-000000000005';
        $otherComponent = '0199f101-0000-7000-8000-000000000006';
        $stages = $ta->tahap;
        $stages[] = ['id'=>$otherStage, 'nama'=>'Responsi', 'pengumpulan'=>false];
        $ta->update(['tahap'=>$stages, 'komponen'=>[
            ['id'=>self::COMPONENT, 'nama'=>'Progres', 'tahap_id'=>self::STAGE, 'bobot'=>50],
            ['id'=>$otherComponent, 'nama'=>'Presentasi', 'tahap_id'=>$otherStage, 'bobot'=>50],
        ]]);
        $controller = new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'tahap_id'=>self::STAGE,'skor'=>[self::COMPONENT=>70]]));
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>1,'tahap_id'=>$otherStage,'skor'=>[$otherComponent=>90]]));
        $this->assertEquals([self::COMPONENT=>70, $otherComponent=>90],TaNilai::firstOrFail()->skor);
    }

    public function test_upload_is_rejected_for_stage_without_collection(): void
    {
        $ta = $this->task(); $stages = $ta->tahap; $stages[0]['pengumpulan']=false; $ta->update(['tahap'=>$stages]);
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com']));
    }

    public function test_revision_methods_preserve_initial_score(): void
    {
        $ta = $this->task();
        $controller = new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/awal']));
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>40]]));
        $stages=$ta->tahap; $stages[0]['revisi_aktif']=true; $stages[0]['revisi_deadline']=now()->addDays(3)->toDateTimeString();
        $ta->update(['tahap'=>$stages]);
        $controller->review($this->request('asprak',3,['submission_id'=>TaPengumpulan::firstOrFail()->id,'catatan'=>'Perbaiki','deadline_revisi'=>now()->addDays(2)->toDateTimeString()]));
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/revisi']));
        foreach (['rata_rata'=>60,'berbobot'=>64,'tertinggi'=>80,'ganti'=>80] as $method=>$expected) {
            $stages[0]['revisi_metode']=$method; $stages[0]['revisi_bobot_awal']=40; $ta->update(['tahap'=>$stages]);
            $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>TaNilai::firstOrFail()->version,'skor'=>[self::COMPONENT=>40],'revisi'=>[self::COMPONENT=>80]]));
            $grade=TaNilai::firstOrFail();
            $this->assertEquals($expected,$grade->skor[self::COMPONENT]);
            $this->assertEquals(40,$grade->initialScores()[self::COMPONENT]);
            $this->assertEquals(80,$grade->revisionScores()[self::COMPONENT]);
        }
    }

    public function test_unsubmitted_expired_revision_keeps_initial_score_and_can_be_approved(): void
    {
        $ta=$this->task();$stages=$ta->tahap;$stages[0]['revisi_aktif']=true;$stages[0]['revisi_deadline']=now()->addDay()->toDateTimeString();$ta->update(['tahap'=>$stages]);$controller=new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/awal']));
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>60]]));
        $controller->review($this->request('asprak',3,['submission_id'=>TaPengumpulan::firstOrFail()->id,'catatan'=>'Perbaiki','deadline_revisi'=>now()->addDay()->toDateTimeString()]));
        $this->travel(2)->days();
        $grade=TaNilai::firstOrFail();
        $controller->approve($this->request('koor',1,['versions'=>[$grade->id=>$grade->version]]));
        $this->assertTrue($grade->fresh()->disetujui_koor);
        $this->assertEquals(60,$grade->fresh()->skor[self::COMPONENT]);
        $this->travelBack();
    }

    public function test_revision_score_without_revision_submission_is_rejected(): void
    {
        $this->task();
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>40],'revisi'=>[self::COMPONENT=>80]]));
    }

    public function test_assistant_cannot_open_revision_before_coordinator(): void
    {
        $ta=$this->task(); $stages=$ta->tahap; $stages[0]['revisi_aktif']=false; $ta->update(['tahap'=>$stages]);
        $controller=new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com']));
        $this->expectException(ValidationException::class);
        $controller->review($this->request('asprak',3,['submission_id'=>TaPengumpulan::firstOrFail()->id,'catatan'=>'Perbaiki','deadline_revisi'=>now()->addDay()->toDateTimeString()]));
    }

    public function test_weight_change_resets_approvals_and_rejects_stale_settings(): void
    {
        $ta=$this->task()->fresh(); $controller=new TugasAkhirController;
        $second=(string)\Illuminate\Support\Str::uuid();
        $ta->update(['komponen'=>[['id'=>self::COMPONENT,'nama'=>'Laporan','bobot'=>50],['id'=>$second,'nama'=>'Presentasi','bobot'=>50]]]);
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>70,$second=>60]]));
        $controller->weights($this->request('koor',1,['version'=>$ta->version,'bobot'=>[self::COMPONENT=>40,$second=>60]]));
        $this->assertFalse(TaNilai::firstOrFail()->disetujui_koor);
        $this->assertFalse(TaNilai::firstOrFail()->disetujui_dosen);
        $this->expectException(ValidationException::class);
        $controller->weights($this->request('koor',1,['version'=>$ta->version,'bobot'=>[self::COMPONENT=>40,$second=>60]]));
    }

    public function test_partial_recap_can_save_without_erasing_other_scores(): void
    {
        $ta=$this->task(); $second='0199f101-0000-7000-8000-000000000006';
        $ta->update(['komponen'=>[['id'=>self::COMPONENT,'nama'=>'Awal','bobot'=>50],['id'=>$second,'nama'=>'Akhir','bobot'=>50]]]);
        $controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>70,$second=>null]]));
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>1,'skor'=>[self::COMPONENT=>null,$second=>90]]));
        $this->assertEquals([self::COMPONENT=>70,$second=>90],TaNilai::firstOrFail()->skor);
    }

    public function test_editor_and_recap_save_the_same_final_task_weights(): void
    {
        $controller=new TugasAkhirController;
        $controller->save($this->request('koor',1,['version'=>0,'ketentuan'=>'Ketentuan','tahap'=>[
            ['nama'=>'Asistensi','ketentuan'=>'Progres','pengumpulan'=>0,'parameter'=>[['nama'=>'Progres','bobot'=>40]]],
            ['nama'=>'Responsi','ketentuan'=>'Presentasi','pengumpulan'=>0,'parameter'=>[['nama'=>'Presentasi','bobot'=>60]]],
        ]]));
        $ta=TugasAkhir::firstOrFail();
        $this->assertSame([40,60],array_column($ta->komponen,'bobot'));
        $ids=array_column($ta->komponen,'id');
        $controller->weights($this->request('koor',1,['version'=>$ta->version,'bobot'=>[$ids[0]=>25,$ids[1]=>75]]));
        $ta->refresh();$this->assertSame([25,75],array_column($ta->komponen,'bobot'));
        $stages=$ta->tahap;
        foreach($stages as $index=>&$stage) $stage['parameter']=[['id'=>$ids[$index],'nama'=>$ta->komponen[$index]['nama'],'bobot'=>50]];
        unset($stage);
        $controller->save($this->request('koor',1,['version'=>$ta->version,'ketentuan'=>'Ketentuan','tahap'=>$stages]));
        $this->assertSame([50,50],array_column($ta->fresh()->komponen,'bobot'));
    }

    public function test_editor_can_save_incomplete_weights_as_null(): void
    {
        (new TugasAkhirController)->save($this->request('koor',1,['version'=>0,'ketentuan'=>'Ketentuan','tahap'=>[
            ['nama'=>'Asistensi','ketentuan'=>'Progres','pengumpulan'=>0,'parameter'=>[['nama'=>'Progres','bobot'=>null],['nama'=>'Presentasi','bobot'=>60]]],
        ]]));
        $this->assertSame([null,60],array_column(TugasAkhir::first()->komponen,'bobot'));
    }

    public function test_editor_rejects_decimal_and_invalid_complete_weight_total(): void
    {
        foreach([50.5,99,0] as $weight){
            try{
                (new TugasAkhirController)->save($this->request('koor',1,['version'=>0,'ketentuan'=>'Ketentuan','tahap'=>[
                    ['nama'=>'Asistensi','ketentuan'=>'Progres','pengumpulan'=>0,'parameter'=>[['nama'=>'Progres','bobot'=>$weight]]],
                ]]));
                $this->fail('Invalid weights accepted.');
            }catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
            $this->assertSame(0,TugasAkhir::count());
        }
    }

    public function test_unchanged_weights_do_not_cancel_existing_approvals(): void
    {
        $ta=$this->task()->fresh();$controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>70]]));
        TaNilai::first()->update(['disetujui_koor'=>true,'disetujui_dosen'=>true]);
        $controller->weights($this->request('koor',1,['version'=>$ta->version,'bobot'=>[self::COMPONENT=>100]]));
        $this->assertEquals($ta->version,$ta->fresh()->version);
        $this->assertTrue(TaNilai::first()->disetujui_koor);
        $this->assertTrue(TaNilai::first()->disetujui_dosen);
    }

    public function test_approved_final_task_weights_are_locked_in_both_endpoints(): void
    {
        $ta=$this->task()->fresh();$second=(string)\Illuminate\Support\Str::uuid();
        $ta->update(['komponen'=>[['id'=>self::COMPONENT,'nama'=>'Laporan','tahap_id'=>self::STAGE,'bobot'=>50],['id'=>$second,'nama'=>'Presentasi','tahap_id'=>self::STAGE,'bobot'=>50]]]);
        $controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>70,$second=>80]]));
        TaNilai::first()->update(['disetujui_koor'=>true]);
        try{$controller->weights($this->request('koor',1,['version'=>$ta->version,'bobot'=>[self::COMPONENT=>40,$second=>60]]));$this->fail('Approved weights changed.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $stages=$ta->tahap;$stages[0]['parameter']=[['id'=>self::COMPONENT,'nama'=>'Laporan','bobot'=>40],['id'=>$second,'nama'=>'Presentasi','bobot'=>60]];
        try{$controller->save($this->request('koor',1,['version'=>$ta->version,'ketentuan'=>'Ketentuan','tahap'=>$stages]));$this->fail('Approved editor weights changed.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $this->assertSame([50,50],array_column($ta->fresh()->komponen,'bobot'));
    }

    public function test_parameter_weights_reject_zero_negative_and_over_one_hundred(): void
    {
        $ta=$this->task()->fresh();
        foreach ([0, -1, 100.01, 50.5] as $weight) {
            try {
                (new TugasAkhirController)->weights($this->request('koor',1,['version'=>$ta->version,'bobot'=>[self::COMPONENT=>$weight]]));
                $this->fail('Invalid weight accepted.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('bobot.'.self::COMPONENT,$e->errors());
            }
        }
        $this->assertEquals(100,$ta->fresh()->komponen[0]['bobot']);
    }

    public function test_late_first_submission_is_initial_score_and_can_then_be_revised(): void
    {
        $ta=$this->task(); $stages=$ta->tahap;
        $stages[0]['deadline']=now()->subDay()->toDateTimeString();
        $stages[0]['revisi_aktif']=true;
        $stages[0]['revisi_deadline']=now()->addDays(2)->toDateTimeString();
        $stages[0]['revisi_metode']='rata_rata';
        $ta->update(['tahap'=>$stages]);
        $controller=new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/late']));
        $submission=TaPengumpulan::firstOrFail();
        $this->assertSame('terlambat',$submission->riwayat[0]['kategori']);
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>60]]));
        $this->assertEquals(60,TaNilai::firstOrFail()->skor[self::COMPONENT]);
        $controller->review($this->request('asprak',3,['submission_id'=>$submission->id,'catatan'=>'Perbaiki','deadline_revisi'=>now()->addDay()->toDateTimeString()]));
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/revision']));
        $this->assertSame('revisi',collect($submission->fresh()->riwayat)->last()['kategori']);
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>TaNilai::firstOrFail()->version,'skor'=>[self::COMPONENT=>60],'revisi'=>[self::COMPONENT=>80]]));
        $this->assertEquals(70,TaNilai::firstOrFail()->skor[self::COMPONENT]);
    }

    public function test_late_submission_is_rejected_when_revision_is_closed_or_expired(): void
    {
        $ta=$this->task();
        foreach ([false,true] as $active) {
            $stages=$ta->tahap; $stages[0]['deadline']=now()->subDays(2)->toDateTimeString();
            $stages[0]['revisi_aktif']=$active; $stages[0]['revisi_deadline']=now()->subDay()->toDateTimeString(); $ta->update(['tahap'=>$stages]);
            try {
                (new TugasAkhirController)->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com']));
                $this->fail('Closed collection accepted.');
            } catch (ValidationException $e) { $this->assertArrayHasKey('tugas_akhir',$e->errors()); }
        }
        $this->assertSame(0,TaPengumpulan::count());
    }

    public function test_open_revision_allows_second_submission_without_assistant_request(): void
    {
        $ta=$this->task(); $stages=$ta->tahap; $stages[0]['revisi_aktif']=true; $stages[0]['revisi_deadline']=now()->addDays(2)->toDateTimeString(); $ta->update(['tahap'=>$stages]);
        $controller=new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/first']));
        $this->assertSame('awal',TaPengumpulan::firstOrFail()->riwayat[0]['kategori']);
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/second']));
        $this->assertSame('revisi', collect(TaPengumpulan::firstOrFail()->riwayat)->last()['kategori']);
    }

    public function test_second_submission_is_rejected_when_coordinator_has_not_opened_revision(): void
    {
        $this->task();
        $controller=new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/first']));
        $this->expectException(ValidationException::class);
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/revision']));
    }

    public function test_student_task_content_renders_before_and_after_submission(): void
    {
        $this->task();
        $controller=new TugasAkhirController;
        foreach ([false,true] as $submitted) {
            if ($submitted) $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/task']));
            $request=$this->request('mahasiswa',2);
            $request->setMethod('GET');
            $request->route()->setParameter('taSection','tugas');
            $data=$controller->index($request)->getData();
            $data['errors']=new \Illuminate\Support\ViewErrorBag;
            $html=view('eoffice::manajemen-praktikum.tugas-akhir.content',$data)->render();
            $this->assertStringContainsString($submitted ? 'Dikumpulkan' : 'Belum Dikumpulkan',$html);
            if (!$submitted) $this->assertStringContainsString('Kumpulkan',$html);
        }
    }

    public function test_coordinator_can_save_all_task_types_and_editor_uses_new_labels(): void
    {
        $ta=$this->task()->fresh();
        $controller=new TugasAkhirController;
        foreach (['individu','kelompok','kelompok_besar'] as $type) {
            $controller->save($this->request('koor',1,['version'=>$ta->version,'tipe'=>$type,'ketentuan'=>'Ketentuan','tahap'=>$ta->tahap,'komponen'=>$ta->komponen]));
            $ta->refresh();
            $this->assertSame($type,$ta->tipe);
        }
        $request=$this->request('koor',1);$request->setMethod('GET');
        $data=$controller->index($request)->getData();$data['errors']=new \Illuminate\Support\ViewErrorBag;
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.content',$data)->render();
        $this->assertStringContainsString('Tipe Tugas Akhir',$html);
        $this->assertStringContainsString('Tambah Pengumpulan Berkas',$html);
        $this->assertStringContainsString('<label>Revisi</label>',$html);
        $this->assertStringContainsString('window.taTextarea?.($el, true)',$html);
        $this->assertStringNotContainsString('+ Tambah Kolom',$html);
        $this->assertStringNotContainsString('+ Tambah Tahap',$html);
        $this->assertStringContainsString('Kolom Penilaian',$html);
        $this->assertStringContainsString('Contoh: Asistensi',$html);
        $this->assertStringNotContainsString('Riwayat Ketentuan',$html);
        $this->assertStringNotContainsString('Ketentuan Tugas Akhir',$html);
    }

    public function test_invalid_task_type_is_rejected(): void
    {
        $ta=$this->task()->fresh();
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->save($this->request('koor',1,['version'=>$ta->version,'tipe'=>'lainnya','ketentuan'=>'Ketentuan','tahap'=>$ta->tahap,'komponen'=>$ta->komponen]));
    }

    public function test_editor_retains_validation_with_animated_stages_and_accessible_trash_buttons(): void
    {
        $this->task();
        $request=$this->request('koor',1);$request->setMethod('GET');
        $data=(new TugasAkhirController)->index($request)->getData();$data['errors']=new \Illuminate\Support\ViewErrorBag;
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.content',$data)->render();
        $this->assertStringContainsString('grid-template-rows .24s ease',$html);
        $this->assertStringContainsString('prefers-reduced-motion:reduce',$html);
        $this->assertStringContainsString(':inert="!stageOpen"',$html);
        $this->assertStringContainsString('font-size:12px;font-weight:700;color:var(--c-fg)',$html);
        $this->assertStringContainsString('maxlength="5000"',$html);
        $dom=new \DOMDocument();@$dom->loadHTML($html);$xpath=new \DOMXPath($dom);
        $button=$xpath->query('//button[@aria-label="Hapus kolom penilaian"]')->item(0);
        $this->assertNotNull($button);
        $this->assertSame(1,$xpath->query('.//svg/path',$button)->length);
        $this->assertSame('parameter._deleteBlocked',$button->getAttribute(':disabled'));
        $this->assertStringNotContainsString('Hapus',trim($button->textContent));
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/editor.blade.php'));
        $this->assertStringContainsString('.slice(0,5010)',$source);
        $this->assertStringContainsString('Math.min(maxHeight,Math.max(minHeight,',$source);
        $this->assertStringNotContainsString('Math.max(maxHeight,height)',$source);
        $this->assertStringContainsString('const width=element.getBoundingClientRect().width',$source);
    }

    public function test_select_matches_pagination_option_states_and_preserves_boolean_values(): void
    {
        $html=view('eoffice::components.manajemen-praktikum.ui.ta-select',['options'=>['false'=>'Belum dibuka','true'=>'Dibuka']])->render();
        $this->assertStringContainsString('.ta-options .ta-option.selected{background:#EEF1F8',$html);
        $this->assertStringContainsString('border-radius:0;background:transparent',$html);
        $this->assertStringContainsString('value = key ===',$html);
        $this->assertStringContainsString('aria-pressed',$html);
        foreach(['ta-select','ta-upload','icon'] as $component){
            $compiled=app('blade.compiler')->compileString(file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/'.$component.'.blade.php')));
            $this->assertNotEmpty(token_get_all($compiled,TOKEN_PARSE));
        }
    }

    public function test_editor_weight_table_groups_stage_cells_without_changing_weight_inputs(): void
    {
        $this->task();$request=$this->request('koor',1);$request->setMethod('GET');
        $data=(new TugasAkhirController)->index($request)->getData();$data['errors']=new \Illuminate\Support\ViewErrorBag;
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.content',$data)->render();
        $dom=new \DOMDocument();@$dom->loadHTML($html);$xpath=new \DOMXPath($dom);
        $headers=$xpath->query('//table[contains(@class,"ta-weight-table")]/thead/tr/th');
        $this->assertSame(4,$headers->length);
        foreach(['NO','TAHAP','NAMA KOLOM PENILAIAN'] as $i=>$label)$this->assertSame($label,trim($headers->item($i)->textContent));
        $this->assertStringContainsString('BOBOT',$headers->item(3)->textContent);
        $this->assertSame(1,$xpath->query('//td[@class="ta-weight-stage" and @*[name()=":rowspan" and .="stage.parameter.length"]]')->length);
        $this->assertStringContainsString('x-if="parameterIndex===0"',$html);
        $input=$xpath->query('//input[contains(@class,"ta-weight-input")]')->item(0);
        foreach(['min'=>'1','max'=>'100','step'=>'1',':readonly'=>'weightsLocked','x-model'=>'parameter.bobot'] as $key=>$value)$this->assertSame($value,$input->getAttribute($key));
        $this->assertStringContainsString('ta-divider ta-editor-section-heading',$html);
        $this->assertStringContainsString('margin:6px 0 0;line-height:18px',$html);
        $this->assertStringContainsString('.ta-upload-hint{margin-bottom:5px}',$html);
        $trash=$xpath->query('//button[@aria-label="Hapus kolom penilaian"]')->item(0);
        $this->assertStringNotContainsString('error',$trash->getAttribute('class'));
        $this->assertSame(1,$xpath->query('.//svg/path',$trash)->length);
    }

    public function test_added_attachments_use_close_icons_without_changing_removal_fields(): void
    {
        $html=view('eoffice::components.manajemen-praktikum.ui.ta-upload',['stored'=>[['path'=>'test/file.pdf','nama'=>'Dokumen.pdf']]])->render();
        $dom=new \DOMDocument();@$dom->loadHTML($html);$xpath=new \DOMXPath($dom);
        foreach(['Hapus berkas tersimpan','Hapus berkas terpilih'] as $label){
            $path=$xpath->query('//button[@aria-label="'.$label.'"]//svg/path')->item(0);
            $this->assertNotNull($path);
            $this->assertSame('m6 6 12 12 M6 18 18 6',$path->getAttribute('d'));
        }
        $this->assertStringContainsString('name="hapus_lampiran[]"',$html);
        $this->assertStringContainsString('files.splice(index,1); sync()',$html);
    }

    public function test_weight_table_uses_centered_stage_cells_and_shared_semantic_summary_color(): void
    {
        $this->task();$request=$this->request('koor',1);$request->setMethod('GET');
        $data=(new TugasAkhirController)->index($request)->getData();$data['errors']=new \Illuminate\Support\ViewErrorBag;
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.content',$data)->render();
        $this->assertStringContainsString('.ta-weight-stage{text-align:center;',$html);
        $this->assertStringContainsString('width:20%;text-align:center',$html);
        $this->assertStringContainsString('table-layout:fixed;border-collapse:separate;border-spacing:0',$html);
        $this->assertStringContainsString('tbody:last-of-type .ta-weight-stage{border-bottom:0}',$html);
        $this->assertStringContainsString('.ta-weight-summary{padding:16px 0;',$html);
        $this->assertStringContainsString('.ta-editor>.ta-body{padding-bottom:0}',$html);
        $this->assertStringContainsString('color:var(--c-success-200)',$html);
        $this->assertStringNotContainsString('color:#047857',$html);
        $moduleSource=file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/module-components.blade.php'));
        $this->assertStringContainsString('color:var(--c-success-200)',$moduleSource);
    }

    public function test_collection_cannot_start_before_opening_date(): void
    {
        $ta=$this->task();$stages=$ta->tahap;$stages[0]['dibuka_pada']=now()->addHours(2)->toDateTimeString();$ta->update(['tahap'=>$stages]);
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com']));
    }

    public function test_stage_requires_at_least_one_grading_column(): void
    {
        $ta=$this->task();$stages=$ta->tahap;$stages[0]['parameter']=[];
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->save($this->request('koor',1,['version'=>1,'ketentuan'=>'Ketentuan','tahap'=>$stages]));
    }

    public function test_requirements_are_limited_to_five_thousand_characters(): void
    {
        $ta=$this->task();
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->save($this->request('koor',1,['version'=>1,'ketentuan'=>str_repeat('a',5001),'tahap'=>$ta->tahap,'komponen'=>$ta->komponen]));
    }

    public function test_stored_attachments_can_be_removed_without_deleting_other_files(): void
    {
        $ta=$this->task();$stages=$ta->tahap;$stages[0]['lampiran']=[['path'=>'stage/file','nama'=>'Tahap.pdf']];
        $ta->update(['tahap'=>$stages,'lampiran'=>[['path'=>'global/file','nama'=>'Ketentuan.pdf'],['path'=>'global/keep','nama'=>'Tetap.pdf']]]);
        $inputStages=$ta->tahap;unset($inputStages[0]['lampiran']);
        (new TugasAkhirController)->save($this->request('koor',1,['version'=>1,'ketentuan'=>'Ketentuan','tahap'=>$inputStages,'komponen'=>$ta->komponen,'hapus_lampiran'=>['stage/file','global/file','other-practice/file']]));
        $ta->refresh();
        $this->assertCount(0,$ta->tahap[0]['lampiran']);
        $this->assertSame('global/keep',$ta->lampiran[0]['path']);
        $this->assertSame('stage/file',$ta->riwayat[0]['tahap'][0]['lampiran'][0]['path']);
    }

    public function test_unused_final_task_can_be_deleted(): void
    {
        $this->task();
        (new TugasAkhirController)->destroy($this->request('koor',1,['version'=>1]));
        $this->assertSame(0,TugasAkhir::count());
    }

    public function test_final_task_with_student_data_cannot_be_deleted(): void
    {
        $this->task();$controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>80]]));
        $this->expectException(ValidationException::class);
        $controller->destroy($this->request('koor',1,['version'=>1]));
    }

    public function test_link_only_submission_and_revision_history(): void
    {
        $ta = $this->task();
        $stages=$ta->tahap;
        $stages[0]['revisi_aktif']=true;
        $stages[0]['revisi_deadline']=now()->addDays(3)->toDateTimeString();
        $ta->update(['tahap'=>$stages]);
        $controller = new TugasAkhirController;
        $controller->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE, 'link' => 'https://example.com/proposal']));
        $submission = TaPengumpulan::firstOrFail();
        $this->assertCount(1, $submission->riwayat);
        $controller->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE, 'link' => 'https://example.com/revisi']));
        $this->assertCount(2, $submission->fresh()->riwayat);
        $this->assertSame('dikumpulkan', $submission->fresh()->status);
    }

    public function test_three_files_of_any_extension_are_stored_privately(): void
    {
        $this->task();
        (new TugasAkhirController)->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE], ['files' => [UploadedFile::fake()->create('proyek.zip', 5120), UploadedFile::fake()->create('kode.py', 1), UploadedFile::fake()->create('laporan.docx', 2)]]));
        $files = TaPengumpulan::firstOrFail()->riwayat[0]['files'];
        $this->assertCount(3, $files);
        foreach ($files as $file) { Storage::disk('local')->assertExists($file['path']); $this->assertStringEndsWith('.bin', $file['path']); }
    }

    public function test_upload_above_five_mb_is_rejected(): void
    {
        $this->task();
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE], ['files' => [UploadedFile::fake()->create('besar.zip', 5121)]]));
    }

    public function test_four_files_are_rejected(): void
    {
        $this->task();
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE], ['files' => array_map(fn($i) => UploadedFile::fake()->create('file'.$i.'.zip', 1), range(1, 4))]));
    }

    public function test_empty_submission_is_rejected(): void
    {
        $this->task();
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE]));
    }

    public function test_expired_deadline_is_rejected(): void
    {
        $ta = $this->task(); $stages = $ta->tahap; $stages[0]['deadline'] = now()->subDay()->toDateTimeString(); $ta->update(['tahap' => $stages]);
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE, 'link' => 'https://example.com']));
    }

    public function test_unpublished_task_is_not_submittable(): void
    {
        $this->task()->update(['published' => false]);
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE, 'link' => 'https://example.com']));
    }

    public function test_shared_grades_require_both_approvals_and_changes_unpublish(): void
    {
        $this->task(); $controller = new TugasAkhirController;
        $controller->grade($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'skor' => [self::COMPONENT => 80]]));
        $grade = TaNilai::firstOrFail();
        $controller->approve($this->request('koor', 1, ['versions' => [$grade->id => 1]]));
        $this->assertTrue($grade->fresh()->disetujui_koor); $this->assertFalse($grade->fresh()->disetujui_dosen);
        $controller->approve($this->request('dosen', 4, ['versions' => [$grade->id => 1]]));
        $this->assertTrue($grade->fresh()->disetujui_dosen);
        $controller->grade($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 1, 'skor' => [self::COMPONENT => 90]]));
        $this->assertFalse($grade->fresh()->disetujui_koor); $this->assertFalse($grade->fresh()->disetujui_dosen); $this->assertCount(2, $grade->fresh()->riwayat);
    }

    public function test_stale_grade_does_not_overwrite_newer_score(): void
    {
        $this->task(); $controller = new TugasAkhirController;
        $controller->grade($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'skor' => [self::COMPONENT => 80]]));
        try {
            $controller->grade($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'skor' => [self::COMPONENT => 99]]));
            $this->fail('Expected conflict');
        } catch (ValidationException $e) { $this->assertEquals(80, TaNilai::firstOrFail()->skor[self::COMPONENT]); }
    }

    public function test_user_outside_practicum_is_denied(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new TugasAkhirController)->index($this->request('mahasiswa', 5));
    }

    public function test_parameter_name_is_locked_after_grading_without_losing_score(): void
    {
        $ta = $this->task(); $controller = new TugasAkhirController;
        $controller->grade($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'skor' => [self::COMPONENT => 80]]));
        $components = $ta->komponen; $components[0]['nama'] = 'Diubah';
        try {$controller->save($this->request('koor', 1, ['version' => 1, 'ketentuan' => 'Ketentuan', 'tahap' => $ta->tahap, 'komponen' => $components]));$this->fail('Graded structure changed.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $this->assertSame('Laporan', $ta->fresh()->komponen[0]['nama']);
        $this->assertEquals(80, TaNilai::firstOrFail()->skor[self::COMPONENT]);
    }

    public function test_parameter_cannot_be_added_after_grading(): void
    {
        $ta=$this->task(); $controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>80]]));
        TaNilai::firstOrFail()->update(['disetujui_koor'=>true,'disetujui_dosen'=>true]);
        $stages=$ta->tahap;
        $stages[0]['parameter']=[['id'=>self::COMPONENT,'nama'=>'Laporan'],['nama'=>'Presentasi']];
        try{$controller->save($this->request('koor',1,['version'=>1,'ketentuan'=>'Ketentuan','tahap'=>$stages]));$this->fail('Graded structure changed.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $this->assertCount(1,$ta->fresh()->komponen);
        $grade=TaNilai::firstOrFail();
        $this->assertEquals(80,$grade->skor[self::COMPONENT]);
        $this->assertTrue($grade->disetujui_koor);
        $this->assertTrue($grade->disetujui_dosen);
    }

    public function test_graded_parameter_deletion_is_blocked_including_zero(): void
    {
        $ta=$this->task(); $controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>0]]));
        $stages=$ta->tahap; $stages[0]['parameter']=[['nama'=>'Kolom pengganti']];
        try {
            $controller->save($this->request('koor',1,['version'=>1,'ketentuan'=>'Ketentuan','tahap'=>$stages]));
            $this->fail('A parameter with zero score must not be deleted.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('Susunan kolom penilaian dikunci',$e->errors()['tugas_akhir'][0]);
        }
        $this->assertCount(1,$ta->fresh()->komponen);
    }

    public function test_parameter_can_be_deleted_after_explicit_clearing_and_history_is_kept(): void
    {
        $ta=$this->task(); $controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>80]]));
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>1,'skor'=>[self::COMPONENT=>null],'kosongkan'=>[self::COMPONENT=>1]]));
        $grade=TaNilai::firstOrFail();
        $this->assertArrayNotHasKey(self::COMPONENT,$grade->skor);
        $this->assertEquals(80,$grade->riwayat[0]['sesudah'][self::COMPONENT]);
        $stages=$ta->tahap; $stages[0]['parameter']=[['nama'=>'Kolom pengganti']];
        $controller->save($this->request('koor',1,['version'=>1,'ketentuan'=>'Ketentuan','tahap'=>$stages]));
        $this->assertCount(1,$ta->fresh()->komponen);
        $this->assertNotSame(self::COMPONENT,$ta->fresh()->komponen[0]['id']);
    }

    public function test_coordinator_creates_draft_and_publishes_multiple_stages(): void
    {
        $controller = new TugasAkhirController;
        $data = ['version' => 0, 'ketentuan' => 'Ketentuan baru', 'tahap' => [['nama' => 'Proposal', 'ketentuan' => 'Dokumen', 'deadline' => now()->addDay()->format('Y-m-d\TH:i')], ['nama' => 'Proyek', 'ketentuan' => 'Hasil proyek', 'deadline' => now()->addDays(2)->format('Y-m-d\TH:i')]], 'komponen' => [['nama' => 'Laporan', 'bobot' => 30], ['nama' => 'Produk', 'bobot' => 70]]];
        $controller->save($this->request('koor', 1, $data));
        $ta = TugasAkhir::firstOrFail();
        $this->assertFalse($ta->published); $this->assertCount(2, $ta->tahap); $this->assertCount(2, $ta->komponen);
        $this->assertNotSame($ta->tahap[0]['id'], $ta->tahap[1]['id']);
        $controller->save($this->request('koor', 1, ['version' => 1, 'ketentuan' => $ta->ketentuan, 'tahap' => $ta->tahap, 'komponen' => $ta->komponen, 'publish' => 1]));
        $this->assertTrue($ta->fresh()->published); $this->assertCount(1, $ta->fresh()->riwayat);
    }

    public function test_total_weight_must_be_one_hundred(): void
    {
        $ta = $this->task(); $components = $ta->komponen; $components[0]['bobot'] = 90;
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->save($this->request('koor', 1, ['version' => 1, 'ketentuan' => 'Ketentuan', 'tahap' => $ta->tahap, 'komponen' => $components]));
    }

    public function test_student_cannot_download_other_students_files(): void
    {
        $ta = $this->task(); $otherId = '0199f101-0000-7000-8000-000000000099';
        DB::table('daftar_praktikan')->insert(['id' => $otherId, 'praktikum_id' => self::PRAK, 'user_id' => 5]);
        Storage::disk('local')->put('private-other.bin', 'private');
        TaPengumpulan::create(['tugas_akhir_id' => $ta->id, 'praktikan_id' => $otherId, 'tahap_id' => self::STAGE, 'riwayat' => [['files' => [['path' => 'private-other.bin', 'nama' => 'private.zip']]]]]);
        $request = $this->request('mahasiswa', 2); $request->query->set('path', 'private-other.bin');
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new TugasAkhirController)->download($request);
    }

    public function test_unpublished_scores_are_hidden_from_students(): void
    {
        $this->task(); $controller = new TugasAkhirController;
        $controller->grade($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'skor' => [self::COMPONENT => 80]]));
        $view = $controller->index($this->request('mahasiswa', 2));
        $this->assertCount(0, $view->getData()['grades']);
        $grade = TaNilai::firstOrFail(); $grade->update(['disetujui_koor' => true, 'disetujui_dosen' => true]);
        $this->assertCount(1, $controller->index($this->request('mahasiswa', 2))->getData()['grades']);
    }

    public function test_used_stage_requires_confirmation_before_removal(): void
    {
        $ta = $this->task(); $controller = new TugasAkhirController;
        $controller->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE, 'link' => 'https://example.com']));
        $stages = [['nama' => 'Pengganti', 'ketentuan' => 'Baru', 'deadline' => now()->addDay()->format('Y-m-d\TH:i')]];
        $this->expectException(ValidationException::class);
        $controller->save($this->request('koor', 1, ['version' => 1, 'ketentuan' => 'Ketentuan', 'tahap' => $stages, 'komponen' => $ta->komponen]));
    }

    public function test_graded_stage_cannot_be_removed_even_with_legacy_components(): void
    {
        $ta=$this->task(); $controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>0]]));
        $this->expectException(ValidationException::class);
        $controller->save($this->request('koor',1,['version'=>$ta->fresh()->version,'ketentuan'=>'Ketentuan','tahap'=>[['nama'=>'Pengganti','ketentuan'=>'Baru','deadline'=>now()->addDay()->format('Y-m-d\TH:i')]],'komponen'=>$ta->komponen]));
    }

    public function test_editor_locks_only_graded_columns_and_their_stage(): void
    {
        $ta=$this->task()->fresh();
        $components=$ta->komponen;
        $emptyId=(string) \Illuminate\Support\Str::uuid();
        $components[]=['id'=>$emptyId,'nama'=>'Belum Dinilai','bobot'=>0];
        $controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>0]]));
        $ta->update(['komponen'=>$components]);
        $data=$controller->index($this->request('koor',1))->getData();
        $data['errors']=new \Illuminate\Support\ViewErrorBag;
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.content',$data)->render();
        $dom=new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        foreach ($dom->getElementsByTagName('form') as $form) {
            if (!str_contains($form->getAttribute('class'),'ta-editor')) continue;
            $editor=json_decode($form->getAttribute('x-data'),true);
            $stage=$editor['tahap'][0];
            $this->assertTrue($stage['_deleteBlocked']);
            $parameters=collect($stage['parameter'])->keyBy('id');
            $this->assertTrue($parameters[self::COMPONENT]['_deleteBlocked']);
            $this->assertTrue($parameters[$emptyId]['_deleteBlocked']);
            $this->assertStringContainsString(':disabled="item._structureLocked"',$html);
            return;
        }
        $this->fail('Form tugas akhir tidak ditemukan.');
    }

    public function test_practicum_publication_button_approves_final_task_grades(): void
    {
        $ta=$this->task(); $controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>80]]));
        $grade=TaNilai::firstOrFail();
        $request=$this->request('koor',1,['ta_versions'=>[$grade->id=>$grade->version]]);
        $controller->fromPracticumPage($request,\Modules\EOffice\Models\Praktikum::firstOrFail(),'koor','approve');
        $this->assertTrue($grade->fresh()->disetujui_koor);
    }

    public function test_csv_selection_exports_only_final_task(): void
    {
        $this->prepareCsvSchema();
        $this->task();
        $request=$this->request('koor',1,['cards'=>['tugas_akhir']]);
        $response=app(\Modules\EOffice\Services\ManajemenPraktikum\NilaiCsvExport::class)->download($request,\Modules\EOffice\Models\Praktikum::firstOrFail());
        ob_start(); $response->sendContent(); $csv=ob_get_clean();
        $this->assertStringNotContainsString('CARD',strtok($csv,"\n"));
        $this->assertStringContainsString('NAMA MAHASISWA',$csv);
        $compiled=app('blade.compiler')->compileString(file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/nilai-csv-picker.blade.php')));
        $this->assertNotEmpty(token_get_all($compiled,TOKEN_PARSE));
    }

    public function test_csv_selection_rejects_unknown_cards(): void
    {
        $this->prepareCsvSchema();
        $this->task();
        $this->expectException(ValidationException::class);
        app(\Modules\EOffice\Services\ManajemenPraktikum\NilaiCsvExport::class)->download($this->request('koor',1,['cards'=>['unknown']]),\Modules\EOffice\Models\Praktikum::firstOrFail());
    }

    public function test_module_csv_includes_additional_components_and_weighted_average(): void
    {
        $this->prepareCsvSchema();
        Schema::table('eo_praktikum',fn(Blueprint $t)=>$t->timestamps());
        (require base_path('Modules/EOffice/database/migrations/2026_10_09_230000_add_module_assessment_components.php'))->up();
        Schema::table('nilai_jenis_tugas',function(Blueprint $t){$t->string('jenis_tugas');$t->float('nilai')->nullable();});
        $module=(string)\Illuminate\Support\Str::uuid();
        DB::table('modul_praktikum')->insert(['id'=>$module,'praktikum_id'=>self::PRAK,'nama'=>'Modul 1','urutan'=>1]);
        $praktikum=\Modules\EOffice\Models\Praktikum::firstOrFail();
        $praktikum->update(['bobot_tp'=>10,'bobot_praktikum'=>30,'bobot_laporan'=>30,'bobot_responsi'=>20,'komponen_modul'=>[['id'=>'tambahan_1','nama'=>'Form Data','bobot'=>10]]]);
        DB::table('nilai_jenis_tugas')->insert(['modul_id'=>$module,'daftar_praktikan_id'=>self::STUDENT,'jenis_tugas'=>'tambahan_1','nilai'=>100]);
        $response=app(\Modules\EOffice\Services\ManajemenPraktikum\NilaiCsvExport::class)->download($this->request('koor',1,['cards'=>[$module]]),$praktikum);
        ob_start();$response->sendContent();$csv=ob_get_clean();
        $lines=explode("\n",trim($csv));$header=str_getcsv($lines[0]);$row=str_getcsv($lines[1]);
        $this->assertContains('FORM DATA',$header);$this->assertNotContains('CARD',$header);
        $this->assertSame('100',$row[array_search('FORM DATA',$header)]);
        $this->assertEquals(10,end($row));
        $this->assertSame('0',$row[array_search('TUGAS PENDAHULUAN',$header)]);
    }

    private function prepareCsvSchema(): void
    {
        $this->createCsvTables();
    }

    public function test_multiple_selected_cards_download_as_separate_csv_files_in_zip(): void
    {
        $this->prepareCsvSchema();$this->task();
        $id=(string) \Illuminate\Support\Str::uuid();
        DB::table('modul_praktikum')->insert(['id'=>$id,'praktikum_id'=>self::PRAK,'nama'=>'Modul 1','urutan'=>1]);
        $response=(new \Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\NilaiController)->exportCsv($this->request('dosen',4,['cards'=>[$id,'tugas_akhir']]),self::PRAK);
        ob_start();$response->sendContent();$contents=ob_get_clean();
        $path=tempnam(sys_get_temp_dir(),'test_nilai_zip_');
        try {
            file_put_contents($path,$contents);$zip=new \ZipArchive;
            $this->assertTrue($zip->open($path));
            $this->assertSame(2,$zip->numFiles);
            $this->assertStringContainsString('TUGAS PENDAHULUAN',$zip->getFromName('Rekap_Nilai_Modul_1_Praktikum_Test.csv'));
            $this->assertStringNotContainsString('TUGAS PENDAHULUAN',$zip->getFromName('Rekap_Nilai_Tugas_Akhir_Praktikum_Test.csv'));
            $zip->close();
        } finally { @unlink($path); }
    }

    private function createCsvTables(): void
    {
        Schema::table('eo_praktikum',function(Blueprint $t){foreach(['bobot_tp','bobot_praktikum','bobot_laporan','bobot_responsi'] as $name)$t->integer($name)->default(25);});
        Schema::table('users',function(Blueprint $t){$t->string('email')->nullable();});
        Schema::create('modul_praktikum',function(Blueprint $t){$t->uuid('id')->primary();$t->uuid('praktikum_id');$t->string('nama');$t->integer('urutan');$t->softDeletes();});
        Schema::create('nilai_jenis_tugas',function(Blueprint $t){$t->id();$t->uuid('modul_id');$t->uuid('daftar_praktikan_id');});
        Schema::create('absensi_praktikum',function(Blueprint $t){$t->id();$t->uuid('daftar_praktikan_id');});
        Schema::table('daftar_praktikan',function(Blueprint $t){$t->string('shift')->nullable();$t->string('kelompok')->nullable();$t->timestamps();});
    }

    public function test_final_task_export_calculates_weighted_average_and_preserves_zero_and_missing_scores(): void
    {
        $this->prepareCsvSchema();$ta=$this->task();
        $second=(string) \Illuminate\Support\Str::uuid();
        $ta->update(['komponen'=>[['id'=>self::COMPONENT,'nama'=>'Asistensi','bobot'=>40],['id'=>$second,'nama'=>'Responsi','bobot'=>60]]]);
        $grade=TaNilai::create(['tugas_akhir_id'=>$ta->id,'praktikan_id'=>self::STUDENT,'skor'=>[self::COMPONENT=>0,$second=>80],'version'=>1,'riwayat'=>[]]);
        $export=function(){
            $response=app(\Modules\EOffice\Services\ManajemenPraktikum\NilaiCsvExport::class)->download($this->request('koor',1,['cards'=>['tugas_akhir']]),\Modules\EOffice\Models\Praktikum::firstOrFail());
            ob_start();$response->sendContent();$csv=ob_get_clean();
            return str_getcsv(explode("\n",trim($csv))[1]);
        };
        $row=$export();$this->assertSame('0',$row[6]);$this->assertEquals(48,end($row));
        $grade->update(['skor'=>[self::COMPONENT=>0]]);
        $row=$export();$this->assertSame('0',$row[7]);$this->assertEquals(0,end($row));
        $grade->update(['skor'=>[self::COMPONENT=>0,$second=>80]]);
        $ta->update(['komponen'=>[['id'=>self::COMPONENT,'nama'=>'Asistensi','bobot'=>0],['id'=>$second,'nama'=>'Responsi','bobot'=>60]]]);
        $row=$export();$this->assertSame('Bobot belum valid',end($row));
    }

    public function test_practicum_grade_views_compile_without_parse_errors(): void
    {
        foreach (['koordinator/nilai','dosen/nilai','tugas-akhir/rekap-inline'] as $view) {
            $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$view.'.blade.php'));
            $compiled=app('blade.compiler')->compileString($source);
            $this->assertNotEmpty(token_get_all($compiled,TOKEN_PARSE),$view);
        }
    }

    public function test_coordinator_and_lecturer_recap_renders_history_without_component_variable_collision(): void
    {
        $ta = $this->task();
        $student = DaftarPraktikan::with('user')->findOrFail(self::STUDENT);
        foreach (['koor', 'dosen', 'mahasiswa'] as $role) {
            $html = view('eoffice::manajemen-praktikum.tugas-akhir.rekap-inline', [
                'ta' => $ta,
                'role' => $role,
                'praktikan' => collect([$student]),
                'grades' => collect(),
                'attendance' => collect(),
            ])->render();
            if ($role === 'mahasiswa') {
                $this->assertStringNotContainsString('mp-grade-history-trigger', $html);
                $this->assertStringContainsString('NAMA MAHASISWA', $html);
                $this->assertStringContainsString('SHIFT', $html);
            } else {
                $this->assertStringContainsString('mp-grade-history-trigger', $html);
                $this->assertStringContainsString('Riwayat', $html);
            }
        }
    }

    public function test_stage_with_submission_cannot_be_removed_even_with_confirmation(): void
    {
        $ta=$this->task(); $controller=new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/document']));
        $submission=TaPengumpulan::firstOrFail(); $history=$submission->riwayat;
        try{$controller->save($this->request('koor',1,['version'=>$ta->fresh()->version,'ketentuan'=>'Ketentuan','konfirmasi_hapus_tahap'=>[self::STAGE],'tahap'=>[['nama'=>'Pengganti','ketentuan'=>'Baru','deadline'=>now()->addDay()->format('Y-m-d\TH:i')]],'komponen'=>$ta->komponen]));$this->fail('Used stage removed.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $this->assertContains(self::STAGE,collect($ta->fresh()->tahap)->pluck('id')->all());
        $this->assertSame($history,$submission->fresh()->riwayat);
    }

    public function test_stale_approval_is_rejected(): void
    {
        $this->task(); $controller = new TugasAkhirController;
        $controller->grade($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'skor' => [self::COMPONENT => 80]]));
        $grade = TaNilai::firstOrFail();
        $controller->grade($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 1, 'skor' => [self::COMPONENT => 90]]));
        $this->expectException(ValidationException::class);
        $controller->approve($this->request('dosen', 4, ['versions' => [$grade->id => 1]]));
    }

    public function test_unsubmit_keeps_files_and_resubmission_is_initial_not_revision(): void
    {
        $ta=$this->task();$controller=new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE],['files'=>[UploadedFile::fake()->create('laporan.pdf',1)]]));
        $submission=TaPengumpulan::first();$path=$submission->entries()->first()['files'][0]['path'];
        $controller->unsubmit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'submission_token'=>$submission->token()]));
        $submission->refresh();
        $this->assertSame('dibatalkan',$submission->status);$this->assertCount(0,$submission->entries());
        $this->assertCount(2,$submission->riwayat);Storage::disk('local')->assertExists($path);
        $request=$this->request('mahasiswa',2);$request->route()->setParameter('taSection','tugas');
        $data=$controller->index($request)->getData();$data['errors']=new \Illuminate\Support\ViewErrorBag;
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.content',$data)->render();
        $this->assertStringContainsString('Pengumpulan dibatalkan dan belum dianggap masuk',$html);
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'reuse_files'=>1]));
        $submission->refresh();$this->assertSame('dikumpulkan',$submission->status);
        $this->assertCount(1,$submission->entries());$this->assertSame('awal',$submission->entries()->first()['kategori']);
        $this->assertSame($path,$submission->entries()->first()['files'][0]['path']);
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>75]]));
        $this->assertEquals(75,TaNilai::first()->skor[self::COMPONENT]);
        $this->expectException(ValidationException::class);
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>1,'skor'=>[self::COMPONENT=>75],'revisi'=>[self::COMPONENT=>90]]));
    }

    public function test_unsubmit_rejects_stale_token_and_grade_zero(): void
    {
        $this->task();$controller=new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/document']));
        try{$controller->unsubmit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'submission_token'=>str_repeat('0',64)]));$this->fail('Stale cancellation accepted.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>0]]));
        $this->expectException(ValidationException::class);
        $controller->unsubmit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'submission_token'=>TaPengumpulan::first()->token()]));
    }

    public function test_unsubmit_is_blocked_after_initial_deadline_even_if_revision_is_open(): void
    {
        $ta=$this->task();$controller=new TugasAkhirController;
        $stages=$ta->tahap;$stages[0]['revisi_aktif']=true;$stages[0]['revisi_deadline']=now()->addDays(5)->toDateTimeString();$ta->update(['tahap'=>$stages]);
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/document']));
        $this->travel(2)->days();
        try{$controller->unsubmit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'submission_token'=>TaPengumpulan::first()->token()]));$this->fail('Late cancellation accepted.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}finally{$this->travelBack();}
        $this->assertSame('dikumpulkan',TaPengumpulan::first()->status);
    }

    public function test_cancelled_resubmission_after_deadline_requires_revision_and_is_late_initial(): void
    {
        $ta=$this->task();$controller=new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/old']));
        $controller->unsubmit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'submission_token'=>TaPengumpulan::first()->token()]));
        $this->travel(2)->days();
        try{
            try{$controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/new']));$this->fail('Closed resubmission accepted.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
            $stages=$ta->tahap;$stages[0]['revisi_aktif']=true;$stages[0]['revisi_deadline']=now()->addDay()->toDateTimeString();$ta->update(['tahap'=>$stages]);
            $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/new']));
            $this->assertSame('terlambat',TaPengumpulan::first()->entries()->first()['kategori']);
            $this->assertCount(1,TaPengumpulan::first()->entries());
        }finally{$this->travelBack();}
    }

    public function test_average_uses_zero_for_missing_scores_and_normalizes_only_the_stage(): void
    {
        $ta=$this->task();$second=(string)\Illuminate\Support\Str::uuid();$third=(string)\Illuminate\Support\Str::uuid();$stage=(string)\Illuminate\Support\Str::uuid();
        $ta->update(['komponen'=>[['id'=>self::COMPONENT,'nama'=>'A','tahap_id'=>self::STAGE,'bobot'=>20],['id'=>$second,'nama'=>'B','tahap_id'=>self::STAGE,'bobot'=>30],['id'=>$third,'nama'=>'C','tahap_id'=>$stage,'bobot'=>50]]]);
        $scores=[self::COMPONENT=>80];
        $this->assertEquals(32,$ta->average($scores,self::STAGE));$this->assertEquals(16,$ta->average($scores));$this->assertEquals(0,$ta->average([]));
        $components=$ta->komponen;$components[2]['bobot']=null;$ta->komponen=$components;
        $this->assertNull($ta->average($scores));$this->assertNull($ta->average($scores,self::STAGE));
    }

    public function test_assistant_average_view_counts_empty_as_zero_and_warns_on_invalid_weights(): void
    {
        Schema::table('users',fn(Blueprint $t)=>$t->string('email')->nullable());
        $ta=$this->task();$second=(string)\Illuminate\Support\Str::uuid();
        $ta->update(['komponen'=>[['id'=>self::COMPONENT,'nama'=>'A','bobot'=>20],['id'=>$second,'nama'=>'B','bobot'=>80]]]);
        $controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>80,$second=>null]]));
        foreach([true,false] as $valid){
            if(!$valid){$components=$ta->komponen;$components[1]['bobot']=null;$ta->update(['komponen'=>$components]);}
            $request=$this->request('asprak',3);$request->setMethod('GET');$request->route()->setParameter('taSection','nilai');
            $data=$controller->index($request)->getData();$data['errors']=new \Illuminate\Support\ViewErrorBag;
            $html=view('eoffice::manajemen-praktikum.tugas-akhir.content',$data)->render();
            $this->assertStringContainsString($valid ? 'average:16' : 'average:null',$html);
            if(!$valid) $this->assertStringContainsString('Rata-rata belum dapat dihitung',$html);
            $this->assertStringNotContainsString('Setujui Nilai',$html);
        }
    }

    public function test_legacy_pending_deleted_stage_no_longer_crashes_other_stage_grading(): void
    {
        $ta=$this->task();$otherStage=(string)\Illuminate\Support\Str::uuid();$otherColumn=(string)\Illuminate\Support\Str::uuid();
        $stages=$ta->tahap;$stages[]=['id'=>$otherStage,'nama'=>'Presentasi','ketentuan'=>'Presentasi','pengumpulan'=>false];
        $ta->update(['tahap'=>$stages,'komponen'=>[['id'=>self::COMPONENT,'nama'=>'Laporan','tahap_id'=>self::STAGE,'bobot'=>50],['id'=>$otherColumn,'nama'=>'Presentasi','tahap_id'=>$otherStage,'bobot'=>50]]]);
        $controller=new TugasAkhirController;
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>null,$otherColumn=>80]]));
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/document']));
        $ta->update(['tahap'=>[$stages[1]],'komponen'=>[['id'=>$otherColumn,'nama'=>'Presentasi','tahap_id'=>$otherStage,'bobot'=>100]]]);
        $grade=TaNilai::first();
        $controller->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>$grade->version,'tahap_id'=>$otherStage,'skor'=>[$otherColumn=>80]]));
        $this->assertEquals(80,$grade->fresh()->skor[$otherColumn]);
        $this->assertSame([],collect($grade->fresh()->riwayat)->last()['belum_ditinjau']);
    }

    public function test_submission_locks_structure_but_allows_deadline_extension_and_text_correction(): void
    {
        $ta=$this->task();$controller=new TugasAkhirController;
        $controller->submit($this->request('mahasiswa',2,['tahap_id'=>self::STAGE,'link'=>'https://example.com/document']));
        $stages=$ta->tahap;$stages[0]['ketentuan']='Perbaikan redaksi';$stages[0]['deadline']=now()->addDays(3)->format('Y-m-d\TH:i');$stages[0]['parameter']=[['id'=>self::COMPONENT,'nama'=>'Laporan','bobot'=>100]];
        $controller->save($this->request('koor',1,['version'=>$ta->fresh()->version,'ketentuan'=>'Ketentuan','tahap'=>$stages]));
        $this->assertSame('Perbaikan redaksi',$ta->fresh()->tahap[0]['ketentuan']);
        $shorter=$stages;$shorter[0]['deadline']=now()->addDay()->format('Y-m-d\TH:i');
        try{$controller->save($this->request('koor',1,['version'=>$ta->fresh()->version,'ketentuan'=>'Ketentuan','tahap'=>$shorter]));$this->fail('Deadline shortened.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $stages[0]['parameter'][]=['nama'=>'Kolom baru'];
        $this->expectException(ValidationException::class);
        $controller->save($this->request('koor',1,['version'=>$ta->fresh()->version,'ketentuan'=>'Ketentuan','tahap'=>$stages]));
    }

    private function preparePublication(): \Modules\EOffice\Models\Praktikum
    {
        $this->prepareCsvSchema();
        DB::table('modul_praktikum')->insert(['id'=>(string)\Illuminate\Support\Str::uuid(),'praktikum_id'=>self::PRAK,'nama'=>'Modul','urutan'=>1]);
        DB::table('nilai_praktikum')->insert(['daftar_praktikan_id'=>self::STUDENT]);
        $this->task();(new TugasAkhirController)->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>0,'skor'=>[self::COMPONENT=>80]]));
        return \Modules\EOffice\Models\Praktikum::first();
    }

    public function test_one_publication_requires_coordinator_then_lecturer_for_modules_and_final_task(): void
    {
        $praktikum=$this->preparePublication();$service=app(\Modules\EOffice\Services\ManajemenPraktikum\GradePublication::class);$grade=TaNilai::first();
        try{$service->approve($this->request('dosen',4,['ta_versions'=>[$grade->id=>$grade->version]]),$praktikum,'dosen');$this->fail('Lecturer bypassed coordinator.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $service->approve($this->request('koor',1,['ta_versions'=>[$grade->id=>$grade->version]]),$praktikum,'koor');
        $this->assertTrue(TaNilai::first()->disetujui_koor);$this->assertFalse(TaNilai::first()->disetujui_dosen);
        $this->assertEquals(0,DB::table('nilai_praktikum')->value('dipublikasikan'));
        $service->approve($this->request('dosen',4,['ta_versions'=>[$grade->id=>$grade->version]]),$praktikum,'dosen');
        $this->assertTrue(TaNilai::first()->disetujui_dosen);$this->assertEquals(1,DB::table('nilai_praktikum')->value('dipublikasikan'));
        $service->cancel($praktikum);$this->assertFalse(TaNilai::first()->disetujui_koor);$this->assertFalse(TaNilai::first()->disetujui_dosen);$this->assertEquals(0,DB::table('nilai_praktikum')->value('dipublikasikan'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('eoffice.manprak.koor.tugas-akhir.approve'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('eoffice.manprak.dosen.tugas-akhir.approve'));
    }

    public function test_publication_rolls_back_final_task_when_module_publication_fails(): void
    {
        $praktikum=$this->preparePublication();$service=app(\Modules\EOffice\Services\ManajemenPraktikum\GradePublication::class);$grade=TaNilai::first();
        $service->approve($this->request('koor',1,['ta_versions'=>[$grade->id=>$grade->version]]),$praktikum,'koor');
        DB::unprepared("CREATE TRIGGER reject_module_publication BEFORE UPDATE ON nilai_praktikum WHEN NEW.dipublikasikan=1 BEGIN SELECT RAISE(ABORT,'test rollback'); END");
        try{$service->approve($this->request('dosen',4,['ta_versions'=>[$grade->id=>$grade->version]]),$praktikum,'dosen');$this->fail('Expected module write failure.');}catch(\Illuminate\Database\QueryException $e){$this->assertStringContainsString('test rollback',$e->getMessage());}
        $this->assertTrue(TaNilai::first()->disetujui_koor);$this->assertFalse(TaNilai::first()->disetujui_dosen);
        $this->assertEquals(0,DB::table('nilai_praktikum')->value('dipublikasikan'));
    }

    public function test_final_task_grade_change_withdraws_entire_publication_for_review(): void
    {
        $praktikum=$this->preparePublication();$service=app(\Modules\EOffice\Services\ManajemenPraktikum\GradePublication::class);$grade=TaNilai::first();
        $service->approve($this->request('koor',1,['ta_versions'=>[$grade->id=>$grade->version]]),$praktikum,'koor');
        $service->approve($this->request('dosen',4,['ta_versions'=>[$grade->id=>$grade->version]]),$praktikum,'dosen');
        (new TugasAkhirController)->grade($this->request('asprak',3,['praktikan_id'=>self::STUDENT,'version'=>$grade->version,'skor'=>[self::COMPONENT=>90]]));
        $this->assertFalse(TaNilai::first()->disetujui_koor);$this->assertFalse(TaNilai::first()->disetujui_dosen);
        $this->assertEquals(0,DB::table('nilai_praktikum')->value('dipublikasikan'));
        $this->assertEquals(0,DB::table('nilai_praktikum')->value('disetujui_koor'));
        $this->assertEquals(0,DB::table('nilai_praktikum')->value('disetujui_dosen'));
    }

    public function test_archived_practicum_cannot_be_modified(): void
    {
        $this->task(); DB::table('eo_praktikum')->update(['is_active' => false]);
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE, 'link' => 'https://example.com']));
    }

    public function test_new_submission_requires_regrading_before_approval(): void
    {
        $this->task(); $controller = new TugasAkhirController;
        $controller->grade($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'skor' => [self::COMPONENT => 80]]));
        $controller->submit($this->request('mahasiswa', 2, ['tahap_id' => self::STAGE, 'link' => 'https://example.com']));
        $grade = TaNilai::firstOrFail();
        $this->assertSame('pengumpulan_baru', collect($grade->riwayat)->last()['jenis']);
        $this->expectException(ValidationException::class);
        $controller->approve($this->request('dosen', 4, ['versions' => [$grade->id => $grade->version]]));
    }

    public function test_attendance_is_recorded_once_per_student_and_can_be_updated(): void
    {
        $this->task(); $controller = new TugasAkhirController;
        foreach (['hadir','terlambat','tidak_hadir','alpa'] as $index => $status) {
            $controller->attendance($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => $index, 'status' => $status, 'keterangan' => 'Keterangan']));
            $this->assertSame(1, TaAbsensi::count());
            $this->assertSame($status, TaAbsensi::firstOrFail()->status);
        }
        $this->assertCount(4, TaAbsensi::firstOrFail()->riwayat);
        $this->assertSame(0, TaNilai::count());
    }

    public function test_attendance_rejects_stale_update_and_invalid_status(): void
    {
        $this->task(); $controller = new TugasAkhirController;
        $controller->attendance($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'status' => 'hadir']));
        $this->expectException(ValidationException::class);
        $controller->attendance($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'status' => 'alpa']));
    }

    public function test_student_cannot_change_attendance(): void
    {
        $this->task();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new TugasAkhirController)->attendance($this->request('mahasiswa', 2, ['praktikan_id' => self::STUDENT, 'version' => 0, 'status' => 'hadir']));
    }

    public function test_attendance_for_student_outside_practicum_is_rejected(): void
    {
        $this->task();
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        (new TugasAkhirController)->attendance($this->request('asprak', 3, ['praktikan_id' => self::COMPONENT, 'version' => 0, 'status' => 'hadir']));
    }

    public function test_attendance_does_not_change_existing_scores(): void
    {
        $this->task(); $controller = new TugasAkhirController;
        $controller->grade($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'skor' => [self::COMPONENT => 80]]));
        $before = TaNilai::firstOrFail()->getAttributes();
        $controller->attendance($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'status' => 'terlambat']));
        $this->assertSame($before, TaNilai::firstOrFail()->getAttributes());
    }

    public function test_invalid_attendance_status_is_rejected(): void
    {
        $this->task();
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->attendance($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'status' => 'izin']));
    }

    public function test_attendance_note_is_limited_to_five_hundred_characters(): void
    {
        $this->task();
        $this->expectException(ValidationException::class);
        (new TugasAkhirController)->attendance($this->request('asprak', 3, ['praktikan_id' => self::STUDENT, 'version' => 0, 'status' => 'hadir', 'keterangan' => str_repeat('a',501)]));
    }
}
