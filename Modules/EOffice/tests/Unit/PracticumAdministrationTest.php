<?php

namespace Modules\EOffice\Tests\Unit;

use App\Http\Middleware\CheckModuleActive;
use App\Http\Middleware\RedirectBasedOnRole;
use App\Models\EoAuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionAssigner;
use App\Services\SupabaseStorage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Http\Controllers\EOfficeController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\CercCoordinatorController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\KelolRoleController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PraktikumController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\AnnouncementPinController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\PengumumanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\TugasController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\NotificationController;
use Modules\EOffice\Http\Middleware\PracticumAdministrationBoundary;
use Modules\EOffice\Http\Middleware\StudentPracticumArchive;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\CercCoordinatorHistory;
use Modules\EOffice\Models\Notifikasi;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\ManajemenPraktikum\ActiveStudent;
use Modules\EOffice\Services\ManajemenPraktikum\CercActivity;
use Modules\EOffice\Services\ManajemenPraktikum\CercCoordinator;
use Modules\EOffice\Services\ManajemenPraktikum\NotificationDestination;
use Modules\EOffice\Services\ManajemenPraktikum\PracticumLifecycle;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Support\RunsLegacyPracticumMigrations;
use Tests\TestCase;

class PracticumAdministrationTest extends TestCase
{
    use RunsLegacyPracticumMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'administration_test', 'database.connections.administration_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true], 'cache.default' => 'array', 'session.driver' => 'array']);
        DB::purge('administration_test');
        $paths = array_values(array_filter(glob(database_path('migrations/*.php')), fn ($p) => preg_match('/(?:users|roles|permissions|spatie_permission|students|lecturers|system_modules)_table/', basename($p)) && ! str_contains(basename($p), 'optimize_users')));
        sort($paths);
        foreach ($paths as $path) {
            (require $path)->up();
        }
        foreach (['audit_logs', 'eo_audit_logs'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->integer('user_id')->nullable();
                $t->string('module')->nullable();
                $t->string('action');
                $t->string('subject_type')->nullable();
                $t->string('subject_id')->nullable();
                $t->text('description')->nullable();
                $t->json('old_data')->nullable();
                $t->json('new_data')->nullable();
                $t->timestamp('created_at')->nullable();
            });
        }
        (require base_path('Modules/EOffice/database/migrations/2026_11_05_000005_add_cerc_history_soft_deletes.php'))->up();
        Schema::create('eo_prak_praktikum', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('nama');
            $t->boolean('is_active')->default(true);
            $t->string('status')->default('aktif');
            $t->integer('koor_id')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('eo_prak_asprak_praktikum', function (Blueprint $t) {
            $t->id();
            $t->string('praktikum_id');
            $t->integer('user_id');
            $t->string('role');
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::table('eo_prak_praktikum', function (Blueprint $t) {
            $t->string('semester')->default('Ganjil');
            $t->string('tahun_ajaran')->default('2026/2027');
            $t->integer('dosen_id')->nullable();
        });
        Schema::create('eo_prak_praktikum_dosen', function (Blueprint $t) {
            $t->string('praktikum_id');
            $t->integer('dosen_id');
        });
        Schema::create('eo_notifikasi', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            $t->boolean('is_read')->default(false);
        });
        $this->runLegacyPracticumMigration(base_path('Modules/EOffice/database/migrations/2026_11_01_000002_add_practicum_administration_roles.php'));
        foreach (['superadmin', 'mahasiswa', 'koor_prak', 'asprak', 'admin_eoffice'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web'], ['module' => 'eoffice']);
        }
        DB::table('eo_prak_praktikum')->insert([['id' => 'active', 'nama' => 'Aktif', 'is_active' => true], ['id' => 'archived', 'nama' => 'Arsip', 'is_active' => false]]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function account(string $role, bool $student = false): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        $user->clearUserCache();
        if ($student) {
            DB::table('students')->insert(['user_id' => $user->id, 'student_number' => '21120124'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT), 'cohort_year' => 2024]);
        }

        return $user;
    }

    public function test_delete_button_templates_compile_and_use_shared_visual_style(): void
    {
        $root = base_path('Modules/EOffice/resources/views');
        $count = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root)) as $file) {
            $path = $file->getPathname();
            if (! $file->isFile() || ! str_ends_with($path, '.blade.php') || str_contains($path, 'backup')) {
                continue;
            }
            $source = file_get_contents($path);
            if (! str_contains($source, 'mp-delete-button')) {
                continue;
            }
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source), TOKEN_PARSE), $path);
            $count++;
        }
        $this->assertGreaterThan(15, $count);
        $styles = file_get_contents($root.'/manajemen-praktikum/partials/_styles.blade.php');
        $this->assertStringContainsString('border:1px solid #FADAE1!important', $styles);
        $this->assertStringContainsString('background:#FFF5F5!important', $styles);
        $this->assertMatchesRegularExpression('/(?:width|inline-size):32px!important/', $styles);
        $this->assertStringContainsString('.mp-delete-button:disabled', $styles);
    }

    public function test_practicum_cards_show_all_lecturers_in_every_role(): void
    {
        foreach (['koordinator', 'asprak', 'mahasiswa', 'dosen'] as $role) {
            $source = file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$role.'/praktikum.blade.php'));
            $this->assertStringContainsString('ui.card-lecturers :lecturers="$p->dosens"', $source);
            $this->assertStringContainsString('gap:24px;align-items:stretch;', $source);
            $this->assertStringNotContainsString('$p->dosens->first()', $source);
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source), TOKEN_PARSE));
        }
        $lecturers = collect([(object) ['id' => 1, 'name' => 'Dosen Satu'], (object) ['id' => 2, 'name' => 'Dosen Dua'], (object) ['id' => 3, 'name' => 'Dosen Tiga dengan nama sangat panjang']]);
        $html = Blade::render('<x-eoffice::manajemen-praktikum.ui.card-lecturers :lecturers="$lecturers" />', compact('lecturers'));
        foreach ($lecturers as $lecturer) {
            $this->assertStringContainsString($lecturer->name, $html);
        }
        $this->assertStringContainsString('Dosen Satu; Dosen Dua; Dosen Tiga dengan nama sangat panjang', $html);
        $empty = Blade::render('<x-eoffice::manajemen-praktikum.ui.card-lecturers :lecturers="$lecturers" />', ['lecturers' => collect()]);
        $this->assertStringContainsString('Belum ditunjuk', $empty);
    }

    public function test_final_registration_decisions_belong_to_admin_not_cerc(): void
    {
        $admin = $this->account('admin_praktikum');
        $cerc = $this->account('koordinator_cerc', true);
        foreach (['koor' => 'eo_prak_pendaftaran_koordinator', 'asprak' => 'eo_prak_pendaftaran_asprak'] as $type => $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->string('praktikum_id');
                $t->softDeletes();
            });
            DB::table($table)->insert(['id' => 1, 'praktikum_id' => 'active']);
            foreach (['approve', 'reject'] as $action) {
                $route = 'eoffice.manprak.admin.pendaftaran-'.$type.'.'.$action;
                $this->assertSame(200, $this->boundary($route, $admin, ['id' => 1])->getStatusCode());
                try {
                    $this->boundary($route, $cerc, ['id' => 1]);
                    $this->fail('CERC accepted a final registration decision.');
                } catch (HttpException $e) {
                    $this->assertSame(403, $e->getStatusCode());
                }
            }
        }
        foreach (['pendaftaran-koor', 'pendaftaran-asprak', 'pendaftaran-detail'] as $view) {
            $source = file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/admin/'.$view.'.blade.php'));
            $this->assertStringContainsString("hasAnyRole(['superadmin','admin_praktikum'])", $source);
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source), TOKEN_PARSE));
        }
        $decision = file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/registration-decision.blade.php'));
        $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($decision), TOKEN_PARSE));
        $this->assertStringContainsString('aria-label="Setujui pendaftaran"', $decision);
        $this->assertStringContainsString('aria-label="Tolak pendaftaran"', $decision);
        $this->assertStringContainsString('role="dialog"', $decision);
        $this->assertStringContainsString('name="override_reason" required minlength="10"', $decision);
    }

    public function test_cerc_activity_history_combines_operations_and_preserves_other_audits(): void
    {
        $cerc = $this->account('koordinator_cerc', true);
        $this->actingAs($cerc);
        CercActivity::record('Penunjukan asisten', 'Praktikum A', null, ['Mahasiswa — Asisten']);
        CercActivity::record('Pengajuan peminjaman ruang', 'PRK-001', null, ['Diajukan']);
        $other = EoAuditLog::create(['action' => 'UPDATE', 'description' => 'Tidak terkait CERC', 'created_at' => now()]);
        $controller = app(CercCoordinatorController::class);
        $data = $controller->index(Request::create('/'))->getData();
        $this->assertSame(2, $data['history']->total());
        $this->assertSame('Praktikum A', $data['history']->last()->new_data['context']);
        $cutoff = $data['historyLatestId'];
        CercActivity::record('Penarikan pengajuan ruang', 'PRK-002');
        $controller->destroyHistory(Request::create('/', 'DELETE', ['before_id' => $cutoff]));
        $this->assertSame(1, CercCoordinatorHistory::activities()->count());
        $this->assertNull($other->fresh()->cerc_deleted_at);
        $source = file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/admin/koordinator-cerc.blade.php'));
        $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source), TOKEN_PARSE));
    }

    public function test_bulk_practicum_status_preserves_assignments_when_already_active(): void
    {
        $user = $this->account('asprak', true);
        DB::table('eo_prak_asprak_praktikum')->insert(['praktikum_id' => 'active', 'user_id' => $user->id, 'role' => 'asprak']);
        $request = Request::create('/', 'POST', ['praktikum_ids' => ['active'], 'action' => 'activate', 'status_confirmed' => '1', 'previous_statuses' => ['active' => 'aktif']]);
        app(PraktikumController::class)->bulkToggleActive($request);
        $this->assertSame(1, DB::table('eo_prak_asprak_praktikum')->whereNull('deleted_at')->count());
        $this->assertSame(0, DB::table('eo_audit_logs')->count());
    }

    public function test_bulk_practicum_status_rejects_stale_selection(): void
    {
        $request = Request::create('/', 'POST', ['praktikum_ids' => ['active'], 'action' => 'deactivate', 'status_confirmed' => '1', 'previous_statuses' => ['active' => 'nonaktif']]);
        try {
            app(PraktikumController::class)->bulkToggleActive($request);
            $this->fail('Stale selection accepted.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('status', $e->errors());
            $this->assertTrue((bool) DB::table('eo_prak_praktikum')->where('id', 'active')->value('is_active'));
        }
    }

    public function test_practicum_candidates_include_only_academically_active_unsuspended_students(): void
    {
        Schema::create('mk_kemahasiswaan', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            $t->string('status');
        });
        $eligible = null;
        foreach (['aktif', 'alumni', 'cuti', 'drop_out', 'pindah_studi', 'wafat', 'mangkir'] as $status) {
            $user = $this->account('mahasiswa', true);
            DB::table('mk_kemahasiswaan')->insert(['user_id' => $user->id, 'status' => $status]);
            $this->assertSame($status === 'aktif', ActiveStudent::eligible($user));
            if ($status === 'aktif') {
                $eligible = $user;
            }
        }
        $unknown = $this->account('mahasiswa', true);
        $this->assertFalse(ActiveStudent::eligible($unknown));
        $this->assertSame([$eligible->id], ActiveStudent::apply(User::query())->pluck('id')->all());
        $eligible->forceFill(['suspended_at' => now()])->save();
        $this->assertFalse(ActiveStudent::eligible($eligible));
    }

    public function test_rejection_feedback_uses_the_same_floating_wrapper_as_success(): void
    {
        view()->share('errors', new ViewErrorBag);
        session()->flash('success', 'Tindakan berhasil.');
        $success = view('eoffice::components.manajemen-praktikum.ui.flash-messages')->render();
        session()->forget('success');
        $bag = new ViewErrorBag;
        $bag->put('default', new MessageBag(['user_id' => 'Mahasiswa tidak aktif.']));
        view()->share('errors', $bag);
        $error = view('eoffice::components.manajemen-praktikum.ui.flash-messages')->render();
        foreach ([$success, $error] as $html) {
            $this->assertStringContainsString('class="mp-feedback-stack" data-feedback-pending', $html);
            $this->assertStringContainsString('mp-floating-feedback', $html);
        }
        $this->assertStringContainsString('Mahasiswa tidak aktif.', $error);
    }

    public function test_notification_list_and_read_all_are_isolated_by_active_role(): void
    {
        (require base_path('Modules/EOffice/database/migrations/2026_11_09_000002_add_practicum_role_to_notifications.php'))->up();
        if (! Schema::hasColumn('eo_notifikasi', 'created_at')) {
            Schema::table('eo_notifikasi', fn (Blueprint $t) => $t->timestamp('created_at')->nullable());
        }
        Schema::table('eo_notifikasi', function (Blueprint $t) {
            $t->string('judul')->nullable();
            $t->text('pesan')->nullable();
            $t->json('practicum_target')->nullable();
        });
        $user = $this->account('mahasiswa', true);
        Role::firstOrCreate(['name' => 'dosen', 'guard_name' => 'web'], ['module' => 'eoffice']);
        $user->assignRole(['koor_prak', 'asprak', 'koordinator_cerc', 'dosen', 'admin_praktikum']);
        foreach (['mahasiswa', 'koor', 'asprak', 'cerc', 'dosen', 'admin'] as $role) {
            DB::table('eo_notifikasi')->insert([
                'user_id' => $user->id, 'judul' => $role, 'pesan' => 'Pesan '.$role, 'is_read' => false, 'practicum_role' => $role, 'created_at' => now(),
            ]);
        }
        $controller = new NotificationController;
        foreach (['mahasiswa', 'koor', 'asprak', 'cerc', 'dosen', 'admin'] as $role) {
            $request = Request::create('/notifikasi', 'GET', ['role' => $role]);
            $request->setUserResolver(fn () => $user);
            $data = $controller->index($request)->getData(true);
            $this->assertCount(1, $data['notifications']);
            $this->assertSame($role, $data['notifications'][0]['title']);
            $this->assertSame(route('eoffice.manprak.'.$role.'.dashboard'), $data['notifications'][0]['url']);
        }
        $request = Request::create('/notifikasi/dibaca', 'POST', ['role' => 'koor']);
        $request->setUserResolver(fn () => $user);
        $controller->readAll($request);
        $this->assertSame(5, DB::table('eo_notifikasi')->where('user_id', $user->id)->where('is_read', false)->count());
        $item = Notifikasi::where('practicum_role', 'mahasiswa')->first();
        $item->update(['practicum_target' => ['section' => 'tugas', 'praktikum_id' => 'active', 'tugas_id' => 42]]);
        $request = Request::create('/notifikasi', 'POST', ['role' => 'mahasiswa']);
        $request->setUserResolver(fn () => $user);
        $controller->read($request, $item->id);
        $this->assertTrue($item->fresh()->is_read);
        $this->assertSame(route('eoffice.manprak.mahasiswa.tugas.index', ['praktikum_id' => 'active', 'tugas_id' => 42]), NotificationDestination::url($item->fresh(), 'mahasiswa'));
        $request = Request::create('/notifikasi', 'POST', ['role' => 'koor']);
        $request->setUserResolver(fn () => $user);
        try {
            $controller->read($request, $item->id);
            $this->fail('Read notification belonging to another role');
        } catch (ModelNotFoundException) {
            $this->assertTrue(true);
        }
        $source = file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/notification-bell.blade.php'));
        $this->assertStringContainsString('<a :href="item.url"', $source);
        $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source), TOKEN_PARSE));
    }

    private function announcementSchema(): void
    {
        Schema::create('eo_prak_daftar_praktikan', function (Blueprint $t) {
            $t->id();
            $t->string('praktikum_id');
            $t->integer('user_id');
        });
        Schema::create('eo_prak_pengumuman_praktikum', function (Blueprint $t) {
            $t->id();
            $t->string('praktikum_id');
            $t->integer('user_id');
            $t->string('judul');
            $t->text('konten');
            $t->boolean('is_published')->default(true);
            $t->string('tipe_sistem')->nullable();
            $t->integer('periode_id')->nullable();
            $t->json('lampiran')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        (require base_path('Modules/EOffice/database/migrations/2026_11_09_000001_add_pin_expiry_to_practicum_announcements.php'))->up();
    }

    public function test_dashboard_announcements_have_no_total_limit_and_use_creation_date_or_active_pin(): void
    {
        $this->announcementSchema();
        $this->travelTo(Carbon::parse('2026-11-09 12:00:00'));
        $student = $this->account('mahasiswa', true);
        $author = $this->account('asprak', true);
        DB::table('eo_prak_daftar_praktikan')->insert(['praktikum_id' => 'active', 'user_id' => $student->id]);
        $create = fn ($title, $created, $extra = []) => Pengumuman::forceCreate(array_merge([
            'praktikum_id' => 'active', 'user_id' => $author->id, 'judul' => $title, 'konten' => 'Isi pengumuman', 'is_published' => true,
            'created_at' => $created, 'updated_at' => now(),
        ], $extra));
        foreach (range(1, 55) as $i) {
            $create('Terbaru '.$i, now()->subDay());
        }
        $boundary = $create('Batas 30 hari', now()->subDays(30));
        $old = $create('Lama baru diedit', now()->subDays(31));
        $pinned = $create('Sematan lama', now()->subDays(60), ['pinned_until' => now()->addHour()]);
        $expiredOld = $create('Sematan lama berakhir', now()->subDays(50), ['pinned_until' => now()]);
        $expiredRecent = $create('Sematan terbaru berakhir', now()->subDays(2), ['pinned_until' => now()->subMinute()]);
        $create('Draf', now(), ['is_published' => false, 'pinned_until' => now()->addDay()]);
        $create('Sistem', now(), ['tipe_sistem' => 'buka', 'pinned_until' => now()->addDay()]);
        $create('Praktikum nonaktif', now(), ['praktikum_id' => 'archived', 'pinned_until' => now()->addDay()]);
        $deleted = $create('Dihapus', now(), ['pinned_until' => now()->addDay()]);
        $deleted->delete();
        DB::table('eo_prak_praktikum')->insert(['id' => 'other-course', 'nama' => 'Lain', 'is_active' => true]);
        $create('Bukan praktikum mahasiswa', now(), ['praktikum_id' => 'other-course', 'pinned_until' => now()->addDay()]);
        $create('Masa depan', now()->addDay());
        $rows = Pengumuman::forStudentDashboard($student->id)->get();
        $this->assertCount(58, $rows);
        $this->assertSame($pinned->id, $rows->first()->id);
        $this->assertContains($boundary->id, $rows->modelKeys());
        $this->assertContains($expiredRecent->id, $rows->modelKeys());
        $this->assertNotContains($old->id, $rows->modelKeys());
        $this->assertNotContains($expiredOld->id, $rows->modelKeys());
        $this->travelTo(now()->addHour());
        $this->assertFalse($pinned->fresh()->isPinned());
        $this->assertFalse(Pengumuman::forStudentDashboard($student->id)->whereKey($pinned->id)->exists());
        $this->assertNotNull($pinned->fresh());
        $this->assertNotNull($old->fresh());
    }

    private function pinRequest(User $actor, int $postId, array $data, string $role = 'asprak')
    {
        $this->actingAs($actor);
        $name = 'eoffice.manprak.'.$role.'.pengumuman.pin';
        $route = clone Route::getRoutes()->getByName($name);
        $request = Request::create(route($name, $postId, false), 'PATCH', $data);
        $route->bind($request);
        $request->setRouteResolver(fn () => $route);
        $request->setUserResolver(fn () => $actor);

        return app(AnnouncementPinController::class)->update($request, $postId);
    }

    public function test_authors_can_pin_with_future_expiry_and_unpin_without_resetting_post_age(): void
    {
        $this->announcementSchema();
        $this->travelTo(Carbon::parse('2026-11-09 12:00:00'));
        $author = $this->account('asprak', true);
        AsprakPraktikum::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'role' => 'asprak']);
        $post = Pengumuman::forceCreate(['praktikum_id' => 'active', 'user_id' => $author->id, 'judul' => 'Lama', 'konten' => 'Isi', 'is_published' => true, 'created_at' => now()->subDays(50)]);
        $original = $post->created_at->toDateTimeString();
        foreach ([null, '2026-11-08T10:00', '2026-11-09T12:00', 'bukan tanggal'] as $expiry) {
            try {
                $this->pinRequest($author, $post->id, ['pin' => 1, 'pinned_until' => $expiry]);
                $this->fail('Invalid expiry accepted.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('pinned_until', $e->errors());
            }
        }
        $this->pinRequest($author, $post->id, ['pin' => 1, 'pinned_until' => '2026-11-10T12:00']);
        $this->assertTrue($post->fresh()->isPinned());
        $this->assertSame($original, $post->fresh()->created_at->toDateTimeString());
        $this->pinRequest($author, $post->id, ['pin' => 0]);
        $this->assertNull($post->fresh()->pinned_until);
        $this->pinRequest($author, $post->id, ['pin' => 0]);
        $this->assertFalse($post->fresh()->isPinned());
    }

    public function test_announcement_pinning_rejects_other_authors_revoked_assignments_and_inactive_courses(): void
    {
        $this->announcementSchema();
        $this->travelTo(Carbon::parse('2026-11-09 12:00:00'));
        $author = $this->account('asprak', true);
        $other = $this->account('asprak', true);
        $assignment = AsprakPraktikum::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'role' => 'asprak']);
        AsprakPraktikum::create(['praktikum_id' => 'active', 'user_id' => $other->id, 'role' => 'asprak']);
        $post = Pengumuman::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'judul' => 'Milik sendiri', 'konten' => 'Isi', 'is_published' => true]);
        $payload = ['pin' => 1, 'pinned_until' => '2026-11-10T12:00'];
        foreach (['other', 'revoked', 'inactive', 'system', 'draft'] as $condition) {
            $actor = $condition === 'other' ? $other : $author;
            if ($condition === 'revoked') {
                $assignment->delete();
            } else {
                $assignment->restore();
            }
            DB::table('eo_prak_praktikum')->where('id', 'active')->update(['is_active' => $condition !== 'inactive']);
            $post->update(['tipe_sistem' => $condition === 'system' ? 'buka' : null, 'is_published' => $condition !== 'draft']);
            try {
                $this->pinRequest($actor, $post->id, $payload);
                $this->fail('Unauthorized pin accepted: '.$condition);
            } catch (HttpException $e) {
                $this->assertContains($e->getStatusCode(), [403, 422]);
            }
            $this->assertNull($post->fresh()->pinned_until);
        }
        $post->update(['tipe_sistem' => null, 'is_published' => true]);
        DB::table('eo_prak_praktikum')->where('id', 'active')->update(['is_active' => true, 'koor_id' => $author->id]);
        $this->pinRequest($author, $post->id, $payload, 'koor');
        $this->assertTrue($post->fresh()->isPinned());
    }

    public function test_pin_views_compile_and_author_actions_render(): void
    {
        $this->announcementSchema();
        $author = $this->account('asprak', true);
        $this->actingAs($author);
        $post = Pengumuman::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'judul' => 'Pengumuman', 'konten' => 'Isi lengkap untuk pratinjau', 'is_published' => true,
            'lampiran' => [['path' => 'pengumuman/test.pdf', 'name' => 'Panduan Praktikum.pdf']]]);
        $html = Blade::render('<x-eoffice::manajemen-praktikum.ui.announcement-pin-actions :announcement="$post" role="asprak" />', ['post' => $post]);
        $this->assertStringContainsString('Tenggat Penyematan', $html);
        $this->assertStringContainsString('Sematkan Pengumuman', $html);
        $this->assertMatchesRegularExpression('/(?:top|inset-block-start):50%;(?:left|inset-inline-start):50%;transform:translate\(-50%,-50%\)/', $html);
        $this->assertStringContainsString('Isi lengkap untuk pratinjau', $html);
        $this->assertStringContainsString('Panduan Praktikum.pdf', $html);
        $this->assertStringContainsString($author->name, $html);
        $this->assertStringContainsString('data-mp-picker-lazy', $html);
        $this->assertStringNotContainsString('type="datetime-local"', $html);
        $this->assertStringContainsString('Pengumuman akan disematkan di halaman ini dan dashboard mahasiswa sampai waktu yang telah ditentukan', $html);
        foreach (['asprak/pengumuman', 'koordinator/pengumuman', 'mahasiswa/dashboard', 'mahasiswa/pengumuman', 'dosen/pengumuman'] as $view) {
            $compiled = app('blade.compiler')->compileString(file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$view.'.blade.php')));
            $this->assertNotEmpty(token_get_all($compiled, TOKEN_PARSE));
        }
    }

    public function test_announcement_preview_is_clickable_and_truncates_only_above_200_characters(): void
    {
        $this->announcementSchema();
        $author = $this->account('asprak', true);
        $this->actingAs($author);
        $post = Pengumuman::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'judul' => 'Judul pengumuman', 'konten' => str_repeat('a', 200), 'is_published' => true]);
        $render = fn () => Blade::render('<x-eoffice::manajemen-praktikum.ui.announcement-preview :announcement="$post" />', ['post' => $post->fresh()]);
        $html = $render();
        $this->assertStringContainsString(str_repeat('a', 200), $html);
        $this->assertStringNotContainsString('Lihat selengkapnya', $html);
        $this->assertStringContainsString('pengumuman_id='.$post->id.'#pengumuman-'.$post->id, $html);
        $post->update(['konten' => str_repeat('é', 201)]);
        $html = $render();
        $this->assertStringContainsString(str_repeat('é', 200), $html);
        $this->assertStringNotContainsString(str_repeat('é', 201), $html);
        $this->assertStringContainsString('... <strong style="font-weight:700;color:#0B266E">Lihat selengkapnya</strong>', $html);
    }

    public function test_announcement_edit_preserves_selected_attachments_adds_files_and_can_remove_all(): void
    {
        $this->announcementSchema();
        $author = $this->account('asprak', true);
        $this->actingAs($author);
        AsprakPraktikum::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'role' => 'asprak']);
        $post = Pengumuman::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'judul' => 'Awal', 'konten' => 'Isi', 'is_published' => true,
            'lampiran' => [['path' => 'old/a.pdf', 'name' => 'A.pdf'], ['path' => 'old/b.pdf', 'name' => 'B.pdf']]]);
        $storage = $this->mock(SupabaseStorage::class);
        $storage->shouldReceive('upload')->once()->andReturn('new/c.pdf');
        $storage->shouldNotReceive('delete');
        $request = Request::create('/edit', 'PUT', ['judul' => 'Baru', 'konten' => 'Isi baru', 'attachments_managed' => 1, 'retained_files' => ['old/b.pdf']], [], [
            'lampiran' => [UploadedFile::fake()->create('C.pdf', 10, 'application/pdf')],
        ]);
        $controller = app(PengumumanController::class);
        $controller->update($request, $post->id);
        $this->assertSame(['old/b.pdf', 'new/c.pdf'], collect($post->fresh()->lampiran)->pluck('path')->all());
        $this->assertSame('Baru', $post->fresh()->judul);
        $controller->update(Request::create('/edit', 'PUT', ['judul' => 'Baru', 'konten' => 'Isi baru']), $post->id);
        $this->assertCount(2, $post->fresh()->lampiran);
        $controller->update(Request::create('/edit', 'PUT', ['judul' => 'Tanpa berkas', 'konten' => 'Isi', 'attachments_managed' => 1]), $post->id);
        $this->assertNull($post->fresh()->lampiran);
    }

    public function test_announcement_edit_rejects_foreign_files_and_more_than_three_attachments(): void
    {
        $this->announcementSchema();
        $author = $this->account('asprak', true);
        $this->actingAs($author);
        AsprakPraktikum::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'role' => 'asprak']);
        $files = collect(range(1, 3))->map(fn ($n) => ['path' => 'old/'.$n.'.pdf', 'name' => $n.'.pdf'])->all();
        $post = Pengumuman::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'judul' => 'Awal', 'konten' => 'Isi', 'is_published' => true, 'lampiran' => $files]);
        $this->mock(SupabaseStorage::class)->shouldNotReceive('upload');
        $controller = app(PengumumanController::class);
        foreach ([['foreign/file.pdf'], array_column($files, 'path')] as $retained) {
            $request = Request::create('/edit', 'PUT', ['judul' => 'Tidak tersimpan', 'konten' => 'Isi', 'attachments_managed' => 1, 'retained_files' => $retained], [], [
                'lampiran' => [UploadedFile::fake()->create('Extra.pdf', 10, 'application/pdf')],
            ]);
            try {
                $controller->update($request, $post->id);
                $this->fail('Invalid attachment selection accepted.');
            } catch (ValidationException) {
                $this->assertSame($files, $post->fresh()->lampiran);
                $this->assertSame('Awal', $post->fresh()->judul);
            }
        }
    }

    public function test_announcement_upload_failure_keeps_old_files_and_cleans_only_new_uploads(): void
    {
        $this->announcementSchema();
        $author = $this->account('asprak', true);
        $this->actingAs($author);
        AsprakPraktikum::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'role' => 'asprak']);
        $post = Pengumuman::create(['praktikum_id' => 'active', 'user_id' => $author->id, 'judul' => 'Awal', 'konten' => 'Isi', 'is_published' => true, 'lampiran' => [['path' => 'old/a.pdf', 'name' => 'A.pdf']]]);
        $storage = $this->mock(SupabaseStorage::class);
        $storage->shouldReceive('upload')->twice()->andReturn('new/temporary.pdf', null);
        $storage->shouldReceive('delete')->once()->with('new/temporary.pdf', 'eoffice')->andReturn(true);
        $request = Request::create('/edit', 'PUT', ['judul' => 'Baru', 'konten' => 'Isi', 'attachments_managed' => 1, 'retained_files' => ['old/a.pdf']], [], [
            'lampiran' => [UploadedFile::fake()->create('B.pdf', 10), UploadedFile::fake()->create('C.pdf', 10)],
        ]);
        try {
            app(PengumumanController::class)->update($request, $post->id);
            $this->fail('Failed upload saved.');
        } catch (ValidationException) {
            $this->assertSame('Awal', $post->fresh()->judul);
            $this->assertSame('old/a.pdf', $post->fresh()->lampiran[0]['path']);
        }
    }

    public function test_all_practicum_list_roles_use_the_same_full_width_header_divider(): void
    {
        foreach (['koordinator', 'asprak', 'mahasiswa', 'dosen'] as $role) {
            $source = file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$role.'/praktikum.blade.php'));
            $this->assertStringContainsString('class="mp-page-header mp-practicum-list-header"', $source);
            $this->assertStringContainsString('<span class="mp-badge success sm"><span class="dot"></span>Aktif</span>', $source);
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source), TOKEN_PARSE));
        }
    }

    public function test_dashboard_deep_link_opens_the_page_containing_the_post_even_with_pins_and_equal_dates(): void
    {
        $this->announcementSchema();
        $this->travelTo(Carbon::parse('2026-11-09 12:00:00'));
        $student = $this->account('mahasiswa', true);
        $this->actingAs($student);
        DB::table('eo_prak_daftar_praktikan')->insert(['praktikum_id' => 'active', 'user_id' => $student->id]);
        $create = fn ($extra = []) => Pengumuman::forceCreate(array_merge([
            'praktikum_id' => 'active', 'user_id' => $student->id, 'judul' => 'Pengumuman', 'konten' => 'Isi', 'is_published' => true, 'created_at' => now(),
        ], $extra));
        $target = $create();
        foreach (range(1, 23) as $i) {
            $create();
        }
        $pin = $create(['created_at' => now()->subDays(60), 'pinned_until' => now()->addDay()]);
        $create(['created_at' => $pin->created_at, 'pinned_until' => now()->addHour()]);
        $controller = app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\PengumumanController::class);
        $rows = $controller->index(Request::create('/pengumuman', 'GET', ['praktikum_id' => 'active', 'pengumuman_id' => $target->id]))->getData()['pengumumans'];
        $this->assertSame(3, $rows->currentPage());
        $this->assertContains($target->id, $rows->pluck('id')->all());
        $this->assertStringNotContainsString('pengumuman_id', $rows->url(1));
        $rows = $controller->index(Request::create('/pengumuman', 'GET', ['praktikum_id' => 'active', 'pengumuman_id' => $pin->id]))->getData()['pengumumans'];
        $this->assertSame(1, $rows->currentPage());
        $this->assertSame($pin->id, $rows->items()[1]->id);
        $foreign = $create(['praktikum_id' => 'archived']);
        $draft = $create(['is_published' => false]);
        $deleted = $create();
        $deleted->delete();
        foreach ([$foreign, $draft, $deleted] as $hidden) {
            try {
                $controller->index(Request::create('/pengumuman', 'GET', ['praktikum_id' => 'active', 'pengumuman_id' => $hidden->id]));
                $this->fail('Hidden announcement exposed.');
            } catch (ModelNotFoundException) {
                $this->assertTrue(true);
            }
        }
        try {
            $controller->index(Request::create('/pengumuman', 'GET', ['praktikum_id' => 'archived', 'pengumuman_id' => $foreign->id]));
            $this->fail('Unenrolled course exposed.');
        } catch (HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_archiving_revokes_only_this_practicum_staffing_and_keeps_global_lecturer_role(): void
    {
        Role::firstOrCreate(['name' => 'dosen', 'guard_name' => 'web'], ['module' => 'eoffice']);
        $admin = $this->account('admin_praktikum');
        $coordinator = $this->account('koor_prak', true);
        $assistant = $this->account('asprak', true);
        $shared = $this->account('asprak', true);
        $lecturer = $this->account('dosen');
        $this->actingAs($admin);
        DB::table('eo_prak_praktikum')->where('id', 'active')->update(['koor_id' => $coordinator->id, 'dosen_id' => $lecturer->id]);
        DB::table('eo_prak_praktikum_dosen')->insert(['praktikum_id' => 'active', 'dosen_id' => $lecturer->id]);
        DB::table('eo_prak_praktikum')->insert(['id' => 'other', 'nama' => 'Lain', 'is_active' => true, 'status' => 'aktif']);
        foreach ([[$coordinator, 'koor', 'active'], [$assistant, 'asprak', 'active'], [$shared, 'asprak', 'active'], [$shared, 'asprak', 'other']] as [$user,$role,$id]) {
            AsprakPraktikum::create(['praktikum_id' => $id, 'user_id' => $user->id, 'role' => $role]);
        }
        $service = app(PracticumLifecycle::class);
        $result = $service->save('active', ['status' => 'nonaktif'], null, true, 'aktif');
        $this->assertFalse($result->is_active);
        $this->assertNull($result->koor_id);
        $this->assertNull($result->getRawOriginal('dosen_id'));
        $this->assertSame(0, $result->dosens()->count());
        $this->assertSame(0, AsprakPraktikum::where('praktikum_id', 'active')->count());
        $this->assertSame(3, AsprakPraktikum::onlyTrashed()->where('praktikum_id', 'active')->count());
        $this->assertFalse($coordinator->fresh()->hasRole('koor_prak'));
        $this->assertFalse($assistant->fresh()->hasRole('asprak'));
        $this->assertTrue($shared->fresh()->hasRole('asprak'));
        $this->assertTrue($lecturer->fresh()->hasRole('dosen'));
        $entry = EoAuditLog::where('description', 'Praktikum diarsipkan dan penugasan pengelola dicabut.')->firstOrFail();
        $this->assertSame([$lecturer->id], $entry->old_data['dosen_ids']);
        $result = $service->save('active', ['status' => 'aktif'], null, true, 'nonaktif');
        $this->assertTrue($result->is_active);
        $this->assertNull($result->koor_id);
        $this->assertSame(0, $result->dosens()->count());
        $this->assertSame(0, AsprakPraktikum::where('praktikum_id', 'active')->count());
    }

    public function test_status_change_requires_confirmation_and_cerc_cannot_change_status(): void
    {
        $this->actingAs($this->account('admin_praktikum'));
        $service = app(PracticumLifecycle::class);
        foreach ([[false, 'aktif'], [true, 'nonaktif']] as [$confirmed,$previous]) {
            try {
                $service->save('active', ['status' => 'nonaktif'], null, $confirmed, $previous);
                $this->fail('Missing or stale confirmation accepted.');
            } catch (ValidationException) {
                $this->assertTrue(Praktikum::find('active')->is_active);
            }
        }
        $this->withoutMiddleware(CheckModuleActive::class);
        $this->actingAs($this->account('koordinator_cerc', true))
            ->patchJson(route('eoffice.manprak.admin.praktikum.toggle-active', 'active'), ['status_confirmed' => 1, 'previous_status' => 'aktif'])->assertForbidden();
        $this->assertTrue(Praktikum::find('active')->is_active);
    }

    public function test_student_archive_middleware_allows_reading_but_rejects_final_task_writes(): void
    {
        $student = $this->account('mahasiswa', true);
        $this->actingAs($student);
        $middleware = new StudentPracticumArchive;
        DB::table('eo_prak_praktikum')->where('id', 'archived')->update(['status' => 'nonaktif']);
        foreach (['GET', 'POST'] as $method) {
            $route = new \Illuminate\Routing\Route($method, 'archive/{praktikum}', fn () => null);
            $route->name('eoffice.manprak.mahasiswa.tugas-akhir.'.($method === 'GET' ? 'index' : 'submit'));
            $request = Request::create('/archive/archived', $method);
            $route->bind($request);
            $request->setRouteResolver(fn () => $route);
            if ($method === 'GET') {
                $this->assertSame('ok', $middleware->handle($request, fn () => response('ok'))->getContent());
            } else {
                try {
                    $middleware->handle($request, fn () => response('changed'));
                    $this->fail('Archived submission accepted.');
                } catch (HttpException $e) {
                    $this->assertSame(403, $e->getStatusCode());
                }
            }
        }
    }

    public function test_archived_module_tasks_and_uploaded_files_remain_readable_but_all_mutations_are_blocked(): void
    {
        $student = $this->account('mahasiswa', true);
        $this->actingAs($student);
        DB::table('eo_prak_praktikum')->where('id', 'archived')->update(['status' => 'nonaktif']);
        Schema::create('eo_prak_daftar_praktikan', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('praktikum_id');
            $t->integer('user_id');
        });
        Schema::create('eo_prak_modul_praktikum', function (Blueprint $t) {
            $t->integer('id')->primary();
            $t->string('praktikum_id');
            $t->string('nama');
            $t->integer('urutan');
        });
        Schema::create('eo_prak_modul_asprak', function (Blueprint $t) {
            $t->integer('modul_id');
            $t->integer('asprak_id');
        });
        Schema::create('eo_prak_tugas_praktikum', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->integer('modul_id');
            $t->string('judul');
            $t->boolean('is_published');
            $t->dateTime('deadline');
        });
        Schema::create('eo_prak_pengumpulan_tugas', function (Blueprint $t) {
            $t->integer('id')->primary();
            $t->string('tugas_id');
            $t->string('daftar_praktikan_id');
            $t->string('file_path');
            $t->string('status_pengumpulan');
        });
        Schema::create('eo_prak_riwayat_pengumpulan_tugas', function (Blueprint $t) {
            $t->id();
            $t->integer('pengumpulan_tugas_id');
            $t->string('file_path');
            $t->timestamps();
        });
        DB::table('eo_prak_daftar_praktikan')->insert(['id' => 'enrolled', 'praktikum_id' => 'archived', 'user_id' => $student->id]);
        DB::table('eo_prak_modul_praktikum')->insert(['id' => 1, 'praktikum_id' => 'archived', 'nama' => 'Modul Lama', 'urutan' => 1]);
        DB::table('eo_prak_tugas_praktikum')->insert(['id' => '10', 'modul_id' => 1, 'judul' => 'Tugas Lama', 'is_published' => true, 'deadline' => '2025-01-01 10:00:00']);
        DB::table('eo_prak_pengumpulan_tugas')->insert(['id' => 1, 'tugas_id' => '10', 'daftar_praktikan_id' => 'enrolled', 'file_path' => 'arsip/Laporan.pdf', 'status_pengumpulan' => 'acc']);
        $view = app(TugasController::class)->index(Request::create('/tasks', 'GET', ['praktikum_id' => 'archived']));
        $task = $view->getData()['modulList']->first()['tugas']->first();
        $this->assertSame('Tugas Lama', $task->judul);
        $this->assertSame(['arsip/Laporan.pdf'], $task->pengumpulan->files);
        foreach (['kumpul' => 'POST', 'kirim-ulang' => 'POST', 'batalkan' => 'PATCH', 'hapus' => 'DELETE'] as $action => $method) {
            $name = 'eoffice.manprak.mahasiswa.tugas.'.$action;
            $route = clone Route::getRoutes()->getByName($name);
            $request = Request::create(route($name, ['id' => '10'], false), $method);
            $route->bind($request);
            $request->setRouteResolver(fn () => $route);
            try {
                (new StudentPracticumArchive)->handle($request, fn () => response('changed'));
                $this->fail('Archived task mutation accepted: '.$action);
            } catch (HttpException $e) {
                $this->assertSame(403, $e->getStatusCode());
            }
        }
        $this->assertSame('arsip/Laporan.pdf', DB::table('eo_prak_pengumpulan_tugas')->value('file_path'));
    }

    public function test_archive_views_compile_and_confirmation_is_shared_by_status_actions(): void
    {
        foreach (['admin/praktikum', 'admin/partials/status-confirmation', 'mahasiswa/tugas', 'tugas-akhir/student-submission'] as $view) {
            $compiled = app('blade.compiler')->compileString(file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$view.'.blade.php')));
            try {
                $this->assertNotEmpty(token_get_all($compiled, TOKEN_PARSE));
            } catch (\ParseError $e) {
                throw new \RuntimeException($view.': '.$e->getMessage(), 0, $e);
            }
        }
        $compiled = app('blade.compiler')->compileString(file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/practicum-status-action.blade.php')));
        $this->assertNotEmpty(token_get_all($compiled, TOKEN_PARSE));
        $html = Blade::render('<x-eoffice::manajemen-praktikum.ui.practicum-status-action :praktikum="$praktikum" />', ['praktikum' => Praktikum::findOrFail('active')]);
        $this->assertStringContainsString('Nonaktifkan Praktikum', $html);
        $this->assertStringContainsString('name="status_confirmed"', $html);
        $this->assertStringContainsString('name="previous_status" value="aktif"', $html);
    }

    public function test_student_practicum_list_places_active_courses_before_archives(): void
    {
        $student = $this->account('mahasiswa', true);
        $other = $this->account('mahasiswa', true);
        $this->actingAs($student);
        Schema::table('eo_prak_praktikum', function (Blueprint $t) {
            $t->integer('matkul_id')->nullable();
        });
        Schema::create('eo_prak_daftar_praktikan', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('praktikum_id');
            $t->integer('user_id');
            $t->timestamps();
        });
        DB::table('eo_prak_praktikum')->insert([
            ['id' => 'older-active', 'nama' => 'Aktif Lama', 'is_active' => true, 'status' => 'aktif', 'tahun_ajaran' => '2025/2026'],
            ['id' => 'other-only', 'nama' => 'Bukan Milik Mahasiswa', 'is_active' => true, 'status' => 'aktif', 'tahun_ajaran' => '2027/2028'],
        ]);
        DB::table('eo_prak_praktikum')->where('id', 'archived')->update(['status' => 'nonaktif', 'tahun_ajaran' => '2027/2028']);
        foreach (['archived', 'older-active', 'active'] as $index => $id) {
            DB::table('eo_prak_daftar_praktikan')->insert([
                'id' => 'entry-'.$index, 'praktikum_id' => $id, 'user_id' => $student->id, 'created_at' => '2026-11-09 08:00:00',
            ]);
        }
        DB::table('eo_prak_daftar_praktikan')->insert(['id' => 'outsider', 'praktikum_id' => 'other-only', 'user_id' => $other->id]);
        $rows = app(\Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\PraktikumController::class)->index(new Request)->getData()['daftarPraktikan'];
        $this->assertSame(['active', 'older-active', 'archived'], $rows->pluck('praktikum_id')->all());
    }

    private function boundary(string $name, User $user, array $parameters = [], array $data = [], $callback = null)
    {
        $this->actingAs($user);
        $route = clone Route::getRoutes()->getByName($name);
        $method = collect($route->methods())->first(fn ($m) => $m !== 'HEAD');
        $request = Request::create(route($name, $parameters, false), $method, $data);
        $route->bind($request);
        $request->setRouteResolver(fn () => $route);
        $request->setUserResolver(fn () => $user);
        $request->setLaravelSession(app('session.store'));
        $this->app->instance('request', $request);

        return (new PracticumAdministrationBoundary)->handle($request, $callback ?? fn () => response('ok'));
    }

    public function test_admin_assigns_one_cerc_and_replacement_preserves_other_roles(): void
    {
        $admin = $this->account('admin_praktikum');
        $first = $this->account('mahasiswa', true);
        $second = $this->account('mahasiswa', true);
        $this->actingAs($admin);
        $service = app(CercCoordinator::class);
        $service->replace($first);
        $this->assertTrue($first->fresh()->hasRole('koordinator_cerc'));
        $service->replace($second);
        $this->assertFalse($first->fresh()->hasRole('koordinator_cerc'));
        $this->assertTrue($first->fresh()->hasRole('mahasiswa'));
        $this->assertTrue($second->fresh()->hasRole('koordinator_cerc'));
        $this->assertSame(1, User::role('koordinator_cerc')->count());
        $service->replace(null);
        $this->assertSame(0, User::role('koordinator_cerc')->count());
        $this->assertSame(3, DB::table('eo_audit_logs')->count());
    }

    public function test_database_rejects_second_cerc_even_outside_assignment_service(): void
    {
        $first = $this->account('mahasiswa', true);
        $second = $this->account('mahasiswa', true);
        $first->assignRole('koordinator_cerc');
        $this->expectException(QueryException::class);
        $second->assignRole('koordinator_cerc');
    }

    public function test_cerc_cannot_assign_its_own_successor(): void
    {
        $cerc = $this->account('koordinator_cerc', true);
        $this->actingAs($cerc);
        $this->expectException(HttpException::class);
        app(CercCoordinator::class)->replace(null);
    }

    public function test_admin_cannot_change_practicum_staff_through_old_endpoint(): void
    {
        $admin = $this->account('admin_praktikum');
        $this->expectException(HttpException::class);
        $this->boundary('eoffice.manprak.admin.kelola-role.assign', $admin, [], ['praktikum_id' => 'active']);
    }

    public function test_cerc_can_only_change_active_practicums(): void
    {
        $cerc = $this->account('koordinator_cerc', true);
        $this->assertSame('ok', $this->boundary('eoffice.manprak.cerc.kelola-role.assign', $cerc, [], ['praktikum_id' => 'active'])->getContent());
        try {
            $this->boundary('eoffice.manprak.cerc.kelola-role.assign', $cerc, [], ['praktikum_id' => 'archived']);
            $this->fail('Archived practicum accessible');
        } catch (HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_cerc_staffing_lifecycle_preserves_assignments_on_other_practicums(): void
    {
        $cerc = $this->account('koordinator_cerc', true);
        $student = $this->account('mahasiswa', true);
        DB::table('eo_prak_praktikum')->insert(['id' => 'second', 'nama' => 'Kedua', 'is_active' => true]);
        $controller = app(KelolRoleController::class);
        foreach (['active', 'second'] as $id) {
            $this->boundary('eoffice.manprak.cerc.kelola-role.assign', $cerc, [], ['praktikum_id' => $id, 'user_id' => $student->id, 'role' => 'asprak'], fn ($r) => $controller->assignRole($r));
        }
        $this->assertTrue($student->fresh()->hasRole('asprak'));
        $record = AsprakPraktikum::where('praktikum_id', 'active')->firstOrFail();
        $this->boundary('eoffice.manprak.cerc.kelola-role.revoke', $cerc, ['id' => $record->id], [], fn () => $controller->revokeRole($record->id));
        $this->assertTrue($student->fresh()->hasRole('asprak'));
        $this->boundary('eoffice.manprak.cerc.kelola-role.restore', $cerc, ['id' => $record->id], [], fn () => $controller->restoreRole($record->id));
        $this->assertFalse($record->fresh()->trashed());
        $this->boundary('eoffice.manprak.cerc.kelola-role.revokeAll', $cerc, ['praktikumId' => 'active'], [], fn ($r) => $controller->revokeAll($r, 'active'));
        $this->assertTrue((bool) DB::table('eo_prak_praktikum')->where('id', 'active')->value('is_active'));
        $this->assertSame(1, AsprakPraktikum::where('user_id', $student->id)->count());
        $this->assertSame(5, CercCoordinatorHistory::activities()->where('action', 'CERC_ACTIVITY')->count());
    }

    public function test_cerc_can_assign_five_assistants_atomically_and_rejects_six(): void
    {
        $cerc = $this->account('koordinator_cerc', true);
        $ids = [];
        foreach (range(1, 6) as $i) {
            $ids[] = $this->account('mahasiswa', true)->id;
        }
        DB::table('eo_prak_praktikum')->where('id', 'active')->update(['status' => 'aktif']);
        $controller = app(KelolRoleController::class);
        try {
            $this->boundary('eoffice.manprak.cerc.kelola-role.assign', $cerc, [], ['praktikum_id' => 'active', 'role' => 'asprak', 'user_ids' => $ids], fn ($r) => $controller->assignRole($r));
            $this->fail('Six assistants accepted');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('user_ids', $e->errors());
        }
        $this->boundary('eoffice.manprak.cerc.kelola-role.assign', $cerc, [], ['praktikum_id' => 'active', 'role' => 'asprak', 'user_ids' => array_slice($ids, 0, 5)], fn ($r) => $controller->assignRole($r));
        $this->assertSame(5, AsprakPraktikum::where('praktikum_id', 'active')->where('role', 'asprak')->count());
    }

    public function test_admin_cannot_smuggle_coordinator_assignment_into_practicum_update(): void
    {
        $admin = $this->account('admin_praktikum');
        $this->expectException(HttpException::class);
        $this->boundary('eoffice.manprak.admin.praktikum.update', $admin, ['praktikum' => 'active'], ['koor_id' => 999]);
    }

    public function test_admin_is_blocked_from_other_systems_but_can_open_its_dashboard(): void
    {
        $admin = $this->account('admin_praktikum');
        $this->actingAs($admin);
        $middleware = new RedirectBasedOnRole;
        $this->assertSame('ok', $middleware->handle(Request::create('/eoffice/manprak/admin/dashboard'), fn () => response('ok'))->getContent());
        $this->expectException(HttpException::class);
        $middleware->handle(Request::create('/capstone/dashboard'), fn () => response('forbidden'));
    }

    public function test_practicum_admin_notification_endpoints_are_not_blocked_by_global_middleware(): void
    {
        $this->actingAs($this->account('admin_praktikum'));
        foreach (['/eoffice/manprak/notifikasi', '/eoffice/manprak/notifikasi/dibaca', '/eoffice/manprak/notifikasi/1/dibaca'] as $path) {
            $this->assertSame('ok', (new RedirectBasedOnRole)->handle(Request::create($path, 'POST'), fn () => response('ok'))->getContent());
        }
    }

    public function test_practicum_role_does_not_override_existing_module_admin_routing(): void
    {
        foreach (['admin_banksoal' => ['/api/v1/bank-soal/dashboard', 'banksoal.dashboard'], 'admin_capstone' => ['/capstone/dashboard', 'capstone.dashboard'], 'admin_eoffice' => ['/eoffice/dashboard', 'eoffice.dashboard'], 'admin_kemahasiswaan' => ['/manajemen-mahasiswa/dashboard', 'manajemenmahasiswa.dashboard']] as $role => [$path, $route]) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
            $user = $this->account($role);
            $user->assignRole('admin_praktikum');
            $user->clearUserCache();
            $this->actingAs($user->fresh());
            $middleware = new RedirectBasedOnRole;
            $this->assertSame('ok', $middleware->handle(Request::create($path), fn () => response('ok'))->getContent());
            $this->assertSame('ok', $middleware->handle(Request::create('/eoffice/manprak/admin/dashboard'), fn () => response('ok'))->getContent());
            $this->assertSame(route($route), $middleware->handle(Request::create('/dashboard'), fn () => response('unexpected'))->headers->get('Location'));
            $user->update(['password' => Hash::make('test-only-password')]);
            $user->clearUserCache();
            $this->app['auth']->guard()->logout();
            $this->post(route('login'), ['email' => $user->email, 'password' => 'test-only-password'])
                ->assertSessionHasNoErrors()->assertRedirect(route($route));
        }
    }

    public function test_practicum_permissions_are_view_only_and_preserve_other_role_grants(): void
    {
        foreach (['eoffice.view', 'eoffice.edit', 'eoffice.delete', 'banksoal.view', 'banksoal.edit', 'banksoal.delete'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web'], ['module' => explode('.', $name)[0]]);
        }
        foreach (['admin_praktikum', 'koordinator_cerc'] as $role) {
            $user = $this->account($role);
            PermissionAssigner::assignByRoles($user, [$role]);
            $this->assertSame(['eoffice.view'], $user->permissions()->pluck('name')->all());
            $this->assertTrue(PermissionAssigner::verifyPermissions($user)['has_correct_permissions']);
            Role::firstOrCreate(['name' => 'admin_banksoal', 'guard_name' => 'web']);
            $user->assignRole('admin_banksoal');
            PermissionAssigner::assignByRoles($user, [$role, 'admin_banksoal']);
            $this->assertEqualsCanonicalizing(['eoffice.view', 'banksoal.view', 'banksoal.edit', 'banksoal.delete'], $user->permissions()->pluck('name')->all());
            $this->assertTrue(PermissionAssigner::verifyPermissions($user)['has_correct_permissions']);
            $user->assignRole('admin_eoffice');
            PermissionAssigner::assignByRoles($user, [$role, 'admin_banksoal', 'admin_eoffice']);
            $this->assertEqualsCanonicalizing(['eoffice.view', 'eoffice.edit', 'eoffice.delete', 'banksoal.view', 'banksoal.edit', 'banksoal.delete'], $user->permissions()->pluck('name')->all());
            $this->assertTrue(PermissionAssigner::verifyPermissions($user)['has_correct_permissions']);
        }
    }

    public function test_existing_student_login_and_intended_url_survive_additional_practicum_role(): void
    {
        $user = $this->account('mahasiswa', true);
        $user->assignRole('admin_praktikum');
        $user->update(['password' => Hash::make('test-only-password')]);
        $user->clearUserCache();
        $this->actingAs($user->fresh());
        $this->assertSame('portal', (new RedirectBasedOnRole)->handle(Request::create('/dashboard'), fn () => response('portal'))->getContent());
        $this->app['auth']->guard()->logout();
        $this->post(route('login'), ['email' => $user->email, 'password' => 'test-only-password'])
            ->assertSessionHasNoErrors()->assertRedirect(route('dashboard'));
        $this->app['auth']->guard()->logout();
        $this->withSession(['url.intended' => url('/profile')])
            ->post(route('login'), ['email' => $user->email, 'password' => 'test-only-password'])
            ->assertSessionHasNoErrors()->assertRedirect(url('/profile'));
    }

    public function test_production_login_accepts_only_practicum_admin_accounts(): void
    {
        $admin = $this->account('admin_praktikum');
        $admin->update(['password' => Hash::make('test-only-password')]);
        $this->app['env'] = 'production';
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->get(route('practicum-admin.login'))->assertOk()->assertSee('Admin Praktikum')->assertDontSee('SUPERADMIN Only');
        $this->withSession(['url.intended' => url('/eoffice/manprak/asprak/pengumuman')])
            ->post(route('practicum-admin.login.store'), ['email' => $admin->email, 'password' => 'test-only-password'])
            ->assertSessionHasNoErrors()->assertSessionMissing('url.intended')->assertRedirect(route('eoffice.manprak.admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_error_page_does_not_trigger_another_access_denial(): void
    {
        $this->actingAs($this->account('admin_praktikum'));
        $middleware = new RedirectBasedOnRole;
        $this->assertSame('error-page', $middleware->handle(Request::create('/error/403'), fn () => response('error-page'))->getContent());
    }

    public function test_cerc_is_an_additional_role_and_does_not_trap_students_in_practicum(): void
    {
        $user = $this->account('mahasiswa', true);
        $user->assignRole(['koordinator_cerc', 'koor_prak', 'asprak']);
        $user->update(['email' => 'cerc-navigation@students.undip.ac.id', 'password' => Hash::make('test-only-password')]);
        $user = $user->fresh();
        $user->clearUserCache();
        $this->actingAs($user);
        $middleware = new RedirectBasedOnRole;
        foreach (['/dashboard', '/eoffice/dashboard'] as $path) {
            $response = $middleware->handle(Request::create($path), fn () => response('portal-accessible'));
            $this->assertSame('portal-accessible', $response->getContent());
        }
        $this->withoutMiddleware(CheckModuleActive::class);
        $this->mock(EOfficeController::class)
            ->shouldReceive('mahasiswaDashboard')->once()->andReturn(response('E-Office mahasiswa'));
        $response = $this->withSession(['session_version' => $user->session_version])->get(route('eoffice.dashboard'));
        $this->assertSame(200, $response->getStatusCode(), $response->headers->get('Location') ?? '');
        $response->assertSee('E-Office mahasiswa');
        $this->app['auth']->guard()->logout();
        $this->post(route('login'), ['email' => $user->email, 'password' => 'test-only-password'])
            ->assertSessionHasNoErrors()->assertRedirect(route('dashboard'));
    }

    public function test_non_admin_cannot_use_practicum_admin_login(): void
    {
        $student = $this->account('mahasiswa', true);
        $student->update(['password' => Hash::make('test-only-password')]);
        $this->post(route('practicum-admin.login.store'), ['email' => $student->email, 'password' => 'test-only-password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_account_command_uses_hidden_password_and_does_not_overwrite_existing_user(): void
    {
        $this->artisan('practicum:create-admin', ['email' => 'admin.praktikum@example.test'])
            ->expectsConfirmation('Buat akun jabatan admin.praktikum@example.test?', 'yes')
            ->expectsQuestion('Password akun', 'Test1234!very-long')
            ->expectsQuestion('Ulangi password', 'Test1234!very-long')
            ->assertExitCode(0);
        $user = User::where('email', 'admin.praktikum@example.test')->firstOrFail();
        $this->assertTrue($user->hasRole('admin_praktikum'));
        $this->assertTrue(Hash::check('Test1234!very-long', $user->password));
        $this->assertStringStartsWith('local:', $user->external_id);
        $this->assertNull($user->last_synced_from_sso);
        $original = $user->password;
        $this->artisan('practicum:create-admin', ['email' => 'admin.praktikum@example.test'])->assertExitCode(1);
        $this->assertSame($original, $user->fresh()->password);
    }

    public function test_new_views_compile_and_routes_are_separated(): void
    {
        foreach (['admin/koordinator-cerc', 'admin/kelola-role', 'admin/pendaftaran-koor', 'admin/pendaftaran-asprak', 'admin/pendaftaran-detail'] as $view) {
            $source = file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$view.'.blade.php'));
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source), TOKEN_PARSE));
        }
        $this->assertContains('role:superadmin|admin_praktikum', Route::getRoutes()->getByName('eoffice.manprak.admin.dashboard')->gatherMiddleware());
        $this->assertContains('role:superadmin|koordinator_cerc', Route::getRoutes()->getByName('eoffice.manprak.cerc.kelola-role.assign')->gatherMiddleware());
    }

    public function test_cerc_and_admin_pages_render_their_own_navigation(): void
    {
        $this->withoutMiddleware(CheckModuleActive::class);
        $cerc = $this->account('koordinator_cerc', true);
        $this->actingAs($cerc)->get(route('eoffice.manprak.cerc.kelola-role.index'))
            ->assertOk()->assertSee('Koordinator CERC')->assertSee('Aktif')->assertDontSee('Arsip')->assertDontSee('Simpan Dosen');
        $admin = $this->account('admin_praktikum');
        $this->actingAs($admin)->get(route('eoffice.manprak.admin.cerc.index'))
            ->assertOk()->assertSee('Penanggung Jawab Aktif')->assertSee('Simpan Penugasan');
        $this->actingAs($cerc)->getJson(route('eoffice.manprak.admin.cerc.index'))->assertForbidden();
    }

    public function test_admin_practicum_detail_uses_shared_identity_and_full_width_members(): void
    {
        session()->flash('success', 'Praktikum berhasil diperbarui.');
        view()->share('errors', new ViewErrorBag);
        $admin = $this->account('admin_praktikum');
        $this->actingAs($admin);
        $practicum = new Praktikum;
        $practicum->forceFill(['id' => 'active', 'nama' => 'Praktikum Detail', 'status' => 'aktif', 'tahun_ajaran' => '2026/2027', 'semester' => 'ganjil', 'created_at' => now()]);
        $practicum->setRelation('dosens', collect([$admin]));
        $practicum->setRelation('koordinator', $admin);
        $practicum->setRelation('matkul', null);
        $html = view('eoffice::manajemen-praktikum.admin.praktikum-detail', [
            'praktikum' => $practicum,
            'praktikans' => new LengthAwarePaginator([], 0, 10),
            'aspraks' => new LengthAwarePaginator([], 21, 10),
            'search' => null, 'totalModul' => 0,
        ])->render();
        $this->assertStringContainsString('mp-detail-summary', $html);
        $this->assertStringContainsString('data-feedback-pending', $html);
        $this->assertStringContainsString('mp-flash mp-flash-success mp-floating-feedback', $html);
        $this->assertStringContainsString('Praktikum berhasil diperbarui.', $html);
        $this->assertStringContainsString('practicum-feedback.js?v=', $html);
        $this->assertStringContainsString('mp-detail-members', $html);
        $this->assertStringContainsString('mp-settings-email', $html);
        $this->assertStringContainsString('>21</span>', $html);
        $this->assertStringContainsString('Belum ada praktikan terdaftar', $html);
    }

    public function test_cerc_dashboard_renders_without_scheduling_tables_and_other_roles_cannot_access_it(): void
    {
        $this->withoutMiddleware(CheckModuleActive::class);
        $cerc = $this->account('koordinator_cerc', true);
        $cerc->forceFill(['name' => 'Elvina Nasywa Ariyani'])->save();
        $this->actingAs($cerc)->get(route('eoffice.manprak.cerc.dashboard'))->assertOk()
            ->assertSee('Dashboard Koordinator CERC')->assertSee('Praktikum Aktif')->assertSee('Belum Ada Koordinator')
            ->assertSee('Pengajuan Menunggu')->assertSee('Kelola Role')->assertSee('Belum Ditunjuk')->assertSee('Jadwal Praktikum Terdekat')
            ->assertSee('0 orang')->assertDontSee('Asisten Aktif')->assertDontSee('>Arsip<', false);
        $html = $this->get(route('eoffice.manprak.cerc.dashboard'))->assertOk()->getContent();
        $this->assertLessThan(strpos($html, 'id="praktikum-aktif"'), strpos($html, 'id="jadwal-terdekat"'));
        $this->assertStringContainsString('<span class="mp-badge primary sm">Koordinator CERC</span>', $html);
        $this->assertStringContainsString('Halo, <strong>Elvina</strong>', $html);
        $this->assertStringContainsString('schedule_per_page=15', $html);
        $student = $this->account('mahasiswa', true);
        $this->actingAs($student)->getJson(route('eoffice.manprak.cerc.dashboard'))->assertForbidden();
        $admin = $this->account('admin_praktikum');
        $this->actingAs($admin)->getJson(route('eoffice.manprak.cerc.dashboard'))->assertForbidden();
    }

    public function test_cerc_staffing_opens_active_list_then_detail_and_rejects_inactive_ids(): void
    {
        $this->withoutMiddleware(CheckModuleActive::class);
        $cerc = $this->account('koordinator_cerc', true);
        $this->actingAs($cerc);
        $url = route('eoffice.manprak.cerc.kelola-role.index');
        $teacher = $this->account('mahasiswa');
        $teacher->update(['name' => 'Dosen Pengampu Contoh']);
        DB::table('eo_prak_praktikum_dosen')->insert(['praktikum_id' => 'active', 'dosen_id' => $teacher->id]);
        DB::table('eo_prak_praktikum')->where('id', 'active')->update(['koor_id' => $cerc->id]);
        $this->get($url)->assertOk()->assertSee('Daftar Praktikum Aktif')->assertSee('Lihat Detail')->assertSee('Kelola Koordinator &amp; Asisten', false)->assertSee('KOORDINATOR PRAKTIKUM')->assertSee('Dosen Pengampu Contoh')->assertSee('2026/2027 / Semester')->assertSee('mp-av', false)->assertDontSee('Pendaftaran Koordinator &amp; Asisten', false)->assertDontSee('Tambah Role')->assertDontSee('Arsip');
        $this->assertFalse(Route::has('eoffice.manprak.cerc.pendaftaran-koor.index'));
        $this->assertFalse(Route::has('eoffice.manprak.cerc.pendaftaran-asprak.approve'));
        $this->get($url.'?q=tidak-ada')->assertOk()->assertSee('Data tidak ditemukan.')->assertDontSee('Lihat Detail');
        AsprakPraktikum::create(['praktikum_id' => 'active', 'user_id' => $cerc->id, 'role' => 'asprak']);
        $this->get($url.'?praktikum_id=active')->assertOk()->assertSee('Anggota Terdaftar')->assertSee('Tambah Role')->assertSee('Cabut Role')->assertSee($cerc->email)->assertDontSee('Pilih Praktikum...')->assertDontSee('Simpan Dosen');
        $this->getJson($url.'?praktikum_id=archived')->assertNotFound();
        $this->getJson($url.'?praktikum_id=unknown')->assertNotFound();
    }

    public function test_staffing_rejects_duplicates_replacement_conflicts_and_suspended_restores(): void
    {
        $cerc = $this->account('koordinator_cerc', true);
        $first = $this->account('mahasiswa', true);
        $second = $this->account('mahasiswa', true);
        $controller = app(KelolRoleController::class);
        $assign = fn ($user, $role) => $this->boundary('eoffice.manprak.cerc.kelola-role.assign', $cerc, [], ['praktikum_id' => 'active', 'user_id' => $user->id, 'role' => $role], fn ($r) => $controller->assignRole($r));
        $assign($first, 'koor');
        try {
            $assign($first, 'koor');
            $this->fail('Duplicate assignment accepted');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('sudah memiliki role', implode(' ', collect($e->errors())->flatten()->all()));
        }
        $old = AsprakPraktikum::where('user_id', $first->id)->where('role', 'koor')->firstOrFail();
        $this->boundary('eoffice.manprak.cerc.kelola-role.revoke', $cerc, ['id' => $old->id], [], fn () => $controller->revokeRole($old->id));
        $assign($second, 'koor');
        try {
            $this->boundary('eoffice.manprak.cerc.kelola-role.restore', $cerc, ['id' => $old->id], [], fn () => $controller->restoreRole($old->id));
            $this->fail('Old coordinator replaced current coordinator');
        } catch (ValidationException $e) {
            $this->assertSame($second->id, Praktikum::find('active')->koor_id);
        }
        $assistant = AsprakPraktikum::where('user_id', $first->id)->where('role', 'asprak')->firstOrFail();
        $assistant->delete();
        $first->update(['suspended_at' => now()]);
        try {
            $this->boundary('eoffice.manprak.cerc.kelola-role.restore', $cerc, ['id' => $assistant->id], [], fn () => $controller->restoreRole($assistant->id));
            $this->fail('Suspended user restored');
        } catch (ValidationException $e) {
            $this->assertTrue($assistant->fresh()->trashed());
        }
    }

    public function test_staffing_orders_active_coordinator_assistants_then_revoked_roles(): void
    {
        $cerc = $this->account('koordinator_cerc', true);
        $this->actingAs($cerc);
        $expected = [];
        foreach ([['koor', false], ['asprak', false], ['koor', true], ['asprak', true]] as [$role,$revoked]) {
            $user = $this->account('mahasiswa', true);
            $record = AsprakPraktikum::create(['praktikum_id' => 'active', 'user_id' => $user->id, 'role' => $role]);
            if ($revoked) {
                $record->delete();
            }$expected[] = $user->id;
        }
        $request = Request::create(route('eoffice.manprak.cerc.kelola-role.index'), 'GET', ['praktikum_id' => 'active']);
        $route = clone Route::getRoutes()->getByName('eoffice.manprak.cerc.kelola-role.index');
        $route->bind($request);
        $request->setRouteResolver(fn () => $route);
        $view = app(KelolRoleController::class)->index($request);
        $this->assertSame($expected, $view->getData()['anggota']->pluck('user_id')->all());
    }

    public function test_cerc_history_soft_delete_is_shared_preserves_rows_and_does_not_delete_new_events(): void
    {
        $admin = $this->account('admin_praktikum');
        $student = $this->account('mahasiswa', true);
        $this->actingAs($admin);
        app(CercCoordinator::class)->replace($student);
        $cutoff = DB::table('eo_audit_logs')->max('id');
        app(CercCoordinator::class)->replace(null);
        $unrelated = DB::table('eo_audit_logs')->insertGetId(['action' => 'UPDATE', 'description' => 'Aktivitas lain', 'created_at' => now()]);
        $count = DB::table('eo_audit_logs')->count();
        $controller = app(CercCoordinatorController::class);
        $controller->destroyHistory(Request::create('/', 'DELETE', ['before_id' => $cutoff]));
        $this->assertSame($count, DB::table('eo_audit_logs')->count());
        $this->assertNotNull(DB::table('eo_audit_logs')->where('id', $cutoff)->value('cerc_deleted_at'));
        $this->assertSame($admin->id, DB::table('eo_audit_logs')->where('id', $cutoff)->value('cerc_deleted_by'));
        $this->assertSame(1, $controller->index(new Request)->getData()['history']->total());
        $other = $this->account('admin_praktikum');
        $this->actingAs($other);
        $this->assertSame(1, $controller->index(new Request)->getData()['history']->total());
        $controller->destroyHistory(Request::create('/', 'DELETE', ['before_id' => $unrelated]));
        $this->assertSame(0, $controller->index(new Request)->getData()['history']->total());
        $this->assertNull(DB::table('eo_audit_logs')->where('id', $unrelated)->value('cerc_deleted_at'));
        $this->assertSame(2, CercCoordinatorHistory::onlyTrashed()->count());
        try {
            $controller->destroyHistory(Request::create('/', 'DELETE', ['ids' => [$unrelated, $cutoff]]));
            $this->fail('Single-row deletion must not be accepted');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('before_id', $e->errors());
        }
        $this->assertNull(DB::table('eo_audit_logs')->where('id', $unrelated)->value('cerc_deleted_at'));
        $this->withoutMiddleware(CheckModuleActive::class);
        $this->actingAs($student)->deleteJson(route('eoffice.manprak.admin.cerc.history.destroy'), ['before_id' => $unrelated])->assertForbidden();
    }

    public function test_unchanged_cerc_assignment_does_not_add_history(): void
    {
        $admin = $this->account('admin_praktikum');
        $student = $this->account('mahasiswa', true);
        $this->actingAs($admin);
        app(CercCoordinator::class)->replace($student);
        $count = DB::table('eo_audit_logs')->count();
        app(CercCoordinator::class)->replace($student);
        $this->assertSame($count, DB::table('eo_audit_logs')->count());
    }

    public function test_cerc_activity_history_renders_snapshots_legacy_entries_and_pagination(): void
    {
        $this->withoutMiddleware(CheckModuleActive::class);
        $admin = $this->account('admin_praktikum');
        $first = $this->account('mahasiswa', true);
        $second = $this->account('mahasiswa', true);
        $first->update(['name' => 'Pemegang Awal']);
        $second->update(['name' => 'Pemegang Pengganti']);
        $this->actingAs($admin);
        $service = app(CercCoordinator::class);
        $service->replace($first);
        $service->replace($second);
        $service->replace(null);
        $first->update(['name' => 'Nama Setelah Diubah']);
        DB::table('eo_audit_logs')->insert(['user_id' => $admin->id, 'action' => 'UPDATE', 'description' => 'Pergantian Koordinator CERC', 'old_data' => json_encode(['user_ids' => [$second->id]]), 'new_data' => json_encode(['user_id' => null]), 'created_at' => now()]);
        DB::table('eo_audit_logs')->insert(['user_id' => $admin->id, 'action' => 'UPDATE', 'description' => 'Aktivitas lain yang tidak terkait', 'created_at' => now()]);
        $response = $this->actingAs($admin)->get(route('eoffice.manprak.admin.cerc.index', ['per_page' => 5]));
        $response->assertOk()->assertSee('Riwayat Aktivitas')->assertSee('Penunjukan')->assertSee('Pergantian')->assertSee('Pencabutan')->assertSee('Pemegang Awal')->assertSee('Pemegang Pengganti')->assertSee($admin->email)->assertDontSee('Aktivitas lain yang tidak terkait');
        $view = app(CercCoordinatorController::class)->index(Request::create('/history', 'GET', ['per_page' => 5]));
        $this->assertSame(4, $view->getData()['history']->total());
        $this->assertSame(5, $view->getData()['history']->perPage());
        $snapshot = json_decode(DB::table('eo_audit_logs')->where('description', 'Pergantian Koordinator CERC')->orderBy('id')->skip(1)->value('old_data'), true);
        $this->assertSame('Pemegang Awal', $snapshot['users'][0]['name']);
    }
}
