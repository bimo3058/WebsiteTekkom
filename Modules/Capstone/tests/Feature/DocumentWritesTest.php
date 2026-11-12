<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Http\Controllers\DocumentController;
use Modules\Capstone\Http\Requests\Document\ReviewDocumentRequest;
use Modules\Capstone\Http\Requests\Document\StoreDocumentRequest;
use Modules\Capstone\Services\DocumentStorageService;
use Modules\Capstone\Services\GroupLifecycleService;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\IndividualTaWorkflow;
use Modules\Capstone\Services\NotificationService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class FakeDocumentStorage extends DocumentStorageService
{
    /** @var array<int, array{0:string,1:string}> */
    public array $stored = [];

    /** @var array<int, string> */
    public array $deleted = [];

    public function __construct()
    {
        $this->notifications = app(NotificationService::class);
        $this->lifecycle = app(GroupLifecycleService::class);
        $this->individualTa = app(IndividualTaWorkflow::class);
        $this->stateMachine = app(GroupStateMachine::class);
    }

    public function store(UploadedFile $file, string $folder, string $prefix = ''): string
    {
        $this->stored[] = [$folder, $prefix];

        return 'capstone/test/'.trim($prefix, '/').'/doc.pdf';
    }

    public function delete(string $path): void
    {
        $this->deleted[] = $path;
    }
}

/**
 * Characterization: document upload + dosen review, pinned before
 * extraction to DocumentStorageService + Form Requests. The forged-upload
 * test is the security property: UI gating alone must never authorize a
 * POST — the workflow gate replays on every write.
 * Isolated real SQL: never migrate the application's configured database.
 */
