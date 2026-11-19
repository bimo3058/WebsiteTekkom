<?php

namespace Modules\EOffice\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\DashboardController;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\KoorPraktikumService;
use Modules\EOffice\Services\PraktikanGroupingService;
use Tests\TestCase;

class PraktikanGroupingWorkflowTest extends TestCase
{
    private const PRAKTIKUM_ID = '0199f001-0000-7000-8000-000000000001';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.connections.grouping_test' => ['driver' => 'sqlite', 'database' => ':memory:'],
            'cache.default' => 'array',
            'session.driver' => 'array',
        ]);
        DB::setDefaultConnection('grouping_test');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('eo_prak_praktikum', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->unsignedBigInteger('koor_id');
            $table->string('status')->default('aktif');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('jumlah_kelompok')->nullable();
            $table->unsignedInteger('jumlah_shift')->nullable();
            $table->string('metode_pembagian')->nullable();
            $table->json('draft_pembagian')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('eo_prak_daftar_praktikan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('praktikum_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('terdaftar');
            $table->string('kelompok')->nullable();
            $table->string('shift')->nullable();
            $table->timestamps();
        });
        foreach (['eo_prak_absensi_praktikum', 'eo_prak_pengumpulan_tugas', 'eo_prak_nilai_praktikum', 'eo_prak_nilai_jenis_tugas'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->uuid('daftar_praktikan_id');
                $table->float('nilai')->nullable();
            });
        }
        foreach (['eo_prak_ta_absensi', 'eo_prak_ta_pengumpulan', 'eo_prak_ta_nilai'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->uuid('praktikan_id');
            });
        }

        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Koordinator', 'email' => 'koor@example.test'],
            ['id' => 2, 'name' => 'Praktikan Satu', 'email' => 'p1@example.test'],
            ['id' => 3, 'name' => 'Praktikan Dua', 'email' => 'p2@example.test'],
        ]);
        DB::table('eo_prak_praktikum')->insert([
            'id' => self::PRAKTIKUM_ID,
            'nama' => 'Praktikum Test',
            'koor_id' => 1,
            'status' => 'aktif',
        ]);
        DB::table('eo_prak_daftar_praktikan')->insert([
            ['id' => '0199f002-0000-7000-8000-000000000001', 'praktikum_id' => self::PRAKTIKUM_ID, 'user_id' => 2],
            ['id' => '0199f002-0000-7000-8000-000000000002', 'praktikum_id' => self::PRAKTIKUM_ID, 'user_id' => 3],
        ]);

        $this->actingAs(User::findOrFail(1));
    }

    public function test_automatic_grouping_stays_as_draft_until_applied(): void
    {
        $controller = new DashboardController(
            $this->createMock(KoorPraktikumService::class),
            new PraktikanGroupingService,
        );

        $controller->updatePlotSettings(Request::create('/settings', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID,
            'jumlah_kelompok' => 2,
            'jumlah_shift' => 2,
            'method' => 'urutan_sistem',
        ]));

        $this->assertSame(0, DB::table('eo_prak_daftar_praktikan')->whereNotNull('kelompok')->count());
        $this->assertNotNull(Praktikum::findOrFail(self::PRAKTIKUM_ID)->draft_pembagian);

        $controller->applyPlot(Request::create('/apply', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID,
        ]));

        $this->assertSame(['1', '2'], DB::table('eo_prak_daftar_praktikan')->orderBy('user_id')->pluck('kelompok')->all());
        $this->assertSame(['1', '2'], DB::table('eo_prak_daftar_praktikan')->orderBy('user_id')->pluck('shift')->all());
        $this->assertNull(Praktikum::findOrFail(self::PRAKTIKUM_ID)->draft_pembagian);
    }

    public function test_admin_cannot_add_participants_after_first_activity(): void
    {
        DB::table('eo_prak_absensi_praktikum')->insert(['daftar_praktikan_id' => '0199f002-0000-7000-8000-000000000001']);
        $controller = new \Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DaftarPraktikanController;
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $controller->store(Request::create('/participants', 'POST', ['praktikum_id' => self::PRAKTIKUM_ID, 'user_ids' => [1]]));
    }

    public function test_import_matches_nim_and_verifies_name_without_name_only_fallback(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('student_number'); $table->softDeletes();
        });
        DB::table('students')->insert([
            ['user_id'=>1, 'student_number'=>'00123'],
            ['user_id'=>2, 'student_number'=>'00456'],
        ]);
        DB::table('users')->whereIn('id', [1,2])->update(['name'=>'Nama Sama']);
        $result = app(\Modules\EOffice\Services\PraktikanImportService::class)->import(self::PRAKTIKUM_ID, [
            ['Daftar peserta'], ['Nama', 'NIM'],
            [' nama   SAMA ', '00123'],
            ['Nama Sama', '00123'],
            ['Nama Sama', '00456'],
            ['Nama Salah', '00123'],
            ['Nama Sama', '99999'],
            ['Nama Sama', ''],
        ]);
        $this->assertSame(1, $result['added']);
        $this->assertSame(2, $result['duplicate']);
        $this->assertCount(3, $result['issues']);
        $this->assertSame([6,7,8], array_column($result['issues'], 'row'));
        $this->assertSame(3, DB::table('eo_prak_daftar_praktikan')->count());
    }

    public function test_import_requires_nim_and_name_headers_on_same_row(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(\Modules\EOffice\Services\PraktikanImportService::class)->import(self::PRAKTIKUM_ID, [['Nama'], ['Koordinator']]);
    }

    public function test_successful_import_shows_only_success_feedback(): void
    {
        $this->mock(\Modules\EOffice\Services\PraktikanImportService::class)->shouldReceive('import')->once()->andReturn(['added'=>3,'duplicate'=>2,'issues'=>[]]);
        $request=Request::create('/participants','POST',['praktikum_id'=>self::PRAKTIKUM_ID],[],['file'=>\Illuminate\Http\UploadedFile::fake()->createWithContent('peserta.csv',"NIM,Nama\n001,Contoh\n")]);
        $request->setLaravelSession(app('session.store'));
        app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DaftarPraktikanController::class)->store($request);
        $this->assertSame('Semua praktikan sudah terdaftar',session('success'));
        $this->assertNull(session('participant_import_reports.'.self::PRAKTIKUM_ID));
    }

    public function test_import_report_completion_does_not_clear_a_newer_report(): void
    {
        $key='participant_import_reports.'.self::PRAKTIKUM_ID;
        session()->put($key,['token'=>'new-report','issues'=>[['row'=>2,'nim'=>'001','reason'=>'Periksa']]]);
        $controller=new \Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DaftarPraktikanController;
        $request=Request::create('/complete','POST',['report_token'=>'old-report','confirmed'=>'1']);
        $request->setLaravelSession(app('session.store'));
        $controller->completeImport($request,self::PRAKTIKUM_ID);
        $this->assertSame('new-report',session($key.'.token'));
        $request->merge(['report_token'=>'new-report']);
        $controller->completeImport($request,self::PRAKTIKUM_ID);
        $this->assertNull(session($key));
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/admin/partials/participant-import-result.blade.php'));
        $this->assertStringContainsString('Tandai selesai',$source);
        $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source),TOKEN_PARSE));
        $confirmation=file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/schedule-confirmation.blade.php'));
        $this->assertStringContainsString('confirmed=true;$nextTick(()=>$refs.actionForm.requestSubmit())',$confirmation);
    }

    public function test_import_completion_requires_confirmation_without_removing_report(): void
    {
        $key='participant_import_reports.'.self::PRAKTIKUM_ID;
        session()->put($key,['token'=>'report','issues'=>[['row'=>1]]]);
        $request=Request::create('/complete','POST',['report_token'=>'report','confirmed'=>'0']);
        $request->setLaravelSession(app('session.store'));
        try{(new \Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DaftarPraktikanController)->completeImport($request,self::PRAKTIKUM_ID);$this->fail('Unconfirmed completion accepted');}
        catch(\Illuminate\Validation\ValidationException $e){
            $this->assertSame('Konfirmasikan penyelesaian hasil impor terlebih dahulu.',$e->errors()['confirmed'][0]);
            $this->assertSame('report',session($key.'.token'));
        }
    }

    public function test_import_reads_all_nim_name_column_pairs_and_repeated_headers(): void
    {
        Schema::create('students',function(Blueprint $t){$t->id();$t->integer('user_id');$t->string('student_number');$t->softDeletes();});
        DB::table('students')->insert([['user_id'=>1,'student_number'=>'001'],['user_id'=>2,'student_number'=>'002'],['user_id'=>3,'student_number'=>'003']]);
        $result=app(\Modules\EOffice\Services\PraktikanImportService::class)->import(self::PRAKTIKUM_ID,[
            ['No','NIM','Nama','NIM 2','Nama 2','NIM (3)','Nama (3)'],
            [1,'001','Koordinator','002','Praktikan Satu','999','Tidak Terdaftar'],
            ['No','NIM','Nama','NIM 2','Nama 2','NIM (3)','Nama (3)'],
            [2,'003','Praktikan Dua','','','',''],
        ]);
        $this->assertSame(1,$result['added']);
        $this->assertSame(2,$result['duplicate']);
        $this->assertCount(1,$result['issues']);
        $this->assertSame('F',$result['issues'][0]['column']);
        $this->assertSame(2,$result['issues'][0]['row']);
    }

    public function test_import_rejects_ambiguous_multiple_nims_without_matching_name_columns(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(\Modules\EOffice\Services\PraktikanImportService::class)->import(self::PRAKTIKUM_ID,[['NIM','Nama','NIM'],['001','Nama','002']]);
    }

    public function test_admin_manual_add_rejects_duplicate_participants(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->softDeletes();
        });
        DB::table('students')->insert(['user_id' => 2]);
        $controller = new \Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DaftarPraktikanController;
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $controller->store(Request::create('/participants', 'POST', ['praktikum_id' => self::PRAKTIKUM_ID, 'user_ids' => [2]]));
    }

    public function test_admin_manual_add_records_student_without_assigning_group_and_audits_it(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->softDeletes();
        });
        DB::table('students')->insert(['user_id' => 1]);
        foreach (['audit_logs', 'eo_audit_logs'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id(); $table->integer('user_id')->nullable(); $table->string('module')->nullable();
                $table->string('action'); $table->string('subject_type')->nullable(); $table->string('subject_id')->nullable();
                $table->text('description')->nullable(); $table->json('old_data')->nullable(); $table->json('new_data')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }
        $controller = new \Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DaftarPraktikanController;
        $controller->store(Request::create('/participants', 'POST', ['praktikum_id' => self::PRAKTIKUM_ID, 'user_ids' => [1]]));
        $student = DB::table('eo_prak_daftar_praktikan')->where('user_id', 1)->first();
        $this->assertNotNull($student);
        $this->assertNull($student->kelompok);
        $this->assertNull($student->shift);
        $this->assertSame(1, DB::table('eo_audit_logs')->count());
    }

    public function test_first_activity_locks_every_grouping_write_including_old_drafts(): void
    {
        $service = new PraktikanGroupingService;
        $draft = $service->automatic(collect([
            '0199f002-0000-7000-8000-000000000001',
            '0199f002-0000-7000-8000-000000000002',
        ]), 2, 1, 'urutan_sistem');
        Praktikum::findOrFail(self::PRAKTIKUM_ID)->update(['draft_pembagian' => $draft]);
        DB::table('eo_prak_absensi_praktikum')->insert(['daftar_praktikan_id' => '0199f002-0000-7000-8000-000000000001']);
        $controller = new DashboardController($this->createMock(KoorPraktikumService::class), $service);
        foreach (['updatePlotSettings', 'autoPlot', 'applyPlot', 'discardPlotDraft', 'resetPlot', 'updatePlot', 'saveGroupMembers', 'importPraktikan'] as $method) {
            try {
                $controller->$method(Request::create('/grouping', 'POST', [
                    'praktikum_id' => self::PRAKTIKUM_ID,
                    'dp_id' => '0199f002-0000-7000-8000-000000000001',
                ]));
                $this->fail($method.' must reject changes after activity.');
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
                $this->assertSame(403, $exception->getStatusCode(), $method);
            }
        }
        $this->assertSame($draft, Praktikum::findOrFail(self::PRAKTIKUM_ID)->draft_pembagian);
        $this->assertSame(0, DB::table('eo_prak_daftar_praktikan')->whereNotNull('kelompok')->count());
    }

    public function test_each_activity_source_locks_only_its_own_practicum(): void
    {
        $lock = new \Modules\EOffice\Services\PracticumGroupingLock;
        foreach (['eo_prak_absensi_praktikum', 'eo_prak_pengumpulan_tugas', 'eo_prak_nilai_praktikum', 'eo_prak_nilai_jenis_tugas', 'eo_prak_ta_absensi', 'eo_prak_ta_pengumpulan', 'eo_prak_ta_nilai'] as $table) {
            $column = str_contains($table, '_ta_') ? 'praktikan_id' : 'daftar_praktikan_id';
            $row = [$column => '0199f002-0000-7000-8000-000000000001'];
            if ($table === 'eo_prak_nilai_jenis_tugas') {
                DB::table($table)->insert($row);
                $this->assertFalse($lock->isLocked(self::PRAKTIKUM_ID));
                DB::table($table)->delete();
                $row['nilai'] = 0;
            }
            DB::table($table)->insert($row);
            $this->assertTrue($lock->isLocked(self::PRAKTIKUM_ID), $table);
            $this->assertFalse($lock->isLocked('0199f001-0000-7000-8000-000000000099'), $table);
            DB::table($table)->delete();
        }
    }

    public function test_hybrid_distribution_keeps_manual_assignment_and_does_not_publish(): void
    {
        $service = new PraktikanGroupingService;
        $draft = $service->manual(2, 2);
        $draft['assignments'] = ['0199f002-0000-7000-8000-000000000001' => '2'];
        Praktikum::findOrFail(self::PRAKTIKUM_ID)->update(['draft_pembagian' => $draft]);
        $controller = new DashboardController($this->createMock(KoorPraktikumService::class), $service);
        $controller->autoPlot(Request::create('/auto', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID, 'method' => 'urutan_sistem', 'remaining_only' => 1,
        ]));
        $result = Praktikum::findOrFail(self::PRAKTIKUM_ID)->draft_pembagian;
        $this->assertSame('2', $result['assignments']['0199f002-0000-7000-8000-000000000001']);
        $this->assertSame('1', $result['assignments']['0199f002-0000-7000-8000-000000000002']);
        $this->assertSame(0, DB::table('eo_prak_daftar_praktikan')->whereNotNull('kelompok')->count());
    }

    public function test_hybrid_rejects_an_inactive_practicum(): void
    {
        Praktikum::findOrFail(self::PRAKTIKUM_ID)->update(['status' => 'nonaktif', 'is_active' => false]);
        $controller = new DashboardController($this->createMock(KoorPraktikumService::class), new PraktikanGroupingService);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $controller->autoPlot(Request::create('/auto', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID, 'method' => 'acak', 'remaining_only' => 1,
        ]));
    }

    public function test_discarding_draft_keeps_active_grouping_unchanged(): void
    {
        DB::table('eo_prak_daftar_praktikan')->where('user_id', 2)->update(['kelompok' => 'Lama', 'shift' => 'Pagi']);
        $controller = new DashboardController(
            $this->createMock(KoorPraktikumService::class),
            new PraktikanGroupingService,
        );

        $controller->updatePlotSettings(Request::create('/settings', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID,
            'jumlah_kelompok' => 2,
            'jumlah_shift' => 1,
            'method' => 'manual',
        ]));
        $controller->discardPlotDraft(Request::create('/discard', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID,
        ]));

        $praktikan = DB::table('eo_prak_daftar_praktikan')->where('user_id', 2)->first();
        $this->assertSame('Lama', $praktikan->kelompok);
        $this->assertSame('Pagi', $praktikan->shift);
        $this->assertNull(Praktikum::findOrFail(self::PRAKTIKUM_ID)->draft_pembagian);
    }

    public function test_editing_an_active_group_automatically_creates_a_new_draft(): void
    {
        DB::table('eo_prak_praktikum')->where('id', self::PRAKTIKUM_ID)->update([
            'jumlah_kelompok' => 2,
            'jumlah_shift' => 2,
            'metode_pembagian' => 'manual',
        ]);
        DB::table('eo_prak_daftar_praktikan')->where('user_id', 2)->update(['kelompok' => '1', 'shift' => '1']);
        DB::table('eo_prak_daftar_praktikan')->where('user_id', 3)->update(['kelompok' => '2', 'shift' => '2']);
        $controller = new DashboardController(
            $this->createMock(KoorPraktikumService::class),
            new PraktikanGroupingService,
        );

        $controller->saveGroupMembers(Request::create('/members', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID,
            'kelompok' => '1',
            'shift' => '1',
            'members' => [
                '0199f002-0000-7000-8000-000000000001',
                '0199f002-0000-7000-8000-000000000002',
            ],
        ]));

        $this->assertSame(['1', '2'], DB::table('eo_prak_daftar_praktikan')->orderBy('user_id')->pluck('kelompok')->all());
        $draft = Praktikum::findOrFail(self::PRAKTIKUM_ID)->draft_pembagian;
        $this->assertSame('1', $draft['assignments']['0199f002-0000-7000-8000-000000000001']);
        $this->assertSame('1', $draft['assignments']['0199f002-0000-7000-8000-000000000002']);
    }
}
