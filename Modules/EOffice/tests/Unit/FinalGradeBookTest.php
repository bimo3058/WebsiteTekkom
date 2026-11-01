<?php

namespace Modules\EOffice\Tests\Unit;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\GradePolicyController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\FinalGradeController;
use Modules\EOffice\Models\NilaiJenisTugas;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\TaNilai;
use Modules\EOffice\Models\TugasAkhir;
use Modules\EOffice\Services\ManajemenPraktikum\FinalGradeBook;
use Modules\EOffice\Services\ManajemenPraktikum\GradePolicyManager;
use Modules\EOffice\Services\ManajemenPraktikum\GradePublication;
use Modules\EOffice\Services\ManajemenPraktikum\NilaiCsvExport;
use Modules\EOffice\Services\ManajemenPraktikum\StudentCohort;
use Modules\EOffice\Services\NotifikasiService;
use Tests\TestCase;

class FinalGradeBookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.final_test' => ['driver' => 'sqlite', 'database' => ':memory:']]);
        DB::setDefaultConnection('final_test');
        DB::purge('final_test');
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->softDeletes();
        });
        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            $t->string('student_number');
            $t->integer('cohort_year');
            $t->timestamps();
        });
        Schema::create('eo_praktikum', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('nama');
            $t->integer('koor_id')->default(1);
            $t->boolean('is_active')->default(true);
            foreach (['tp', 'praktikum', 'laporan', 'responsi'] as $key) {
                $t->integer('bobot_'.$key)->default($key === 'tp' ? 10 : 30);
            }$t->timestamps();
            $t->softDeletes();
        });
        (require base_path('Modules/EOffice/database/migrations/2026_10_09_230000_add_module_assessment_components.php'))->up();
        Schema::table('eo_praktikum', fn (Blueprint $t) => $t->string('status')->default('aktif'));
        Schema::create('modul_praktikum', function (Blueprint $t) {
            $t->id();
            $t->uuid('praktikum_id');
            $t->string('nama');
            $t->integer('urutan')->default(1);
            $t->timestamps();
        });
        Schema::create('daftar_praktikan', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('praktikum_id');
            $t->integer('user_id');
            $t->string('shift')->nullable();
            $t->string('kelompok')->nullable();
            $t->timestamps();
        });
        Schema::create('nilai_jenis_tugas', function (Blueprint $t) {
            $t->id();
            $t->integer('modul_id');
            $t->uuid('daftar_praktikan_id');
            $t->string('jenis_tugas');
            $t->decimal('nilai', 8, 4)->nullable();
            $t->timestamps();
        });
        Schema::create('nilai_praktikum', function (Blueprint $t) {
            $t->id();
            $t->uuid('daftar_praktikan_id');
            $t->boolean('disetujui_koor')->default(false);
            $t->boolean('disetujui_dosen')->default(false);
            $t->boolean('dipublikasikan')->default(false);
            $t->timestamps();
        });
        Schema::create('eo_praktikum_dosen', function (Blueprint $t) {
            $t->uuid('praktikum_id');
            $t->integer('dosen_id');
        });
        Schema::create('eo_audit_logs', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id')->nullable();
            $t->string('action');
            $t->string('subject_type');
            $t->integer('subject_id')->nullable();
            $t->text('description')->nullable();
            $t->json('old_data')->nullable();
            $t->json('new_data')->nullable();
            $t->timestamp('created_at')->nullable();
        });
        Schema::create('absensi_praktikum', function (Blueprint $t) {
            $t->id();
            $t->uuid('daftar_praktikan_id');
        });
        foreach (['2026_10_07_000001_create_tugas_akhir_tables.php', '2026_10_07_000002_create_ta_absensi_table.php', '2026_10_07_000003_add_tipe_to_tugas_akhir.php', '2026_10_30_000001_create_practicum_final_grades.php','2026_11_01_000001_add_final_assessment_columns_and_score_history.php'] as $file) {
            (require base_path('Modules/EOffice/database/migrations/'.$file))->up();
        }
        DB::table('users')->insert([['id' => 1, 'name' => 'Koor'], ['id' => 2, 'name' => 'Mahasiswa A'], ['id' => 3, 'name' => 'Mahasiswa B'], ['id' => 4, 'name' => 'Dosen']]);
        DB::table('eo_praktikum_dosen')->insert(['praktikum_id' => 'prak', 'dosen_id' => 4]);
        DB::table('eo_praktikum')->insert(['id' => 'prak', 'nama' => 'Praktikum Test', 'bobot_gabungan_modul' => 60, 'bobot_gabungan_ta' => 40]);
        foreach ([2 => 2023, 3 => 2024] as $id => $year) {
            DB::table('students')->insert(['user_id' => $id, 'student_number' => '211201'.substr((string) $year, 2).'140136', 'cohort_year' => $year]);
            DB::table('daftar_praktikan')->insert(['id' => 's'.$id, 'praktikum_id' => 'prak', 'user_id' => $id, 'shift' => '1', 'kelompok' => '1']);
            DB::table('nilai_praktikum')->insert(['daftar_praktikan_id' => 's'.$id]);
        }
        DB::table('modul_praktikum')->insert([['id' => 1, 'praktikum_id' => 'prak', 'nama' => 'Modul 1'], ['id' => 2, 'praktikum_id' => 'prak', 'nama' => 'Modul 2']]);
        TugasAkhir::create(['praktikum_id' => 'prak', 'ketentuan' => 'TA', 'published' => true, 'tahap' => [['id' => 'stage', 'nama' => 'Tahap', 'revisi_metode' => 'rata_rata']], 'komponen' => [['id' => 'c', 'tahap_id' => 'stage', 'nama' => 'Laporan', 'bobot' => 100]]]);
    }

    private function moduleSubmissionFixture(): \Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\TugasController
    {
        Schema::create('asprak_praktikum',function(Blueprint $t){$t->id();$t->string('praktikum_id');$t->integer('user_id');$t->string('role');$t->softDeletes();});
        Schema::create('modul_asprak',function(Blueprint $t){$t->id();$t->integer('modul_id');$t->integer('asprak_id');});
        Schema::create('tugas_praktikum',function(Blueprint $t){$t->id();$t->integer('modul_id');$t->string('jenis_tugas');$t->string('judul');$t->timestamps();});
        Schema::create('pengumpulan_tugas',function(Blueprint $t){$t->id();$t->integer('tugas_id');$t->string('daftar_praktikan_id');$t->float('nilai')->nullable();$t->string('status_pengumpulan')->default('belum_dicek');$t->text('file_revisi_asprak')->nullable();$t->text('catatan_revisi')->nullable();$t->boolean('is_revision')->default(false);$t->timestamps();});
        Schema::table('nilai_praktikum',fn(Blueprint $t)=>$t->float('nilai_tugas')->nullable());
        Schema::create('riwayat_pengumpulan_tugas',function(Blueprint $t){$t->id();$t->integer('pengumpulan_tugas_id');$t->string('file_path')->nullable();$t->boolean('is_revision')->default(false);$t->timestamps();});
        DB::table('asprak_praktikum')->insert(['id'=>1,'praktikum_id'=>'prak','user_id'=>1,'role'=>'asprak']);
        DB::table('modul_asprak')->insert(['modul_id'=>1,'asprak_id'=>1]);
        DB::table('tugas_praktikum')->insert(['id'=>1,'modul_id'=>1,'jenis_tugas'=>'laporan','judul'=>'Laporan']);
        DB::table('pengumpulan_tugas')->insert(['id'=>1,'tugas_id'=>1,'daftar_praktikan_id'=>'s2','file_revisi_asprak'=>json_encode([['path'=>'revision/old.pdf','original_name'=>'Old.pdf']]),'status_pengumpulan'=>'revisi']);
        $this->actingAs(User::find(1));
        return app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\TugasController::class);
    }

    public function test_assistant_export_uses_shared_picker_and_rejects_unassigned_modules(): void
    {
        $this->moduleSubmissionFixture();
        $controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\AbsensiController::class);
        $this->mock(NilaiCsvExport::class)->shouldReceive('download')->once()->withArgs(fn($request,$praktikum)=>$request->input('cards')===['1','tugas_akhir','nilai_akhir'] && $praktikum->id==='prak')->andReturn(response('export'));
        $response=$controller->exportCsv(Request::create('/export','GET',['cards'=>['1','tugas_akhir','nilai_akhir']]));
        $this->assertSame('export',$response->getContent());
        try {$controller->exportCsv(Request::create('/export','GET',['cards'=>['2']]));$this->fail('Unassigned module exported');}
        catch(\Symfony\Component\HttpKernel\Exception\HttpException $error){$this->assertSame(403,$error->getStatusCode());}
        $praktikum=Praktikum::findOrFail('prak');$moduls=\Modules\EOffice\Models\Modul::whereKey(1)->get();
        $html=view('eoffice::components.manajemen-praktikum.ui.nilai-csv-picker',['praktikum'=>$praktikum,'moduls'=>$moduls,'role'=>'asprak'])->render();
        foreach(['asprak/absensi/export-csv','name="cards[]"','value="tugas_akhir"','value="nilai_akhir"'] as $text)$this->assertStringContainsString($text,$html);
        $this->assertStringNotContainsString('value="2"',$html);
    }

    public function test_module_json_grade_save_without_attendance_preserves_other_cells(): void
    {
        $this->moduleSubmissionFixture();
        Schema::create('pengajuan_modul',function(Blueprint $t){$t->id();$t->string('praktikum_id');$t->string('status');});
        DB::table('nilai_jenis_tugas')->insert(['modul_id'=>1,'daftar_praktikan_id'=>'s2','jenis_tugas'=>'responsi','nilai'=>75]);
        $controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\AbsensiController::class);
        $payload=['tanggal'=>'2026-10-31','absensi'=>[],'nilai'=>['s2'=>['laporan'=>'80.25']]];
        $make=fn($data)=>Request::create('/save','POST',[],[],[],['CONTENT_TYPE'=>'application/json','HTTP_ACCEPT'=>'application/json'],json_encode($data));
        $this->assertSame(200,$controller->store($make($payload),1)->status());
        $this->assertEquals(80.25,DB::table('nilai_jenis_tugas')->where('jenis_tugas','laporan')->value('nilai'));
        $this->assertEquals(75,DB::table('nilai_jenis_tugas')->where('jenis_tugas','responsi')->value('nilai'));
        $this->assertSame(0,DB::table('absensi_praktikum')->count());
        $payload['nilai']['s2']['laporan']=null;$controller->store($make($payload),1);
        $this->assertNull(DB::table('nilai_jenis_tugas')->where('jenis_tugas','laporan')->value('nilai'));
        $payload['nilai']['foreign']=['laporan'=>100];
        try{$controller->store($make($payload),1);$this->fail('Foreign participant accepted');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $payload['nilai']=['s2'=>['laporan'=>90]];DB::table('eo_praktikum')->update(['is_active'=>false]);
        try{$controller->store($make($payload),1);$this->fail('Archived grade changed');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $this->assertNull(DB::table('nilai_jenis_tugas')->where('jenis_tugas','laporan')->value('nilai'));
    }

    public function test_module_bulk_save_updates_only_supplied_students_and_rejects_foreign_ids(): void
    {
        $controller=$this->moduleSubmissionFixture();
        DB::table('nilai_jenis_tugas')->insert(['modul_id'=>1,'daftar_praktikan_id'=>'s3','jenis_tugas'=>'laporan','nilai'=>78]);
        $controller->updateNilaiJenis(Request::create('/test','POST',['nilai'=>['s2'=>['nilai_jenis'=>0]]]),1);
        $this->assertSame(0.0,(float)DB::table('nilai_jenis_tugas')->where('daftar_praktikan_id','s2')->value('nilai'));
        $this->assertSame(78.0,(float)DB::table('nilai_jenis_tugas')->where('daftar_praktikan_id','s3')->value('nilai'));
        $this->assertSame('acc',DB::table('pengumpulan_tugas')->where('id',1)->value('status_pengumpulan'));
        try {
            $controller->updateNilaiJenis(Request::create('/test','POST',['nilai'=>['s2'=>['nilai_jenis'=>50],'foreign'=>['nilai_jenis'=>90]]]),1);
            $this->fail('Foreign participant accepted');
        } catch (ValidationException $e) { $this->assertArrayHasKey('nilai',$e->errors()); }
        $this->assertSame(0.0,(float)DB::table('nilai_jenis_tugas')->where('daftar_praktikan_id','s2')->value('nilai'));
        $controller->updateNilaiJenis(Request::create('/test','POST',['nilai'=>['s2'=>['nilai_jenis'=>'']]]),1);
        $this->assertNull(DB::table('nilai_jenis_tugas')->where('daftar_praktikan_id','s2')->value('nilai'));
    }

    public function test_submission_filters_search_all_students_and_keep_unfiltered_totals(): void
    {
        $controller=$this->moduleSubmissionFixture();
        foreach ([['search'=>'mahasiswa a'],['search'=>'24140136'],['kelompok'=>'1','shift'=>'1'],['shift'=>'99']] as $index=>$filters) {
            app()->instance('request',Request::create('/test','GET',$filters));
            $data=$controller->pengumpulan(1)->getData();
            $this->assertSame([1,1,2,0][$index],$data['praktikans']->total());
            $this->assertSame(2,$data['totalPraktikan']);
            if ($index===0) $this->assertSame('s2',$data['praktikans']->first()->id);
            if ($index===1) $this->assertSame('s3',$data['praktikans']->first()->id);
        }
    }

    public function test_revision_can_retain_existing_files_and_rejects_forged_or_empty_attachments(): void
    {
        $controller=$this->moduleSubmissionFixture();
        $controller->beriRevisi(Request::create('/test','POST',['retained_files'=>['revision/old.pdf']]),1);
        $this->assertSame('Old.pdf',json_decode(DB::table('pengumpulan_tugas')->where('id',1)->value('file_revisi_asprak'),true)[0]['original_name']);
        foreach ([[],['foreign/file.pdf']] as $retained) {
            try {
                $controller->beriRevisi(Request::create('/test','POST',['retained_files'=>$retained]),1);
                $this->fail('Invalid retained files accepted');
            } catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); }
            $this->assertSame('revision/old.pdf',json_decode(DB::table('pengumpulan_tugas')->where('id',1)->value('file_revisi_asprak'),true)[0]['path']);
        }
    }

    public function test_module_revision_replaces_removed_file_and_keeps_other_files(): void
    {
        $controller=$this->moduleSubmissionFixture();
        $storage=$this->mock(\App\Services\SupabaseStorage::class);
        $storage->shouldReceive('upload')->once()->andReturn('revision/new.pdf');
        $controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\TugasController::class);
        $request=Request::create('/test','POST',[],[],['file_revisi'=>[\Illuminate\Http\UploadedFile::fake()->create('New.pdf',1,'application/pdf')]]);
        $controller->beriRevisi($request,1);
        $files=json_decode(DB::table('pengumpulan_tugas')->where('id',1)->value('file_revisi_asprak'),true);
        $this->assertSame([['path'=>'revision/new.pdf','original_name'=>'New.pdf']],$files);
        $this->assertSame('revisi',DB::table('pengumpulan_tugas')->where('id',1)->value('status_pengumpulan'));
    }

    public function test_revision_upload_failure_does_not_overwrite_existing_files(): void
    {
        $this->moduleSubmissionFixture();
        $storage=$this->mock(\App\Services\SupabaseStorage::class);
        $storage->shouldReceive('upload')->once()->andReturn(null);
        $controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\TugasController::class);
        $request=Request::create('/test','POST',['retained_files'=>['revision/old.pdf']],[],['file_revisi'=>[\Illuminate\Http\UploadedFile::fake()->create('New.pdf',1,'application/pdf')]]);
        $controller->beriRevisi($request,1);
        $files=json_decode(DB::table('pengumpulan_tugas')->where('id',1)->value('file_revisi_asprak'),true);
        $this->assertSame('revision/old.pdf',$files[0]['path']);
    }

    public function test_module_revision_modal_renders_old_files_and_resend_action(): void
    {
        $this->moduleSubmissionFixture();
        $p=\Modules\EOffice\Models\PengumpulanTugas::findOrFail(1);
        $html=view('eoffice::manajemen-praktikum.asprak.module-revision-modal',compact('p'))->render();
        foreach (['Edit Revisi','Kirim Revisi Kembali','Old.pdf','retained_files[]','Hapus lampiran revisi lama','name="file_revisi[]"'] as $text) $this->assertStringContainsString($text,$html);
        $this->assertStringNotContainsString('?download=',$html);
    }

    public function test_module_submission_table_renders_shared_identity_format_and_one_save_action(): void
    {
        $this->moduleSubmissionFixture();
        Schema::table('users',fn(Blueprint $t)=>$t->string('email')->nullable());
        Schema::table('pengumpulan_tugas',fn(Blueprint $t)=>$t->text('file_path')->nullable());
        DB::table('pengumpulan_tugas')->where('id',1)->update(['file_path'=>json_encode([['path'=>'uploads/random.pdf','original_name'=>'Print IRS.pdf']])]);
        DB::table('users')->where('id',2)->update(['email'=>'student@example.test']);
        $tugas=\Modules\EOffice\Models\Tugas::with('modul.praktikum')->findOrFail(1);
        $praktikans=\Modules\EOffice\Models\DaftarPraktikan::with('user.student')->orderBy('id')->get();
        $pengumpulan=\Modules\EOffice\Models\PengumpulanTugas::all()->each(fn($p)=>$p->setRelation('riwayat',collect()))->keyBy('daftar_praktikan_id');
        $nilaiJenis=collect();
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-pengumpulan.blade.php'));
        $source=preg_replace('/^<x-eoffice::manajemen-praktikum.layout.*$/m','',$source);
        $source=str_replace('</x-eoffice::manajemen-praktikum.layout>','',$source);
        $source=str_replace(['<x-slot name="header">','</x-slot>'],['<div>','</div>'],$source);
        $html=\Illuminate\Support\Facades\Blade::render($source,compact('tugas','praktikans','pengumpulan','nilaiJenis'));
        foreach (['>NO</th>','>NAMA MAHASISWA</th>','>NIM</th>','KELOMPOK</th>','>SHIFT</th>','id="moduleSubmissionGrades"','form="moduleSubmissionGrades"',':disabled="!hasChanges || false"','Edit Revisi','Kirim Revisi Kembali','student@example.test'] as $text) $this->assertStringContainsString($text,$html);
        $this->assertStringNotContainsString('?download=',$html);
        $this->assertStringNotContainsString('>NO.</th>',$html);
        $this->assertStringContainsString('Print IRS.pdf',$html);
        $this->assertStringContainsString('name=Print%20IRS.pdf',$html);
        $this->assertSame(1,substr_count($html,'>Simpan</button>'));
        if (getenv('PRACTICUM_SUBMISSION_PREVIEW')==='1') {
            $css=view('eoffice::manajemen-praktikum.partials._styles')->render();
            $alpine=str_replace('\\','/',base_path('node_modules/alpinejs/dist/cdn.min.js'));
            file_put_contents(storage_path('framework/testing/module-submission-fixture.html'),'<!doctype html><meta charset="utf-8">'.$css.'<style>[x-cloak]{display:none!important}body{padding:24px;font-family:Arial}</style><script defer src="file:///'.$alpine.'"></script>'.$html);
        }
    }

    private function book(): array
    {
        return app(FinalGradeBook::class)->compute(Praktikum::firstOrFail());
    }

    private function finalColumn(): array
    {
        return ['id'=>'019a0000-0000-7000-8000-000000000001','nama'=>'Presentasi','bobot'=>20];
    }

    public function test_student_class_report_and_csv_require_published_grades_and_membership(): void
    {
        $this->scores('85');
        $book = app(FinalGradeBook::class);
        $practicum = Praktikum::firstOrFail();
        $this->assertEmpty($book->report($practicum, null, true)['rows']);
        $this->publish();
        $this->assertCount(2, $book->report($practicum->fresh(), null, true)['rows']);
        $this->actingAs(User::findOrFail(2));
        $controller = app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\NilaiController::class);
        $request = Request::create('/export', 'GET', ['praktikum_id'=>'prak', 'cards'=>['nilai_akhir']]);
        $response = $controller->exportCsv($request);
        ob_start(); $response->sendContent(); $csv = ob_get_clean();
        $this->assertStringContainsString('Mahasiswa A', $csv);
        $this->assertStringContainsString('Mahasiswa B', $csv);
        DB::table('nilai_praktikum')->where('daftar_praktikan_id', 's3')->update(['dipublikasikan'=>false]);
        $this->assertEmpty($book->report($practicum->fresh(), null, true)['rows']);
        try { $controller->exportCsv($request); $this->fail('Unpublished grades exported'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $error) { $this->assertSame(403, $error->getStatusCode()); }
        $this->actingAs(User::findOrFail(4));
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $controller->exportCsv($request);
    }

    public function test_final_score_validation_does_not_expose_internal_student_or_component_ids(): void
    {
        $column = $this->finalColumn();
        try {
            app(\Modules\EOffice\Services\ManajemenPraktikum\FinalAssessment::class)->save(Praktikum::firstOrFail(), [
                'version' => 0,
                'students' => ['s2' => ['version' => 0, 'scores' => [$column['id'] => '101']]],
            ], 'koor');
            $this->fail('Out-of-range score accepted');
        } catch (ValidationException $exception) {
            $message = implode(' ', collect($exception->errors())->flatten()->all());
            $this->assertSame('Nilai harus berada antara 0 dan 100.', $message);
            $this->assertStringNotContainsString($column['id'], $message);
        }
    }

    public function test_final_additional_column_is_precise_required_and_frozen_in_publication(): void
    {
        $this->scores('80');$this->actingAs(User::findOrFail(1));
        $service=app(\Modules\EOffice\Services\ManajemenPraktikum\FinalAssessment::class);$c=$this->finalColumn();
        $service->save(Praktikum::first(),['version'=>0,'settings'=>['modules'=>50,'ta'=>30,'components'=>[$c]],'students'=>[]],'koor');
        $book=$this->book();$this->assertNull($book['rows']['s2']['final']);$this->assertStringContainsString('belum dinilai',implode(' ',$book['rows']['s2']['errors']));
        try{DB::transaction(fn()=>app(FinalGradeBook::class)->propose(Praktikum::first()));$this->fail('Missing additional grades accepted');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $this->actingAs(User::findOrFail(4));
        $service->save(Praktikum::first(),['version'=>1,'students'=>['s2'=>['version'=>0,'scores'=>[$c['id']=>'0']],'s3'=>['version'=>0,'scores'=>[$c['id']=>'91.25']]]],'dosen');
        $book=$this->book();$this->assertSame('64.00',$book['rows']['s2']['final']);$this->assertSame('82.25',$book['rows']['s3']['final']);$this->assertSame('AB',$book['rows']['s3']['letter']);
        $this->assertSame(2,DB::table('eo_prak_score_histories')->where('scope','final')->count());
        $this->publish();
        DB::table('eo_prak_extra_scores')->update(['scores'=>json_encode([$c['id']=>'100'])]);
        $report=app(FinalGradeBook::class)->report(Praktikum::first(),'3');
        $this->assertSame('82.25',$report['rows']['s3']['final']);$this->assertSame('91.25',$report['rows']['s3']['extras'][$c['id']]);$this->assertSame('Presentasi',$report['components'][0]['nama']);
        $this->expectException(ValidationException::class);
        $service->save(Praktikum::first(),['version'=>1,'students'=>['s3'=>['version'=>1,'scores'=>[$c['id']=>'70']]]],'dosen');
    }

    public function test_final_columns_reject_over_limit_invalid_weights_and_foreign_students_atomically(): void
    {
        $this->actingAs(User::findOrFail(1));$service=app(\Modules\EOffice\Services\ManajemenPraktikum\FinalAssessment::class);$c=$this->finalColumn();
        foreach([
            ['version'=>0,'settings'=>['modules'=>60,'ta'=>40,'components'=>[$c]],'students'=>[]],
            ['version'=>0,'settings'=>['modules'=>50,'ta'=>30,'components'=>array_map(fn($i)=>['id'=>(string)\Illuminate\Support\Str::uuid(),'nama'=>'Extra '.$i,'bobot'=>5],range(1,4))],'students'=>[]],
            ['version'=>0,'settings'=>['modules'=>50,'ta'=>30,'components'=>[$c]],'students'=>['foreign'=>['version'=>0,'scores'=>[$c['id']=>'80']]]],
        ] as $payload){try{$service->save(Praktikum::first(),$payload,'koor');$this->fail('Invalid settings accepted');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}$this->assertSame(0,Praktikum::first()->bobot_gabungan_version);}
        $this->assertSame(0,DB::table('eo_prak_extra_scores')->count());
    }

    public function test_final_score_conflicts_rollback_other_students_and_lecturer_cannot_change_settings(): void
    {
        $this->actingAs(User::findOrFail(1));$service=app(\Modules\EOffice\Services\ManajemenPraktikum\FinalAssessment::class);$c=$this->finalColumn();
        $service->save(Praktikum::first(),['version'=>0,'settings'=>['modules'=>50,'ta'=>30,'components'=>[$c]],'students'=>['s3'=>['version'=>0,'scores'=>[$c['id']=>'60']]]],'koor');
        try{$service->save(Praktikum::first(),['version'=>1,'students'=>['s2'=>['version'=>0,'scores'=>[$c['id']=>'90']],'s3'=>['version'=>0,'scores'=>[$c['id']=>'95']]]],'koor');$this->fail('Stale grades accepted');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        $this->assertFalse(DB::table('eo_prak_extra_scores')->where('praktikan_id','s2')->exists());$this->assertSame(1,DB::table('eo_prak_score_histories')->count());
        $this->actingAs(User::findOrFail(4));$this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $service->save(Praktikum::first(),['version'=>1,'settings'=>['modules'=>50,'ta'=>30,'components'=>[$c]],'students'=>[]],'dosen');
    }

    public function test_module_history_records_real_changes_with_actor_and_previous_value(): void
    {
        $this->actingAs(User::findOrFail(1));
        $grade=NilaiJenisTugas::create(['modul_id'=>1,'daftar_praktikan_id'=>'s2','jenis_tugas'=>'laporan','nilai'=>0]);
        $grade->update(['nilai'=>80]);$grade->update(['nilai'=>80]);$this->actingAs(User::findOrFail(4));$grade->update(['nilai'=>90]);
        $entries=DB::table('eo_prak_score_histories')->where('scope','module')->orderBy('id')->get();
        $this->assertCount(3,$entries);$this->assertNull($entries[0]->before_value);$this->assertEquals(0,$entries[1]->before_value);$this->assertEquals(80,$entries[2]->before_value);$this->assertEquals(90,$entries[2]->after_value);$this->assertSame('Dosen',$entries[2]->editor_name);
        $html=\Illuminate\Support\Facades\Blade::render('<x-eoffice::manajemen-praktikum.ui.module-score-history module-id="1" student-id="s2" score-key="laporan" />');
        $this->assertStringContainsString('Dosen',$html);$this->assertStringContainsString('mp-grade-history-trigger',$html);
    }

    public function test_three_column_final_card_renders_for_authorized_and_read_only_roles(): void
    {
        $this->scores('80');$this->actingAs(User::findOrFail(1));
        $components=array_map(fn($i)=>['id'=>(string)\Illuminate\Support\Str::uuid(),'nama'=>'Tambahan '.$i,'bobot'=>$i===1?10:5],[1,2,3]);
        app(\Modules\EOffice\Services\ManajemenPraktikum\FinalAssessment::class)->save(Praktikum::first(),['version'=>0,'settings'=>['modules'=>50,'ta'=>30,'components'=>$components],'students'=>collect(['s2','s3'])->mapWithKeys(fn($id)=>[$id=>['version'=>0,'scores'=>array_fill_keys(array_column($components,'id'),'80')]])->all()],'koor');
        $praktikum=Praktikum::first();$fixture='';
        foreach(['koor','dosen','asprak'] as $role){
            $html=\Illuminate\Support\Facades\Blade::render('<x-eoffice::manajemen-praktikum.ui.final-grades :praktikum="$praktikum" :role="$role" embedded="true" />',compact('praktikum','role'));
            $this->assertStringContainsString('Tambahan 1',$html);$this->assertStringContainsString('80.00',$html);
            if($role!=='asprak')$this->assertStringContainsString('data-final-bulk-dialog',$html);
            else $this->assertStringNotContainsString('data-final-bulk-dialog',$html);
            $fixture.='<main class="mp-app" data-role="'.$role.'" x-data="{globalSearch:\'\',globalKelompok:\'\',globalShift:\'\',lastPayload:null}" @final-assessment-change="lastPayload=$event.detail">'.$html.'</main>';
        }
        if(getenv('PRACTICUM_FINAL_PREVIEW')==='1'){
            $css=view('eoffice::manajemen-praktikum.partials._styles')->render().view('eoffice::manajemen-praktikum.partials._shell-styles')->render();
            $assets=glob(public_path('build/assets/*.css'));$built=$assets?'<link rel="stylesheet" href="file:///'.str_replace('\\','/',$assets[0]).'">':'';
            $alpine=str_replace('\\','/',base_path('node_modules/alpinejs/dist/cdn.min.js'));
            file_put_contents(storage_path('framework/testing/final-assessment-fixture.html'),'<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'.$built.$css.'<style>[x-cloak]{display:none!important}.mp-app{margin:16px}</style>'.$fixture.'<script src="file:///'.$alpine.'"></script>');
        }
    }

    public function test_main_save_and_final_csv_include_additional_scores_and_weights(): void
    {
        $this->scores('80');$this->actingAs(User::findOrFail(1));$p=Praktikum::first();$c=$this->finalColumn();
        $data=['module_version'=>$p->komponen_modul_version??0];
        foreach(['bobot_tp','bobot_praktikum','bobot_laporan','bobot_responsi'] as $key)$data[$key]=$p->$key;
        $data['final_payload']=json_encode(['version'=>0,'settings'=>['modules'=>50,'ta'=>30,'components'=>[$c]],'students'=>['s2'=>['version'=>0,'scores'=>[$c['id']=>'91.25']],'s3'=>['version'=>0,'scores'=>[$c['id']=>'0']]]]);
        app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\NilaiController::class)->updateBobot(Request::create('/test','POST',$data),$p->id);
        $this->assertSame('82.25',$this->book()['rows']['s2']['final']);
        $csv=app(NilaiCsvExport::class)->download(Request::create('/test','GET',['cards'=>['nilai_akhir']]),$p->fresh());
        ob_start();$csv->sendContent();$contents=ob_get_clean();
        $this->assertStringContainsString('PRESENTASI (20%)',$contents);$this->assertStringContainsString('91.25',$contents);$this->assertStringContainsString('82.25',$contents);
        $this->actingAs(User::findOrFail(4));
        $request=Request::create('/test','POST',['final_payload'=>json_encode(['version'=>1,'students'=>['s3'=>['version'=>1,'scores'=>[$c['id']=>'75.50']]]])]);
        $route=new \Illuminate\Routing\Route('POST','test',fn()=>null);$route->bind($request);$route->setParameter('finalRole','dosen');$request->setRouteResolver(fn()=>$route);
        app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\FinalAssessmentController::class)->save($request,$p->fresh());
        $this->assertSame('79.10',$this->book()['rows']['s3']['final']);
    }

    public function test_final_score_endpoint_rejects_non_assigned_lecturer_and_archived_practicum(): void
    {
        $this->actingAs(User::findOrFail(2));$service=app(\Modules\EOffice\Services\ManajemenPraktikum\FinalAssessment::class);
        try{$service->save(Praktikum::first(),['version'=>0,'students'=>[]],'dosen');$this->fail('Foreign lecturer accepted');}catch(\Symfony\Component\HttpKernel\Exception\HttpException $e){$this->assertSame(403,$e->getStatusCode());}
        $this->actingAs(User::findOrFail(1));Praktikum::first()->update(['is_active'=>false]);
        $this->expectException(ValidationException::class);$service->save(Praktikum::first(),['version'=>0,'students'=>[]],'koor');
    }

    private function scores(string $score): void
    {
        foreach (['s2', 's3'] as $student) {
            foreach ([1, 2] as $module) {
                foreach (['tugas_pendahuluan', 'tugas_pengganti', 'laporan', 'responsi'] as $type) {
                    DB::table('nilai_jenis_tugas')->insert(['modul_id' => $module, 'daftar_praktikan_id' => $student, 'jenis_tugas' => $type, 'nilai' => $score]);
                }
            }
            TaNilai::create(['tugas_akhir_id' => TugasAkhir::first()->id, 'praktikan_id' => $student, 'skor' => ['c' => $score], 'riwayat' => []]);
        }
    }

    private function publish(): void
    {
        $book = app(FinalGradeBook::class);
        DB::transaction(fn () => $book->propose(Praktikum::first()));
        DB::transaction(fn () => $book->publish(Praktikum::first()));
        DB::table('nilai_praktikum')->update(['dipublikasikan' => true, 'disetujui_koor' => true, 'disetujui_dosen' => true]);
    }

    public function test_nim_uses_seventh_and_eighth_digits_and_rejects_mismatched_data(): void
    {
        $service = app(StudentCohort::class);
        $this->assertSame(2023, $service->fromNim('21120123140136'));
        $this->assertSame(2024, $service->verified(Student::where('user_id', 3)->first()));
        foreach (['2112012314013', '21120199140136', '99999923140136', 'abc'] as $nim) {
            try {
                $service->fromNim($nim);
                $this->fail('Invalid NIM accepted');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('angkatan', $e->errors());
            }
        }
        $profile = Student::where('user_id', 2)->first();
        $profile->cohort_year = 2021;
        $this->expectException(ValidationException::class);
        $service->verified($profile);
    }

    public function test_all_predicate_boundaries_for_both_cohorts(): void
    {
        $manager = app(GradePolicyManager::class);
        $policy = $manager->active();
        foreach ([2023 => ['100' => 'A', '80' => 'A', '79.99' => 'B', '70' => 'B', '69.99' => 'C', '60' => 'C', '59.99' => 'D', '51' => 'D', '50.99' => 'E', '0' => 'E'], 2024 => ['100' => 'A', '85' => 'A', '84.99' => 'AB', '80' => 'AB', '79.99' => 'B', '75' => 'B', '74.99' => 'BC', '70' => 'BC', '69.99' => 'C', '60' => 'C', '59.99' => 'D', '40' => 'D', '39.99' => 'E', '0' => 'E']] as $cohort => $values) {
            foreach ($values as $value => $letter) {
                $this->assertSame($letter, $manager->predicate($policy, $cohort, (string) $value)['letter']);
            }
        }
    }

    public function test_individual_module_columns_use_ordered_values_and_frozen_publication(): void
    {
        $this->scores('82.25');
        DB::table('nilai_jenis_tugas')->where('modul_id',2)->update(['nilai'=>90.5]);
        $book=app(FinalGradeBook::class);$praktikum=Praktikum::first();
        $this->assertSame(['82.25','90.50'],array_values($book->report($praktikum)['rows']['s2']['module_values']));
        $this->publish();
        DB::table('nilai_jenis_tugas')->update(['nilai'=>0]);
        $this->assertSame(['82.25','90.50'],array_values($book->report($praktikum->fresh(),'2')['rows']['s2']['module_values']));
        foreach(['koor','dosen','mahasiswa'] as $role){
            $this->actingAs(User::find($role==='mahasiswa'?2:1));
            $html=view('eoffice::components.manajemen-praktikum.ui.final-grades',['praktikum'=>$praktikum->fresh(),'role'=>$role])->render();
            foreach(['MODUL 1','MODUL 2','82,25','90,50'] as $text)$this->assertStringContainsString($text,$html);
        }
    }

    public function test_empty_grades_are_zero_and_all_modules_have_equal_weight(): void
    {
        $row = $this->book()['rows']['s2'];
        $this->assertSame('0.00', $row['final']);
        $this->assertSame('E', $row['letter']);
        DB::table('nilai_jenis_tugas')->insert(['modul_id' => 1, 'daftar_praktikan_id' => 's2', 'jenis_tugas' => 'laporan', 'nilai' => 100]);
        $row = $this->book()['rows']['s2'];
        $this->assertSame('15.00', $row['module']);
        $this->assertSame('9.00', $row['final']);
    }

    public function test_final_rounds_only_once_and_cohorts_receive_different_predicates(): void
    {
        $this->scores('84.995');
        $rows = $this->book()['rows'];
        $this->assertSame('85.00', $rows['s2']['final']);
        $this->assertSame('A', $rows['s3']['letter']);
        DB::table('nilai_jenis_tugas')->update(['nilai' => '84.994']);
        TaNilai::query()->update(['skor' => json_encode(['c' => '84.994'])]);
        $rows = $this->book()['rows'];
        $this->assertSame('84.99', $rows['s3']['final']);
        $this->assertSame('AB', $rows['s3']['letter']);
        $this->assertSame('A', $rows['s2']['letter']);
        DB::table('nilai_jenis_tugas')->update(['nilai' => '84.995']);
        $this->assertSame('84.99', $this->book()['rows']['s3']['final'], 'Rounding the module subtotal first would incorrectly produce 85.00.');
    }

    public function test_revision_methods_are_recomputed_exactly_not_from_cached_float(): void
    {
        $this->scores('60');
        $task = TugasAkhir::first();
        $grade = TaNilai::where('praktikan_id', 's2')->first();
        $grade->update(['skor' => ['c' => 1], 'riwayat' => [['awal' => ['c' => '60'], 'revisi' => ['c' => '90']]]]);
        foreach (['rata_rata' => '75.00', 'ganti' => '90.00', 'tertinggi' => '90.00', 'berbobot' => '82.50'] as $method => $expected) {
            $task->update(['tahap' => [['id' => 'stage', 'nama' => 'Tahap', 'revisi_metode' => $method, 'revisi_bobot_awal' => 25]]]);
            $this->assertSame($expected, $this->book()['rows']['s2']['ta']);
        }
    }

    public function test_invalid_weights_and_cohort_block_proposal(): void
    {
        DB::table('eo_praktikum')->update(['bobot_gabungan_ta' => null]);
        $this->assertNull($this->book()['rows']['s2']['final']);
        $this->expectException(ValidationException::class);
        DB::transaction(fn () => app(FinalGradeBook::class)->propose(Praktikum::first()));
    }

    public function test_combined_weights_reject_fractions_stale_versions_and_approved_changes(): void
    {
        $this->actingAs(User::find(1));
        $controller = app(FinalGradeController::class);
        foreach ([['version' => 0, 'modules' => 60.5, 'ta' => 39.5], ['version' => 0, 'modules' => 60, 'ta' => 30], ['version' => 9, 'modules' => 60, 'ta' => 40]] as $data) {
            try {
                $controller->weights(Request::create('/test', 'POST', $data));
                $this->fail('Invalid weights accepted');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
        $controller->weights(Request::create('/test', 'POST', ['version' => 0, 'modules' => 70, 'ta' => 30]));
        $this->assertSame(70, (int) Praktikum::first()->bobot_gabungan_modul);
        DB::table('nilai_praktikum')->update(['disetujui_koor' => true]);
        $this->expectException(ValidationException::class);
        $controller->weights(Request::create('/test', 'POST', ['version' => 1, 'modules' => 80, 'ta' => 20]));
    }

    public function test_new_policy_can_add_future_cohorts_without_altering_old_rules(): void
    {
        $manager = app(GradePolicyManager::class);
        $old = $manager->active();
        $rules = $old->rules;
        $rules[1]['to'] = 2026;
        $rules[] = ['from' => 2027, 'to' => null, 'bands' => [['minimum' => '90', 'letter' => 'A', 'points' => '4.00'], ['minimum' => '0', 'letter' => 'E', 'points' => '0.00']]];
        $new = $manager->create(['nama' => 'Pedoman 2027', 'expected_policy' => $old->id, 'rules' => $rules]);
        $this->assertSame('AB', $manager->predicate($new, 2024, '82.00')['letter']);
        $this->assertSame('E', $manager->predicate($new, 2027, '89.99')['letter']);
        $this->assertSame('A', $manager->predicate($new, 2027, '90.00')['letter']);
        $this->assertSame(2, count($old->fresh()->rules));
    }

    public function test_changed_inputs_require_new_proposal(): void
    {
        $this->scores('80');
        $book = app(FinalGradeBook::class);
        DB::transaction(fn () => $book->propose(Praktikum::first()));
        DB::table('nilai_jenis_tugas')->where('id', 1)->update(['nilai' => 0]);
        $this->expectException(ValidationException::class);
        DB::transaction(fn () => $book->publish(Praktikum::first()));
    }

    public function test_published_snapshots_survive_new_policy_and_student_report_is_private(): void
    {
        $this->scores('82');
        $this->publish();
        $manager = app(GradePolicyManager::class);
        $old = $manager->active();
        $rules = $old->rules;
        $rules[1]['bands'][1]['letter'] = 'BA';
        $manager->create(['nama' => 'Versi baru', 'expected_policy' => $old->id, 'rules' => $rules]);
        $report = app(FinalGradeBook::class)->report(Praktikum::first(), '3');
        $this->assertCount(1, $report['rows']);
        $this->assertSame('AB', $report['rows']['s3']['letter']);
        $this->assertSame($old->id, $report['policy']->id);
        $this->assertSame('BA', $this->book()['rows']['s3']['letter']);
        DB::table('nilai_praktikum')->where('daftar_praktikan_id', 's3')->update(['dipublikasikan' => false]);
        $this->assertEmpty(app(FinalGradeBook::class)->report(Praktikum::first(), '3')['rows']);
    }

    public function test_policy_is_immutable_and_rejects_gaps_and_stale_version(): void
    {
        $manager = app(GradePolicyManager::class);
        $old = $manager->active();
        foreach ([2022, 2024] as $end) {
            $rules = $old->rules;
            $rules[0]['to'] = $end;
            try {
                $manager->create(['nama' => 'Tidak valid', 'expected_policy' => $old->id, 'rules' => $rules]);
                $this->fail('Gap/overlap accepted');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
        $new = $manager->create(['nama' => 'Versi 2', 'expected_policy' => $old->id, 'rules' => $old->rules]);
        $this->assertSame(2, $new->version);
        try {
            $manager->create(['nama' => 'Stale', 'expected_policy' => $old->id, 'rules' => $old->rules]);
            $this->fail('Stale version accepted');
        } catch (ValidationException $e) {
            $this->assertNotEmpty($e->errors());
        }
        $this->expectException(\LogicException::class);
        $old->update(['nama' => 'Rewrite']);
    }

    public function test_score_changes_invalidate_all_publication_flags_atomically(): void
    {
        $this->scores('80');
        $this->publish();
        NilaiJenisTugas::first()->update(['nilai' => 70]);
        $this->assertSame(0, DB::table('nilai_praktikum')->where('dipublikasikan', true)->count());
        $this->assertEmpty(app(FinalGradeBook::class)->report(Praktikum::first(), '2')['rows']);
        $this->assertSame(2, DB::table('eo_prak_final_results')->count());
    }

    public function test_single_publication_requires_lecturer_and_rolls_back_if_ta_version_is_stale(): void
    {
        $this->scores('82');
        $this->actingAs(User::find(1));
        $service = app(GradePublication::class);
        $this->mock(NotifikasiService::class, fn ($m) => $m->shouldReceive('kirim')->zeroOrMoreTimes());
        try {
            $service->approve(Request::create('/test', 'POST', ['ta_versions' => []]), Praktikum::first(), 'koor');
            $this->fail('Stale TA version accepted');
        } catch (ValidationException $e) {
            $this->assertNotEmpty($e->errors());
        }
        $this->assertSame(0, DB::table('eo_prak_grade_batches')->count());
        $request = Request::create('/test', 'POST', ['ta_versions' => TaNilai::pluck('version', 'id')->all()]);
        $service->approve($request, Praktikum::first(), 'koor');
        $this->assertSame('pending', DB::table('eo_prak_grade_batches')->value('status'));
        $this->assertEmpty(app(FinalGradeBook::class)->report(Praktikum::first(), '2')['rows']);
        $this->actingAs(User::find(4));
        $service->approve($request, Praktikum::first(), 'dosen');
        $this->assertSame('published', DB::table('eo_prak_grade_batches')->value('status'));
        $this->assertSame('82.00', app(FinalGradeBook::class)->report(Praktikum::first(), '2')['rows']['s2']['final']);
    }

    public function test_final_csv_and_multiple_cards_zip_include_correct_final_values(): void
    {
        $this->scores('82');
        $request = Request::create('/test', 'GET', ['cards' => ['nilai_akhir']]);
        $response = app(NilaiCsvExport::class)->download($request, Praktikum::first());
        ob_start();
        $response->sendContent();
        $csv = ob_get_clean();
        $this->assertStringContainsString('82.00,82.00,82.00,AB', $csv);
        $this->assertStringNotContainsString('CARD', $csv);
        $response = app(NilaiCsvExport::class)->download(Request::create('/test', 'GET', ['cards' => ['1', 'tugas_akhir', 'nilai_akhir']]), Praktikum::first());
        $this->assertStringContainsString('Rekap_Nilai_Praktikum_Test.zip', $response->headers->get('Content-Disposition'));
        ob_start();
        $response->sendContent();
        $zip = ob_get_clean();
        $this->assertSame('PK', substr($zip, 0, 2));
    }

    public function test_admin_verification_only_corrects_enrolled_students_and_is_audited(): void
    {
        DB::table('students')->where('user_id', 2)->update(['cohort_year' => 2021]);
        $this->actingAs(User::find(1));
        $student = Student::where('user_id', 2)->first();
        app(GradePolicyController::class)->verify(Request::create('/test', 'POST', ['nim' => $student->student_number, 'stored_year' => '2021']), $student, app(StudentCohort::class));
        $this->assertSame(2023, (int) $student->fresh()->cohort_year);
        $this->assertSame('verify_cohort', DB::table('eo_audit_logs')->value('action'));
    }

    public function test_new_views_compile_and_recap_renders(): void
    {
        foreach (['components/manajemen-praktikum/ui/final-grades', 'manajemen-praktikum/admin/grade-policies'] as $view) {
            $compiled = app('blade.compiler')->compileString(file_get_contents(base_path('Modules/EOffice/resources/views/'.$view.'.blade.php')));
            $this->assertNotEmpty(token_get_all($compiled,TOKEN_PARSE));
        }
        $this->actingAs(User::find(1));
        $html = view('eoffice::components.manajemen-praktikum.ui.final-grades',['praktikum' => Praktikum::first(), 'role' => 'koor'])->render();
        $this->assertStringContainsString('Nilai Keseluruhan',$html);
        $this->assertStringContainsString('Mahasiswa A',$html);
        $this->assertStringContainsString('RATA-RATA MODUL',$html);
        $this->assertStringContainsString('Rekapitulasi nilai mahasiswa pada Praktikum Test',$html);
        $this->assertStringContainsString('Semua Predikat',$html);
        $this->assertStringNotContainsString('Simpan Bobot',$html);
        $this->assertStringNotContainsString('Pratinjau nilai.',$html);
        $this->assertStringContainsString('mp-grade-disclosure',$html);
        $this->assertStringContainsString('mp-grade-disclosure has-separator',$html);
        $accordion=file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/grade-accordion.blade.php'));
        $this->assertStringContainsString('grid-template-columns:minmax(0,1fr)',$accordion);
        $this->assertStringContainsString('min-height:0;min-width:0;max-width:100%',$accordion);
        $this->assertStringContainsString('points="6 9 12 15 18 9"',$html);
        $this->assertStringNotContainsString('<strong>',$html);
        $this->assertStringContainsString('text-align:center;font-weight:700',$html);
        $this->assertStringContainsString('font-weight:600',$html);
        $this->assertStringContainsString(':inert="!expanded"',$html);
        $this->assertStringContainsString('>SHIFT</th>',$html);
        $this->assertSame(11,preg_match_all('/<th\b/',preg_replace('~<template x-for="\(column, columnIndex\) in components"[\s\S]*?</template>~','',$html)));
        $this->assertStringContainsString('align-items:center;gap:16px;flex-wrap:wrap;padding:16px 20px',$html);
        $this->assertStringContainsString('class="ta-options"',$html);
        $this->assertStringContainsString('x-model="predicate"',$html);
        $this->assertStringNotContainsString('final_predicate',$html);
        $this->assertSame('1',(string)app(FinalGradeBook::class)->report(Praktikum::first())['rows']['s2']['shift']);
        if(getenv('PRACTICUM_TA_COLLECTION_PREVIEW')==='1'){
            $css=view('eoffice::manajemen-praktikum.partials._styles')->render();$alpine=str_replace('\\','/',base_path('node_modules/alpinejs/dist/cdn.min.js'));
            $assets=glob(public_path('build/assets/*.css'));$built=$assets?'<link rel="stylesheet" href="file:///'.str_replace('\\','/',$assets[0]).'">':'';
            file_put_contents(storage_path('framework/testing/final-grade-fixture.html'),'<!doctype html><meta charset="utf-8">'.$built.$css.'<style>[x-cloak]{display:none!important}body{padding:24px}</style><script defer src="file:///'.$alpine.'"></script>'.$html);
        }
    }

    public function test_main_save_updates_combined_weights_and_rejects_stale_version(): void
    {
        $this->actingAs(User::find(1));
        $practicum=Praktikum::first();
        $data=['module_version'=>$practicum->komponen_modul_version??0];
        foreach (['bobot_tp','bobot_praktikum','bobot_laporan','bobot_responsi'] as $key) $data[$key]=$practicum->$key;
        $data['final_weights']=['version'=>0,'modules'=>70,'ta'=>30];
        $controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\NilaiController::class);
        $controller->updateBobot(Request::create('/test','POST',$data),$practicum->id);
        $this->assertSame(70,(int)$practicum->fresh()->bobot_gabungan_modul);
        $this->assertSame(30,(int)$practicum->fresh()->bobot_gabungan_ta);
        try {
            $controller->updateBobot(Request::create('/test','POST',$data),$practicum->id);
            $this->fail('Stale combined weights accepted');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('bobot_gabungan',$e->errors());
        }
        $this->assertSame(1,(int)$practicum->fresh()->bobot_gabungan_version);
        $data['final_weights']=['version'=>1,'modules'=>80,'ta'=>30];
        try {
            $controller->updateBobot(Request::create('/test','POST',$data),$practicum->id);
            $this->fail('Invalid combined total accepted');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('bobot_gabungan',$e->errors());
        }
        $this->assertSame(70,(int)$practicum->fresh()->bobot_gabungan_modul);
        unset($data['final_weights']);
        $controller->updateBobot(Request::create('/test','POST',$data),$practicum->id);
        $this->assertSame(1,(int)$practicum->fresh()->bobot_gabungan_version);
    }

    public function test_publication_confirmation_requires_explicit_confirmation_for_both_roles(): void
    {
        foreach (['koordinator'=>'koor','dosen'=>'dosen'] as $directory=>$role) {
            $html=view('eoffice::components.manajemen-praktikum.ui.publication-confirmation',['role'=>$role])->render();
            $this->assertStringContainsString('Konfirmasi Publikasi Nilai',$html);
            $this->assertStringContainsString('x-teleport="body"',$html);
            $this->assertStringContainsString('top:50%;left:50%;transform:translate(-50%,-50%)',$html);
            $this->assertStringContainsString('publicationConfirmed=true;',$html);
            $this->assertStringContainsString('$refs.publicationForm.requestSubmit()',$html);
            $this->assertStringContainsString('seluruh nilai modul, tugas akhir, dan nilai keseluruhan',$html);
            $this->assertStringContainsString($role==='koor'?'sampai mendapat persetujuan dosen':'dapat dilihat mahasiswa',$html);
            $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$directory.'/nilai.blade.php'));
            $this->assertStringContainsString('if(!publicationConfirmed){$event.preventDefault();showPublicationConfirm=true}',$source);
            $this->assertStringContainsString('if(publicationSubmitting){$event.preventDefault();return;}',$source);
            $this->assertStringContainsString('padding-block:20px!important; display:flex; align-items:center;',$source);
            $this->assertStringContainsString('ui.publication-confirmation role="'.$role.'"',$source);
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source),TOKEN_PARSE));
        }
    }

    public function test_searchable_grade_filters_and_save_confirmation_keep_required_controls(): void
    {
        $html=view('eoffice::components.manajemen-praktikum.ui.grade-group-filter',[
            'model'=>'globalKelompok','label'=>'Semua Kelompok','searchPlaceholder'=>'Cari kelompok...',
        ])->render();
        $this->assertStringContainsString('Cari kelompok...',$html);
        $this->assertStringContainsString('Semua Kelompok',$html);
        $this->assertStringContainsString('globalKelompok=$el.value.trim()',$html);
        $this->assertStringContainsString('@keydown.enter.prevent="open=false"',$html);
        $this->assertStringContainsString('class="ta-options"',$html);
        $this->assertStringNotContainsString('x-for=',$html);
        $this->assertStringNotContainsString('options.filter',$html);
        $page=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/koordinator/nilai.blade.php'));
        $this->assertStringContainsString(':disabled="!hasChanges"',$page);
        $this->assertStringContainsString("(hasChanges ? 'pointer' : 'not-allowed')",$page);
        $this->assertDoesNotMatchRegularExpression('/<th\b[^>]*border-(?:left|right)[^>]*>\s*(?:KELOMPOK|SHIFT)/',$page);
        $this->assertStringContainsString('if(!hasChanges){$event.preventDefault();return;}',$page);
        $modal=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/partials/module-weight-confirmation.blade.php'));
        $this->assertStringContainsString('top:50%;left:50%;transform:translate(-50%,-50%)',$modal);
        $this->assertStringContainsString('x-teleport="body"',$modal);
    }
}
