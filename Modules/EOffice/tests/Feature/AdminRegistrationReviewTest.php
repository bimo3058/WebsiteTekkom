<?php

namespace Modules\EOffice\Tests\Feature;

use App\Models\EoAuditLog;
use App\Models\Role;
use App\Models\User;
use App\Services\SupabaseStorage;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PendaftaranKoorController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\RegistrationReviewController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\PendaftaranKoorController as DosenController;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Services\KoorPraktikumService;
use Modules\EOffice\Services\NotifikasiService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminRegistrationReviewTest extends TestCase
{
    private const PRAKTIKUM = '0198dc18-8888-7000-8000-000000000001';

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.registration_test' => ['driver' => 'sqlite', 'database' => ':memory:'], 'cache.default' => 'array', 'session.driver' => 'array']);
        DB::setDefaultConnection('registration_test');
        foreach ([
            'users' => ['name', 'email', 'external_id', 'deleted_at'],
            'students' => ['user_id', 'student_number'],
            'roles' => ['name', 'module', 'guard_name'],
            'permissions' => ['name', 'guard_name'],
            'model_has_roles' => ['model_id', 'model_type', 'role_id'],
            'model_has_permissions' => ['model_id', 'model_type', 'permission_id'],
            'role_has_permissions' => ['role_id', 'permission_id'],
            'asprak_praktikum' => ['user_id', 'praktikum_id', 'role', 'deleted_at'],
            'eo_praktikum_dosen' => ['praktikum_id', 'dosen_id'],
            'eo_notifikasi' => ['user_id', 'judul', 'pesan', 'peminjaman_id', 'is_read'],
            'pendaftaran_koordinator' => ['user_id', 'praktikum_id', 'ipk', 'motivasi', 'status', 'status_dosen', 'catatan_dosen', 'alasan_penolakan', 'transkrip_path', 'berkas_cerc_path', 'direview_oleh', 'direview_pada'],
            'pendaftaran_asprak' => ['user_id', 'praktikum_id', 'ipk', 'motivasi', 'jadwal', 'status', 'status_koor', 'catatan_koor', 'alasan_penolakan', 'transkrip_path', 'berkas_cerc_path', 'cv_path', 'direview_oleh', 'direview_pada'],
            'pendaftaran_praktikan' => ['user_id', 'praktikum_id', 'periode_id', 'irs_path', 'status', 'alasan_penolakan', 'catatan_koor', 'direview_oleh', 'direview_pada'],
        ] as $table => $columns) {
            Schema::create($table, function (Blueprint $t) use ($columns) {
                $t->id();
                foreach ($columns as $column) {
                    $t->string($column)->nullable();
                }
                $t->timestamps();
            });
        }
        Schema::create('eo_praktikum', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('nama');
            $t->unsignedBigInteger('koor_id')->nullable();
            $t->string('status')->default('aktif');
            $t->softDeletes();
            $t->timestamps();
        });
        foreach (['audit_logs', 'eo_audit_logs'] as $table) {
            Schema::create($table, function (Blueprint $t) use ($table) {
                $t->id();
                $t->unsignedBigInteger('user_id')->nullable();
                $t->string('action');
                $t->string('subject_type')->nullable();
                $t->unsignedBigInteger('subject_id')->nullable();
                $t->string('description');
                if ($table === 'audit_logs') {
                    $t->string('module');
                } else {
                    $t->json('old_data')->nullable();
                    $t->json('new_data')->nullable();
                }
                $t->timestamp('created_at')->nullable();
            });
        }
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Admin Uji', 'email' => 'admin@example.test'],
            ['id' => 2, 'name' => 'Mahasiswa Uji', 'email' => 'mahasiswa@example.test'],
            ['id' => 3, 'name' => 'Dosen Uji', 'email' => 'dosen@example.test'],
        ]);
        foreach (['admin_eoffice', 'koor_prak', 'asprak', 'mahasiswa', 'dosen', 'superadmin'] as $i => $role) {
            DB::table('roles')->insert(['id' => $i + 1, 'name' => $role, 'module' => 'eoffice', 'guard_name' => 'web']);
        }
        DB::table('model_has_roles')->insert(['model_id' => 2, 'model_type' => User::class, 'role_id' => 4]);
        DB::table('eo_praktikum')->insert(['id' => self::PRAKTIKUM, 'nama' => 'Sistem Digital']);
        DB::table('eo_praktikum_dosen')->insert(['praktikum_id' => self::PRAKTIKUM, 'dosen_id' => 3]);
        DB::table('pendaftaran_koordinator')->insert([
            'id' => 1, 'user_id' => 2, 'praktikum_id' => self::PRAKTIKUM,
            'status' => 'pending', 'status_dosen' => 'menunggu',
            'transkrip_path' => 'koor-transkrip/2/transkrip.pdf', 'created_at' => now(),
        ]);
        view()->share('errors', new ViewErrorBag);
        $this->withoutVite();
    }

    protected function tearDown(): void
    {
        DB::purge('registration_test');
        parent::tearDown();
    }

    private function requestAs(string $role = 'admin_eoffice', array $input = []): Request
    {
        $user = User::findOrFail($role === 'dosen' ? 3 : ($role === 'mahasiswa' ? 2 : 1));
        $user->setRelation('roles', Role::where('name', $role)->get());
        $this->actingAs($user);
        $request = Request::create('/eoffice/manajemen-praktikum/admin/pendaftaran/koor/1', 'POST', $input);
        $request->setUserResolver(fn () => $user);
        $request->setLaravelSession(app('session.store'));
        app()->instance('request', $request);

        return $request;
    }

    private function controller(?KoorPraktikumService $service = null): PendaftaranKoorController
    {
        return new PendaftaranKoorController(new NotifikasiService, $service ?? new KoorPraktikumService);
    }

    private function invalid(callable $callback, string $field): void
    {
        try {
            $callback();
            $this->fail('Expected validation failure.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($field, $e->errors());
        }
    }

    private function httpError(callable $callback, int $status): void
    {
        try {
            $callback();
            $this->fail('Expected HTTP error.');
        } catch (HttpException $e) {
            $this->assertSame($status, $e->getStatusCode());
        }
    }

    public function test_normal_approval_requires_lecturer_approval_and_explicit_override_requires_reason(): void
    {
        $this->invalid(fn () => $this->controller()->approve($this->requestAs(), 1), 'approval');
        foreach ([null, 'singkat', str_repeat(' ', 12)] as $reason) {
            $this->invalid(fn () => $this->controller()->approve($this->requestAs(input: ['override_dosen' => 1, 'override_reason' => $reason]), 1), 'override_reason');
        }
        $this->assertSame('pending', PendaftaranKoordinator::find(1)->status);
        $this->assertSame(0, DB::table('asprak_praktikum')->count());
    }

    public function test_admin_override_assigns_both_roles_and_preserves_original_lecturer_review(): void
    {
        DB::table('pendaftaran_koordinator')->where('id', 1)->update([
            'status' => 'rejected', 'status_dosen' => 'ditolak', 'catatan_dosen' => 'Catatan asli dosen',
            'direview_oleh' => 3, 'direview_pada' => '2026-09-10 09:00:00', 'alasan_penolakan' => 'Kandidat lain dipilih',
        ]);
        $reason = str_repeat('Dokumen telah diverifikasi ulang oleh admin. ', 10);
        $response = $this->controller()->approve($this->requestAs(input: ['override_dosen' => 1, 'override_reason' => $reason]), 1);
        $this->assertSame(302, $response->getStatusCode());
        $row = PendaftaranKoordinator::find(1);
        $this->assertSame('approved', $row->status);
        $this->assertSame('ditolak', $row->status_dosen);
        $this->assertSame('Catatan asli dosen', $row->catatan_dosen);
        $this->assertSame(3, (int) $row->direview_oleh);
        $this->assertSame('2026-09-10 09:00:00', $row->direview_pada->format('Y-m-d H:i:s'));
        $this->assertSame(2, (int) DB::table('eo_praktikum')->value('koor_id'));
        $this->assertEqualsCanonicalizing(['koor', 'asprak'], DB::table('asprak_praktikum')->pluck('role')->all());
        $this->assertEqualsCanonicalizing(['mahasiswa', 'koor_prak', 'asprak'], User::find(2)->roles->pluck('name')->all());
        $audit = EoAuditLog::where('action', 'OVERRIDE_APPROVAL')->firstOrFail();
        $this->assertSame(trim($reason), $audit->new_data['override_reason']);
        $this->assertSame('rejected', $audit->old_data['status']);
        $this->assertSame('approved', $audit->new_data['status']);
        $this->assertLessThanOrEqual(255, strlen($audit->description));
        $this->assertSame(1, (int) $audit->user_id);
        $this->assertSame(1, DB::table('audit_logs')->where('action', 'OVERRIDE_APPROVAL')->count());
        $this->assertSame(1, DB::table('eo_notifikasi')->count());
        $this->invalid(fn () => $this->controller()->approve($this->requestAs(input: ['override_dosen' => 1, 'override_reason' => $reason]), 1), 'approval');
        $this->assertSame(2, DB::table('asprak_praktikum')->count());
    }

    public function test_superadmin_can_override_pending_lecturer_review(): void
    {
        $this->controller()->approve($this->requestAs('superadmin', ['override_dosen' => true, 'override_reason' => 'Kebutuhan praktikum telah diverifikasi.']), 1);
        $this->assertSame('approved', PendaftaranKoordinator::find(1)->status);
        $this->assertSame('menunggu', PendaftaranKoordinator::find(1)->status_dosen);
    }

    public function test_normal_final_approval_still_works_after_lecturer_accepts(): void
    {
        DB::table('pendaftaran_koordinator')->where('id', 1)->update(['status_dosen' => 'disetujui']);
        $this->controller()->approve($this->requestAs(), 1);
        $this->assertSame('approved', PendaftaranKoordinator::find(1)->status);
        $this->assertSame(1, DB::table('audit_logs')->where('action', 'APPROVE')->count());
    }

    public function test_override_does_not_replace_an_existing_coordinator(): void
    {
        DB::table('eo_praktikum')->update(['koor_id' => 3]);
        $this->invalid(fn () => $this->controller()->approve($this->requestAs(input: ['override_dosen' => 1, 'override_reason' => 'Data telah diperiksa kembali.']), 1), 'approval');
        $this->assertSame('pending', PendaftaranKoordinator::find(1)->status);
        $this->assertSame(3, (int) DB::table('eo_praktikum')->value('koor_id'));
        $this->assertSame(0, DB::table('audit_logs')->count());
    }

    public function test_assignment_failure_rolls_back_approval_and_assignment(): void
    {
        $service = \Mockery::mock(KoorPraktikumService::class);
        $service->shouldReceive('assign')->once()->andReturnUsing(function ($praktikum) {
            $praktikum->update(['koor_id' => 2]);
            throw new \RuntimeException('Simulated assignment failure');
        });
        try {
            $this->controller($service)->approve($this->requestAs(input: ['override_dosen' => 1, 'override_reason' => 'Data sudah diverifikasi.']), 1);
            $this->fail('Expected assignment failure');
        } catch (\RuntimeException $e) {
            $this->assertSame('Simulated assignment failure', $e->getMessage());
        }
        $this->assertSame('pending', PendaftaranKoordinator::find(1)->status);
        $this->assertNull(DB::table('eo_praktikum')->value('koor_id'));
        $this->assertSame(0, DB::table('eo_audit_logs')->count());
    }

    public function test_non_admin_cannot_override_or_access_registration_documents(): void
    {
        foreach (['dosen', 'mahasiswa'] as $role) {
            $request = $this->requestAs($role, ['override_dosen' => 1, 'override_reason' => 'Mencoba mengubah persetujuan.']);
            $this->httpError(fn () => $this->controller()->approve($request, 1), 403);
            $controller = new RegistrationReviewController;
            $this->httpError(fn () => $controller->index($request), 403);
            $this->httpError(fn () => $controller->show($request, 'koor', 1), 403);
            $this->httpError(fn () => $controller->document($request, 'koor', 1, 'transkrip_path', \Mockery::mock(SupabaseStorage::class)), 403);
        }
    }

    public function test_documents_are_served_for_each_registration_type_through_fixed_fields(): void
    {
        DB::table('pendaftaran_asprak')->insert(['id' => 1, 'transkrip_path' => 'asprak/2/transkrip.pdf', 'berkas_cerc_path' => 'asprak/2/cerc.pdf', 'cv_path' => 'asprak/2/cv.pdf']);
        DB::table('pendaftaran_praktikan')->insert(['id' => 1, 'irs_path' => 'praktikan-irs/2/irs.pdf']);
        DB::table('pendaftaran_koordinator')->where('id', 1)->update(['berkas_cerc_path' => 'koor/2/cerc.pdf']);
        $cases = [
            ['koor', 'transkrip_path', 'koor-transkrip/2/transkrip.pdf'],
            ['koor', 'berkas_cerc_path', 'koor/2/cerc.pdf'],
            ['asprak', 'transkrip_path', 'asprak/2/transkrip.pdf'],
            ['asprak', 'berkas_cerc_path', 'asprak/2/cerc.pdf'],
            ['asprak', 'cv_path', 'asprak/2/cv.pdf'],
            ['praktikan', 'irs_path', 'praktikan-irs/2/irs.pdf'],
        ];
        foreach ($cases as [$type, $field, $path]) {
            $storage = \Mockery::mock(SupabaseStorage::class);
            $storage->shouldReceive('download')->once()->with($path, 'eoffice')->andReturn(['content' => "%PDF-1.4\n%%EOF", 'mime_type' => 'application/pdf']);
            $response = (new RegistrationReviewController)->document($this->requestAs(), $type, 1, $field, $storage);
            $this->assertSame(200, $response->getStatusCode());
            $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
            $this->assertStringStartsWith('inline;', $response->headers->get('Content-Disposition'));
            $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
            $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        }
    }

    public function test_missing_and_unsafe_document_paths_are_rejected_without_storage_access(): void
    {
        $storage = \Mockery::mock(SupabaseStorage::class);
        $storage->shouldNotReceive('download');
        $controller = new RegistrationReviewController;
        $request = $this->requestAs();
        $this->httpError(fn () => $controller->document($request, 'unknown', 1, 'transkrip_path', $storage), 404);
        $this->httpError(fn () => $controller->document($request, 'koor', 1, 'irs_path', $storage), 404);
        foreach ([null, '', '../secret', 'folder/../secret', '/etc/passwd', 'C:\\secret.pdf', 'https://example.test/file.pdf'] as $path) {
            DB::table('pendaftaran_koordinator')->where('id', 1)->update(['transkrip_path' => $path]);
            $this->httpError(fn () => $controller->document($request, 'koor', 1, 'transkrip_path', $storage), 404);
        }
    }

    public function test_non_previewable_content_downloads_instead_of_executing(): void
    {
        $storage = \Mockery::mock(SupabaseStorage::class);
        $storage->shouldReceive('download')->once()->andReturn(['content' => '<html><script>alert(1)</script></html>', 'mime_type' => 'text/html']);
        $response = (new RegistrationReviewController)->document($this->requestAs(), 'koor', 1, 'transkrip_path', $storage);
        $this->assertSame('application/octet-stream', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('attachment;', $response->headers->get('Content-Disposition'));
    }

    public function test_lecturer_cannot_change_or_delete_an_admin_approved_registration(): void
    {
        DB::table('pendaftaran_koordinator')->where('id', 1)->update(['status' => 'approved']);
        $request = $this->requestAs('dosen');
        $controller = new DosenController(new NotifikasiService);
        $controller->approve($request, 1);
        $controller->reject($request, 1);
        $controller->destroy(1);
        $this->assertSame('approved', PendaftaranKoordinator::findOrFail(1)->status);
        $this->assertSame('menunggu', PendaftaranKoordinator::find(1)->status_dosen);
        $this->assertSame(0, DB::table('eo_notifikasi')->count());
    }

    public function test_lecturer_bulk_rejection_skips_registration_approved_by_admin(): void
    {
        DB::table('pendaftaran_koordinator')->insert([
            'id' => 2, 'user_id' => 1, 'praktikum_id' => self::PRAKTIKUM,
            'status' => 'approved', 'status_dosen' => 'menunggu',
        ]);
        (new DosenController(new NotifikasiService))->approve($this->requestAs('dosen'), 1);
        $this->assertSame('approved', PendaftaranKoordinator::find(2)->status);
        $this->assertSame('menunggu', PendaftaranKoordinator::find(2)->status_dosen);
        $this->assertSame('disetujui', PendaftaranKoordinator::find(1)->status_dosen);
    }

    public function test_detail_renders_documents_override_form_and_keeps_audit_reason_escaped(): void
    {
        $controller = new RegistrationReviewController;
        $request = $this->requestAs();
        $html = $controller->show($request, 'koor', 1)->render();
        $this->assertStringContainsString('Override &amp; Setujui', $html);
        $this->assertStringContainsString('name="override_reason"', $html);
        $this->assertStringContainsString('/dokumen/transkrip_path', $html);
        $this->assertStringContainsString('Tidak diunggah', $html);
        $this->controller()->approve($this->requestAs(input: ['override_dosen' => 1, 'override_reason' => '<script>alert("audit")</script>']), 1);
        $html = $controller->show($this->requestAs(), 'koor', 1)->render();
        $this->assertStringContainsString('Persetujuan melalui override admin', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert("audit")</script>', $html);
        $this->assertStringNotContainsString('name="override_reason"', $html);
    }

    public function test_praktikan_list_filters_registrations_and_renders_irs_links(): void
    {
        foreach (['pending', 'approved', 'rejected'] as $i => $status) {
            DB::table('pendaftaran_praktikan')->insert(['id' => $i + 1, 'user_id' => 2, 'praktikum_id' => self::PRAKTIKUM, 'status' => $status, 'irs_path' => 'irs/file.pdf']);
        }
        $controller = new RegistrationReviewController;
        $view = $controller->index($this->requestAs(input: ['status' => 'pending', 'search' => 'Mahasiswa']));
        $this->assertSame(1, $view->getData()['pendaftaran']->total());
        $this->assertStringContainsString('Lihat IRS', $view->render());
        $html = $controller->show($this->requestAs(), 'praktikan', 1)->render();
        $this->assertStringContainsString('Cetak IRS', $html);
        $this->assertStringNotContainsString('Override &amp; Setujui', $html);
    }

    public function test_document_and_detail_routes_require_admin_role(): void
    {
        foreach (['pendaftaran.show', 'pendaftaran.document', 'pendaftaran-praktikan.index'] as $name) {
            $route = app('router')->getRoutes()->getByName('eoffice.manprak.admin.'.$name);
            $this->assertNotNull($route);
            $this->assertContains('auth', $route->gatherMiddleware());
            $this->assertContains('role:superadmin|admin_eoffice', $route->gatherMiddleware());
        }
    }

    public function test_audit_failure_rolls_back_status_roles_and_notification(): void
    {
        Schema::drop('audit_logs');
        try {
            $this->controller()->approve($this->requestAs(input: ['override_dosen' => 1, 'override_reason' => 'Data telah diverifikasi ulang.']), 1);
            $this->fail('Expected audit failure.');
        } catch (QueryException $e) {
            $this->assertStringContainsString('audit_logs', $e->getMessage());
        }
        $this->assertSame('pending', PendaftaranKoordinator::find(1)->status);
        $this->assertNull(DB::table('eo_praktikum')->value('koor_id'));
        $this->assertSame(0, DB::table('asprak_praktikum')->count());
        $this->assertSame(['mahasiswa'], User::find(2)->roles->pluck('name')->all());
        $this->assertSame(0, DB::table('eo_notifikasi')->count());
    }

    public function test_asprak_detail_shows_cv_and_coordinator_review_without_lecturer_override(): void
    {
        DB::table('pendaftaran_asprak')->insert([
            'id' => 1, 'user_id' => 2, 'praktikum_id' => self::PRAKTIKUM, 'status' => 'pending',
            'status_koor' => 'disetujui', 'cv_path' => 'asprak/2/cv.pdf', 'jadwal' => json_encode(['Senin 08:00']),
        ]);
        $html = (new RegistrationReviewController)->show($this->requestAs(), 'asprak', 1)->render();
        $this->assertStringContainsString('/dokumen/cv_path', $html);
        $this->assertStringContainsString('Senin 08:00', $html);
        $this->assertStringContainsString('Setujui pendaftaran', $html);
        $this->assertStringNotContainsString('name="override_dosen"', $html);
    }

    public function test_unavailable_storage_document_returns_not_found(): void
    {
        $storage = \Mockery::mock(SupabaseStorage::class);
        $storage->shouldReceive('download')->once()->andReturnNull();
        $this->httpError(fn () => (new RegistrationReviewController)->document($this->requestAs(), 'koor', 1, 'transkrip_path', $storage), 404);
    }
}
