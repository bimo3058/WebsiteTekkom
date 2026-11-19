<?php
namespace Modules\EOffice\Tests\Unit;

use App\Models\{User,Role};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Schema};
use Illuminate\Support\{Carbon,Str};
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{PracticumSchedulePeriod as Period,PracticumBookingRequest as Package,PracticumBookingMeeting as Meeting,Praktikum,Peminjaman,Modul,DaftarPraktikan};
use Modules\EOffice\Services\ManajemenPraktikum\{PracticumBookingWorkflow as Workflow,PracticumSchedulePlanner as Planner};
use Tests\TestCase;

class PracticumSchedulingTest extends TestCase
{
    use \Tests\Support\RunsLegacyPracticumMigrations;
    private User $cerc; private User $admin; private Period $period; private string $practicum;
    protected function setUp(): void
    {
        parent::setUp();config(['database.default'=>'schedule_test','database.connections.schedule_test'=>['driver'=>'sqlite','database'=>':memory:','prefix'=>''],'cache.default'=>'array']);DB::purge('schedule_test');
        $this->travelTo(Carbon::parse('2026-11-09 08:00:00'));
        \Illuminate\Support\Facades\Notification::fake();
        Schema::create('eo_audit_logs',function(Blueprint $t){$t->id();$t->integer('user_id')->nullable();$t->string('action');$t->text('description')->nullable();$t->json('old_data')->nullable();$t->json('new_data')->nullable();$t->timestamp('created_at')->nullable();$t->timestamp('cerc_deleted_at')->nullable();$t->integer('cerc_deleted_by')->nullable();});
        Schema::create('users',function(Blueprint $t){$t->id();$t->string('name');$t->string('email');$t->string('external_id')->nullable();$t->string('password')->nullable();$t->integer('session_version')->default(0);$t->timestamp('suspended_at')->nullable();$t->timestamps();$t->softDeletes();});
        Schema::create('roles',function(Blueprint $t){$t->id();$t->string('name');$t->string('guard_name')->default('web');$t->string('module')->default('eoffice');$t->boolean('is_academic')->default(false);$t->timestamps();});
        Schema::create('permissions',function(Blueprint $t){$t->id();$t->string('name');$t->string('guard_name')->default('web');$t->timestamps();});
        foreach(['model_has_roles'=>'role_id','model_has_permissions'=>'permission_id'] as $table=>$column)Schema::create($table,function(Blueprint $t)use($column){$t->unsignedBigInteger($column);$t->string('model_type');$t->unsignedBigInteger('model_id');});
        Schema::create('role_has_permissions',function(Blueprint $t){$t->unsignedBigInteger('role_id');$t->unsignedBigInteger('permission_id');});
        Schema::create('eo_prak_praktikum',function(Blueprint $t){$t->uuid('id')->primary();$t->string('nama');$t->string('tahun_ajaran');$t->string('semester');$t->boolean('is_active')->default(true);$t->integer('koor_id')->nullable();$t->json('draft_pembagian')->nullable();$t->timestamps();$t->softDeletes();});
        Schema::create('eo_prak_modul_praktikum',function(Blueprint $t){$t->id();$t->uuid('praktikum_id');$t->string('nama');$t->integer('urutan');$t->timestamps();});
        Schema::create('eo_prak_daftar_praktikan',function(Blueprint $t){$t->uuid('id')->primary();$t->uuid('praktikum_id');$t->integer('user_id');$t->string('kelompok')->nullable();$t->string('shift')->nullable();$t->timestamps();});
        Schema::create('eo_prak_asprak_praktikum',function(Blueprint $t){$t->id();$t->uuid('praktikum_id');$t->integer('user_id');$t->string('role');$t->timestamps();$t->softDeletes();});
        Schema::create('eo_prak_praktikum_dosen',function(Blueprint $t){$t->uuid('praktikum_id');$t->integer('dosen_id');});
        Schema::create('eo_mr_ruangans',function(Blueprint $t){$t->id();$t->string('nama');$t->string('kategori')->default('Laboratorium');$t->string('lokasi')->nullable();$t->string('lantai')->nullable();$t->json('fasilitas')->nullable();$t->integer('kapasitas');$t->boolean('is_active')->default(true);$t->timestamps();});
        Schema::create('eo_mr_blacklists',function(Blueprint $t){$t->id();$t->integer('user_id');$t->text('alasan')->nullable();});
        Schema::create('eo_mr_pengaturan',function(Blueprint $t){$t->id();$t->string('key');$t->string('value');$t->timestamps();});
        Schema::create('eo_mr_tanggal_libur',function(Blueprint $t){$t->id();$t->date('tanggal');$t->string('keterangan')->nullable();$t->timestamps();});
        Schema::create('eo_mr_jadwal_internal',function(Blueprint $t){$t->uuid('id')->primary();$t->integer('ruangan_id');$t->string('tipe_jadwal');$t->string('kategori')->default('kuliah');$t->integer('hari')->nullable();$t->date('tanggal_spesifik')->nullable();$t->date('tgl_mulai_efektif')->nullable();$t->date('tgl_selesai_efektif')->nullable();$t->time('jam_mulai');$t->time('jam_selesai');$t->string('keterangan')->nullable();$t->timestamps();});
        Schema::create('eo_mr_peminjamans',function(Blueprint $t){$t->id();$t->integer('user_id');$t->integer('created_by')->nullable();$t->integer('ruangan_id');$t->text('tujuan');$t->string('source_id')->nullable();$t->string('nomor_telepon')->nullable();$t->string('berkas_pendukung')->nullable();$t->date('tanggal_pinjam');$t->time('jam_mulai');$t->time('jam_selesai');$t->string('status');$t->text('alasan_penolakan')->nullable();$t->timestamp('waktu_approval')->nullable();$t->timestamps();$t->softDeletes();});
        $this->runLegacyPracticumMigration(base_path('Modules/EOffice/database/migrations/2026_11_05_000001_create_practicum_scheduling_tables.php'));
        (require base_path('Modules/EOffice/database/migrations/2026_11_05_000003_use_room_source_for_practicum_bookings.php'))->up();
        $this->mock(\Modules\EOffice\Services\NotifikasiService::class)->shouldReceive('kirim')->zeroOrMoreTimes();
        foreach(['koordinator_cerc','admin_eoffice','mahasiswa','superadmin','admin_praktikum'] as $name)Role::create(['name'=>$name,'guard_name'=>'web']);
        $this->cerc=User::create(['name'=>'Pengurus CERC','email'=>'cerc@example.test']);$this->cerc->assignRole('koordinator_cerc');
        $this->admin=User::create(['name'=>'Admin Ruang','email'=>'room@example.test']);$this->admin->assignRole('admin_eoffice');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->period=Period::create(['tahun_ajaran'=>'2026/2027','semester'=>'Ganjil','starts_on'=>'2026-11-09','ends_on'=>'2027-01-31','version'=>0]);
        $this->practicum=(string)Str::uuid();
        Praktikum::forceCreate(['id'=>$this->practicum,'nama'=>'Praktikum Jaringan','tahun_ajaran'=>'2026/2027','semester'=>'Ganjil','is_active'=>true,'koor_id'=>$this->cerc->id,'draft_pembagian'=>null]);
        foreach(range(1,3) as $i)Modul::create(['praktikum_id'=>$this->practicum,'nama'=>'Modul '.$i,'urutan'=>$i]);
        foreach(range(1,4) as $i)DaftarPraktikan::create(['praktikum_id'=>$this->practicum,'user_id'=>$i,'kelompok'=>(string)ceil($i/2),'shift'=>$i<3?'1':'2']);
        DB::table('eo_mr_ruangans')->insert([['id'=>1,'nama'=>'Lab A','kapasitas'=>30,'is_active'=>true],['id'=>2,'nama'=>'Lab B','kapasitas'=>30,'is_active'=>true]]);
        $this->actingAs($this->cerc);
    }
    public function test_calendar_confirmation_persists_draft_and_keeps_calendar_position(): void
    {
        $id=(string)Str::uuid();
        $request=Request::create('/','POST',['version'=>0,'payload'=>json_encode(['plans'=>$this->plans()]),'week'=>'2026-11-16','calendar_room'=>'1']);
        $response=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class)->save($request,app(Workflow::class),$id);
        $this->assertSame(route('eoffice.manprak.cerc.scheduling.show',['id'=>$id,'week'=>'2026-11-16','calendar_room'=>'1']).'#kalender-ruang',$response->getTargetUrl());
        $this->assertSame('draft',Package::findOrFail($id)->status);
        $this->assertSame(6,Meeting::where('request_id',$id)->count());
        $this->assertSame(0,Peminjaman::count());
    }
    public function test_completed_shift_is_visible_while_other_shift_remains_incomplete(): void
    {
        $plans=$this->plans();$plans[0]['slots'][1]=['shift'=>'2','weekday'=>'','room_id'=>'','starts_at'=>'','ends_at'=>''];
        $p=app(Workflow::class)->saveDraft((string)Str::uuid(),0,['plans'=>$plans]);
        $this->assertCount(3,$p->meetings);$this->assertSame(['1'],$p->meetings->pluck('shift')->unique()->values()->all());
        $this->assertNotEmpty(app(Workflow::class)->review($p)['errors']);
        $p->meetings()->delete();
        $request=Request::create('/','GET',['week'=>'2026-11-16','calendar_room'=>'1']);
        $data=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class)->show($request,$p->id)->getData();
        $this->assertCount(3,$data['meetings']);$this->assertNotEmpty(array_filter($data['calendar']['events'],fn($event)=>$event['status']==='Rancangan'));
        $this->expectException(ValidationException::class);app(Workflow::class)->submit($p->id,$p->version);
    }
    private function plans(): array{return [['praktikum_id'=>$this->practicum,'week_start'=>'2026-11-16','modules'=>['1','2','3'],'slots'=>[['shift'=>'1','weekday'=>1,'room_id'=>1,'starts_at'=>'08:00','ends_at'=>'10:00'],['shift'=>'2','weekday'=>3,'room_id'=>1,'starts_at'=>'08:00','ends_at'=>'10:00']]]];}
    public function test_submission_creates_standard_pending_bookings_with_session_source_ids(): void
    {
        $p=$this->submitted();
        $this->assertSame(6,Peminjaman::where('status','menunggu')->where('source','praktikum')->count());
        foreach($p->meetings()->with('booking')->get() as $m)$this->assertSame((string)$m->id,(string)$m->booking->getRawOriginal('source_id'));
        $this->assertSame(1,\Modules\EOffice\Models\CercCoordinatorHistory::activities()->where('description','Pengajuan peminjaman ruang')->count());
        $this->assertSame(0,Meeting::official()->count());
    }
    public function test_repeated_submission_is_rejected_without_duplicate_bookings(): void
    {
        $p=$this->submitted();$this->assertSame(6,Peminjaman::count());
        try{app(Workflow::class)->submit($p->id,$p->version);$this->fail('Repeated submission accepted');}
        catch(ValidationException){$this->assertSame(6,Peminjaman::count());}
    }
    public function test_package_approval_routes_are_removed_and_mr_core_stays_independent(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('eoffice.peminjaman.admin.praktikum.decide'));
        foreach(['Models/Peminjaman.php','Models/MrJadwalInternal.php','app/Http/Controllers/ManajemenRuangan/Admin/PersetujuanController.php'] as $file){
            $source=file_get_contents(base_path('Modules/EOffice/'.$file));
            $this->assertStringNotContainsString('RoomApprovalInbox',$source);
            $this->assertStringNotContainsString('practicumMeeting',$source);
            $this->assertStringNotContainsString('RoomBookingGuard',$source);
        }
    }
    private function draft(): Package{return app(Workflow::class)->saveDraft((string)Str::uuid(),0,['period_id'=>$this->period->id,'plans'=>$this->plans()]);}
    private function submitted(): Package{$p=$this->draft();return app(Workflow::class)->submit($p->id,$p->version);}
    private function approved(): Package{$p=$this->submitted();Peminjaman::whereIn('id',$p->meetings()->pluck('booking_id'))->update(['status'=>'disetujui']);return app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumBookingStatus::class)->sync($p);}
    private function ordinary(string $date='2026-11-16',string $start='08:00',string $end='10:00'): Peminjaman{return Peminjaman::create(['user_id'=>$this->admin->id,'ruangan_id'=>1,'tujuan'=>'Kuliah resmi','tanggal_pinjam'=>$date,'jam_mulai'=>$start,'jam_selesai'=>$end,'status'=>'disetujui']);}
    public function test_three_modules_and_two_shifts_generate_six_meetings_and_holiday_moves_whole_week(): void
    {
        DB::table('eo_mr_tanggal_libur')->insert(['tanggal'=>'2026-11-16']);$p=$this->draft();
        $rows=$p->meetings()->get();$this->assertCount(6,$rows);
        $this->assertSame('2026-11-23',$rows->firstWhere('shift','1')->date->toDateString());
        $this->assertSame('2026-11-25',$rows->firstWhere('shift','2')->date->toDateString());
        $this->assertNotEmpty($p->plan['notes']);$this->assertEmpty(app(Workflow::class)->review($p)['errors']);
    }
    public function test_independent_decisions_publish_only_approved_sessions(): void
    {
        $p=$this->submitted();$rows=$p->meetings()->with('booking')->get(); $rows[0]->booking->update(['status'=>'disetujui']);$rows[1]->booking->update(['status'=>'ditolak']);
        $sync=app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumBookingStatus::class);
        $this->assertSame('pending',$sync->sync($p)->status);
        $this->assertSame(1,Meeting::official()->count());
        foreach($rows->slice(2) as $m)$m->booking->update(['status'=>'ditolak']);
        $this->assertSame('partial',$sync->sync($p)->status);
        $this->assertSame(1,Meeting::where('state','approved')->count());
    }
    public function test_failed_later_booking_rolls_back_the_whole_submission(): void
    {
        $p=$this->draft();
        $real=new \Modules\EOffice\Services\RoomBookingService();
        $mock=\Mockery::mock(\Modules\EOffice\Services\RoomBookingService::class);
        $calls=0;$mock->shouldReceive('bookRoom')->andReturnUsing(function($payload)use($real,&$calls){
            if(++$calls===3)throw new \Exception('Ruangan baru saja dipesan pihak lain.');
            return $real->bookRoom($payload);
        });
        $this->app->instance(\Modules\EOffice\Services\RoomBookingService::class,$mock);
        try{app(Workflow::class)->submit($p->id,$p->version);$this->fail('Failure ignored');}
        catch(ValidationException){$this->assertSame(0,Peminjaman::count());$this->assertSame('draft',$p->fresh()->status);$this->assertSame(0,$p->meetings()->whereNotNull('booking_id')->count());}
    }
    public function test_incomplete_draft_can_save_but_cannot_submit(): void
    {
        DaftarPraktikan::first()->update(['shift'=>null]);$p=$this->draft();$this->assertSame('draft',$p->status);$this->assertCount(0,$p->meetings);
        $this->expectException(ValidationException::class);app(Workflow::class)->submit($p->id,$p->version);
    }
    public function test_withdrawal_cancels_pending_bookings_and_allows_resubmission(): void
    {
        $p=$this->submitted();$oldIds=$p->meetings()->pluck('booking_id');
        $p=app(Workflow::class)->withdraw($p->id,$p->version);
        $this->assertSame('draft',$p->status);$this->assertSame(6,Peminjaman::whereIn('id',$oldIds)->where('status','dibatalkan')->count());
        $this->assertSame(0,$p->meetings()->whereNotNull('booking_id')->count());
        $p=app(Workflow::class)->submit($p->id,$p->version);
        $this->assertSame(6,Peminjaman::where('status','menunggu')->count());$this->assertSame(12,Peminjaman::count());
    }
    public function test_cancellation_of_selected_week_is_immediate_and_preserves_other_weeks(): void
    {
        $p=$this->approved();$target=$p->meetings()->first();
        app(Workflow::class)->amendment([$target->id],'cancel','Libur kegiatan kampus');
        $this->assertSame('dibatalkan',$target->booking->fresh()->status);
        $this->assertSame('cancelled',$target->fresh()->state);
        $this->assertSame(5,Meeting::official()->count());$this->assertSame(6,Meeting::count());
        $this->assertContains('bookings_cancelled',array_column($p->fresh()->history,'action'));
    }
    public function test_reschedule_is_blocked_without_cancelling_the_original_booking(): void
    {
        $p=$this->approved();$target=$p->meetings()->first();
        try{app(Workflow::class)->amendment([$target->id],'change','Pindah ruangan');$this->fail('Unconfirmed reschedule used');}
        catch(ValidationException){$this->assertSame('disetujui',$target->booking->fresh()->status);}
    }
    public function test_stale_version_and_changed_roster_block_submission(): void
    {
        $p=$this->draft();DaftarPraktikan::first()->update(['kelompok'=>'9']);
        try{app(Workflow::class)->submit($p->id,$p->version);$this->fail('Stale roster accepted');}catch(ValidationException $e){$this->assertStringContainsString('berubah',implode(' ',collect($e->errors())->flatten()->all()));}
        $this->expectException(ValidationException::class);app(Workflow::class)->saveDraft($p->id,0,['period_id'=>$this->period->id,'plans'=>$this->plans()]);
    }
    public function test_standard_room_service_blocks_overlaps_and_accepts_adjacent_slots(): void
    {
        $booking=$this->ordinary();
        // SQLite stores Eloquent's date cast as datetime; PostgreSQL's DATE column normalizes it.
        DB::table('eo_mr_peminjamans')->where('id',$booking->id)->update(['tanggal_pinjam'=>'2026-11-16']);
        $service=app(\Modules\EOffice\Services\RoomBookingService::class);
        $payload=['source'=>'praktikum','source_id'=>(string)Str::uuid(),'ruangan_id'=>1,'tanggal'=>'2026-11-16','jam_mulai'=>'09:00','jam_selesai'=>'11:00','user_id'=>$this->cerc->id];
        try{$service->bookRoom($payload);$this->fail('Overlap accepted');}catch(\Exception){$this->assertSame(1,Peminjaman::count());}
        $payload['jam_mulai']='10:00';$payload['jam_selesai']='11:00';
        $this->assertSame('menunggu',$service->bookRoom($payload)->status);
    }
    public function test_missing_source_reference_prevents_cancelling_the_wrong_booking(): void
    {
        $p=$this->approved();$target=$p->meetings()->first();$target->booking->update(['source_id'=>null]);
        try{app(Workflow::class)->amendment([$target->id],'cancel','Batal');$this->fail('Missing reference ignored');}
        catch(ValidationException){$this->assertSame('disetujui',$target->booking->fresh()->status);}
    }
    public function test_source_packet_cannot_be_cancelled_by_unauthorized_user(): void
    {
        $p=$this->draft();$this->actingAs($this->admin);$this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);app(Workflow::class)->submit($p->id,$p->version);
    }
    public function test_internal_timetable_capacity_and_period_boundaries_block_packets(): void
    {
        DB::table('eo_mr_jadwal_internal')->insert(['id'=>Str::uuid(),'ruangan_id'=>1,'tipe_jadwal'=>'rutin','hari'=>1,'jam_mulai'=>'08:00','jam_selesai'=>'10:00']);
        DB::table('eo_mr_ruangans')->where('id',1)->update(['kapasitas'=>1]);$p=$this->draft();$errors=implode(' ',app(Workflow::class)->review($p)['errors']);
        $this->assertStringContainsString('internal',$errors);$this->assertStringContainsString('kapasitas',$errors);
        $this->period->update(['ends_on'=>'2026-11-17','version'=>1]);$this->assertStringContainsString('periode',implode(' ',app(Workflow::class)->review($p)['errors']));
    }
    public function test_scheduling_templates_compile(): void
    {
        $paths=array_merge(glob(base_path('Modules/EOffice/resources/views/manajemen-praktikum/scheduling/*.blade.php')),glob(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/*schedule*.blade.php')));
        foreach(['cerc','koordinator','asprak','mahasiswa'] as $role)$paths[]=base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$role.'/dashboard.blade.php');
        $paths[]=base_path('Modules/EOffice/resources/views/manajemen-praktikum/mahasiswa/praktikum.blade.php');
        foreach($paths as $path){$compiled=app('blade.compiler')->compileString(file_get_contents($path));try{$this->assertNotEmpty(token_get_all($compiled,TOKEN_PARSE));}catch(\ParseError $e){throw new \RuntimeException(basename($path).': '.$e->getMessage(),0,$e);}}
    }

    public function test_integer_source_id_schema_blocks_submission_with_a_readable_message(): void
    {
        Schema::table('eo_mr_peminjamans',fn(Blueprint $t)=>$t->bigInteger('source_id')->nullable()->change());
        $p=$this->draft();
        try{app(Workflow::class)->submit($p->id,$p->version);$this->fail('Incompatible source type accepted');}
        catch(ValidationException $e){$this->assertStringContainsString('UUID',implode(' ',collect($e->errors())->flatten()->all()));$this->assertSame(0,Peminjaman::count());$this->assertSame('draft',$p->fresh()->status);}
    }

    public function test_withdrawal_cannot_cancel_partially_approved_requests(): void
    {
        $p=$this->submitted();$p->meetings()->first()->booking->update(['status'=>'disetujui']);
        try{app(Workflow::class)->withdraw($p->id,$p->version);$this->fail('Partial approval ignored');}
        catch(ValidationException){$this->assertSame(0,Peminjaman::where('status','dibatalkan')->count());}
    }

    public function test_published_schedule_is_scoped_for_students_assistants_and_lecturers(): void
    {
        $this->approved();$practicum=Praktikum::findOrFail($this->practicum);
        $render=fn($role)=>\Illuminate\Support\Facades\Blade::render('<x-eoffice::manajemen-praktikum.ui.practicum-schedule :praktikum="$praktikum" :role="$role" />',['praktikum'=>$practicum,'role'=>$role]);
        $this->actingAs($this->cerc);$studentHtml=$render('mahasiswa');$this->assertSame(6,substr_count($studentHtml,'class="mp-tr"'));$this->assertStringContainsString('seluruh modul dan shift',$studentHtml);$this->assertStringNotContainsString('CATATAN',$studentHtml);
        Schema::create('eo_prak_modul_asprak',function(Blueprint $t){$t->integer('asprak_id');$t->integer('modul_id');});
        $membership=\Modules\EOffice\Models\AsprakPraktikum::create(['praktikum_id'=>$this->practicum,'user_id'=>$this->cerc->id,'role'=>'asprak']);
        DB::table('eo_prak_modul_asprak')->insert(['asprak_id'=>$membership->id,'modul_id'=>1]);
        $this->assertSame(2,substr_count($render('asprak'),'class="mp-tr"'));
        DB::table('eo_prak_praktikum_dosen')->insert(['praktikum_id'=>$this->practicum,'dosen_id'=>$this->cerc->id]);
        $this->assertSame(6,substr_count($render('dosen'),'class="mp-tr"'));
        $outsider=User::forceCreate(['id'=>99,'name'=>'Tidak Terdaftar','email'=>'outsider@example.test']);
        $this->actingAs($outsider);$this->assertStringNotContainsString('Jadwal Praktikum',$render('mahasiswa'));
    }

    public function test_dashboard_schedule_shows_only_upcoming_official_sessions_and_preserves_semester_view(): void
    {
        $this->approved();
        $this->actingAs($this->cerc);
        $practicum=Praktikum::findOrFail($this->practicum);
        $render=fn($dashboard)=>\Illuminate\Support\Facades\Blade::render('<x-eoffice::manajemen-praktikum.ui.practicum-schedule :praktikum="$praktikum" role="mahasiswa" :dashboard="$dashboard" />',['praktikum'=>$practicum,'dashboard'=>$dashboard]);
        $this->assertSame(0,substr_count($render(true),'class="mp-tr"'));
        $this->travelTo(Carbon::parse('2026-11-16 08:30:00'));
        $html=$render(true);
        $this->assertSame(1,substr_count($html,'class="mp-tr"'));
        $this->assertStringContainsString('Jadwal Praktikum Terdekat',$html);
        $this->assertStringContainsString('Lihat Semua Jadwal',$html);
        $this->assertStringContainsString('dashboard_schedule_per_page=15',$html);
        $this->assertSame(6,substr_count($render(false),'class="mp-tr"'));
        $this->travelTo(Carbon::parse('2026-11-16 10:01:00'));
        $this->assertSame(0,substr_count($render(true),'class="mp-tr"'));
        $outsider=User::forceCreate(['id'=>99,'name'=>'Tidak Terdaftar','email'=>'outsider-dashboard@example.test']);
        $this->actingAs($outsider);
        $this->assertStringNotContainsString('Jadwal Praktikum Terdekat',$render(true));
    }

    public function test_student_dashboard_combines_all_enrolled_active_practicums_without_leaking_other_schedules(): void
    {
        $package=$this->approved();$this->travelTo(Carbon::parse('2026-11-16 08:30:00'));
        $original=$package->meetings()->with('booking')->orderBy('date')->firstOrFail();
        $addSession=function($practicum,$status='disetujui',$shift='1')use($original){
            $module=Modul::create(['praktikum_id'=>$practicum->id,'nama'=>'Modul '.$practicum->nama,'urutan'=>1]);
            $meeting=$original->replicate();$meeting->booking_id=null;$meeting->shift=$shift;$meeting->module_id=(string)$module->id;$meeting->praktikum_id=$practicum->id;$meeting->module_name=$module->nama;$meeting->save();
            $booking=$original->booking->getAttributes();unset($booking['id']);
            $booking['source_id']=(string)$meeting->id;$booking['status']=$status;
            $meeting->update(['booking_id'=>DB::table('eo_mr_peminjamans')->insertGetId($booking)]);
        };
        foreach([['Digital Terdaftar',true,true],['Praktikum Nonaktif',false,true],['Praktikum Orang Lain',true,false]] as [$name,$active,$enrolled]){
            $course=Praktikum::forceCreate(['id'=>(string)Str::uuid(),'nama'=>$name,'tahun_ajaran'=>'2026/2027','semester'=>'Ganjil','is_active'=>$active]);
            $shift=$name==='Digital Terdaftar'?'2':'1';
            if($enrolled)DaftarPraktikan::create(['praktikum_id'=>$course->id,'user_id'=>$this->cerc->id,'shift'=>$shift]);
            $addSession($course,'disetujui',$shift);
            if($name==='Digital Terdaftar'){
                $addSession($course,'disetujui','1');
                foreach(['menunggu','ditolak','dibatalkan'] as $status)$addSession($course,$status,'2');
            }
        }
        $render=fn()=>\Illuminate\Support\Facades\Blade::render('<x-eoffice::manajemen-praktikum.ui.practicum-schedule role="mahasiswa" :dashboard="true" />');
        $html=$render();
        $this->assertSame(2,substr_count($html,'class="mp-tr"'));
        $this->assertStringContainsString('NAMA PRAKTIKUM',$html);
        $this->assertStringContainsString('Praktikum Jaringan',$html);$this->assertStringContainsString('Digital Terdaftar',$html);
        $this->assertStringNotContainsString('Praktikum Nonaktif',$html);$this->assertStringNotContainsString('Praktikum Orang Lain',$html);
        $this->assertStringContainsString('dashboard_schedule_range=all',$html);
        $request=Request::create('/dashboard','GET',['dashboard_schedule_range'=>'all','dashboard_schedule_per_page'=>15]);
        $this->app->instance('request',$request);
        \Illuminate\Pagination\Paginator::currentPageResolver(fn()=>1);
        $html=$render();$this->assertSame(4,substr_count($html,'class="mp-tr"'));
        $this->assertStringContainsString('Lihat Jadwal Terdekat',$html);
        $this->assertStringNotContainsString('Praktikum Nonaktif',$html);
        foreach([null,''] as $unassignedShift){
            DaftarPraktikan::where('praktikum_id',$this->practicum)->where('user_id',$this->cerc->id)->update(['shift'=>$unassignedShift]);
            $html=$render();$this->assertSame(1,substr_count($html,'class="mp-tr"'));
            $this->assertStringNotContainsString('Praktikum Jaringan',$html);
        }
    }

    public function test_editor_and_list_render_per_booking_status_without_custom_approval(): void
    {
        view()->share('errors',new \Illuminate\Support\ViewErrorBag());
        $controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class);
        $render=function($view,$page){$source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/scheduling/'.$page.'.blade.php'));$source=preg_replace('/^<x-dynamic-component[^\\r\\n]*>|<\\/x-dynamic-component>\\s*$/m','',$source);return \Illuminate\Support\Facades\Blade::render($source,$view->getData());};
        $p=$this->submitted();$html=$render($controller->show(new Request,$p->id),'editor');
        $this->assertStringContainsString('Tarik ke Rancangan',$html);
        $this->assertStringContainsString('STATUS',$html);
        $this->assertStringNotContainsString('Keputusan Admin Ruang',$html);
        $list=$render($controller->index(Request::create('/list','GET',['tab'=>'requests'])),'index');
        $this->assertStringContainsString('Praktikum Jaringan',$list);$this->assertStringNotContainsString($p->code,$list);
    }

    public function test_room_calendar_distinguishes_occupied_pending_holidays_and_draft_conflicts(): void
    {
        $this->ordinary();
        Peminjaman::create(['user_id'=>$this->cerc->id,'ruangan_id'=>1,'tujuan'=>'Pengajuan lain','tanggal_pinjam'=>'2026-11-17','jam_mulai'=>'08:00','jam_selesai'=>'09:00','status'=>'menunggu']);
        DB::table('eo_mr_tanggal_libur')->insert(['tanggal'=>'2026-11-18']);
        $controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class);
        $calendar=$controller->calendar(Request::create('/calendar','GET',['week'=>'2026-11-16']));
        $presenter=app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumCalendarPresentation::class);$grid=$presenter->build($calendar);
        $this->assertSame('occupied',$grid['cells']['2026-11-16'][1][8]['state']);$this->assertFalse($grid['cells']['2026-11-16'][1][8]['selectable']);
        $this->assertSame(2,$grid['cells']['2026-11-16'][1][8]['rowspan']);$this->assertTrue($grid['cells']['2026-11-16'][1][9]['skip']);
        $this->assertSame('available',$grid['cells']['2026-11-16'][1][10]['state']);
        $this->assertSame('pending',$grid['cells']['2026-11-17'][1][8]['state']);$this->assertFalse($grid['cells']['2026-11-17'][1][8]['selectable']);
        $this->assertSame('Hari libur',$grid['cells']['2026-11-18'][1][8]['closed']);$this->assertFalse($grid['cells']['2026-11-18'][1][8]['selectable']);
        $calendar['events'][]=['id'=>'draft-test','room_id'=>'1','date'=>'2026-11-16','start'=>'08:00','end'=>'10:00','status'=>'Rancangan','title'=>'Rancangan bentrok','room'=>'Lab A'];
        $this->assertSame('conflict',$presenter->build($calendar)['cells']['2026-11-16'][1][8]['state']);
        $filtered=$controller->calendar(Request::create('/calendar','GET',['week'=>'2026-11-16','calendar_room'=>'2']));
        $this->assertSame([2],$presenter->build($filtered)['rooms']->pluck('id')->all());
    }

    public function test_calendar_slot_controls_are_only_rendered_for_editable_new_packages(): void
    {
        $calendar=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class)->calendar(Request::create('/calendar','GET',['week'=>'2026-11-16']));
        $package=new Package();$render=fn($editable)=>view('eoffice::manajemen-praktikum.scheduling.calendar',['calendar'=>$calendar,'package'=>$package,'editable'=>$editable])->render();
        $editableHtml=$render(true);$this->assertStringContainsString('@pointerdown="startCalendarRange(JSON.parse(',$editableHtml);
        $this->assertStringContainsString('Semua Ruangan',$editableHtml);$this->assertStringContainsString('Minggu Depan',$editableHtml);
        $this->assertStringNotContainsString('@pointerdown="startCalendarRange(',$render(false));
        $package->kind='cancel';$this->assertStringNotContainsString('@pointerdown="startCalendarRange(',$render(true));
    }

    public function test_package_search_finds_practicum_names_and_keeps_empty_results_empty(): void
    {
        $this->draft();$controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class);
        $this->assertSame(1,$controller->index(Request::create('/list','GET',['q'=>'jaringan']))->getData()['packages']->total());
        $this->assertSame(0,$controller->index(Request::create('/list','GET',['q'=>'tidak cocok']))->getData()['packages']->total());
    }

    public function test_availability_excludes_sidang_and_unknown_room_categories(): void
    {
        DB::table('eo_mr_ruangans')->insert(['id'=>3,'nama'=>'Sidang 1','kategori'=>'Sidang','kapasitas'=>50,'is_active'=>true]);
        $rooms=app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumRoomBooking::class)->availability('2026-11-16','08:00','10:00');
        $this->assertSame([1,2],array_column($rooms,'id'));
        $plans=$this->plans();$plans[0]['slots'][0]['room_id']=3;
        $p=app(Workflow::class)->saveDraft((string)Str::uuid(),0,['plans'=>$plans]);
        $this->assertStringContainsString('Kelas atau Laboratorium',implode(' ',app(Workflow::class)->review($p)['errors']));
    }

    public function test_legacy_notes_are_preserved_but_not_forwarded_to_new_room_bookings(): void
    {
        $plans=$this->plans();$plans[0]['slots'][0]['note']='Catatan ruang';
        $p=app(Workflow::class)->saveDraft((string)Str::uuid(),0,['plans'=>$plans]);app(Workflow::class)->submit($p->id,$p->version);
        $this->assertSame(0,Peminjaman::where('tujuan','like','%Catatan ruang')->count());
        $this->assertSame(3,$p->meetings()->where('note','Catatan ruang')->count());
        $this->assertSame(6,Peminjaman::whereNotNull('source_id')->count());
    }

    public function test_source_migration_repairs_inverse_link_and_preserves_other_sources(): void
    {
        $package=$this->approved();$target=$package->meetings()->first();$bookingId=$target->booking_id;
        $migration=require base_path('Modules/EOffice/database/migrations/2026_11_05_000003_use_room_source_for_practicum_bookings.php');$migration->down();
        $other=$this->ordinary('2026-11-17');$other->update(['source'=>'capstone']);$target->update(['booking_id'=>null,'note'=>'Catatan tetap']);
        $migration->up();$this->assertFalse(Schema::hasColumn('eo_mr_peminjamans','practicum_meeting_id'));
        $this->assertSame($bookingId,$target->fresh()->booking_id);$this->assertSame('Catatan tetap',$target->fresh()->note);
        $this->assertSame('capstone',$other->fresh()->source);$this->assertSame(6,Peminjaman::where('source','praktikum')->count());
    }

    public function test_invalid_old_link_prevents_column_removal(): void
    {
        $migration=require base_path('Modules/EOffice/database/migrations/2026_11_05_000003_use_room_source_for_practicum_bookings.php');$migration->down();$booking=$this->ordinary();
        DB::table('eo_mr_peminjamans')->where('id',$booking->id)->update(['practicum_meeting_id'=>(string)Str::uuid()]);
        try{$migration->up();$this->fail('Invalid legacy link removed');}catch(\RuntimeException $error){$this->assertStringContainsString('tidak konsisten',$error->getMessage());}
        $this->assertTrue(Schema::hasColumn('eo_mr_peminjamans','practicum_meeting_id'));$this->assertSame(1,Peminjaman::count());
    }

    public function test_selected_calendar_range_opens_a_matching_period_draft_without_creating_bookings(): void
    {
        $request=Request::create('/create','GET',['slot_date'=>'2026-11-18','slot_room'=>'2','slot_start'=>'09:00','slot_end'=>'11:00']);
        $view=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class)->create($request);$data=$view->getData();
        $this->assertSame($this->period->id,$data['package']->period_id);$this->assertSame('09:00',$data['calendarSeed']['start']);$this->assertSame('11:00',$data['calendarSeed']['end']);
        $this->assertSame(0,Package::count());$this->assertSame(0,Peminjaman::count());
    }

    public function test_draft_requires_numeric_shift_and_rejects_excessive_note(): void
    {
        $plans=$this->plans();$plans[0]['slots'][0]['shift']='A';
        try{app(Workflow::class)->saveDraft((string)Str::uuid(),0,['period_id'=>$this->period->id,'plans'=>$plans]);$this->fail('Non numeric shift accepted');}catch(ValidationException){$this->assertSame(0,Package::count());}
        $plans=$this->plans();$plans[0]['slots'][0]['note']=str_repeat('x',501);$this->expectException(ValidationException::class);
        app(Workflow::class)->saveDraft((string)Str::uuid(),0,['period_id'=>$this->period->id,'plans'=>$plans]);
    }

    public function test_draft_resolves_admin_period_from_practicum_without_client_period_id(): void
    {
        $other=Period::create(['tahun_ajaran'=>'2027/2028','semester'=>'Genap','starts_on'=>'2028-02-01','ends_on'=>'2028-06-30','version'=>0]);
        $p=app(Workflow::class)->saveDraft((string)Str::uuid(),0,['plans'=>$this->plans()]);$this->assertSame($this->period->id,$p->period_id);$this->assertCount(6,$p->meetings);
        $p=app(Workflow::class)->saveDraft($p->id,$p->version,['period_id'=>$other->id,'plans'=>$this->plans()]);$this->assertSame($this->period->id,$p->period_id);
    }

    public function test_missing_execution_period_does_not_fall_back_to_registration_dates(): void
    {
        $this->period->delete();Schema::create('eo_prak_periode_pendaftaran',function(Blueprint $t){$t->id();$t->date('dibuka_pada');$t->date('ditutup_pada');});
        DB::table('eo_prak_periode_pendaftaran')->insert(['dibuka_pada'=>'2026-11-09','ditutup_pada'=>'2026-11-20']);
        try{app(Workflow::class)->saveDraft((string)Str::uuid(),0,['plans'=>$this->plans()]);$this->fail('Registration dates used for module schedule');}catch(ValidationException $e){$this->assertStringContainsString('Admin Praktikum',implode(' ',collect($e->errors())->flatten()->all()));}
        $this->assertSame(0,Package::count());
    }

    public function test_cerc_draft_has_no_period_dropdown_or_first_available_period_fallback(): void
    {
        view()->share('errors',new \Illuminate\Support\ViewErrorBag());$request=Request::create('/create','GET',['period_id'=>$this->period->id]);
        $view=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class)->create($request);$this->assertNull($view->getData()['package']->period_id);
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/scheduling/editor.blade.php'));$source=preg_replace('/^<x-dynamic-component[^\r\n]*>|<\/x-dynamic-component>\s*$/m','',$source);
        $html=\Illuminate\Support\Facades\Blade::render($source,$view->getData());$this->assertStringNotContainsString('Pilih periode...',$html);$this->assertStringNotContainsString('x-model="periodId"',$html);
    }

    public function test_one_package_can_publish_active_practicums_from_different_periods(): void
    {
        $otherId=(string)Str::uuid();Praktikum::forceCreate(['id'=>$otherId,'nama'=>'Praktikum Semester Lain','tahun_ajaran'=>'2027/2028','semester'=>'Genap','is_active'=>true,'koor_id'=>$this->cerc->id]);
        Period::create(['tahun_ajaran'=>'2027/2028','semester'=>'Genap','starts_on'=>'2028-02-01','ends_on'=>'2028-06-30','version'=>0]);
        $module=Modul::create(['praktikum_id'=>$otherId,'nama'=>'Modul Baru','urutan'=>1]);
        DaftarPraktikan::create(['praktikum_id'=>$otherId,'user_id'=>$this->cerc->id,'kelompok'=>'1','shift'=>'1']);
        $plans=$this->plans();$plans[]=['praktikum_id'=>$otherId,'week_start'=>'2028-02-07','modules'=>[(string)$module->id],'slots'=>[['shift'=>'1','weekday'=>1,'room_id'=>1,'starts_at'=>'08:00','ends_at'=>'10:00']]];
        $p=app(Workflow::class)->saveDraft((string)Str::uuid(),0,['plans'=>$plans]);
        $this->assertCount(2,$p->plan['plans']);$this->assertCount(7,$p->meetings);$this->assertEmpty(app(Workflow::class)->review($p)['errors']);
        $p=app(Workflow::class)->submit($p->id,$p->version);$this->actingAs($this->admin);
        $otherPeriod=Period::where('tahun_ajaran','2027/2028')->firstOrFail();$otherPeriod->increment('version');
        $this->assertSame('pending',$p->status);$this->assertSame(7,Peminjaman::where('status','menunggu')->count());
    }
    public function test_package_accepts_more_than_one_hundred_active_practicums_and_rejects_inactive(): void
    {
        $plans=[];
        foreach(range(1,101) as $i){$id=(string)Str::uuid();Praktikum::forceCreate(['id'=>$id,'nama'=>'Praktikum '.$i,'tahun_ajaran'=>'2026/2027','semester'=>'Ganjil','is_active'=>true,'koor_id'=>$this->cerc->id]);$plans[]=['praktikum_id'=>$id,'slots'=>[]];}
        $p=app(Workflow::class)->saveDraft((string)Str::uuid(),0,['plans'=>$plans]);$this->assertCount(101,$p->plan['plans']);
        Praktikum::whereKey($plans[0]['praktikum_id'])->update(['is_active'=>false]);
        $this->expectException(ValidationException::class);app(Workflow::class)->saveDraft($p->id,$p->version,['plans'=>$plans]);
    }

    public function test_repair_is_dry_run_by_default_and_only_fills_exact_inverse_links(): void
    {
        $p=$this->approved();$target=$p->meetings()->first();
        DB::table('eo_mr_peminjamans')->where('id',$target->booking_id)->update(['source_id'=>null]);
        $this->artisan('practicum:repair-booking-sources')->assertSuccessful();
        $this->assertNull($target->booking->fresh()->getRawOriginal('source_id'));
        $this->artisan('practicum:repair-booking-sources',['--apply'=>true])->assertSuccessful();
        $this->assertSame((string)$target->id,(string)$target->booking->fresh()->getRawOriginal('source_id'));
        $this->artisan('practicum:repair-booking-sources',['--apply'=>true])->assertSuccessful();
        $this->assertSame(6,Peminjaman::count());
    }

    public function test_repair_does_not_overwrite_another_source_or_guess_unlinked_bookings(): void
    {
        $p=$this->approved();$target=$p->meetings()->first();
        DB::table('eo_mr_peminjamans')->where('id',$target->booking_id)->update(['source'=>'capstone','source_id'=>null]);
        $other=$this->ordinary('2026-11-17');$other->update(['source'=>'praktikum']);
        $this->artisan('practicum:repair-booking-sources',['--apply'=>true])->assertSuccessful();
        $this->assertNull($other->fresh()->getRawOriginal('source_id'));
        $this->assertSame('capstone',$target->booking->fresh()->source);
        $this->assertNull($target->booking->fresh()->getRawOriginal('source_id'));
    }

    public function test_rebooking_one_cancelled_occurrence_does_not_regenerate_other_modules_or_shifts(): void
    {
        $p=$this->approved();
        $target=$p->meetings()->where('module_id','2')->where('shift','2')->firstOrFail();
        app(Workflow::class)->amendment([$target->id],'cancel','Satu pertemuan dibatalkan');
        $plans=$this->plans();
        $plans[0]['slots'][0]=['shift'=>'1','weekday'=>null,'room_id'=>null,'starts_at'=>null,'ends_at'=>null];
        $new=app(Workflow::class)->saveDraft((string)Str::uuid(),0,['period_id'=>$this->period->id,'plans'=>$plans]);
        $this->assertSame([], $new->plan['generation_errors']);
        $this->assertSame(1,$new->meetings()->count());
        $replacement=$new->meetings()->first();
        $this->assertSame('2',(string)$replacement->module_id);
        $this->assertSame('2',$replacement->shift);
        $this->assertSame($target->date->toDateString(),$replacement->date->toDateString());
        app(Workflow::class)->submit($new->id,$new->version);
        $this->assertSame(5,Peminjaman::where('status','disetujui')->count());
        $this->assertSame(1,Peminjaman::where('status','menunggu')->count());
        $this->assertSame('dibatalkan',$target->booking->fresh()->status);
    }

    public function test_cancelled_occurrence_is_not_blocked_by_its_other_pending_package_meetings(): void
    {
        $p=$this->submitted();
        $target=$p->meetings()->where('module_id','2')->where('shift','2')->firstOrFail();
        app(Workflow::class)->amendment([$target->id],'cancel','Batal satu sesi');
        $this->assertSame('pending',$p->fresh()->status);
        $new=app(Workflow::class)->saveDraft((string)Str::uuid(),0,['plans'=>$this->plans()]);
        $this->assertSame(1,$new->meetings()->count());
        $this->assertSame([],app(Workflow::class)->review($new)['errors']);
        app(Workflow::class)->submit($new->id,$new->version);
        $this->assertSame(6,Peminjaman::where('status','menunggu')->count());
        $this->assertSame('dibatalkan',$target->booking->fresh()->status);
    }

    public function test_repaired_approved_booking_can_be_cancelled_through_official_service(): void
    {
        $p=$this->approved();$target=$p->meetings()->first();
        DB::table('eo_mr_peminjamans')->where('id',$target->booking_id)->update(['source_id'=>null]);
        $this->artisan('practicum:repair-booking-sources',['--apply'=>true])->assertSuccessful();
        app(Workflow::class)->amendment([$target->id],'cancel','Kegiatan dibatalkan');
        $this->assertSame('dibatalkan',$target->booking->fresh()->status);
        $this->assertSame('cancelled',$target->fresh()->state);
        $this->assertSame(5,Peminjaman::where('status','disetujui')->count());
        $this->assertStringNotContainsString('mp-alert',file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/scheduling/messages.blade.php')));
    }

    public function test_cannot_cancel_started_sessions_or_sessions_with_wrong_references(): void
    {
        $p=$this->approved();$target=$p->meetings()->first();
        $this->travelTo($target->date->copy()->setTimeFromTimeString($target->starts_at));
        try{app(Workflow::class)->amendment([$target->id],'cancel','Batal');$this->fail('Started session cancelled');}
        catch(ValidationException){$this->assertSame('disetujui',$target->booking->fresh()->status);}
    }

    public function test_status_sync_is_idempotent_and_missing_references_are_not_official(): void
    {
        $p=$this->approved();$sync=app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumBookingStatus::class);
        $this->assertSame($p->version,$sync->sync($p)->version);
        $target=$p->meetings()->first();$target->booking->update(['source_id'=>null]);
        $this->assertSame(5,Meeting::official()->count());
        $this->assertSame('pending',$sync->sync($p)->status);
        $this->assertSame('pending',$target->fresh()->state);
    }

    public function test_partial_cancellation_failure_rolls_back_other_selected_sessions(): void
    {
        $p=$this->approved();$rows=$p->meetings()->orderBy('id')->get();
        $rows->last()->booking()->update(['source_id'=>null]);
        try{app(Workflow::class)->amendment($rows->pluck('id')->all(),'cancel','Batal');$this->fail('Missing source allowed');}
        catch(ValidationException){$this->assertSame(0,Peminjaman::where('status','dibatalkan')->count());$this->assertSame(6,Meeting::where('state','approved')->count());}
    }

    public function test_requests_exclude_unsubmitted_and_withdrawn_drafts_even_with_status_filter(): void
    {
        $draft=$this->draft();$pending=$this->submitted();
        $controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class);
        $this->assertSame([$pending->id],$controller->index(Request::create('/','GET',['tab'=>'requests']))->getData()['packages']->pluck('id')->all());
        $this->assertSame([$draft->id],$controller->index(Request::create('/','GET',['tab'=>'drafts']))->getData()['packages']->pluck('id')->all());
        $this->assertSame(0,$controller->index(Request::create('/','GET',['tab'=>'requests','status'=>'draft']))->getData()['packages']->total());
        app(Workflow::class)->withdraw($pending->id,$pending->fresh()->version);
        $this->assertSame(0,$controller->index(Request::create('/','GET',['tab'=>'requests']))->getData()['packages']->total());
    }

    public function test_legacy_rejection_is_restored_without_a_booking_and_is_idempotent(): void
    {
        $p=$this->draft();$created=$p->created_at->toDateTimeString();
        $p->update(['status'=>'pending','submitted_at'=>now(),'decided_by'=>$this->admin->id,'decided_at'=>now(),
            'decision_reason'=>'tes nolak','history'=>[
                ['action'=>'submitted','actor_id'=>$this->cerc->id],
                ['action'=>'rejected','actor_id'=>$this->admin->id,'reason'=>'tes nolak'],
                ['action'=>'booking_status_synced','status'=>'pending'],
            ]]);
        $p->meetings()->update(['state'=>'pending']);
        $sync=app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumBookingStatus::class);
        $p=$sync->sync($p);
        $this->assertSame('rejected',$p->status);$this->assertSame(6,$p->meetings()->where('state','rejected')->count());
        $this->assertSame('tes nolak',$p->decision_reason);$this->assertSame($created,$p->created_at->toDateTimeString());
        $this->assertSame(0,Peminjaman::count());$this->assertSame($p->version,$sync->sync($p)->version);
    }

    public function test_legacy_approval_reads_exact_booking_link_but_never_fabricates_source_ids(): void
    {
        $p=$this->approved();$p->update(['status'=>'pending','decided_by'=>$this->admin->id,'decided_at'=>now(),
            'history'=>[['action'=>'approved','actor_id'=>$this->admin->id],['action'=>'booking_status_synced','status'=>'pending']]]);
        Peminjaman::whereIn('id',$p->meetings()->pluck('booking_id'))->update(['source_id'=>null]);
        $p->meetings()->update(['state'=>'pending']);
        $sync=app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumBookingStatus::class);
        $p=$sync->sync($p);$this->assertSame('approved',$p->status);
        $this->assertSame(6,Meeting::official()->count());$this->assertSame(6,Peminjaman::whereNull('source_id')->count());
        $target=$p->meetings()->first();$target->booking->update(['status'=>'ditolak']);
        $this->assertSame('partial',$sync->sync($p)->status);$this->assertSame(5,Meeting::official()->count());
    }

    public function test_previous_rejection_is_not_reused_after_a_new_submission(): void
    {
        $p=$this->draft();$p->update(['status'=>'pending','submitted_at'=>now(),'decided_at'=>now(),'decided_by'=>$this->admin->id,
            'history'=>[['action'=>'rejected','actor_id'=>$this->admin->id],['action'=>'submitted','actor_id'=>$this->cerc->id]]]);
        $this->assertSame('pending',app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumBookingStatus::class)->sync($p)->status);
    }

    public function test_list_displays_creation_time_not_sync_time_and_sorts_by_creation(): void
    {
        $this->travelTo(Carbon::parse('2026-11-05 19:33:00'));$old=$this->draft();
        $this->travelTo(Carbon::parse('2026-11-07 22:00:00'));$new=$this->draft();
        $this->travelTo(Carbon::parse('2026-11-08 23:00:00'));$old->touch();
        $view=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class)->index(Request::create('/','GET',['tab'=>'drafts']));
        $this->assertSame([$new->id,$old->id],$view->getData()['packages']->pluck('id')->all());
        view()->share('errors',new \Illuminate\Support\ViewErrorBag());
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/scheduling/index.blade.php'));
        $source=preg_replace('/^<x-dynamic-component[^\r\n]*>|<\/x-dynamic-component>\s*$/m','',$source);
        $html=\Illuminate\Support\Facades\Blade::render($source,$view->getData());
        $this->assertStringContainsString('Dibuat 05 Nov 2026, 19:33',$html);
        $this->assertStringNotContainsString('08 Nov 2026, 23:00',$html);
    }

    public function test_deleting_one_draft_occurrence_persists_without_removing_other_weeks_or_shifts(): void
    {
        $p=$this->draft();$key=$this->practicum.'|1|1';
        $p=app(Workflow::class)->saveDraft($p->id,$p->version,['plans'=>$this->plans(),'excluded_meetings'=>[$key]]);
        $this->assertSame(5,$p->meetings()->count());$this->assertSame(0,$p->meetings()->where('module_id','1')->where('shift','1')->count());
        $this->assertSame(1,$p->meetings()->where('module_id','1')->where('shift','2')->count());
        $this->assertSame(2,$p->meetings()->where('module_id','2')->count());
        $this->assertSame([$key],$p->plan['excluded_meetings']);
        $p=app(Workflow::class)->saveDraft($p->id,$p->version,['plans'=>$p->plan['plans'],'excluded_meetings'=>$p->plan['excluded_meetings']]);
        $this->assertSame(5,$p->meetings()->count());$this->assertSame(0,Peminjaman::count());
    }

    public function test_deleting_all_draft_occurrences_does_not_regenerate_them_when_opened(): void
    {
        $p=$this->draft();$excluded=$p->meetings->map(fn($m)=>$m->praktikum_id.'|'.$m->module_id.'|'.$m->shift)->all();
        $p=app(Workflow::class)->saveDraft($p->id,$p->version,['plans'=>$this->plans(),'excluded_meetings'=>$excluded]);
        $data=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class)->show(Request::create('/','GET',['week'=>'2026-11-16']),$p->id)->getData();
        $this->assertCount(0,$data['meetings']);$this->assertNotEmpty($data['report']['errors']);
        $this->assertSame(0,Peminjaman::count());
    }

    public function test_single_draft_override_and_note_do_not_change_other_occurrences(): void
    {
        $p=$this->draft();$key=$this->practicum.'|1|1';
        $p=app(Workflow::class)->saveDraft($p->id,$p->version,['plans'=>$this->plans(),'overrides'=>[$key=>['ends_at'=>'09:30','note'=>'Catatan khusus']]]);
        $target=$p->meetings()->where('module_id','1')->where('shift','1')->first();
        $this->assertSame('09:30',$target->ends_at);$this->assertSame('Catatan khusus',$target->note);
        $this->assertSame(5,$p->meetings()->where('ends_at','10:00')->count());
    }

    public function test_deleting_draft_occurrences_is_blocked_after_submission(): void
    {
        $p=$this->submitted();$key=$this->practicum.'|1|1';
        try{app(Workflow::class)->saveDraft($p->id,$p->version,['plans'=>$this->plans(),'excluded_meetings'=>[$key]]);$this->fail('Submitted booking modified');}
        catch(ValidationException){$this->assertSame(6,$p->meetings()->count());$this->assertSame(6,Peminjaman::count());}
    }

    public function test_saved_draft_calendar_has_edit_buttons_but_submitted_calendar_does_not(): void
    {
        $p=$this->draft();$request=Request::create('/','GET',['week'=>'2026-11-16']);
        $controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class);
        $data=$controller->show($request,$p->id)->getData();
        $html=view('eoffice::manajemen-praktikum.scheduling.calendar',array_merge($data,['editable'=>true]))->render();
        $this->assertStringContainsString('editCalendarMeeting(',$html);
        $this->assertStringContainsString('mp-calendar-draft-edit',$html);
        $html=view('eoffice::manajemen-praktikum.scheduling.calendar',array_merge($data,['editable'=>false]))->render();
        $this->assertStringNotContainsString('editCalendarMeeting(',$html);
    }

    public function test_list_summary_counts_shifts_per_practicum_and_sessions_without_duplicates(): void
    {
        $otherId=(string)Str::uuid();Praktikum::forceCreate(['id'=>$otherId,'nama'=>'Praktikum Sistem Digital','tahun_ajaran'=>'2026/2027','semester'=>'Ganjil','is_active'=>true,'koor_id'=>$this->cerc->id]);
        $module=Modul::create(['praktikum_id'=>$otherId,'nama'=>'Modul Digital','urutan'=>1]);
        DaftarPraktikan::create(['praktikum_id'=>$otherId,'user_id'=>$this->cerc->id,'kelompok'=>'1','shift'=>'1']);
        $plans=$this->plans();$plans[]=['praktikum_id'=>$otherId,'week_start'=>'2026-11-16','modules'=>[(string)$module->id],'slots'=>[['shift'=>'1','weekday'=>1,'room_id'=>2,'starts_at'=>'08:00','ends_at'=>'10:00']]];
        $package=app(Workflow::class)->saveDraft((string)Str::uuid(),0,['plans'=>$plans]);
        $data=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class)->index(new Request)->getData();
        $summary=$data['packages']->firstWhere('id',$package->id)->schedule_summary;
        $this->assertSame(2,$summary['count']);$this->assertSame(3,$summary['shifts']);$this->assertSame(7,$summary['sessions']);
        $this->assertSame(['Ganjil 2026/2027'],$summary['periods']);
        $this->assertSame(6,$summary['practicums'][0]['sessions']);
    }

    public function test_list_shows_two_names_and_clickable_remaining_practicums_without_draft_status(): void
    {
        $plans=[];
        foreach(range(1,10) as $i){$id=(string)Str::uuid();Praktikum::forceCreate(['id'=>$id,'nama'=>'Praktikum Contoh '.$i,'tahun_ajaran'=>'2026/2027','semester'=>'Ganjil','is_active'=>true,'koor_id'=>$this->cerc->id]);$plans[]=['praktikum_id'=>$id,'slots'=>[]];}
        $package=app(Workflow::class)->saveDraft((string)Str::uuid(),0,['plans'=>$plans]);
        $view=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class)->index(new Request);
        $summary=$view->getData()['packages']->first()->schedule_summary;
        $this->assertSame(10,$summary['count']);$this->assertCount(10,$summary['practicums']);
        $this->assertSame(0,$summary['sessions']);$this->assertSame(0,$summary['shifts']);
        view()->share('errors',new \Illuminate\Support\ViewErrorBag());
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/scheduling/index.blade.php'));
        $source=preg_replace('/^<x-dynamic-component[^\r\n]*>|<\/x-dynamic-component>\s*$/m','',$source);
        $html=\Illuminate\Support\Facades\Blade::render($source,$view->getData());
        $this->assertStringContainsString('+8 lainnya',$html);$this->assertStringContainsString('aria-haspopup="dialog"',$html);
        $this->assertStringContainsString('Daftar Praktikum',$html);$this->assertStringContainsString('x-for="item in practicumItems"',$html);
        $this->assertStringNotContainsString('>JENIS</th>',$html);$this->assertStringNotContainsString('>PERTEMUAN</th>',$html);
        $this->assertStringNotContainsString('>STATUS</th>',$html);$this->assertStringNotContainsString($package->code,$html);
        $this->assertStringContainsString('>RINGKASAN JADWAL</th>',$html);
    }

    public function test_calendar_full_day_names_and_slot_form_search_and_minute_selectors_render(): void
    {
        $controller=app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\PracticumScheduleController::class);
        $data=$controller->create(Request::create('/','GET',['week'=>'2026-11-16']))->getData();
        $html=view('eoffice::manajemen-praktikum.scheduling.calendar',array_merge($data,['editable'=>true]))->render();
        $this->assertStringContainsString('>Senin</strong>',$html);$this->assertStringContainsString('>Minggu</strong>',$html);
        $this->assertStringContainsString('data-pickable="true"',$html);
        $dialog=view('eoffice::manajemen-praktikum.scheduling.slot-dialog',$data)->render();
        $this->assertStringContainsString('Cari nama praktikum...',$dialog);
        $this->assertStringContainsString('x-model="slotStart"',$dialog);$this->assertStringContainsString('x-model="timeMinute"',$dialog);
        $this->assertStringNotContainsString('slot-note',$dialog);$this->assertStringNotContainsString('slotNote',$dialog);
        $this->assertStringNotContainsString('Tutup pemilihan slot',$dialog);$this->assertStringNotContainsString('Gunakan Slot',$dialog);
        $this->assertSame(1,substr_count($dialog,'Rentang jam terpilih digunakan setiap minggu selama modul berjalan. Periksa kembali jadwal shift lain sebelum menyimpan rancangan.'));
    }

    private function cercDashboard(array $query=[]): array
    {
        $request=Request::create('/','GET',$query);$request->setUserResolver(fn()=>$this->cerc);
        return app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Cerc\DashboardController::class)->index($request)->getData();
    }

    public function test_cerc_dashboard_counts_people_not_duplicate_assignments_and_ignores_revoked_roles(): void
    {
        $other=(string)Str::uuid();Praktikum::forceCreate(['id'=>$other,'nama'=>'Praktikum Kedua','is_active'=>true,'koor_id'=>null,'tahun_ajaran'=>'2026/2027','semester'=>'Ganjil']);
        foreach([$this->practicum,$other] as $id)\Modules\EOffice\Models\AsprakPraktikum::create(['praktikum_id'=>$id,'user_id'=>$this->cerc->id,'role'=>'asprak']);
        \Modules\EOffice\Models\AsprakPraktikum::create(['praktikum_id'=>$this->practicum,'user_id'=>$this->admin->id,'role'=>'koor']);
        $revoked=\Modules\EOffice\Models\AsprakPraktikum::create(['praktikum_id'=>$other,'user_id'=>$this->admin->id,'role'=>'asprak']);$revoked->delete();
        $data=$this->cercDashboard();$this->assertSame(2,$data['stats']['practicums']);$this->assertSame(1,$data['stats']['without_coordinator']);$this->assertSame(1,$data['stats']['assistants']);
        $this->assertSame($other,$data['praktikums']->first()->id);$this->assertSame(1,(int)$data['praktikums']->first()->assistants_count);
        $this->assertSame([$other],$this->cercDashboard(['filter'=>'without_coordinator'])['praktikums']->pluck('id')->all());
        $this->assertSame(0,$this->cercDashboard(['q'=>'tidak ditemukan'])['praktikums']->total());
        Praktikum::whereKey($other)->update(['is_active'=>false]);$this->assertSame(1,$this->cercDashboard()['stats']['practicums']);
    }

    public function test_cerc_dashboard_reads_live_booking_status_and_only_next_seven_days_without_mutating_data(): void
    {
        $p=$this->approved();$this->travelTo(Carbon::parse('2026-11-16 08:30:00'));
        $before=Peminjaman::orderBy('id')->get()->toJson();$data=$this->cercDashboard();
        $this->assertSame(2,$data['schedules']->total());$this->assertSame($before,Peminjaman::orderBy('id')->get()->toJson());
        $target=$p->meetings()->whereDate('date','2026-11-18')->first();$target->booking->update(['status'=>'menunggu']);
        $data=$this->cercDashboard();$this->assertSame(1,$data['stats']['pending']);$this->assertSame(1,$data['schedules']->total());
        $target->booking->update(['status'=>'ditolak']);$data=$this->cercDashboard();$this->assertSame(1,$data['attention']['rejected']);$this->assertSame(0,$data['stats']['pending']);
        Praktikum::whereKey($this->practicum)->update(['is_active'=>false]);$data=$this->cercDashboard();$this->assertSame(0,$data['schedules']->total());$this->assertSame(0,$data['attention']['rejected']);
    }

    public function test_cerc_dashboard_pagination_defaults_to_five_and_sizes_are_independent(): void
    {
        $data=$this->cercDashboard();$this->assertSame(5,$data['praktikums']->perPage());$this->assertSame(5,$data['schedules']->perPage());
        $data=$this->cercDashboard(['per_page'=>15,'schedule_per_page'=>10]);
        $this->assertSame(15,$data['praktikums']->perPage());$this->assertSame(10,$data['schedules']->perPage());
        $data=$this->cercDashboard(['per_page'=>5,'schedule_per_page'=>15]);$this->assertSame(15,$data['schedules']->perPage());
        $this->expectException(ValidationException::class);$this->cercDashboard(['per_page'=>20]);
    }
}
