<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SystemModule;
use App\Models\User;
use App\Services\PermissionAssigner;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Security expectations for individually selected module permissions. */
class ModulePermissionEnforcementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'permission_enforcement_test',
            'database.connections.permission_enforcement_test' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true,
            ],
            'services.supabase.url' => 'https://supabase.test',
            'services.supabase.key' => 'test-key',
            'services.supabase.bucket' => 'test-bucket',
        ]);
        Http::preventStrayRequests();

        // Run only the core identity migrations against this private connection.
        $paths = array_values(array_filter(
            glob(database_path('migrations/*.php')),
            fn ($path) => preg_match('/(?:users|roles|permissions|spatie_permission|system_modules|audit_logs|import_statuses)_table/', basename($path))
                && ! str_contains(basename($path), 'optimize_users'),
        ));
        sort($paths);
        foreach ($paths as $path) {
            (require $path)->up();
        }
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);

        foreach (['bank_soal', 'capstone', 'eoffice', 'manajemen_mahasiswa'] as $slug) {
            SystemModule::create(['name' => $slug, 'slug' => $slug, 'is_active' => true]);
        }

        // Minimal business fixtures. Routes, controllers, policies and permission
        // middleware remain the application's actual implementations.
        Schema::create('bs_cpl', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->text('deskripsi');
        });
        Schema::create('capstone_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('eo_prak_matkul_praktikum', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->integer('sks');
            $table->integer('semester');
            $table->timestamps();
        });
        Schema::create('mk_pengumuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('judul');
            $table->text('konten');
            $table->string('kategori')->nullable();
            $table->string('target_audience');
            $table->string('status_publish');
            $table->timestamps();
        });
        Schema::create('mk_repo_mulmed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengumuman_id')->nullable()->constrained('mk_pengumuman')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public static function individualPermissions(): array
    {
        $cases = [];
        foreach (['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'] as $module) {
            foreach (['view', 'edit', 'delete'] as $action) {
                $cases["$module.$action"] = [$module, $action];
            }
        }

        return $cases;
    }

    #[DataProvider('individualPermissions')]
    public function test_a_single_direct_permission_does_not_grant_other_actions(string $module, string $action): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo("$module.$action");

        foreach (['view', 'edit', 'delete'] as $candidate) {
            $this->assertSame($candidate === $action, $user->can("$module.$candidate"));
        }
    }

    public static function moduleAdmins(): array
    {
        return [
            'Bank Soal' => ['banksoal'],
            'Capstone' => ['capstone'],
            'EOffice' => ['eoffice'],
            'Kemahasiswaan' => ['kemahasiswaan'],
        ];
    }

    #[DataProvider('moduleAdmins')]
    public function test_selecting_only_view_should_revoke_effective_edit_for_module_admin(string $module): void
    {
        $user = User::factory()->create();
        $role = Role::findByName('admin_'.$module, 'web');
        $user->assignRole($role);

        // Keep the real seeded role permissions to expose inherited access.
        PermissionAssigner::syncSelection($user, [$role->name], ["$module.view"]);
        $user = $user->fresh();

        $this->assertSame(["$module.view"], $user->permissions->pluck('name')->all());
        $this->assertTrue($user->can("$module.view"));
        $this->assertFalse($user->can("$module.edit"), 'Edit is still inherited from the module admin role.');
    }

    public static function writeRequests(): array
    {
        $cases = [];
        foreach (['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'] as $module) {
            foreach (['edit', 'delete'] as $action) {
                $cases["$module.$action"] = [$module, $action];
            }
        }

        return $cases;
    }

    #[DataProvider('writeRequests')]
    public function test_view_only_user_should_be_forbidden_from_writing(string $module, string $action): void
    {
        $user = $this->endpointActor($module, ["$module.view"]);
        [$method, $url, $payload, $table, $original] = $this->writeFixture($module, $action, $user);

        $response = $this->json($method, $url, $payload);

        // Include actual persisted state in failures to distinguish a rejected
        // form from a successful unauthorized database mutation.
        $unchanged = DB::table($table)->where($original)->exists();
        $this->assertSame(403, $response->status(),
            "$method $url returned {$response->status()}. Original record unchanged: ".($unchanged ? 'yes' : 'NO'));
        $this->assertTrue($unchanged);
    }

    #[DataProvider('writeRequests')]
    public function test_matching_permission_allows_the_same_write_request(string $module, string $action): void
    {
        $user = $this->endpointActor($module, ["$module.view", "$module.$action"]);
        [$method, $url, $payload, $table, $original] = $this->writeFixture($module, $action, $user);

        $response = $this->json($method, $url, $payload);

        $this->assertContains($response->status(), [200, 302], $response->getContent());
        $response->assertSessionMissing('error');
        $this->assertFalse(DB::table($table)->where($original)->exists(), 'The request must actually change the fixture.');
    }

    private function endpointActor(string $module, array $permissions): User
    {
        $roleName = $module === 'kemahasiswaan' ? 'dosen' : 'admin_'.$module;
        $role = Role::findByName($roleName, 'web');
        // Isolate endpoint enforcement from the separately tested inheritance.
        $role->syncPermissions([]);
        $user = User::factory()->create();
        $user->assignRole($role);
        $user->syncPermissions($permissions);

        if ($module === 'capstone') {
            Sanctum::actingAs($user, ['capstone:access']);
            $this->withHeader('X-Capstone-Role', 'admin');
        } else {
            $this->actingAs($user, 'web');
        }

        return $user;
    }

    private function writeFixture(string $module, string $action, User $user): array
    {
        $fixtures = [
            'banksoal' => [
                'bs_cpl', ['id' => 1, 'kode' => 'CPL-1', 'deskripsi' => 'Original'],
                '/api/v1/bank-soal/admin/api/cpl/1',
                ['kode' => 'CPL-1', 'deskripsi' => 'Changed'],
            ],
            'capstone' => [
                'capstone_periods', ['id' => 1, 'name' => 'Original', 'is_active' => false, 'deleted_at' => null],
                '/api/capstone/admin/periods/1', ['name' => 'Changed'],
            ],
            'eoffice' => [
                'eo_prak_matkul_praktikum', ['id' => 1, 'kode' => 'TEST1', 'nama' => 'Original', 'sks' => 1, 'semester' => 1],
                '/eoffice/manprak/admin/matkul-praktikum/1',
                ['kode' => 'TEST1', 'nama' => 'Changed', 'sks' => 1, 'semester' => 1],
            ],
            'kemahasiswaan' => [
                'mk_pengumuman', [
                    'id' => 1, 'user_id' => $user->id, 'judul' => 'Original',
                    'konten' => str_repeat('Original content. ', 5),
                    'target_audience' => 'all', 'status_publish' => 'draft',
                ],
                '/manajemen-mahasiswa/pengumuman/1',
                [
                    'judul' => 'Changed', 'konten' => str_repeat('Changed content. ', 5),
                    'target_audience' => 'all', 'status_publish' => 'draft',
                ],
            ],
        ];
        [$table, $original, $url, $payload] = $fixtures[$module];
        DB::table($table)->insert($original);

        return [$action === 'delete' ? 'DELETE' : 'PUT', $url, $action === 'delete' ? [] : $payload, $table, $original];
    }
}