class DocumentWritesTest extends TestCase
{
    private FakeDocumentStorage $storage;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.document_writes_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('document_writes_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('name')->nullable();
            $t->string('student_number')->nullable();
            $t->timestamps();
        });
        Schema::create('lecturers', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('name')->nullable();
            $t->timestamps();
        });
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('guard_name')->default('web');
            $t->timestamps();
        });
        Schema::create('model_has_roles', function (Blueprint $t) {
            $t->unsignedBigInteger('role_id');
            $t->string('model_type');
            $t->unsignedBigInteger('model_id');
            $t->index(['model_id', 'model_type']);
        });
        Schema::create('capstone_periods', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->boolean('is_active')->default(true);
            $t->boolean('is_finalized')->default(false);
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_groups', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->unsignedBigInteger('title_id')->nullable();
            $t->string('code')->nullable();
            $t->string('status')->default('FORMING');
            $t->unsignedBigInteger('supervisor_1_id')->nullable();
            $t->unsignedBigInteger('supervisor_2_id')->nullable();
            $t->string('group_mode')->default('GROUP');
            $t->boolean('has_existing_group')->default(false);
            $t->boolean('is_solo')->default(false);
            $t->timestamps();
        });
        Schema::create('capstone_group_members', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->boolean('is_leader')->default(false);
            $t->unsignedBigInteger('period_id')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_phase_document_requirements', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('phase');
            $t->string('name');
            $t->text('description')->nullable();
            $t->boolean('is_required')->default(false);
            $t->timestamps();
        });
        Schema::create('capstone_documents', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id')->nullable();
            $t->string('phase');
            $t->string('document_type')->default('GENERAL');
            $t->string('file_path')->nullable();
            $t->string('original_name')->nullable();
            $t->integer('version')->default(1);
            $t->string('status')->default('SUBMITTED');
            $t->text('feedback')->nullable();
            $t->unsignedBigInteger('reviewed_by')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_document_versions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('document_id');
            $t->integer('version');
            $t->string('file_path')->nullable();
            $t->string('original_name')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_seminar_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id')->nullable();
            $t->string('type')->default('SEMPRO');
            $t->date('date')->nullable();
            $t->string('start_time')->nullable();
            $t->string('end_time')->nullable();
            $t->string('room')->nullable();
            $t->unsignedBigInteger('examiner_1_id')->nullable();
            $t->unsignedBigInteger('examiner_2_id')->nullable();
            $t->string('status')->default('SCHEDULED');
            $t->timestamps();
        });
        Schema::create('capstone_supervisions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('supervisor_id');
            $t->string('role')->nullable();
            $t->unsignedBigInteger('assigned_by')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_expo_student_documents', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('expo_registration_id')->nullable();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id')->nullable();
            $t->string('file_path')->nullable();
            $t->string('original_name')->nullable();
            $t->string('status')->default('SUBMITTED');
            $t->text('feedback')->nullable();
            $t->unsignedBigInteger('reviewed_by')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_notifications', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('type');
            $t->string('title');
            $t->text('message')->nullable();
            $t->string('related_type')->nullable();
            $t->unsignedBigInteger('related_id')->nullable();
            $t->boolean('is_read')->default(false);
            $t->timestamps();
        });

        $this->storage = new FakeDocumentStorage;
        app()->instance(DocumentStorageService::class, $this->storage);
    }

    protected function tearDown(): void
    {
        DB::purge('document_writes_test');
        parent::tearDown();
    }

    private function makeUser(string $name = 'User', ?string $email = null): User
    {
        return User::find(DB::table('users')->insertGetId(['name' => $name, 'email' => $email]));
    }

    private function withRole(User $user, string $role): User
    {
        $roleId = DB::table('roles')->insertGetId(['name' => $role, 'guard_name' => 'web']);
        DB::table('model_has_roles')->insert(['role_id' => $roleId, 'model_type' => User::class, 'model_id' => $user->id]);

        return $user;
    }

    /** @return array{user:User, student:int, group:int, period:int} */
    private function seedMember(string $status = 'PDC1_ACTIVE'): array
    {
        $user = $this->withRole($this->makeUser('Mhs', 'mhs@test.local'), 'mahasiswa');
        $studentId = DB::table('students')->insertGetId(['user_id' => $user->id, 'name' => 'Mhs']);
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1']);
        $groupId = DB::table('capstone_groups')->insertGetId(['period_id' => $periodId, 'status' => $status]);
        DB::table('capstone_group_members')->insert(['group_id' => $groupId, 'student_id' => $studentId, 'is_leader' => true, 'period_id' => $periodId]);

        return ['user' => $user, 'student' => $studentId, 'group' => $groupId, 'period' => $periodId];
    }

    /** @return array{user:User, lecturer:int} */
    private function seedSupervisor(int $groupId): array
    {
        $user = $this->withRole($this->makeUser('Dosen', 'dosen@test.local'), 'dosen');
        $lecturerId = DB::table('lecturers')->insertGetId(['user_id' => $user->id, 'name' => 'Dosen']);
        DB::table('capstone_supervisions')->insert(['group_id' => $groupId, 'supervisor_id' => $lecturerId, 'role' => 'SUPERVISOR_1']);

        return ['user' => $user, 'lecturer' => $lecturerId];
    }

    private function controller(): DocumentController
    {
        return app(DocumentController::class);
    }

    private function uploadRequest(User $user, string $phase, ?string $documentType = null): StoreDocumentRequest
    {
        $params = ['phase' => $phase];
        if ($documentType !== null) {
            $params['document_type'] = $documentType;
        }
        $files = ['file' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')];
        $request = StoreDocumentRequest::create('/x', 'POST', $params, [], $files);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $request;
    }

    private function reviewRequest(User $user, string $status, ?string $feedback = null): ReviewDocumentRequest
    {
        $request = ReviewDocumentRequest::create('/x', 'POST', ['status' => $status, 'feedback' => $feedback]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $request;
    }

    public function test_store_blocks_forged_upload_to_locked_phase(): void
    {
        $seed = $this->seedMember('PDC1_ACTIVE');
        $this->actingAs($seed['user']);

        // UI disables the PDC2 button for this group, but the POST must
        // still be rejected: the phase gate replays on every write.
        $response = $this->controller()->store($this->uploadRequest($seed['user'], 'PDC2'));

        $this->assertSame(403, $response->getStatusCode());
        $this->assertStringContainsString('PDC1_ACTIVE', $response->getData(true)['message']);
        $this->assertSame(0, DB::table('capstone_documents')->count());
        $this->assertSame([], $this->storage->stored);
    }

    public function test_store_uploads_first_document(): void
    {
        $seed = $this->seedMember('PDC1_ACTIVE');
        $this->actingAs($seed['user']);

        $response = $this->controller()->store($this->uploadRequest($seed['user'], 'PDC1'));

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('Document uploaded successfully', $response->getData(true)['message']);
        $this->assertDatabaseHas('capstone_documents', [
            'group_id' => $seed['group'], 'phase' => 'PDC1', 'status' => 'SUBMITTED',
        ]);
        $this->assertCount(1, $this->storage->stored);
    }

    public function test_store_replaces_unapproved_document(): void
    {
        $seed = $this->seedMember('PDC1_ACTIVE');
        $this->actingAs($seed['user']);
        DB::table('capstone_documents')->insert([
            'group_id' => $seed['group'], 'student_id' => $seed['student'], 'phase' => 'PDC1',
            'document_type' => 'GENERAL', 'file_path' => 'capstone/old/x.pdf', 'version' => 1, 'status' => 'SUBMITTED',
        ]);

        $response = $this->controller()->store($this->uploadRequest($seed['user'], 'PDC1'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Document revised (replaced) successfully', $response->getData(true)['message']);
        $this->assertSame(1, DB::table('capstone_documents')->count());
        $this->assertSame(2, (int) DB::table('capstone_documents')->value('version'));
        $this->assertSame(1, DB::table('capstone_document_versions')->count());
        $this->assertSame([], $this->storage->deleted);
    }

    public function test_store_rejects_replacing_approved_document(): void
    {
        $seed = $this->seedMember('PDC1_ACTIVE');
        $this->actingAs($seed['user']);
        // Two required types keep the phase open (Ringkasan missing) while
        // Laporan is APPROVED, so the request reaches the approved-replace
        // refusal (precedence pin: the workflow gate runs first).
        DB::table('capstone_phase_document_requirements')->insert([
            ['period_id' => $seed['period'], 'phase' => 'PDC1', 'name' => 'Laporan', 'is_required' => true],
            ['period_id' => $seed['period'], 'phase' => 'PDC1', 'name' => 'Ringkasan', 'is_required' => true],
        ]);
        DB::table('capstone_documents')->insert([
            'group_id' => $seed['group'], 'student_id' => $seed['student'], 'phase' => 'PDC1',
            'document_type' => 'Laporan', 'file_path' => 'capstone/old/x.pdf', 'status' => 'APPROVED',
        ]);

        $response = $this->controller()->store($this->uploadRequest($seed['user'], 'PDC1', 'Laporan'));

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('Approved documents cannot be replaced.', $response->getData(true)['message']);
        $this->assertSame([], $this->storage->stored);
    }

    public function test_store_requires_configured_document_type(): void
    {
        $seed = $this->seedMember('PDC1_ACTIVE');
        $this->actingAs($seed['user']);
        DB::table('capstone_phase_document_requirements')->insert([
            'period_id' => $seed['period'], 'phase' => 'PDC1', 'name' => 'Laporan', 'is_required' => true,
        ]);

        $this->expectException(ValidationException::class);
        $this->controller()->store($this->uploadRequest($seed['user'], 'PDC1'));
    }

    public function test_update_approves_and_notifies_member(): void
    {
        $seed = $this->seedMember('PDC1_ACTIVE');
        $dosen = $this->seedSupervisor($seed['group']);
        $this->actingAs($dosen['user']);
        $docId = DB::table('capstone_documents')->insertGetId([
            'group_id' => $seed['group'], 'student_id' => $seed['student'], 'phase' => 'PDC1',
            'document_type' => 'GENERAL', 'file_path' => 'capstone/test/doc.pdf', 'status' => 'SUBMITTED',
        ]);

        $response = $this->controller()->update($this->reviewRequest($dosen['user'], 'APPROVED', 'Bagus'), (string) $docId);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Document review updated', $response->getData(true)['message']);
        $this->assertDatabaseHas('capstone_documents', ['id' => $docId, 'status' => 'APPROVED', 'feedback' => 'Bagus']);
        $this->assertDatabaseHas('capstone_notifications', ['type' => 'PROPOSAL_APPROVED', 'related_id' => $docId]);
    }

    public function test_update_rejects_non_supervisor(): void
    {
        $seed = $this->seedMember('PDC1_ACTIVE');
        $this->seedSupervisor($seed['group']);
        $outsider = $this->withRole($this->makeUser('Dosen 2', 'dosen2@test.local'), 'dosen');
        DB::table('lecturers')->insertGetId(['user_id' => $outsider->id, 'name' => 'Dosen 2']);
        $this->actingAs($outsider);
        $docId = DB::table('capstone_documents')->insertGetId([
            'group_id' => $seed['group'], 'student_id' => $seed['student'], 'phase' => 'PDC1',
            'document_type' => 'GENERAL', 'file_path' => 'capstone/test/doc.pdf', 'status' => 'SUBMITTED',
        ]);

        $this->expectException(HttpException::class);
        $this->controller()->update($this->reviewRequest($outsider, 'APPROVED'), (string) $docId);
    }

    public function test_update_validates_status(): void
    {
        $seed = $this->seedMember('PDC1_ACTIVE');
        $dosen = $this->seedSupervisor($seed['group']);
        $this->actingAs($dosen['user']);
        $docId = DB::table('capstone_documents')->insertGetId([
            'group_id' => $seed['group'], 'student_id' => $seed['student'], 'phase' => 'PDC1',
            'document_type' => 'GENERAL', 'file_path' => 'capstone/test/doc.pdf', 'status' => 'SUBMITTED',
        ]);

        $this->expectException(ValidationException::class);
        $this->controller()->update($this->reviewRequest($dosen['user'], 'BOGUS'), (string) $docId);
    }

    public function test_update_rejects_expo_document_for_member(): void
    {
        $seed = $this->seedMember('PDC1_ACTIVE');
        $dosen = $this->seedSupervisor($seed['group']);
        $this->actingAs($dosen['user']);
        $expoId = DB::table('capstone_expo_student_documents')->insertGetId([
            'group_id' => $seed['group'], 'student_id' => $seed['student'],
            'file_path' => 'capstone/test/expo.pdf', 'original_name' => 'expo.pdf', 'status' => 'SUBMITTED',
        ]);

        $response = $this->controller()->update($this->reviewRequest($dosen['user'], 'REJECTED', 'Ulangi'), 'expo-'.$expoId);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Expo document review updated', $response->getData(true)['message']);
        $this->assertDatabaseHas('capstone_expo_student_documents', ['id' => $expoId, 'status' => 'REJECTED']);
    }
}
