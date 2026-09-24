<?php

namespace Modules\Capstone\Tests;

use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\AcademicRoleSynchronizer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Http\Controllers\Admin\BladeMonitoringController;
use Modules\Capstone\Http\Controllers\Admin\PeriodRegistrationApprovalController;
use Modules\Capstone\Http\Controllers\Admin\PhaseDocumentRequirementController;
use Modules\Capstone\Http\Controllers\Admin\TaRegistrationApprovalController;
use Modules\Capstone\Http\Controllers\AssessmentComponentTemplateController;
use Modules\Capstone\Http\Controllers\BidController;
use Modules\Capstone\Http\Controllers\BladeController;
use Modules\Capstone\Http\Controllers\BladePeriodController;
use Modules\Capstone\Http\Controllers\BursaIdeController;
use Modules\Capstone\Http\Controllers\CalendarController;
use Modules\Capstone\Http\Controllers\DocumentController;
use Modules\Capstone\Http\Controllers\ExpoStudentController;
use Modules\Capstone\Http\Controllers\GradeConfigurationController;
use Modules\Capstone\Http\Controllers\GroupController;
use Modules\Capstone\Http\Controllers\IndividualTaController;
use Modules\Capstone\Http\Controllers\LaunchController;
use Modules\Capstone\Http\Controllers\PeerReviewController;
use Modules\Capstone\Http\Controllers\PeriodAssessmentConfigController;
use Modules\Capstone\Http\Controllers\PeriodPeerReviewConfigController;
use Modules\Capstone\Http\Controllers\RegistrationController;
use Modules\Capstone\Http\Controllers\ReportDetailController;
use Modules\Capstone\Http\Controllers\ReportSummaryController;
use Modules\Capstone\Http\Controllers\ScheduleController;
use Modules\Capstone\Http\Controllers\SemproController;
use Modules\Capstone\Http\Controllers\StudentProposalController;
use Modules\Capstone\Http\Controllers\SupervisorEvaluationController;
use Modules\Capstone\Http\Controllers\TaDefenseScheduleController;
use Modules\Capstone\Http\Controllers\TaRegistrationController;
use Modules\Capstone\Http\Controllers\TaSubmissionController;
use Modules\Capstone\Http\Middleware\BladeAccessMiddleware;
use Modules\Capstone\Http\Middleware\BladeSessionFeatureMiddleware;
use Modules\Capstone\Http\Requests\Admin\StoreTaDefenseRequest;
use Modules\Capstone\Http\Requests\Admin\UpdateTaDefenseRequest;
use Modules\Capstone\Models\AssessmentComponentTemplate;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Bid;
use Modules\Capstone\Models\Document;
use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Models\ExpoScore;
use Modules\Capstone\Models\ExpoSelfEvaluation;
use Modules\Capstone\Models\ExpoStudentDocument;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\JoinRequest;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\PeerReview;
use Modules\Capstone\Models\PeerReviewIndicator;
use Modules\Capstone\Models\PeerReviewIndicatorTemplate;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodAssessmentComponent;
use Modules\Capstone\Models\PeriodPeerReviewIndicator;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\PhaseDocumentRequirement;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\StudentPeerReviewStatus;
use Modules\Capstone\Models\TaDefenseEvaluation;
use Modules\Capstone\Models\TaDefenseExaminer;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Models\TaRegistration;
use Modules\Capstone\Models\TaSubmission;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Services\DocumentStorageService;
use Modules\Capstone\Services\ExpoService;
use Modules\Capstone\Services\GradeCalculationService;
use Modules\Capstone\Services\GroupService;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\IndividualTaWorkflow;
use Modules\Capstone\Services\NotificationService;
use Modules\Capstone\Services\SchedulingService;
use Modules\Capstone\Support\BladeFeatureAccess;
use Modules\Capstone\Support\CapstoneActor;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/** Isolated real SQL: never migrate the application's configured database. */
class BladeCalendarAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.capstone_blade_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('capstone_blade_test');
        Schema::create('permissions', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('guard_name')->default('web');
        });
        foreach ([
            'database/migrations/2026_02_15_111353_create_users_table.php',
            'database/migrations/2026_02_15_114337_create_students_tables.php',
            'database/migrations/2026_02_15_114539_create_lecturers_tables.php',
            'Modules/Capstone/database/migrations/2026_05_05_000001_create_capstone_periods_table.php',
            'Modules/Capstone/database/migrations/2026_05_05_000002_create_capstone_groups_table.php',
            'Modules/Capstone/database/migrations/2026_05_05_000011_create_capstone_documents_table.php',
            'Modules/Capstone/database/migrations/2026_05_05_000013_create_capstone_seminar_schedules_table.php',
        ] as $file) {
            (require base_path($file))->up();
        }
        Schema::table('capstone_groups', fn (Blueprint $t) => $t->unsignedBigInteger('title_id')->nullable());
        Schema::table('capstone_seminar_schedules', function (Blueprint $t) {
            $t->foreignId('examiner_1_id')->nullable()->change();
            $t->foreignId('examiner_2_id')->nullable()->change();
        });
        (require __DIR__.'/../database/migrations/2026_09_08_110000_support_capstone_mentoring_calendar.php')->up();
        Schema::create('capstone_group_members', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->boolean('is_leader')->default(false);
            $t->string('status')->nullable();
            $t->unsignedBigInteger('removed_by')->nullable();
            $t->text('removal_reason')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_period_registrations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->unsignedBigInteger('period_id');
            $t->string('status')->default('PENDING');
            $t->text('rejection_reason')->nullable();
            $t->unsignedBigInteger('reviewed_by')->nullable();
            $t->timestamp('reviewed_at')->nullable();
            $t->timestamp('flagged_at')->nullable();
            $t->unsignedBigInteger('flagged_by')->nullable();
            $t->timestamps();
        });

        Schema::create('capstone_supervisions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('supervisor_id');
            $t->string('role')->nullable();
        });
        Schema::create('capstone_ta_defense_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id')->nullable();
            $t->unsignedBigInteger('examiner_1_id')->nullable();
            $t->unsignedBigInteger('examiner_2_id')->nullable();
            $t->unsignedBigInteger('location_id')->nullable();
            $t->date('date');
            $t->time('start_time');
            $t->time('end_time');
            $t->string('room')->nullable();
            $t->string('status')->default('SCHEDULED');
            $t->timestamps();
        });
        Schema::create('capstone_ta_defense_schedule_student', function (Blueprint $t) {
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('student_id');
            $t->timestamps();
        });
        Schema::create('capstone_ta_defense_examiners', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('examiner_id');
            $t->string('role')->nullable();
            $t->timestamps();
        });
        Gate::before(fn () => true);
    }

    protected function tearDown(): void
    {
        DB::purge('capstone_blade_test');
        parent::tearDown();
    }

    public function test_admin_and_lecturer_menus_have_no_student_prerequisites(): void
    {
        $navigation = json_decode(file_get_contents(module_path('Capstone', 'resources/reference/navigation.json')), true);
        foreach (['admin', 'dosen'] as $role) {
            $user = $this->actor($role);
            $data = ['actor' => CapstoneActor::payload($user), 'activeRole' => $role,
                'pagePath' => '/'.$role.'/dashboard', 'featureAccess' => ['registered' => false, 'group_status' => null]];
            $html = view('capstone::layouts.sidebar', $data)->render();
            $this->assertStringNotContainsString('aria-disabled="true"', $html);
            foreach ($navigation[$role] as $item) {
                foreach ($item['items'] ?? [$item] as $link) {
                    $this->assertStringContainsString('href="'.url('/capstone'.$link['url']).'"', $html);
                    $request = $this->requestFor($user, '/capstone'.$link['url']);
                    $response = (new BladeAccessMiddleware)->handle($request, fn () => response('allowed'), $role);
                    $this->assertSame('allowed', $response->getContent(), $link['url']);
                    $this->assertFalse($request->attributes->has('capstone_feature_access'));
                }
            }
        }
        $this->assertSame(0, PeriodRegistration::count());
        $this->assertSame(0, GroupMember::count());
    }

    public function test_admin_monitoring_uses_student_profiles_and_only_reminds_incomplete_members(): void
    {
        Schema::create('capstone_student_peer_review_status', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->boolean('has_completed_peer_review')->default(false);
            $t->string('ta_status');
        });
        $student = $this->actor('mahasiswa');
        $other = $this->actor('mahasiswa');
        $group = $this->group($student, null, 'EXPO_REGISTERED');
        GroupMember::create(['group_id' => $group->id, 'student_id' => $other->student->id]);
        DB::table('capstone_student_peer_review_status')->insert(['group_id' => $group->id, 'student_id' => $student->student->id, 'has_completed_peer_review' => true, 'ta_status' => 'TA_ACTIVE']);
        $controller = new BladeMonitoringController;
        $data = $controller->peerReviews(Request::create('/', 'GET', ['period_id' => $group->period_id]))->getData(true)['data'];
        $this->assertCount(1, $data);
        $this->assertSame(50, $data[0]['completion_percentage']);
        $this->assertSame(1, $data[0]['completed_count']);
        $this->assertSame($student->name, $data[0]['members'][0]['student_name']);
        $notifications = \Mockery::mock(NotificationService::class);
        $notifications->shouldReceive('sendToMany')->once()->with([$other->id], 'PEER_REVIEW_REMINDER', 'Pengingat Peer Review', 'Lengkapi peer review kelompok Anda.', 'Group', $group->id);
        $this->assertSame(1, $controller->remind($group, $notifications)->getData(true)['count']);
        $group->update(['status' => 'FORMING']);
        $this->expectException(HttpException::class);
        $controller->remind($group, $notifications);
    }

    public function test_progress_endpoint_filters_period_and_uses_persisted_documents(): void
    {
        Schema::create('capstone_phase_document_requirements', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('phase');
            $t->string('name');
            $t->boolean('is_required')->default(true);
        });
        $group = $this->group();
        $this->group();
        DB::table('capstone_documents')->insert(['group_id' => $group->id, 'phase' => 'PDC1', 'file_path' => 'test.pdf']);
        $service = \Mockery::mock(GroupService::class);
        $service->shouldReceive('transformGroupForProgress')->once()->withArgs(fn ($found, $docs, $requirements) => $found->id === $group->id && $docs->count() === 1 && $requirements->isEmpty())->andReturn(['id' => $group->id, 'progress_percentage' => 10]);
        $controller = new BladeMonitoringController;
        $data = $controller->progress(Request::create('/', 'GET', ['period_id' => $group->period_id]), $service)->getData(true)['data'];
        $this->assertSame([['id' => $group->id, 'progress_percentage' => 10]], $data);
    }

    public function test_admin_can_reschedule_pending_evaluations_but_cannot_change_submitted_evaluations(): void
    {
        foreach (['2026_05_05_000014_create_capstone_seminar_evaluations_table.php', '2026_05_05_000028_create_capstone_notifications_table.php', '2026_05_05_000029_create_capstone_audit_logs_table.php'] as $file) {
            (require __DIR__.'/../database/migrations/'.$file)->up();
        }
        Schema::create('eo_mr_ruangans', function (Blueprint $t) {
            $t->id();
            $t->string('nama');
            $t->string('lokasi')->nullable();
            $t->integer('lantai')->nullable();
            $t->integer('kapasitas')->default(0);
            $t->json('fasilitas')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        $roomId = DB::table('eo_mr_ruangans')->insertGetId(['nama' => 'Ruang Sidang', 'kapasitas' => 30]);
        Schema::table('capstone_seminar_schedules', function (Blueprint $t) {
            $t->unsignedBigInteger('eoffice_ruangan_id')->nullable();
            $t->unsignedBigInteger('eoffice_peminjaman_id')->nullable();
        });
        $admin = $this->actor('admin');
        $first = $this->actor('dosen')->lecturer->id;
        $second = $this->actor('dosen')->lecturer->id;
        $replacement = $this->actor('dosen')->lecturer->id;
        $group = $this->group();
        $schedule = SeminarSchedule::create(['group_id' => $group->id, 'type' => 'SEMPRO', 'date' => '2026-10-01', 'start_time' => '09:00', 'end_time' => '10:00', 'room' => 'A', 'examiner_1_id' => $first, 'examiner_2_id' => $second, 'status' => 'SCHEDULED']);
        foreach ([$first, $second] as $examiner) {
            $schedule->evaluations()->create(['examiner_id' => $examiner, 'status' => 'PENDING']);
        }
        $service = \Mockery::mock(SchedulingService::class);
        $service->shouldReceive('validateExaminerConstraints')->once()->withArgs(fn ($found, $ids) => $found->id === $group->id && $ids === [$first, $replacement])->andReturn(null);
        $service->shouldReceive('validateScheduleConflicts')->once()->with([$first, $replacement], '2026-10-02', '09:00', '10:00', 'Ruang Sidang', $schedule->id, null, null, null, $roomId)->andReturn([]);
        $controller = new SemproController(app(GroupStateMachine::class), $service);
        $payload = ['date' => '2026-10-02', 'start_time' => '09:00', 'end_time' => '10:00', 'eoffice_ruangan_id' => $roomId, 'examiner_1_id' => $first, 'examiner_2_id' => $replacement];
        $request = $this->requestFor($admin, '/', 'PUT', $payload);
        $this->assertSame(200, $controller->update($request, $schedule->id)->getStatusCode());
        $this->assertSame('Ruang Sidang', $schedule->fresh()->room);
        $this->assertEqualsCanonicalizing([$first, $replacement], $schedule->evaluations()->pluck('examiner_id')->all());
        $schedule->evaluations()->where('examiner_id', $first)->update(['status' => 'SUBMITTED', 'score' => 85]);
        foreach (['update', 'cancel'] as $method) {
            try {
                $controller->$method($request, $schedule->id);
                $this->fail('Submitted evaluations must block '.$method);
            } catch (HttpException $e) {
                $this->assertSame(422, $e->getStatusCode());
            }
        }
        $this->assertSame('SCHEDULED', $schedule->fresh()->status);
        $this->assertEquals(85, $schedule->evaluations()->where('examiner_id', $first)->value('score'));
        $schedule->evaluations()->update(['status' => 'PENDING', 'score' => null]);
        $this->assertSame(200, $controller->cancel($request, $schedule->id)->getStatusCode());
        $this->assertSame('CANCELLED', $schedule->fresh()->status);
        $this->assertSame(2, $schedule->evaluations()->count());
        $this->assertSame(['SEMPRO_UPDATED', 'SEMPRO_CANCELLED'], AuditLog::pluck('action')->all());
    }

    public function test_report_evaluator_uses_lecturer_profile_even_before_scores_exist(): void
    {
        $this->actor('admin');
        $student = $this->actor('mahasiswa');
        [$group,,$component] = $this->expoFixture($student);
        Schema::table('capstone_expo_scores', fn (Blueprint $t) => $t->unsignedBigInteger('component_id')->nullable());
        $lecturer = $this->actor('dosen');
        $group->update(['supervisor_1_id' => $lecturer->lecturer->id]);
        $this->assertNotEquals($lecturer->id, $lecturer->lecturer->id);
        $controller = new ReportDetailController;
        $request = Request::create('/', 'GET', ['period_id' => $group->period_id]);
        $read = fn () => $controller->evaluatorDetail($request, $student->student->id, 'EXPO', $lecturer->lecturer->id)->getData(true)['data'];
        $result = $read();
        $this->assertSame($lecturer->name, $result['evaluator']['name']);
        $this->assertSame('NOT_STARTED', $result['evaluator']['status']);
        DB::table('capstone_expo_scores')->insert(['group_id' => $group->id, 'student_id' => $student->student->id, 'evaluator_id' => $lecturer->lecturer->id, 'period_component_id' => $component, 'score' => 85]);
        $result = $read();
        $this->assertSame($lecturer->name, $result['evaluator']['name']);
        $this->assertEquals(85, $result['evaluator']['score']);
        $this->assertSame('COMPLETE', $result['evaluator']['status']);
    }

    public function test_peer_report_summary_and_name_sort_use_capstone_tables_and_user_names(): void
    {
        Schema::table('capstone_groups', fn (Blueprint $t) => $t->string('code')->nullable());
        Schema::create('capstone_peer_reviews', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('reviewer_id');
            $t->unsignedBigInteger('reviewee_id');
            $t->decimal('score', 5, 2);
            $t->timestamps();
        });
        $this->actor('admin');
        $first = $this->actor('mahasiswa');
        $second = $this->actor('mahasiswa');
        $first->update(['name' => 'Zeta']);
        $second->update(['name' => 'Alpha']);
        $group = $this->group($first);
        $other = $this->group();
        foreach ([[$group->id, $first->student->id, $second->student->id, 80], [$group->id, $second->student->id, $first->student->id, 60], [$other->id, $first->student->id, $second->student->id, 100]] as [$groupId,$reviewer,$reviewee,$score]) {
            PeerReview::create(['group_id' => $groupId, 'reviewer_id' => $reviewer, 'reviewee_id' => $reviewee, 'score' => $score]);
        }
        $summary = (new \ReflectionMethod(ReportSummaryController::class, 'getPeerReviewSummary'))->invoke(new ReportSummaryController, $group->period_id);
        $this->assertSame(2, $summary['total_reviews']);
        $this->assertEquals(70, $summary['average_score']);
        $this->assertCount(1, $summary['top_groups']);
        $controller = new ReportDetailController;
        foreach (['reviewer', 'reviewee'] as $sort) {
            $query = PeerReview::where('group_id', $group->id);
            (new \ReflectionMethod($controller, 'applyPeerReviewSorting'))->invoke($controller, $query, $sort, 'asc');
            $this->assertSame($second->student->id, $query->first()->getAttribute($sort.'_id'));
        }
    }

    public function test_schedule_summary_can_select_the_source_when_schedule_ids_overlap(): void
    {
        Schema::table('capstone_groups', fn (Blueprint $t) => $t->string('code')->nullable());
        $seminarGroup = $this->group();
        $taGroup = $this->group();
        $seminar = SeminarSchedule::create(['group_id' => $seminarGroup->id, 'type' => 'SEMPRO', 'date' => '2026-10-01', 'start_time' => '09:00', 'end_time' => '10:00', 'room' => 'Seminar', 'status' => 'SCHEDULED']);
        $ta = TaDefenseSchedule::create(['group_id' => $taGroup->id, 'date' => '2026-10-01', 'start_time' => '11:00', 'end_time' => '12:00', 'room' => 'TA', 'status' => 'SCHEDULED']);
        $this->assertSame($seminar->id, $ta->id);
        $controller = new SupervisorEvaluationController;
        foreach (['seminar' => $seminarGroup->id, 'ta' => $taGroup->id] as $source => $groupId) {
            $request = Request::create('/', 'GET', ['schedule_source' => $source]);
            $response = $controller->adminScheduleSummary($request, $seminar->id)->getData(true)['data'];
            $this->assertSame($groupId, $response['group']['id']);
            $this->assertSame([], $response['summary']);
        }
        $this->assertSame(404, $controller->adminScheduleSummary(Request::create('/', 'GET', ['schedule_source' => 'ta']), 999)->getStatusCode());
    }

    public function test_admin_links_and_combined_sidebar_remain_enabled_for_pending_views(): void
    {
        // A view still being converted must not masquerade as a prerequisite lock.
        request()->attributes->set('capstone_feature_access', ['registered' => false]);
        foreach (['button', 'feature-link'] as $component) {
            $html = Blade::render('<x-capstone::'.$component.' href="/admin/users">Users</x-capstone::'.$component.'>');
            $this->assertStringContainsString('href="'.url('/capstone/admin/users').'"', $html);
            $this->assertStringNotContainsString('aria-disabled="true"', $html);
        }
        $html = Blade::render('<x-capstone::feature-link href="/admin/evaluation-summary/1" ::href="url(\'/admin/evaluation-summary/\'+item.id)">Eval</x-capstone::feature-link>');
        $this->assertStringContainsString(':href=', $html);
        $this->assertStringNotContainsString('aria-disabled="true"', $html);
        $html = view('capstone::layouts.sidebar', ['actor' => ['roles' => ['admin', 'dosen']], 'activeRole' => 'admin', 'pagePath' => '/admin/dashboard', 'featureAccess' => []])->render();
        $this->assertStringNotContainsString('aria-disabled="true"', $html);
        foreach (['/admin/settings', '/admin/users', '/dosen/titles', '/dosen/evaluation'] as $path) {
            $this->assertStringContainsString('href="'.url('/capstone'.$path).'"', $html);
        }
    }

    public function test_period_registration_unlocks_all_group_submenus_and_links_using_student_profile_id(): void
    {
        Schema::table('capstone_periods', fn (Blueprint $t) => $t->boolean('is_finalized')->default(false));
        (require __DIR__.'/../database/migrations/2026_05_05_000028_create_capstone_notifications_table.php')->up();
        $this->actor('admin'); // Account ID must differ from the student profile ID.
        $user = $this->actor('mahasiswa');
        $this->assertNotEquals($user->id, $user->student->id);
        $period = Period::create(['name' => 'Registration test', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_active' => true]);
        $paths = ['/mahasiswa/group', '/mahasiswa/titles', '/mahasiswa/propose-title', '/mahasiswa/bidding'];
        $renderSidebar = function () use ($user) {
            $state = BladeFeatureAccess::snapshot($user);
            request()->attributes->set('capstone_feature_access', $state);

            return view('capstone::layouts.sidebar', ['actor' => ['roles' => ['mahasiswa']], 'activeRole' => 'mahasiswa', 'pagePath' => '/mahasiswa/dashboard', 'featureAccess' => $state])->render();
        };
        $html = $renderSidebar();
        foreach ($paths as $path) {
            $this->assertStringNotContainsString('href="'.url('/capstone'.$path).'"', $html);
            $response = (new BladeAccessMiddleware)->handle($this->requestFor($user, '/capstone'.$path), fn () => response('allowed'), 'mahasiswa');
            $this->assertSame(403, $response->getStatusCode());
            foreach (['button', 'feature-link'] as $component) {
                $link = Blade::render('<x-capstone::'.$component.' href="'.$path.'" ::href="targetUrl" @click="navigate()">Open</x-capstone::'.$component.'>');
                $this->assertStringContainsString('aria-disabled="true"', $link);
                $this->assertStringNotContainsString('href=', $link);
                $this->assertStringNotContainsString('@click=', $link);
            }
        }
        $request = $this->requestFor($user, '/capstone/session/capstone/mahasiswa/periods/register', 'POST', ['period_id' => $period->id]);
        $response = (new RegistrationController)->register($request);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertEquals($user->student->id, PeriodRegistration::first()->user_id);
        $this->assertSame('PENDING', PeriodRegistration::first()->status);
        // A pending join request keeps every feature locked with a pending message.
        $pendingState = BladeFeatureAccess::snapshot($user);
        $this->assertFalse($pendingState['registered']);
        $this->assertTrue($pendingState['pending_registration']);
        $this->assertSame('Your join request is still pending admin approval', BladeFeatureAccess::reason('/mahasiswa/group', $pendingState));
        $response = (new BladeAccessMiddleware)->handle($this->requestFor($user, '/capstone/mahasiswa/group'), fn () => response('allowed'), 'mahasiswa');
        $this->assertSame(403, $response->getStatusCode());
        // Admin approval unlocks the menus.
        $admin = User::where('id', '<>', $user->id)->first();
        $approveRequest = $this->requestFor($admin, '/api/capstone/admin/period-registrations/'.PeriodRegistration::first()->id.'/approve', 'PUT');
        $approveResponse = (new PeriodRegistrationApprovalController)->approve($approveRequest, PeriodRegistration::first()->id);
        $this->assertSame(200, $approveResponse->getStatusCode());
        $this->assertSame('APPROVED', PeriodRegistration::first()->fresh()->status);
        $html = $renderSidebar();
        $this->assertMatchesRegularExpression('/<button[^>]*aria-disabled="false"[^>]*title="Group &amp; Titles"/', $html);
        foreach ($paths as $path) {
            $this->assertStringContainsString('href="'.url('/capstone'.$path).'"', $html);
            $response = (new BladeAccessMiddleware)->handle($this->requestFor($user, '/capstone'.$path), fn () => response('allowed'), 'mahasiswa');
            $this->assertSame('allowed', $response->getContent());
            foreach (['button', 'feature-link'] as $component) {
                $link = Blade::render('<x-capstone::'.$component.' href="'.$path.'">Open</x-capstone::'.$component.'>');
                $this->assertStringContainsString('href="'.url('/capstone'.$path).'"', $link);
                $this->assertStringNotContainsString('aria-disabled="true"', $link);
            }
        }
        foreach (['documents', 'schedule', 'expo', 'peer-review', 'grades', 'ta-submission'] as $feature) {
            $this->assertStringNotContainsString('href="'.url('/capstone/mahasiswa/'.$feature).'"', $html);
        }
        $request = $this->requestFor($user, '/capstone/session/capstone/mahasiswa/group', 'POST');
        $this->assertSame('allowed', (new BladeSessionFeatureMiddleware)->handle($request, fn () => response('allowed'))->getContent());
    }

    public function test_student_submenu_matrix_follows_ctms_registration_and_phase_rules(): void
    {
        $groupLinks = ['group', 'titles', 'propose-title', 'bidding'];
        $pdcLinks = ['documents', 'ta-submission', 'schedule', 'grades'];
        $stages = [
            'FORMING' => $groupLinks,
            'PDC1_ACTIVE' => [...$groupLinks, ...$pdcLinks],
            'READY_FOR_SEMPRO' => [...$groupLinks, ...$pdcLinks],
            'PDC2_READY_FOR_EXPO' => [...$groupLinks, ...$pdcLinks, 'expo'],
            'EXPO_REGISTERED' => [...$groupLinks, ...$pdcLinks, 'expo', 'peer-review'],
            'READY_FOR_TA_INDIVIDUAL' => [...$groupLinks, ...$pdcLinks, 'expo', 'peer-review'],
        ];
        foreach ($stages as $status => $enabled) {
            $state = ['registered' => true, 'group_status' => $status];
            $html = view('capstone::layouts.sidebar', ['actor' => ['roles' => ['mahasiswa']], 'activeRole' => 'mahasiswa', 'pagePath' => '/mahasiswa/dashboard', 'featureAccess' => $state])->render();
            foreach ([...$groupLinks, ...$pdcLinks, 'expo', 'peer-review'] as $feature) {
                $href = 'href="'.url('/capstone/mahasiswa/'.$feature).'"';
                if (in_array($feature, $enabled, true)) {
                    $this->assertStringContainsString($href, $html, $status.': '.$feature);
                } else {
                    $this->assertStringNotContainsString($href, $html, $status.': '.$feature);
                }
            }
        }
    }

    public function test_every_manifest_and_menu_url_matches_the_intended_laravel_route(): void
    {
        $manifest = json_decode(file_get_contents(module_path('Capstone', 'resources/reference/pages.json')), true);
        foreach ($manifest as $page) {
            $path = preg_replace_callback('/\[([^\]]+)\]/', fn ($match) => $match[1] === 'phase' ? 'pdc1' : '17', $page['route']);
            $route = app('router')->getRoutes()->match(Request::create('/capstone'.($path === '/' ? '' : $path)));
            if (in_array($page['route'], ['/', '/login', '/auth/exchange', '/mahasiswa/ta-defense'], true)) {
                continue;
            }
            $expected = trim(preg_replace('/\[([^\]]+)\]/', '_$1_', $page['route']), '/');
            $this->assertSame($expected, $route->defaults['capstone_page'] ?? null, $page['route']);
        }
        $navigation = json_decode(file_get_contents(module_path('Capstone', 'resources/reference/navigation.json')), true);
        foreach ($navigation as $items) {
            foreach ($items as $item) {
                foreach ($item['items'] ?? [$item] as $link) {
                    $route = app('router')->getRoutes()->match(Request::create('/capstone'.$link['url']));
                    $this->assertSame(ltrim($link['url'], '/'), $route->defaults['capstone_page'] ?? null, $link['url']);
                }
            }
        }
        foreach (['GET /period-wizard/options' => 'options', 'GET /period-wizard/17' => 'show', 'POST /period-wizard' => 'store', 'PUT /period-wizard/17' => 'update'] as $operation => $method) {
            [$verb,$path] = explode(' ', $operation);
            $route = app('router')->getRoutes()->match(Request::create('/capstone/session/capstone/admin'.$path, $verb));
            $this->assertSame(BladePeriodController::class.'@'.$method, $route->getActionName());
            $this->assertContains('capstone.role:admin', $route->gatherMiddleware());
        }
    }

    public function test_registered_missing_page_has_explicit_unavailable_response_and_period_routes_render(): void
    {
        $user = $this->actor('admin');
        foreach (['/capstone/admin/assessments' => 503, '/capstone/admin/periods/new' => 200, '/capstone/admin/periods/17/edit' => 200] as $path => $status) {
            $request = $this->requestFor($user, $path);
            $route = app('router')->getRoutes()->match($request);
            $request->setRouteResolver(fn () => $route);
            $request->attributes->set('capstone_role', 'admin');
            $result = (new BladeController)->page($request);
            if ($status === 503) {
                $this->assertSame(503, $result->getStatusCode());
                $this->assertStringContainsString('Halaman belum tersedia', $result->getContent());
            } else {
                $this->assertStringContainsString('period-wizard-form', $result->render());
            }
        }
    }

    private function studentWorkspaceSchema(): void
    {
        Schema::table('capstone_periods', fn (Blueprint $t) => $t->boolean('is_finalized')->default(false));
        Schema::table('capstone_groups', function (Blueprint $t) {
            $t->boolean('is_solo')->default(false);
            $t->boolean('has_active_proposal')->default(false);
        });
        Schema::table('capstone_group_members', fn (Blueprint $t) => $t->unsignedBigInteger('period_id')->nullable());
        foreach (['2026_05_05_000003_create_capstone_titles_table.php', '2026_05_05_000009_create_capstone_bids_table.php', '2026_05_05_000028_create_capstone_notifications_table.php', '2026_05_05_000029_create_capstone_audit_logs_table.php', '2026_09_18_000000_make_capstone_bids_uniques_reject_aware.php'] as $file) {
            (require __DIR__.'/../database/migrations/'.$file)->up();
        }
        Schema::table('capstone_titles', fn (Blueprint $t) => $t->unsignedBigInteger('period_id')->nullable());
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('guard_name')->default('web');
        });
        Schema::create('model_has_roles', function (Blueprint $t) {
            $t->unsignedBigInteger('role_id');
            $t->unsignedBigInteger('model_id');
            $t->string('model_type');
        });
    }

    private function workspaceLecturer(): User
    {
        $user = $this->actor('dosen');
        $id = DB::table('roles')->insertGetId(['name' => 'dosen', 'guard_name' => 'web']);
        DB::table('model_has_roles')->insert(['role_id' => $id, 'model_id' => $user->id, 'model_type' => $user->getMorphClass()]);

        return $user;
    }

    private function workspaceTitle(User $lecturer, array $extra = []): Title
    {
        return Title::create([...['title' => 'Test title', 'lecturer_id' => $lecturer->lecturer->id, 'description' => 'Description', 'scope' => 'Scope', 'problem_statement' => 'Problem', 'title_source' => 'LECTURER', 'status' => 'open', 'quota' => 1], ...$extra]);
    }

    private function assertWorkspaceDenied(callable $action, int $status = 403): void
    {
        try {
            $action();
            $this->fail('Action unexpectedly succeeded.');
        } catch (HttpException $e) {
            $this->assertSame($status, $e->getStatusCode());
        } catch (ModelNotFoundException $e) {
            $this->assertSame(404, $status);
        }
    }

    public function test_all_student_routes_have_feature_folders_and_action_routes(): void
    {
        $manifest = json_decode(file_get_contents(module_path('Capstone', 'resources/reference/pages.json')), true);
        foreach ($manifest as $page) {
            if (! str_starts_with($page['route'], '/mahasiswa/') || $page['route'] === '/mahasiswa/ta-defense') {
                continue;
            }
            $view = trim(preg_replace('/\[([^\]]+)\]/', '_$1_', $page['route']), '/');
            $this->assertFileExists(module_path('Capstone', 'resources/views/pages/'.$view.'.blade.php'));
            $this->assertMatchesRegularExpression('/@extends\(\'capstone::pages\.mahasiswa\.[\w-]+\.index\'\)/', file_get_contents(module_path('Capstone', 'resources/views/pages/'.$view.'.blade.php')));
        }
        foreach (['POST /group/store-solo' => 'storeSolo', 'POST /group/mark-ready-for-finalization' => 'markReadyForFinalization', 'POST /group/cancel-ready-for-finalization' => 'cancelReadyForFinalization', 'PUT /bids/reorder' => 'reorder', 'DELETE /proposal/1' => 'destroy'] as $operation => $method) {
            [$verb,$path] = explode(' ', $operation);
            $route = app('router')->getRoutes()->match(Request::create('/capstone/session/capstone/mahasiswa'.$path, $verb));
            $this->assertStringEndsWith('@'.$method, $route->getActionName());
            $this->assertContains('capstone.role:mahasiswa', $route->gatherMiddleware());
        }
    }

    public function test_group_creation_requires_the_registered_period_and_preserves_student_identity(): void
    {
        $this->studentWorkspaceSchema();
        $this->actor('admin');
        $student = $this->actor('mahasiswa');
        $period = Period::create(['name' => 'Registered period', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_active' => true]);
        $controller = app(GroupController::class);
        $request = $this->requestFor($student, '/', 'POST', ['period_id' => $period->id]);
        $this->assertWorkspaceDenied(fn () => $controller->storeSolo($request));
        PeriodRegistration::create(['user_id' => $student->student->id, 'period_id' => $period->id, 'status' => 'PENDING']);
        $this->assertWorkspaceDenied(fn () => $controller->storeSolo($request));
        PeriodRegistration::where('user_id', $student->student->id)->update(['status' => 'APPROVED']);
        $response = $controller->storeSolo($request);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('FORMING_SOLO', Group::first()->status);
        $this->assertEquals($student->student->id, GroupMember::first()->student_id);
        $this->assertWorkspaceDenied(fn () => $controller->store($request), 422);
        $this->assertSame(1, Group::count());
    }

    public function test_group_ready_actions_require_approved_work_and_notify_account_ids(): void
    {
        $this->studentWorkspaceSchema();
        $lecturer = $this->workspaceLecturer();
        $student = $this->actor('mahasiswa');
        $other = $this->actor('mahasiswa');
        $group = $this->group($student, null, 'READY_FOR_BIDDING');
        $group->period->update(['min_group_size' => 2]);
        GroupMember::create(['student_id' => $other->student->id, 'group_id' => $group->id]);
        $controller = app(GroupController::class);
        $request = $this->requestFor($student, '/', 'POST', ['group_id' => $group->id]);
        $this->assertWorkspaceDenied(fn () => $controller->markReadyForFinalization($request));
        $this->workspaceTitle($lecturer, ['title_source' => 'STUDENT', 'proposed_by_group_id' => $group->id, 'supervisor_approval_status' => 'APPROVED']);
        $this->assertWorkspaceDenied(fn () => $controller->markReadyForFinalization($this->requestFor($other, '/', 'POST', ['group_id' => $group->id])));
        $this->assertSame(200, $controller->markReadyForFinalization($request)->getStatusCode());
        $this->assertSame('READY_FOR_FINALIZATION', $group->fresh()->status);
        $this->assertEqualsCanonicalizing([$student->id, $other->id], Notification::pluck('user_id')->all());
        $actions = app(GroupService::class)->resolveAllowedActions($group->fresh(), $student);
        $this->assertFalse($actions['can_add_member']);
        $this->assertFalse($actions['can_remove_member']);
        $this->assertTrue($actions['can_cancel_ready_for_finalization']);
        $controller->cancelReadyForFinalization($request);
        $this->assertSame('TITLE_APPROVED', $group->fresh()->status);
        $group->period->update(['is_finalized' => true]);
        $this->assertWorkspaceDenied(fn () => $controller->markReadyForFinalization($request));
    }

    public function test_solo_proposal_can_be_created_edited_and_cancelled_with_owner_and_period_guards(): void
    {
        $this->studentWorkspaceSchema();
        $lecturer = $this->workspaceLecturer();
        $student = $this->actor('mahasiswa');
        $other = $this->actor('mahasiswa');
        $group = $this->group($student, null, 'FORMING_SOLO');
        $group->update(['is_solo' => true]);
        $controller = new StudentProposalController;
        $data = ['title' => 'Solo idea', 'description' => 'Description', 'scope' => 'Scope', 'problem_statement' => 'Problem', 'specializations' => ['AI'], 'proposed_supervisor_id' => $lecturer->lecturer->id];
        $response = $controller->store($this->requestFor($student, '/', 'POST', $data));
        $this->assertSame(201, $response->getStatusCode());
        $title = Title::first();
        $this->assertEquals($group->period_id, $title->period_id);
        $this->assertTrue((bool) $group->fresh()->has_active_proposal);
        $this->assertEquals($lecturer->id, Notification::first()->user_id);
        $this->assertWorkspaceDenied(fn () => $controller->store($this->requestFor($student, '/', 'POST', $data)));
        $this->group($other, null, 'FORMING_SOLO')->update(['is_solo' => true]);
        $this->assertWorkspaceDenied(fn () => $controller->destroy($this->requestFor($other, '/', 'DELETE'), $title->id), 404);
        $edit = [...$data, 'title_id' => $title->id, 'specializations' => ['Network']];
        $this->assertSame(200, $controller->update($this->requestFor($student, '/', 'PUT', $edit))->getStatusCode());
        $this->assertSame(['Network'], $title->fresh()->specializations);
        $group->period->update(['is_finalized' => true]);
        $this->assertWorkspaceDenied(fn () => $controller->update($this->requestFor($student, '/', 'PUT', $edit)));
        $this->assertWorkspaceDenied(fn () => $controller->destroy($this->requestFor($student, '/', 'DELETE'), $title->id));
        $group->period->update(['is_finalized' => false]);
        $controller->destroy($this->requestFor($student, '/', 'DELETE'), $title->id);
        $this->assertSame(0, Title::where('proposed_by_group_id', $group->id)->count());
        $this->assertFalse((bool) $group->fresh()->has_active_proposal);
    }

    public function test_bid_reorder_preserves_ids_under_unique_constraints_and_rejects_other_groups(): void
    {
        $this->studentWorkspaceSchema();
        $lecturer = $this->workspaceLecturer();
        $student = $this->actor('mahasiswa');
        $other = $this->actor('mahasiswa');
        $group = $this->group($student, null, 'READY_FOR_BIDDING');
        $group->period->update(['min_group_size' => 1]);
        $controller = app(BidController::class);
        $bids = [];
        foreach ([1, 2] as $priority) {
            $title = $this->workspaceTitle($lecturer, ['period_id' => $group->period_id]);
            $response = $controller->store($this->requestFor($student, '/', 'POST', ['title_id' => $title->id, 'priority' => $priority]));
            $this->assertSame(201, $response->getStatusCode());
            $bids[] = $response->getData(true)['data']['id'];
        }
        $this->assertNull($group->bids()->find($bids[0])->proposed_supervisor_1_id);
        $otherGroup = $this->group($other, null, 'READY_FOR_BIDDING');
        $foreign = Bid::create(['group_id' => $otherGroup->id, 'title_id' => $this->workspaceTitle($lecturer)->id, 'priority' => 1, 'status' => 'PENDING']);
        $request = $this->requestFor($student, '/', 'PUT', ['bids' => [['id' => $bids[0], 'priority' => 2], ['id' => $foreign->id, 'priority' => 1]]]);
        $this->assertWorkspaceDenied(fn () => $controller->reorder($request), 422);
        $request = $this->requestFor($student, '/', 'PUT', ['bids' => [['id' => $bids[0], 'priority' => 2], ['id' => $bids[1], 'priority' => 1]]]);
        $this->assertSame(200, $controller->reorder($request)->getStatusCode());
        $this->assertSame([$bids[1], $bids[0]], $group->bids()->orderBy('priority')->pluck('id')->all());
        $this->assertEquals(1, $foreign->fresh()->priority);
        $group->period->update(['is_finalized' => true]);
        $this->assertWorkspaceDenied(fn () => $controller->reorder($request));
        $this->assertWorkspaceDenied(fn () => $controller->destroy($this->requestFor($student, '/', 'DELETE'), $bids[1]));
        $group->period->update(['is_finalized' => false]);
        $controller->destroy($this->requestFor($student, '/', 'DELETE'), $bids[1]);
        $this->assertEquals(1, $group->bids()->first()->priority);
    }

    public function test_rejected_bid_frees_slot_and_allows_rebidding_same_title(): void
    {
        $this->studentWorkspaceSchema();
        $lecturer = $this->workspaceLecturer();
        $student = $this->actor('mahasiswa');
        $group = $this->group($student, null, 'READY_FOR_BIDDING');
        $group->period->update(['min_group_size' => 1]);
        $controller = app(BidController::class);
        $titleA = $this->workspaceTitle($lecturer, ['period_id' => $group->period_id]);
        $titleB = $this->workspaceTitle($lecturer, ['period_id' => $group->period_id]);
        $response = $controller->store($this->requestFor($student, '/', 'POST', ['title_id' => $titleA->id, 'priority' => 1]));
        $this->assertSame(201, $response->getStatusCode());
        $response = $controller->store($this->requestFor($student, '/', 'POST', ['title_id' => $titleB->id, 'priority' => 2]));
        $this->assertSame(201, $response->getStatusCode());
        $bidA = $group->bids()->where('title_id', $titleA->id)->firstOrFail();
        $rec = $controller->recommend($this->requestFor($lecturer, '/', 'PUT', ['recommendation' => 'REJECT']), $bidA->id);
        $this->assertSame(200, $rec->getStatusCode());
        $this->assertSame('REJECTED', $bidA->fresh()->status);
        $this->assertSame([1], $group->bids()->where('status', 'PENDING')->orderBy('priority')->pluck('priority')->all());
        // Re-bidding the rejected title must succeed (hit 23505 before partial uniques).
        $response = $controller->store($this->requestFor($student, '/', 'POST', ['title_id' => $titleA->id]));
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(2, $response->getData(true)['data']['priority']);
        $this->assertNull(Bid::find($response->getData(true)['data']['id'])->proposed_supervisor_1_id);
    }

    public function test_accepting_marketplace_join_promotes_proposal_without_assigning_title_early(): void
    {
        $this->studentWorkspaceSchema();
        (require __DIR__.'/../database/migrations/2026_05_05_000006_create_capstone_group_invitations_table.php')->up();
        (require __DIR__.'/../database/migrations/2026_05_05_000007_create_capstone_group_supervisor_proposals_table.php')->up();
        Schema::create('capstone_join_requests', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('requester_id');
            $t->string('status')->default('PENDING');
            $t->text('message')->nullable();
            $t->timestamps();
        });
        $lecturer = $this->workspaceLecturer();
        $leader = $this->actor('mahasiswa');
        $requester = $this->actor('mahasiswa');
        $teammate = $this->actor('mahasiswa');
        $roleId = DB::table('roles')->insertGetId(['name' => 'mahasiswa', 'guard_name' => 'web']);
        DB::table('model_has_roles')->insert(['role_id' => $roleId, 'model_id' => $requester->id, 'model_type' => $requester->getMorphClass()]);
        $target = $this->group($leader, null, 'FORMING_SOLO');
        $target->update(['is_solo' => true]);
        $target->period->update(['min_group_size' => 3, 'max_group_size' => 4]);
        $source = $this->group($requester, null, 'FORMING', $target->period);
        GroupMember::where('group_id', $source->id)->update(['period_id' => $target->period_id]);
        GroupMember::create(['group_id' => $source->id, 'period_id' => $target->period_id, 'student_id' => $teammate->student->id]);
        $title = $this->workspaceTitle($lecturer, ['title_source' => 'STUDENT', 'proposed_by_group_id' => $target->id, 'proposed_supervisor_id' => $lecturer->lecturer->id, 'supervisor_approval_status' => 'UNDER_REVIEW', 'period_id' => $target->period_id]);
        $join = JoinRequest::create(['group_id' => $target->id, 'requester_id' => $requester->student->id, 'status' => 'PENDING']);
        $controller = app(BursaIdeController::class);
        $response = $controller->acceptRequest($this->requestFor($leader, '/', 'POST'), $join->id);
        $this->assertSame(200, $response->getStatusCode(), $response->getContent());
        $this->assertSame('ACCEPTED', $join->fresh()->status);
        $this->assertSame('APPROVED', $title->fresh()->supervisor_approval_status);
        $this->assertSame('TITLE_APPROVED', $target->fresh()->status);
        $this->assertNull($target->fresh()->title_id);
        $this->assertSame(3, $target->members()->count());
        $this->assertNull(Group::find($source->id));
        $this->assertEquals($leader->student->id, $target->members()->where('is_leader', true)->first()->student_id);
    }

    private function periodWizardSchema(): void
    {
        Schema::table('capstone_periods', function (Blueprint $t) {
            $t->boolean('is_finalized')->default(false);
            $t->integer('max_supervisor_load')->nullable();
            foreach (['bidding', 'pdc1', 'pdc2', 'expo', 'ta'] as $phase) {
                $t->dateTime($phase.'_reminder_at')->nullable();
            }
        });
        foreach (['capstone_assessment_component_templates', 'capstone_peer_review_indicator_templates'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('code')->nullable();
                $t->string('description')->nullable();
                $t->decimal('weight', 5, 2);
                $t->boolean('is_active')->default(true);
                $t->unsignedBigInteger('created_by')->nullable();
                $t->integer('sort_order')->default(0);
                $t->timestamps();
            });
        }
        foreach (['capstone_period_assessment_components' => 'capstone_assessment_component_templates', 'capstone_period_peer_review_indicators' => 'capstone_peer_review_indicator_templates'] as $table => $templates) {
            Schema::create($table, function (Blueprint $t) use ($table, $templates) {
                $t->id();
                $t->foreignId('period_id')->constrained('capstone_periods');
                $t->foreignId('template_id')->constrained($templates);
                $t->integer('sort_order')->default(0);
                $t->timestamps();
                if ($table === 'capstone_period_assessment_components') {
                    $t->string('type');
                }
            });
        }
    }

    public function test_configuration_endpoints_use_the_correct_template_tables_and_keep_existing_ids(): void
    {
        $this->periodWizardSchema();
        $period = Period::create(['name' => 'Source', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
        $target = Period::create(['name' => 'Target', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
        $template = AssessmentComponentTemplate::create(['name' => 'Assessment', 'code' => 'A', 'weight' => 100]);
        // Different IDs prove that an assessment template cannot masquerade as a peer template.
        $peer = PeerReviewIndicatorTemplate::create(['name' => 'Unused', 'weight' => 50]);
        $peer = PeerReviewIndicatorTemplate::create(['name' => 'Peer template', 'weight' => 100]);
        $assessmentController = new PeriodAssessmentConfigController;
        $peerController = new PeriodPeerReviewConfigController;
        foreach ([$period, $target] as $p) {
            $this->assertSame(201, $assessmentController->store(Request::create('/', 'POST', ['type' => 'EXPO', 'template_ids' => [$template->id]]), $p->id)->getStatusCode());
            $this->assertSame(201, $peerController->store(Request::create('/', 'POST', ['template_ids' => [$peer->id]]), $p->id)->getStatusCode());
        }
        $component = PeriodAssessmentComponent::where('period_id', $target->id)->firstOrFail();
        $indicator = PeriodPeerReviewIndicator::where('period_id', $target->id)->firstOrFail();
        $this->assertSame('Peer template', $peerController->show(Request::create('/'), $target->id)->getData(true)['data']['selected_indicators'][0]['name']);
        $assessmentController->copy(Request::create('/', 'POST', ['source_period_id' => $period->id]), $target->id);
        $peerController->copy(Request::create('/', 'POST', ['source_period_id' => $period->id]), $target->id);
        $this->assertNotNull($component->fresh());
        $this->assertNotNull($indicator->fresh());
        foreach ([$assessmentController, $peerController] as $controller) {
            try {
                $controller->copy(Request::create('/', 'POST', ['source_period_id' => $period->id]), $period->id);
                $this->fail('Self copy must be rejected');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('source_period_id', $e->errors());
            }
        }
        $peerController->store(Request::create('/', 'POST', ['template_ids' => []]), $target->id);
        $this->assertSame(0, PeriodPeerReviewIndicator::where('period_id', $target->id)->count());
        $target->update(['is_finalized' => true]);
        foreach ([$assessmentController, $peerController] as $controller) {
            try {
                $controller->store(Request::create('/', 'POST', ['type' => 'EXPO', 'template_ids' => []]), $target->id);
                $this->fail('Finalized period must be locked');
            } catch (HttpException $e) {
                $this->assertSame(403, $e->getStatusCode());
            }
        }
        $this->assertNotNull($component->fresh());
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_assessment_bank_validates_prefixed_table_and_lists_inactive_templates(): void
    {
        $this->periodWizardSchema();
        $admin = $this->actor('admin');
        $controller = new AssessmentComponentTemplateController;
        $payload = ['code' => 'CPL-01', 'name' => 'CPL-01', 'description' => 'Assessment description', 'weight' => 100, 'is_active' => false];
        $response = $controller->store($this->requestFor($admin, '/', 'POST', $payload));
        $this->assertSame(201, $response->getStatusCode());
        $template = AssessmentComponentTemplate::firstOrFail();
        $this->assertFalse($template->is_active);
        $this->assertSame($template->id, $controller->index()->getData(true)['data'][0]['id']);
        try {
            $controller->store($this->requestFor($admin, '/', 'POST', $payload));
            $this->fail('Duplicate code must fail validation');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('code', $e->errors());
        }
        $controller->update($this->requestFor($admin, '/', 'PUT', ['code' => 'CPL-01', 'is_active' => true]), $template->id);
        $this->assertTrue($template->fresh()->is_active);
        $period = Period::create(['name' => 'Configured', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
        $component = PeriodAssessmentComponent::create(['period_id' => $period->id, 'template_id' => $template->id, 'type' => 'EXPO']);
        $controller->destroy($template->id);
        $this->assertFalse($template->fresh()->is_active);
        $this->assertNotNull($component->fresh());
    }

    public function test_period_wizard_persists_configurations_atomically_and_preserves_component_ids(): void
    {
        $this->periodWizardSchema();
        $template = AssessmentComponentTemplate::create(['name' => 'Demo', 'code' => 'DEMO', 'weight' => 100]);
        $peer = PeerReviewIndicatorTemplate::create(['name' => 'Teamwork', 'weight' => 100]);
        $controller = new BladePeriodController;
        $body = ['name' => 'Semester', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_active' => false, 'min_group_size' => 3, 'max_group_size' => 4, 'max_supervisor_load' => 5, 'expo_reminder_at' => '2026-06-01', 'assessments' => array_fill_keys($controller::TYPES, []), 'peer_ids' => [$peer->id]];
        $body['assessments']['EXPO'] = [$template->id];
        $response = $controller->store(Request::create('/', 'POST', $body));
        $this->assertSame(201, $response->getStatusCode());
        $period = Period::firstOrFail();
        $this->assertSame(5, $period->max_supervisor_load);
        $this->assertSame(5, $period->max_supervise_load);
        $this->assertStringStartsWith('2026-06-01', $period->expo_reminder_at);
        $component = PeriodAssessmentComponent::firstOrFail();
        $this->assertSame($template->id, $component->template_id);
        $this->assertSame($peer->id, PeriodPeerReviewIndicator::firstOrFail()->template_id);
        $body['name'] = 'Renamed';
        $this->assertSame(200, $controller->update(Request::create('/', 'PUT', $body), $period)->getStatusCode());
        $this->assertSame($component->id, PeriodAssessmentComponent::firstOrFail()->id);
        DB::unprepared("CREATE TRIGGER reject_peer BEFORE INSERT ON capstone_period_peer_review_indicators BEGIN SELECT RAISE(ABORT, 'Simulated wizard failure'); END");
        try {
            $controller->store(Request::create('/', 'POST', $body));
            $this->fail('Expected failed transaction');
        } catch (QueryException $e) {
            $this->assertStringContainsString('Simulated wizard failure', $e->getMessage());
        }
        $this->assertSame(1, Period::count());
        $this->assertSame(1, PeriodAssessmentComponent::count());
        $period->update(['is_finalized' => true]);
        try {
            $controller->update(Request::create('/', 'PUT', $body), $period);
            $this->fail('Finalized period must be locked');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_capstone_entry_uses_blade_session_dashboard_for_each_role(): void
    {
        $launchRoute = app('router')->getRoutes()->getByName('capstone.dashboard');
        $this->assertSame('capstone/launch', $launchRoute->uri());
        $this->assertContains('auth', $launchRoute->gatherMiddleware());
        $this->assertContains('module.active:capstone', $launchRoute->gatherMiddleware());
        $dashboardRoute = app('router')->getRoutes()->getByName('capstone.blade.dashboard');
        $this->assertSame(BladeController::class.'@dashboard', $dashboardRoute->getActionName());
        $this->assertContains(BladeAccessMiddleware::class, $dashboardRoute->gatherMiddleware());
        $homeRoute = app('router')->getRoutes()->getByName('capstone.page.home');
        $this->assertSame(route('capstone.blade.dashboard'), ($homeRoute->getAction('uses'))()->getTargetUrl());

        foreach (['admin', 'dosen', 'mahasiswa'] as $role) {
            $user = $this->actor($role);
            $sync = \Mockery::mock(AcademicRoleSynchronizer::class);
            $sync->shouldReceive('syncIfNeeded')->once()->with($user)->andReturn(false);
            $request = $this->requestFor($user, '/capstone/launch');
            $response = (new LaunchController)->launch($request, $sync);
            $this->assertSame(route('capstone.blade.dashboard'), $response->getTargetUrl());
            $response = (new BladeAccessMiddleware)->handle($request, fn ($request) => (new BladeController)->dashboard($request));
            $this->assertSame(url('/capstone/'.$role.'/dashboard'), $response->getTargetUrl());
        }
    }

    public function test_document_configuration_preserves_other_phases_and_locks_finalized_periods(): void
    {
        (require __DIR__.'/../database/migrations/2026_05_05_000012_create_capstone_phase_document_requirements_table.php')->up();
        Schema::table('capstone_periods', fn (Blueprint $t) => $t->boolean('is_finalized')->default(false));
        $period = $this->group()->period;
        $other = $this->group()->period;
        $requirements = PhaseDocumentRequirement::class;
        $expo = $requirements::create(['period_id' => $period->id, 'phase' => 'EXPO', 'name' => 'Poster', 'is_required' => true]);
        $ta = $requirements::create(['period_id' => $period->id, 'phase' => 'TA', 'name' => 'Old', 'is_required' => true]);
        $controller = new PhaseDocumentRequirementController;
        $payload = ['period_id' => $period->id, 'phase' => 'TA', 'requirements' => [['phase' => 'TA', 'name' => 'Thesis', 'is_required' => true]]];
        $this->assertSame(200, $controller->bulkUpdate(Request::create('/', 'PUT', $payload))->getStatusCode());
        $this->assertSame('Poster', $expo->fresh()->name);
        $this->assertNull($ta->fresh());
        $this->assertSame(1, $requirements::where('phase', 'TA')->count());
        $payload['requirements'] = [];
        $this->assertSame(200, $controller->bulkUpdate(Request::create('/', 'PUT', $payload))->getStatusCode());
        $this->assertSame(0, $requirements::where('phase', 'TA')->count());
        $payload['requirements'] = [['phase' => 'EXPO', 'name' => 'Wrong phase']];
        $this->assertSame(422, $controller->bulkUpdate(Request::create('/', 'PUT', $payload))->getStatusCode());
        $this->assertSame(422, $controller->update(Request::create('/', 'PUT', ['period_id' => $other->id]), (string) $expo->id)->getStatusCode());
        $this->assertSame($period->id, $expo->fresh()->period_id);
        $period->forceFill(['is_finalized' => true])->save();
        $payload['requirements'] = [];
        $this->assertSame(403, $controller->bulkUpdate(Request::create('/', 'PUT', $payload))->getStatusCode());
        $this->assertSame(403, $controller->store(Request::create('/', 'POST', ['period_id' => $period->id, 'phase' => 'TA', 'name' => 'Forbidden']))->getStatusCode());
        $this->assertSame(403, $controller->update(Request::create('/', 'PUT', ['name' => 'Forbidden']), (string) $expo->id)->getStatusCode());
        $this->assertSame(403, $controller->destroy((string) $expo->id)->getStatusCode());
        $this->assertSame('Poster', $expo->fresh()->name);
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_document_configuration_rolls_back_when_replacement_fails(): void
    {
        (require __DIR__.'/../database/migrations/2026_05_05_000012_create_capstone_phase_document_requirements_table.php')->up();
        Schema::table('capstone_periods', fn (Blueprint $t) => $t->boolean('is_finalized')->default(false));
        $period = $this->group()->period;
        $model = PhaseDocumentRequirement::class;
        $existing = $model::create(['period_id' => $period->id, 'phase' => 'TA', 'name' => 'Original']);
        DB::unprepared("CREATE TRIGGER fail_requirement BEFORE INSERT ON capstone_phase_document_requirements WHEN NEW.name = 'Fail' BEGIN SELECT RAISE(ABORT, 'Simulated failure'); END");
        try {
            (new PhaseDocumentRequirementController)->bulkUpdate(Request::create('/', 'PUT', [
                'period_id' => $period->id, 'phase' => 'TA', 'requirements' => [['phase' => 'TA', 'name' => 'First'], ['phase' => 'TA', 'name' => 'Fail']],
            ]));
            $this->fail('Expected a database failure');
        } catch (QueryException $e) {
            $this->assertStringContainsString('Simulated failure', $e->getMessage());
        }
        $this->assertSame('Original', $existing->fresh()->name);
        $this->assertSame(1, $model::count());
        $this->assertSame(0, DB::transactionLevel());
    }

    private function actor(string $role): User
    {
        $n = User::count() + 1;
        $user = User::withoutEvents(fn () => User::create(['external_id' => 'blade-'.$n, 'name' => 'Test '.$n, 'email' => 'blade'.$n.'@example.test']));
        $user->setRelation('roles', collect([new Role(['name' => $role === 'admin' ? 'admin_capstone' : $role])]));
        if ($role === 'mahasiswa') {
            $user->setRelation('student', Student::create(['user_id' => $user->id, 'student_number' => 'NIM-'.$n, 'cohort_year' => 2024]));
        }
        if ($role === 'dosen') {
            $user->setRelation('lecturer', Lecturer::create(['user_id' => $user->id, 'employee_number' => 'NIP-'.$n]));
        }

        return $user;
    }

    private function group(?User $student = null, ?User $lecturer = null, string $status = 'PDC1_ACTIVE', ?Period $period = null): Group
    {
        $period ??= Period::create(['name' => 'Test period', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_active' => true]);
        $group = Group::create(['period_id' => $period->id, 'status' => $status, 'supervisor_1_id' => $lecturer?->lecturer?->id]);
        if ($student) {
            GroupMember::create(['group_id' => $group->id, 'student_id' => $student->student->id, 'is_leader' => true]);
        }

        return $group;
    }

    private function requestFor(User $user, string $path, string $method = 'GET', array $data = []): Request
    {
        $request = Request::create($path, $method, $data);
        $request->setUserResolver(fn () => $user);
        $request->setLaravelSession(app('session')->driver());

        return $request;
    }

    public function test_status_matrix_matches_next_menu_prerequisites(): void
    {
        foreach (['group', 'titles', 'documents', 'schedule', 'expo', 'peer-review', 'grades'] as $feature) {
            $this->assertNotNull(BladeFeatureAccess::reason('/mahasiswa/'.$feature, ['registered' => false]));
        }
        foreach (['registration', 'dashboard'] as $feature) {
            $this->assertNull(BladeFeatureAccess::reason('/mahasiswa/'.$feature, ['registered' => false]));
        }
        $state = ['registered' => true, 'group_status' => 'FORMING'];
        $this->assertNull(BladeFeatureAccess::reason('/mahasiswa/group', $state));
        $this->assertNotNull(BladeFeatureAccess::reason('/mahasiswa/documents', $state));
        $state['group_status'] = 'PDC1_ACTIVE';
        $this->assertNull(BladeFeatureAccess::reason('/mahasiswa/grades', $state));
        $this->assertNotNull(BladeFeatureAccess::reason('/mahasiswa/expo/42', $state));
        $state['group_status'] = 'PDC2_READY_FOR_EXPO';
        $this->assertNull(BladeFeatureAccess::reason('/mahasiswa/expo/42', $state));
        $this->assertNotNull(BladeFeatureAccess::reason('/mahasiswa/peer-review', $state));
        $state['group_status'] = 'EXPO_REGISTERED';
        $this->assertNull(BladeFeatureAccess::reason('/mahasiswa/peer-review', $state));
        // Later phases must not lose access to earlier coursework.
        $state['group_status'] = 'TA_IN_PROGRESS';
        $this->assertNull(BladeFeatureAccess::reason('/mahasiswa/peer-review', $state));
    }

    public function test_direct_page_is_denied_until_group_enters_the_required_phase(): void
    {
        $user = $this->actor('mahasiswa');
        $group = $this->group($user, null, 'FORMING');
        $middleware = new BladeAccessMiddleware;
        $response = $middleware->handle($this->requestFor($user, '/capstone/mahasiswa/schedule'), fn () => response('allowed'), 'mahasiswa');
        $this->assertSame(403, $response->getStatusCode());
        $this->assertStringContainsString('Available after PDC1 starts', $response->getContent());
        $group->update(['status' => 'PDC1_ACTIVE']);
        $response = $middleware->handle($this->requestFor($user, '/capstone/mahasiswa/schedule'), fn () => response('allowed'), 'mahasiswa');
        $this->assertSame('allowed', $response->getContent());
    }

    public function test_existing_pdc1_work_unlocks_legacy_group_menus_and_session_endpoints(): void
    {
        $user = $this->actor('mahasiswa');
        $group = $this->group($user, null, 'TITLE_APPROVED');
        $this->assertFalse(BladeFeatureAccess::snapshot($user)['pdc1_started']);
        DB::table('capstone_documents')->insert([
            'group_id' => $group->id, 'student_id' => $user->student->id,
            'phase' => 'PDC1', 'document_type' => 'C100', 'status' => 'SUBMITTED', 'file_path' => 'test.pdf',
        ]);
        $state = BladeFeatureAccess::snapshot($user);
        $this->assertTrue($state['pdc1_started']);
        Schema::create('capstone_phase_document_requirements', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('phase');
            $t->string('name');
            $t->boolean('is_required')->default(true);
        });
        $this->actingAs($user);
        $documents = new DocumentController(app(GroupStateMachine::class), \Mockery::mock(DocumentStorageService::class));
        $workflow = $documents->workflow($this->requestFor($user, '/'))->getData(true);
        $this->assertTrue(collect($workflow['phases'])->firstWhere('phase', 'PDC1')['can_upload']);
        $this->assertFalse(collect($workflow['phases'])->firstWhere('phase', 'SEMPRO')['can_upload']);
        foreach (['documents', 'schedule', 'grades'] as $feature) {
            $response = (new BladeAccessMiddleware)->handle($this->requestFor($user, '/capstone/mahasiswa/'.$feature), fn () => response('allowed'), 'mahasiswa');
            $this->assertSame('allowed', $response->getContent());
        }
        foreach (['documents', 'my-grades'] as $endpoint) {
            $response = (new BladeSessionFeatureMiddleware)->handle($this->requestFor($user, '/capstone/session/capstone/mahasiswa/'.$endpoint), fn () => response('allowed'));
            $this->assertSame('allowed', $response->getContent());
        }
        $html = view('capstone::layouts.sidebar', ['actor' => ['roles' => ['mahasiswa']], 'activeRole' => 'mahasiswa', 'pagePath' => '/mahasiswa/dashboard', 'featureAccess' => $state])->render();
        foreach (['Progress &amp; Docs', 'Schedules', 'Evaluations'] as $title) {
            $this->assertMatchesRegularExpression('/<button[^>]*aria-disabled="false"[^>]*title="'.preg_quote($title, '/').'"/', $html);
        }
        $this->assertNotNull(BladeFeatureAccess::reason('/mahasiswa/expo', $state));
        $this->assertNotNull(BladeFeatureAccess::reason('/mahasiswa/peer-review', $state));
        $this->assertSame('TITLE_APPROVED', $group->fresh()->status);
        $group->update(['status' => 'DISSOLVED']);
        $this->assertFalse(BladeFeatureAccess::snapshot($user)['pdc1_started']);
    }

    public function test_documents_from_another_group_do_not_unlock_student_access(): void
    {
        $user = $this->actor('mahasiswa');
        $this->group($user, null, 'TITLE_APPROVED');
        $other = $this->group();
        DB::table('capstone_documents')->insert(['group_id' => $other->id, 'phase' => 'PDC1', 'file_path' => 'test.pdf']);
        $state = BladeFeatureAccess::snapshot($user);
        $this->assertFalse($state['pdc1_started']);
        $this->assertNotNull(BladeFeatureAccess::reason('/mahasiswa/documents', $state));
        foreach (['TA_IN_PROGRESS', 'PDC2_COMPLETED'] as $status) {
            foreach (['documents', 'schedule', 'grades'] as $feature) {
                $this->assertNull(BladeFeatureAccess::reason('/mahasiswa/'.$feature, ['registered' => true, 'group_status' => $status]));
            }
        }
    }

    public function test_forged_role_does_not_open_admin_pages(): void
    {
        $user = $this->actor('mahasiswa');
        $request = $this->requestFor($user, '/capstone/admin/schedule');
        $request->headers->set('X-Capstone-Role', 'admin');
        $this->expectException(HttpException::class);
        (new BladeAccessMiddleware)->handle($request, fn () => response('allowed'), 'admin');
    }

    public function test_session_actions_cannot_bypass_registration_or_expo_gate(): void
    {
        $user = $this->actor('mahasiswa');
        $middleware = new BladeSessionFeatureMiddleware;
        $request = $this->requestFor($user, '/capstone/session/capstone/mahasiswa/group', 'POST');
        $this->assertSame(403, $middleware->handle($request, fn () => response('allowed'))->getStatusCode());
        $this->group($user, null, 'PDC1_ACTIVE');
        foreach (['expo-events/1/register', 'peer-review', 'ta-defense-schedules/my-schedule', 'my-grades'] as $path) {
            $request = $this->requestFor($user, '/capstone/session/capstone/mahasiswa/'.$path, 'POST');
            $response = $middleware->handle($request, fn () => response('allowed'));
            $this->assertSame(str_starts_with($path, 'expo') || $path === 'peer-review' ? 403 : 200, $response->getStatusCode());
        }
    }

    public function test_existing_membership_unlocks_registration_without_writing_during_render(): void
    {
        $user = $this->actor('mahasiswa');
        $this->group($user);
        $this->assertTrue(BladeFeatureAccess::snapshot($user)['registered']);
        $this->assertSame(0, PeriodRegistration::count());
    }

    public function test_locked_links_have_no_href_and_evaluation_parent_stays_open_for_grades(): void
    {
        request()->attributes->set('capstone_feature_access', ['registered' => true, 'group_status' => 'PDC1_ACTIVE']);
        $html = Blade::render('<x-capstone::button href="/mahasiswa/expo">Expo</x-capstone::button>');
        $this->assertStringContainsString('aria-disabled="true"', $html);
        $this->assertStringNotContainsString('href=', $html);
        $html = view('capstone::layouts.sidebar', ['actor' => ['roles' => ['mahasiswa']], 'activeRole' => 'mahasiswa', 'pagePath' => '/mahasiswa/dashboard', 'featureAccess' => ['registered' => true, 'group_status' => 'PDC1_ACTIVE']])->render();
        $this->assertStringContainsString('/capstone/mahasiswa/grades', $html);
        $this->assertStringNotContainsString('href="'.url('/capstone/mahasiswa/peer-review').'"', $html);
        $this->assertMatchesRegularExpression('/<button[^>]*aria-disabled="false"[^>]*title="Evaluations"/', $html);
    }

    private function seminar(Group $group, string $type = 'BIMBINGAN', array $extra = []): int
    {
        return DB::table('capstone_seminar_schedules')->insertGetId([...['group_id' => $group->id, 'type' => $type, 'date' => '2026-09-09', 'start_time' => '09:00:00', 'end_time' => '10:00:00', 'room' => 'Lab'], ...$extra]);
    }

    public function test_student_calendar_excludes_other_students_individual_defenses(): void
    {
        $student = $this->actor('mahasiswa');
        $other = $this->actor('mahasiswa');
        $group = $this->group($student);
        GroupMember::create(['group_id' => $group->id, 'student_id' => $other->student->id]);
        $own = $this->seminar($group);
        $this->seminar($this->group($other));
        foreach ([$student, $other] as $actor) {
            DB::table('capstone_ta_defense_schedules')->insert(['group_id' => $group->id, 'student_id' => $actor->student->id, 'date' => '2026-09-09', 'start_time' => '11:00', 'end_time' => '12:00']);
        }
        $request = $this->requestFor($student, '/');
        $request->attributes->set('capstone_role', 'mahasiswa');
        $events = (new CalendarController)->index($request)->getData(true)['data'];
        $this->assertCount(2, $events);
        $this->assertSame('bim_'.$own, $events[0]['id']);
        $this->assertSame($student->student->id, $events[1]['student_id']);
        $this->assertSame('2026-09-09', $events[0]['date']);
    }

    public function test_lecturer_calendar_includes_examiner_assignment_and_filters_period(): void
    {
        $lecturer = $this->actor('dosen');
        $mine = $this->group(null, $lecturer);
        $other = $this->group();
        $this->seminar($mine);
        $exam = $this->seminar($other, 'SEMPRO', ['examiner_1_id' => $lecturer->lecturer->id]);
        $this->seminar($this->group());
        $request = $this->requestFor($lecturer, '/');
        $request->attributes->set('capstone_role', 'dosen');
        $events = (new CalendarController)->index($request)->getData(true)['data'];
        $this->assertCount(2, $events);
        $this->assertContains($exam, array_column($events, 'id'));
        $request->query->set('period_id', $mine->period_id);
        $this->assertCount(1, (new CalendarController)->index($request)->getData(true)['data']);
    }

    public function test_mentoring_saves_times_and_allows_multiple_sessions(): void
    {
        $lecturer = $this->actor('dosen');
        $this->actingAs($lecturer);
        $group = $this->group(null, $lecturer);
        $controller = new ScheduleController;
        for ($i = 0; $i < 2; $i++) {
            $request = $this->requestFor($lecturer, '/', 'POST', ['group_id' => $group->id, 'type' => 'BIMBINGAN', 'date' => '2026-09-'.(10 + $i), 'start_time' => '09:00', 'end_time' => '10:00', 'room' => 'Lab', 'mode' => 'offline', 'notes' => 'Session', 'status' => 'APPROVED']);
            $request->attributes->set('capstone_role', 'dosen');
            $this->assertSame(201, $controller->store($request)->getStatusCode());
        }
        $this->assertSame(2, DB::table('capstone_seminar_schedules')->count());
        $saved = DB::table('capstone_seminar_schedules')->first();
        $this->assertSame('09:00', $saved->start_time);
        $this->assertSame('SCHEDULED', $saved->status);
        $this->assertSame('Session', $saved->notes);
    }

    public function test_a_lecturer_cannot_reassign_another_groups_schedule_to_their_group(): void
    {
        $lecturer = $this->actor('dosen');
        $this->actingAs($lecturer);
        $mine = $this->group(null, $lecturer);
        $id = $this->seminar($this->group());
        $request = $this->requestFor($lecturer, '/', 'PUT', ['group_id' => $mine->id]);
        $request->attributes->set('capstone_role', 'dosen');
        $this->expectException(HttpException::class);
        (new ScheduleController)->update($request, (string) $id);
    }

    public function test_a_lecturer_cannot_delete_a_seminar_even_in_their_own_group(): void
    {
        $lecturer = $this->actor('dosen');
        $this->actingAs($lecturer);
        $id = $this->seminar($this->group(null, $lecturer), 'SEMPRO');
        $request = $this->requestFor($lecturer, '/', 'DELETE');
        $request->attributes->set('capstone_role', 'dosen');
        $this->expectException(HttpException::class);
        (new ScheduleController)->destroy($request, (string) $id);
    }

    public function test_seminar_uniqueness_is_preserved_by_calendar_migration(): void
    {
        $group = $this->group();
        $this->seminar($group, 'SEMPRO');
        $this->expectException(QueryException::class);
        $this->seminar($group, 'SEMPRO');
    }

    public function test_document_upload_checks_all_requirements_and_sempro_schedule_before_storage(): void
    {
        Schema::create('capstone_phase_document_requirements', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('phase');
            $t->string('name');
            $t->boolean('is_required')->default(true);
        });
        $student = $this->actor('mahasiswa');
        $this->actingAs($student);
        $group = $this->group($student);
        foreach (['A', 'B'] as $name) {
            DB::table('capstone_phase_document_requirements')->insert(['period_id' => $group->period_id, 'phase' => 'PDC1', 'name' => $name]);
        }
        $first = DB::table('capstone_documents')->insertGetId(['group_id' => $group->id, 'student_id' => $student->student->id, 'phase' => 'PDC1', 'document_type' => 'A', 'status' => 'APPROVED', 'file_path' => 'test.pdf']);
        $storage = \Mockery::mock(DocumentStorageService::class);
        $storage->shouldNotReceive('store');
        $storage->shouldNotReceive('delete');
        $controller = new DocumentController(app(GroupStateMachine::class), $storage);
        $request = $this->requestFor($student, '/', 'POST', ['phase' => 'SEMPRO', 'document_type' => 'GENERAL']);
        $request->files->set('file', UploadedFile::fake()->create('test.pdf', 1, 'application/pdf'));
        $this->assertSame(403, $controller->store($request)->getStatusCode());
        DB::table('capstone_documents')->insert(['group_id' => $group->id, 'student_id' => $student->student->id, 'phase' => 'PDC1', 'document_type' => 'B', 'status' => 'APPROVED', 'file_path' => 'test.pdf']);
        $response = $controller->store($request);
        $this->assertSame(403, $response->getStatusCode());
        $this->assertStringContainsString('SEMPRO belum dijadwalkan', $response->getData(true)['message']);
        $this->seminar($group, 'SEMPRO');
        $workflow = $controller->workflow($request)->getData(true);
        $this->assertTrue(collect($workflow['phases'])->firstWhere('phase', 'SEMPRO')['can_upload']);
        $request->merge(['phase' => 'PDC1', 'document_type' => 'A']);
        $this->assertSame(403, $controller->store($request)->getStatusCode());
        $this->assertSame('APPROVED', DB::table('capstone_documents')->where('id', $first)->value('status'));
    }

    public function test_calendar_migration_rolls_back_without_deleting_records(): void
    {
        $id = $this->seminar($this->group());
        $migration = require __DIR__.'/../database/migrations/2026_09_08_110000_support_capstone_mentoring_calendar.php';
        $migration->down();
        $this->assertFalse(Schema::hasColumn('capstone_seminar_schedules', 'mode'));
        $this->assertSame($id, DB::table('capstone_seminar_schedules')->value('id'));
        $migration->up();
        $this->assertTrue(Schema::hasColumn('capstone_seminar_schedules', 'mode'));
    }

    private function peerReviewSchema(): void
    {
        (require __DIR__.'/../database/migrations/2026_05_05_000024_create_capstone_peer_review_indicators_table.php')->up();
        (require __DIR__.'/../database/migrations/2026_05_05_000025_create_capstone_peer_reviews_table.php')->up();
        Schema::create('capstone_peer_review_indicator_templates', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->text('description')->nullable();
            $t->decimal('weight', 5, 2);
            $t->timestamps();
        });
        Schema::create('capstone_period_peer_review_indicators', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->unsignedBigInteger('template_id');
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });
        Schema::table('capstone_peer_reviews', function (Blueprint $t) {
            $t->unsignedBigInteger('period_indicator_id')->nullable();
            $t->decimal('raw_score', 5, 2)->nullable();
            $t->boolean('is_final_submission')->default(false);
            $t->timestamp('submitted_at')->nullable();
        });
        (require __DIR__.'/../database/migrations/2026_09_09_000001_support_period_peer_review_submissions.php')->up();
        Schema::create('capstone_student_peer_review_status', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('student_id');
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('period_id');
            $t->boolean('has_completed_peer_review')->default(false);
            $t->string('ta_status')->default('TA_BLOCKED');
            $t->timestamps();
        });
    }

    public function test_final_peer_review_is_atomic_and_cannot_be_revised(): void
    {
        $this->peerReviewSchema();
        $reviewer = $this->actor('mahasiswa');
        $reviewee = $this->actor('mahasiswa');
        $group = $this->group($reviewer, null, 'EXPO_REGISTERED');
        GroupMember::create(['group_id' => $group->id, 'student_id' => $reviewee->student->id]);
        $indicators = [];
        foreach (['Contribution', 'Teamwork'] as $name) {
            $indicators[] = PeerReviewIndicator::create(['period_id' => $group->period_id, 'name' => $name, 'weight' => 50]);
        }
        $controller = new PeerReviewController;
        $reviews = array_map(fn ($i) => ['reviewee_id' => $reviewee->student->id, 'indicator_id' => $i->id, 'score' => 75], $indicators);
        $request = $this->requestFor($reviewer, '/', 'POST', ['reviews' => [$reviews[0]]]);
        $this->assertSame(422, $controller->store($request)->getStatusCode());
        $this->assertSame(0, PeerReview::count());
        $request->merge(['reviews' => $reviews]);
        $this->assertSame(201, $controller->store($request)->getStatusCode());
        $this->assertSame(2, PeerReview::where('is_final_submission', true)->count());
        $this->assertSame('3.00', PeerReview::first()->raw_score);
        $this->assertSame(403, $controller->store($request)->getStatusCode());
        $this->assertSame('75.00', PeerReview::first()->score);
        $status = StudentPeerReviewStatus::first();
        $this->assertTrue($status->has_completed_peer_review);
        $this->assertSame('TA_BLOCKED', $status->ta_status);
        $this->assertTrue($controller->index($this->requestFor($reviewer, '/'))->getData(true)['has_submitted']);
    }

    public function test_peer_review_rejects_self_review_wrong_period_and_pre_expo_submission(): void
    {
        $this->peerReviewSchema();
        $reviewer = $this->actor('mahasiswa');
        $reviewee = $this->actor('mahasiswa');
        $group = $this->group($reviewer, null, 'EXPO_REGISTERED');
        GroupMember::create(['group_id' => $group->id, 'student_id' => $reviewee->student->id]);
        $foreign = $this->group();
        $ind = PeerReviewIndicator::create(['period_id' => $group->period_id, 'name' => 'Local', 'weight' => 100]);
        $other = PeerReviewIndicator::create(['period_id' => $foreign->period_id, 'name' => 'Other period', 'weight' => 100]);
        $controller = new PeerReviewController;
        foreach ([[$reviewer->student->id, $ind->id], [$reviewee->student->id, $other->id]] as [$studentId,$indicatorId]) {
            $request = $this->requestFor($reviewer, '/', 'POST', ['reviews' => [['reviewee_id' => $studentId, 'indicator_id' => $indicatorId, 'score' => 100]]]);
            $this->assertSame(422, $controller->store($request)->getStatusCode());
        }
        $group->update(['status' => 'PDC1_ACTIVE']);
        $this->assertSame(403, $controller->store($request)->getStatusCode());
        $this->assertSame(0, PeerReview::count());
    }

    public function test_period_template_peer_review_uses_one_to_four_scale_and_preserves_final_lock(): void
    {
        $this->peerReviewSchema();
        $reviewer = $this->actor('mahasiswa');
        $reviewee = $this->actor('mahasiswa');
        $group = $this->group($reviewer, null, 'EXPO_REGISTERED');
        GroupMember::create(['group_id' => $group->id, 'student_id' => $reviewee->student->id]);
        $template = PeerReviewIndicatorTemplate::create(['name' => 'Teamwork', 'weight' => 100]);
        $indicator = PeriodPeerReviewIndicator::create(['period_id' => $group->period_id, 'template_id' => $template->id]);
        $controller = new PeerReviewController;
        $form = $controller->index($this->requestFor($reviewer, '/'))->getData(true);
        $this->assertSame('period_indicator_id', $form['indicator_key']);
        $this->assertSame('Teamwork', $form['indicators'][0]['name']);
        $request = $this->requestFor($reviewer, '/', 'POST', ['reviews' => [['reviewee_id' => $reviewee->student->id, 'period_indicator_id' => $indicator->id, 'score' => 75]]]);
        $this->assertSame(422, $controller->store($request)->getStatusCode());
        $request->merge(['reviews' => [['reviewee_id' => $reviewee->student->id, 'period_indicator_id' => $indicator->id, 'score' => 3]]]);
        $this->assertSame(201, $controller->store($request)->getStatusCode());
        $review = PeerReview::with('periodIndicator.template')->first();
        $this->assertSame('3.00', $review->raw_score);
        $this->assertSame('75.00', $review->score);
        $this->assertNull($review->indicator_id);
        $this->assertSame('Teamwork', $review->periodIndicator->template->name);
        $this->assertSame(403, $controller->store($request)->getStatusCode());
    }

    private function individualTaFixture(): array
    {
        $this->peerReviewSchema();
        foreach (['2026_05_05_000012_create_capstone_phase_document_requirements_table.php', '2026_05_05_000015_create_capstone_ta_submissions_table.php', '2026_05_05_000028_create_capstone_notifications_table.php', '2026_09_19_000001_create_capstone_ta_registrations_table.php'] as $file) {
            (require __DIR__.'/../database/migrations/'.$file)->up();
        }
        Schema::table('capstone_ta_submissions', fn (Blueprint $t) => $t->unsignedBigInteger('period_id')->nullable());
        Schema::create('capstone_period_assessment_components', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('type');
        });
        foreach (['nil_dosen' => 'nilai_dosen', 'milestone' => 'milestone', 'expo' => 'expo'] as $suffix) {
            Schema::create('capstone_'.$suffix.'_scores', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('group_id');
                $t->unsignedBigInteger('student_id')->nullable();
                $t->unsignedBigInteger('period_component_id');
                $t->unsignedBigInteger('evaluator_id');
                $t->decimal('score', 5, 2);
            });
        }
        $lecturer = $this->actor('dosen');
        $student = $this->actor('mahasiswa');
        $other = $this->actor('mahasiswa');
        $group = $this->group($student, $lecturer, 'EXPO_DONE');
        GroupMember::create(['group_id' => $group->id, 'student_id' => $other->student->id]);
        foreach (['EXPO' => 'Expo report', 'TA' => 'Thesis'] as $phase => $name) {
            PhaseDocumentRequirement::create(['period_id' => $group->period_id, 'phase' => $phase, 'name' => $name, 'is_required' => true]);
        }
        Document::create(['group_id' => $group->id, 'student_id' => $student->student->id, 'phase' => 'EXPO', 'document_type' => 'Expo report', 'file_path' => 'expo.pdf', 'status' => 'APPROVED']);
        foreach (['NILAI_DOSEN' => 'nilai_dosen', 'MILESTONE' => 'milestone', 'EXPO' => 'expo'] as $type => $suffix) {
            $id = DB::table('capstone_period_assessment_components')->insertGetId(['period_id' => $group->period_id, 'type' => $type]);
            DB::table('capstone_'.$suffix.'_scores')->insert(['group_id' => $group->id, 'student_id' => $student->student->id, 'period_component_id' => $id, 'evaluator_id' => $lecturer->lecturer->id, 'score' => 90]);
        }
        $indicator = PeerReviewIndicator::create(['period_id' => $group->period_id, 'name' => 'Contribution', 'weight' => 100]);
        foreach ([[$student, $other], [$other, $student]] as [$from,$to]) {
            PeerReview::create(['group_id' => $group->id, 'reviewer_id' => $from->student->id, 'reviewee_id' => $to->student->id, 'indicator_id' => $indicator->id, 'score' => 100, 'raw_score' => 4, 'is_final_submission' => true]);
        }

        return [$student, $other, $group, $lecturer];
    }

    public function test_ta_readiness_requires_all_reviews_documents_and_supervisor_components(): void
    {
        [$student,$other,$group] = $this->individualTaFixture();
        $workflow = new IndividualTaWorkflow;
        $this->assertTrue($workflow->forStudent($student->student->id)['can_access']);
        // Another student's grades cannot unlock this student's individual phase.
        $this->assertFalse($workflow->forStudent($other->student->id)['can_access']);
        DB::table('capstone_milestone_scores')->update(['score' => 0]);
        $this->assertTrue($workflow->forStudent($student->student->id)['can_access']);
        DB::table('capstone_milestone_scores')->delete();
        $state = $workflow->forStudent($student->student->id);
        $this->assertFalse($state['can_access']);
        $this->assertSame('TA_LOCKED', $state['status']);
        $this->assertFalse($state['readiness']['milestone']['completed']);
    }

    public function test_ta_document_upload_approval_and_private_download_keep_server_locks(): void
    {
        [$student,$other,$group,$lecturer] = $this->individualTaFixture();
        TaRegistration::create(['student_id' => $student->student->id, 'group_id' => $group->id, 'period_id' => $group->period_id, 'status' => 'APPROVED']);
        $workflow = new IndividualTaWorkflow;
        $storage = \Mockery::mock(DocumentStorageService::class);
        $storage->shouldReceive('store')->once()->andReturn('individual/thesis.pdf');
        $storage->shouldNotReceive('get');
        $request = $this->requestFor($student, '/', 'POST', ['document_type' => 'Thesis']);
        $request->files->set('file', UploadedFile::fake()->create('thesis.pdf', 1, 'application/pdf'));
        $controller = new IndividualTaController;
        $this->assertSame(201, $controller->upload($request, $workflow, $storage)->getStatusCode());
        $this->assertSame('TA_DOCUMENTS_UNDER_REVIEW', TaSubmission::first()->status);
        $document = Document::where('phase', $workflow::DOCUMENT_PHASE)->first();
        $this->actingAs($lecturer);
        $documents = new DocumentController(app(GroupStateMachine::class), $storage);
        $review = $this->requestFor($lecturer, '/', 'PUT', ['status' => 'APPROVED']);
        $this->assertSame(200, $documents->update($review, (string) $document->id)->getStatusCode());
        $this->assertSame('TA_DOCUMENTS_APPROVED', TaSubmission::first()->status);
        $legacy = new TaSubmissionController(app(GroupStateMachine::class));
        $legacyRequest = $this->requestFor($student, '/', 'POST', ['file_path' => 'replacement.pdf']);
        $this->assertSame(403, $legacy->upload($legacyRequest)->getStatusCode());
        $this->assertSame(403, $legacy->revise($legacyRequest)->getStatusCode());
        try {
            $controller->upload($request, $workflow, $storage);
            $this->fail('Approved document was replaced');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertSame('individual/thesis.pdf', $document->fresh()->file_path);
        $this->assertSame([$student->id], Notification::pluck('user_id')->all());
        $otherRequest = $this->requestFor($other, '/');
        $otherRequest->attributes->set('capstone_role', 'mahasiswa');
        $this->expectException(HttpException::class);
        $documents->download($otherRequest, (string) $document->id);
    }

    public function test_ta_registration_approval_gates_document_upload(): void
    {
        [$student,$other,$group] = $this->individualTaFixture();
        $workflow = new IndividualTaWorkflow;
        $state = $workflow->forStudent($student->student->id);
        $this->assertTrue($state['can_access']);
        $this->assertSame('TA_AWAITING_APPROVAL', $state['status']);
        $this->assertFalse($state['can_upload']);
        $this->assertFalse($state['sidang_approved']);
        $controller = new TaRegistrationController;
        $request = $this->requestFor($student, '/', 'POST', []);
        $this->assertSame(201, $controller->store($request, $workflow)->getStatusCode());
        $this->assertSame('PENDING', TaRegistration::first()->status);
        // Duplicate request while pending is blocked.
        $this->assertSame(400, $controller->store($request, $workflow)->getStatusCode());
        // Upload stays locked while the request is pending.
        $storage = \Mockery::mock(DocumentStorageService::class);
        $storage->shouldReceive('store')->once()->andReturn('individual/thesis.pdf');
        $uploadRequest = $this->requestFor($student, '/', 'POST', ['document_type' => 'Thesis']);
        $uploadRequest->files->set('file', UploadedFile::fake()->create('thesis.pdf', 1, 'application/pdf'));
        $individual = new IndividualTaController;
        $this->assertWorkspaceDenied(fn () => $individual->upload($uploadRequest, $workflow, $storage));
        // Admin approval unlocks the upload.
        $admin = $this->actor('admin');
        $approval = new TaRegistrationApprovalController;
        $approveRequest = $this->requestFor($admin, '/', 'PUT');
        $this->assertSame(200, $approval->approve($approveRequest, TaRegistration::first()->id)->getStatusCode());
        $this->assertSame('APPROVED', TaRegistration::first()->fresh()->status);
        $unlocked = $workflow->forStudent($student->student->id);
        $this->assertTrue($unlocked['can_upload']);
        $this->assertSame('TA_DOCUMENTS_REQUIRED', $unlocked['status']);
        $this->assertSame(201, $individual->upload($uploadRequest, $workflow, $storage)->getStatusCode());
        // Approved registrations cannot be cancelled.
        $this->assertSame(400, $controller->destroy($this->requestFor($student, '/', 'DELETE'))->getStatusCode());
        // Rejected requests can be re-submitted.
        TaRegistration::query()->update(['status' => 'PENDING']);
        $rejectRequest = $this->requestFor($admin, '/', 'PUT', ['rejection_reason' => 'Incomplete prerequisites']);
        $this->assertSame(200, $approval->reject($rejectRequest, TaRegistration::first()->id)->getStatusCode());
        $rejected = $workflow->forStudent($student->student->id);
        $this->assertSame('TA_AWAITING_APPROVAL', $rejected['status']);
        $this->assertSame('Incomplete prerequisites', $rejected['registration']->rejection_reason);
        $this->assertSame(201, $controller->store($request, $workflow)->getStatusCode());
        $this->assertSame('PENDING', TaRegistration::first()->status);
        $this->assertSame(200, $controller->destroy($this->requestFor($student, '/', 'DELETE'))->getStatusCode());
        $this->assertSame(0, TaRegistration::count());
    }

    public function test_admin_ta_schedule_updates_examiners_atomically_and_locks_submitted_evaluations(): void
    {
        [$student,$other,$group,$supervisor] = $this->individualTaFixture();
        $admin = $this->actor('admin');
        $this->actingAs($admin);
        $examiner1 = $this->actor('dosen');
        $examiner2 = $this->actor('dosen');
        $replacement = $this->actor('dosen');
        Schema::create('capstone_ta_defense_evaluations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('examiner_id');
            $t->string('status');
            $t->timestamps();
        });
        $schedule = TaDefenseSchedule::create(['group_id' => $group->id, 'student_id' => $student->student->id, 'examiner_1_id' => $examiner1->lecturer->id, 'examiner_2_id' => $examiner2->lecturer->id, 'date' => '2027-01-15', 'start_time' => '09:00', 'end_time' => '10:00', 'room' => 'Lab', 'status' => 'SCHEDULED']);
        $schedule->students()->attach($student->student->id);
        $service = \Mockery::mock(SchedulingService::class)->makePartial();
        $service->shouldReceive('validateScheduleConflicts')->once()->with([$replacement->lecturer->id, $examiner2->lecturer->id], '2027-01-15', '09:00', '10:00', 'Lab', null, $schedule->id, null, null, null)->andReturn([]);
        $controller = new TaDefenseScheduleController($service);
        $request = UpdateTaDefenseRequest::create('/', 'PUT', ['examiner_1_id' => $replacement->lecturer->id]);
        $request->setUserResolver(fn () => $admin);
        $request->setValidator(Validator::make($request->all(), $request->rules()));
        $this->assertSame(200, $controller->update($request, $schedule->id)->getStatusCode());
        $this->assertSame($replacement->lecturer->id, $schedule->fresh()->examiner_1_id);
        $this->assertSame(2, TaDefenseEvaluation::count());
        TaDefenseEvaluation::query()->update(['status' => 'SUBMITTED']);
        $request->merge(['examiner_1_id' => $examiner1->lecturer->id]);
        $request->setValidator(Validator::make($request->all(), $request->rules()));
        $this->assertSame(403, $controller->update($request, $schedule->id)->getStatusCode());
        $this->assertSame(2, TaDefenseEvaluation::where('status', 'SUBMITTED')->count());
        $this->assertSame($replacement->lecturer->id, $schedule->fresh()->examiner_1_id);
        $request->merge(['examiner_1_id' => $supervisor->lecturer->id]);
        $request->setValidator(Validator::make($request->all(), $request->rules()));
        $this->assertSame(400, $controller->update($request, $schedule->id)->getStatusCode());
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_admin_creates_ta_schedule_only_for_approved_students_and_generates_pending_evaluations(): void
    {
        [$student,$other,$group] = $this->individualTaFixture();
        $admin = $this->actor('admin');
        $this->actingAs($admin);
        $first = $this->actor('dosen');
        $second = $this->actor('dosen');
        Schema::table('capstone_ta_defense_schedules', function (Blueprint $t) {
            $t->unsignedBigInteger('period_id')->nullable();
            $t->timestamp('evaluation_deadline')->nullable();
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('eoffice_ruangan_id')->nullable();
            $t->unsignedBigInteger('eoffice_peminjaman_id')->nullable();
        });
        Schema::create('capstone_ta_defense_evaluations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('examiner_id');
            $t->string('status');
            $t->timestamps();
        });
        Schema::create('eo_mr_ruangans', function (Blueprint $t) {
            $t->id();
            $t->string('nama');
            $t->string('lokasi')->nullable();
            $t->integer('lantai')->nullable();
            $t->integer('kapasitas')->default(0);
            $t->json('fasilitas')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        $roomId = DB::table('eo_mr_ruangans')->insertGetId(['nama' => 'Lab', 'kapasitas' => 30]);
        $service = \Mockery::mock(SchedulingService::class)->makePartial();
        $service->shouldReceive('validateScheduleConflicts')->once()->andReturn([]);
        $controller = new TaDefenseScheduleController($service);
        $data = ['group_id' => $group->id, 'period_id' => $group->period_id, 'student_ids' => [$student->student->id], 'examiner_1_id' => $first->lecturer->id, 'examiner_2_id' => $second->lecturer->id, 'date' => '2027-02-10', 'start_time' => '09:00', 'end_time' => '10:00', 'room' => 'Lab', 'eoffice_ruangan_id' => $roomId];
        $request = StoreTaDefenseRequest::create('/', 'POST', $data);
        $request->setUserResolver(fn () => $admin);
        $request->setValidator(Validator::make($data, $request->rules()));
        $this->assertSame(400, $controller->store($request)->getStatusCode());
        $this->assertSame(0, TaDefenseSchedule::count());
        TaSubmission::create(['student_id' => $student->student->id, 'group_id' => $group->id, 'period_id' => $group->period_id, 'status' => 'TA_DOCUMENTS_APPROVED']);
        TaRegistration::create(['student_id' => $student->student->id, 'group_id' => $group->id, 'period_id' => $group->period_id, 'status' => 'APPROVED']);
        $this->assertSame(201, $controller->store($request)->getStatusCode());
        $this->assertSame(2, TaDefenseEvaluation::where('status', 'PENDING')->count());
        $this->assertSame(2, TaDefenseExaminer::count());
        $this->assertSame('TA_READY_FOR_SIDANG', TaSubmission::first()->status);
        $this->assertSame(400, $controller->store($request)->getStatusCode());
        $this->assertSame(1, TaDefenseSchedule::count());
        $this->assertSame(0, DB::transactionLevel());
    }

    private function expoSchema(): void
    {
        foreach (['2026_05_05_000020_create_capstone_expo_events_table.php', '2026_05_05_000021_create_capstone_expo_registrations_table.php', '2026_05_05_000029_create_capstone_audit_logs_table.php'] as $file) {
            (require __DIR__.'/../database/migrations/'.$file)->up();
        }
        Schema::table('capstone_groups', fn (Blueprint $t) => $t->string('code')->nullable());
        Schema::create('capstone_ta_submissions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
        });
        Schema::create('capstone_assessment_component_templates', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('code');
            $t->string('description')->nullable();
            $t->decimal('weight', 5, 2);
        });
        Schema::create('capstone_period_assessment_components', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->unsignedBigInteger('template_id');
            $t->string('type');
            $t->integer('sort_order')->default(0);
        });
        Schema::create('capstone_expo_scores', function (Blueprint $t) {
            $t->id();
            $t->foreignId('evaluator_id')->constrained('lecturers');
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->unsignedBigInteger('period_component_id');
            $t->decimal('score', 5, 2);
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        (require __DIR__.'/../database/migrations/2026_09_09_000000_support_expo_self_evaluation.php')->up();
        Schema::create('capstone_expo_student_documents', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('expo_registration_id');
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->string('file_path');
            $t->string('storage_location');
            $t->string('original_name');
            $t->string('status');
            $t->timestamps();
        });
    }

    private function expoFixture(User $student): array
    {
        $this->expoSchema();
        $group = $this->group($student, null, 'PDC2_READY_FOR_EXPO');
        $event = ExpoEvent::create(['period_id' => $group->period_id, 'name' => 'Expo', 'date' => '2026-09-09', 'start_time' => '09:00', 'end_time' => '12:00', 'room' => 'Hall', 'capacity' => 1, 'is_published' => true, 'created_by' => $student->id]);
        DB::table('capstone_ta_submissions')->insert(['group_id' => $group->id]);
        $template = DB::table('capstone_assessment_component_templates')->insertGetId(['name' => 'Prototype', 'code' => 'EXPO-1', 'weight' => 100]);
        $component = DB::table('capstone_period_assessment_components')->insertGetId(['period_id' => $group->period_id, 'template_id' => $template, 'type' => 'EXPO']);

        return [$group, $event, $component];
    }

    public function test_expo_withdrawal_restores_capacity_and_allows_reregistration(): void
    {
        $student = $this->actor('mahasiswa');
        [$group,$event] = $this->expoFixture($student);
        $service = app(ExpoService::class);
        $registration = $service->registerGroupToEvent($event->id, $group->id, $student->id);
        $this->assertSame('EXPO_REGISTERED', $group->fresh()->status);
        $service->withdrawGroupFromEvent($event->id, $group->id, $student->id);
        $this->assertSame('PDC2_READY_FOR_EXPO', $group->fresh()->status);
        $this->assertSame('WITHDRAWN', $registration->fresh()->status);
        $this->assertSame('CANCELLED', DB::table('capstone_seminar_schedules')->value('status'));
        $again = $service->registerGroupToEvent($event->id, $group->id, $student->id);
        $this->assertSame($registration->id, $again->id);
        $this->assertSame(1, DB::table('capstone_seminar_schedules')->count());
    }

    public function test_expo_self_evaluation_is_owned_by_student_and_locks_after_submission(): void
    {
        $student = $this->actor('mahasiswa');
        [$group,$event,$component] = $this->expoFixture($student);
        $registration = app(ExpoService::class)->registerGroupToEvent($event->id, $group->id, $student->id);
        $controller = new ExpoStudentController;
        $request = $this->requestFor($student, '/', 'POST', ['scores' => [['period_component_id' => $component, 'score' => 85, 'notes' => 'Test']]]);
        $this->assertSame(201, $controller->evaluate($request, $event)->getStatusCode());
        $score = ExpoSelfEvaluation::first();
        $this->assertSame($student->student->id, $score->student_id);
        $this->assertSame(0, ExpoScore::count());
        $this->assertSame(403, $controller->evaluate($request, $event)->getStatusCode());
        $detail = $controller->detail($request, $event)->getData(true);
        $this->assertSame('85.00', $detail['my_scores'][0]['score']);
        $this->assertTrue($detail['members'][0]['has_submitted_evaluation']);
        $this->expectException(\InvalidArgumentException::class);
        app(ExpoService::class)->withdrawGroupFromEvent($event->id, $group->id, $student->id);
    }

    public function test_unregistered_student_cannot_read_expo_detail(): void
    {
        $student = $this->actor('mahasiswa');
        [, $event] = $this->expoFixture($student);
        $this->expectException(ModelNotFoundException::class);
        (new ExpoStudentController)->detail($this->requestFor($student, '/'), $event);
    }

    public function test_grades_use_student_profile_id_instead_of_account_id(): void
    {
        $this->actor('admin');
        $student = $this->actor('mahasiswa');
        $group = $this->group($student);
        $this->assertNotSame($student->id, $student->student->id);
        $service = \Mockery::mock(GradeCalculationService::class);
        $service->shouldReceive('calculateFinalGradeForStudent')->once()->with($student->student->id, $group->id)->andReturn(['pdc1' => ['grade' => 85, 'status' => 'COMPLETE', 'components' => []]]);
        $controller = new GradeConfigurationController($service);
        $response = $controller->getMyGrades($this->requestFor($student, '/'))->getData(true);
        $this->assertSame($student->student->id, $response['data']['student']['id']);
        $this->assertSame($student->student->student_number, $response['data']['student']['nim']);
        $this->assertSame(85,$response['data']['grades']['pdc1']['grade']);
    }

    public function test_stale_session_role_falls_back_but_explicit_wrong_role_is_denied(): void
    {
        $student = $this->actor('mahasiswa');
        $request = $this->requestFor($student,'/capstone/dashboard');
        $request->session()->put('capstone.blade_role','admin');
        $response = (new BladeAccessMiddleware)->handle($request,fn () => response('allowed'));
        $this->assertSame('allowed',$response->getContent());
        $this->assertSame('mahasiswa',$request->attributes->get('capstone_role'));
        $this->expectException(HttpException::class);
        (new BladeAccessMiddleware)->handle($request,fn () => response('allowed'),'admin');
    }

    public function test_invitation_response_can_reach_recipient_checks_before_period_registration(): void
    {
        $student = $this->actor('mahasiswa');
        $middleware = new BladeSessionFeatureMiddleware;
        foreach (['accept', 'reject'] as $action) {
            $request = $this->requestFor($student,'/capstone/session/capstone/mahasiswa/group-invitations/1/'.$action,'POST');
            $this->assertSame('recipient controller',$middleware->handle($request,fn () => response('recipient controller'))->getContent());
        }
        $request = $this->requestFor($student,'/capstone/session/capstone/mahasiswa/group','POST');
        $this->assertSame(403,$middleware->handle($request,fn () => response('should not reach'))->getStatusCode());
    }

    public function test_expo_rejects_wrong_components_and_document_replacement(): void
    {
        $student = $this->actor('mahasiswa');
        [$group,$event,$component] = $this->expoFixture($student);
        $registration = app(ExpoService::class)->registerGroupToEvent($event->id,$group->id,$student->id);
        $controller = new ExpoStudentController;
        $request = $this->requestFor($student,'/','POST',['scores' => [['period_component_id' => $component + 1, 'score' => 90]]]);
        $this->assertSame(422,$controller->evaluate($request,$event)->getStatusCode());
        $this->assertSame(0,ExpoSelfEvaluation::count());
        ExpoStudentDocument::create(['expo_registration_id' => $registration->id, 'group_id' => $group->id, 'student_id' => $student->student->id, 'file_path' => 'old.pdf', 'storage_location' => 'supabase', 'original_name' => 'old.pdf', 'status' => 'APPROVED']);
        $request = $this->requestFor($student,'/','POST');
        $request->files->set('file',UploadedFile::fake()->create('new.pdf',1,'application/pdf'));
        $storage = \Mockery::mock(DocumentStorageService::class);
        $storage->shouldNotReceive('store');
        $this->assertSame(403,$controller->document($request,$event,$storage)->getStatusCode());
        $this->assertSame('old.pdf',ExpoStudentDocument::first()->file_path);
    }
}
