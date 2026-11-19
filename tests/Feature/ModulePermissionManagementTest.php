<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemModule;
use App\Models\User;
use App\Services\PermissionAssigner;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModulePermissionManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'module_permissions_test',
            'database.connections.module_permissions_test' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true,
            ],
        ]);
        $paths = array_values(array_filter(
            glob(database_path('migrations/*.php')),
            fn ($path) => preg_match('/(?:users|roles|permissions|spatie_permission|students|lecturers|system_modules|audit_logs|import_statuses)_table/', basename($path))
                && ! str_contains(basename($path), 'optimize_users'),
        ));
        sort($paths);
        foreach ($paths as $path) {
            (require $path)->up();
        }

        foreach (['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'] as $module) {
            foreach (['view', 'edit', 'delete'] as $action) {
                Permission::create(['name' => "$module.$action", 'module' => $module, 'guard_name' => 'web']);
            }
            SystemModule::create([
                'name' => $module,
                'slug' => ['banksoal' => 'bank_soal', 'kemahasiswaan' => 'manajemen_mahasiswa'][$module] ?? $module,
                'is_active' => false,
            ]);
        }
        // These tests cover form/controller behavior; route authorization is independent.
        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureMicrosoftSsoSession::class,
            \App\Http\Middleware\RedirectBasedOnRole::class,
            \Spatie\Permission\Middleware\RoleMiddleware::class,
        ]);
        $this->actingAs(User::factory()->create());
    }

    public static function moduleAdmins(): array
    {
        return array_map(fn ($module) => [$module], ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan']);
    }

    #[DataProvider('moduleAdmins')]
    public function test_switching_to_module_admin_revokes_other_permissions_even_if_submitted(string $module): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->role('mahasiswa'));
        $user->syncPermissions(Permission::all());

        $this->patch(route('superadmin.users.update', $user), [
            'name' => $user->name, 'email' => $user->email,
            'roles' => [$this->role('admin_'.$module)->name],
            'permissions' => Permission::pluck('name')->all(),
        ])->assertSessionHasNoErrors()->assertSessionMissing('error')->assertSessionHas('success');

        $this->assertSame(["$module.delete", "$module.edit", "$module.view"], $user->fresh()->permissions->pluck('name')->sort()->values()->all());
        $this->assertTrue($user->fresh()->can("$module.view"));
        $other = $module === 'capstone' ? 'eoffice' : 'capstone';
        $this->assertFalse($user->fresh()->can("$other.view"));
    }

    public function test_role_only_update_revokes_old_permissions_and_grants_admin_defaults(): void
    {
        $user = User::factory()->create();
        $user->syncPermissions(Permission::all());

        $this->patch(route('superadmin.users.update-roles', $user), [
            'roles' => [$this->role('admin_capstone')->id],
        ])->assertSessionHasNoErrors()->assertSessionMissing('error')->assertSessionHas('success');

        $this->assertCount(3, $user->fresh()->permissions);
        $this->assertTrue(PermissionAssigner::verifyPermissions($user->fresh())['has_correct_permissions']);
    }

    public static function academicRoles(): array
    {
        return [['dosen'], ['mahasiswa']];
    }

    #[DataProvider('academicRoles')]
    public function test_selecting_main_academic_role_automatically_grants_all_module_access(string $role): void
    {
        $user = User::factory()->create();
        $this->patch(route('superadmin.users.update', $user), [
            'name' => $user->name, 'email' => $user->email, 'roles' => [$this->role($role)->name],
        ])->assertSessionHasNoErrors()->assertSessionMissing('error')->assertSessionHas('success');

        $this->assertCount(12, $user->fresh()->permissions);
    }

    public function test_scoped_admin_overrides_academic_inheritance_and_supports_multiple_admin_modules(): void
    {
        $academicRole = $this->role('dosen');
        $academicRole->syncPermissions(Permission::all());
        $user = User::factory()->create();
        $user->roles()->sync([$academicRole->id, $this->role('admin_capstone')->id, $this->role('admin_eoffice')->id]);

        PermissionAssigner::assignByRoles($user, ['dosen', 'admin_capstone', 'admin_eoffice']);

        $this->assertCount(6, $user->fresh()->permissions);
        $this->assertCount(6, $user->fresh()->getAllPermissions());
        $this->assertFalse($user->fresh()->hasPermissionTo('banksoal.view'));
        $this->assertFalse($user->fresh()->can('banksoal.view'));
        $this->assertTrue($user->fresh()->can('capstone.view'));
        $this->assertTrue($user->fresh()->can('eoffice.view'));
    }

    public function test_disabled_modules_permissions_can_be_selected_and_saved(): void
    {
        $user = User::factory()->create();
        $role = $this->role('admin_capstone');
        $user->assignRole($role);

        $this->patch(route('superadmin.users.update', $user), [
            'name' => $user->name, 'email' => $user->email,
            'roles' => [$role->name], 'permissions' => ['capstone.view', 'capstone.edit'],
        ])->assertSessionHasNoErrors()->assertSessionMissing('error')->assertSessionHas('success');

        $this->assertEqualsCanonicalizing(['capstone.view', 'capstone.edit'], $user->fresh()->permissions->pluck('name')->all());
        $this->get(route('superadmin.users.edit', $user))
            ->assertOk()->assertSee('Nonaktif')->assertSee('userPermissionEditor');
    }

    public function test_bulk_updates_cannot_restore_other_modules_for_scoped_admins(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->role('admin_capstone'));
        $user->syncPermissions(Permission::all());

        $this->post(route('superadmin.permissions.bulk-update'), [
            'role' => 'admin_capstone', 'user_ids' => [$user->id],
            'permissions' => Permission::pluck('name')->all(),
        ])->assertSessionHasNoErrors()->assertSessionMissing('error')->assertSessionHas('success');

        $this->assertCount(3, $user->fresh()->permissions);
    }

    public function test_permission_card_accepts_role_ids_and_applies_scope(): void
    {
        $user = User::factory()->create();
        $this->post(route('superadmin.users.update-permissions', $user), [
            'roles' => [$this->role('admin_eoffice')->id],
            'permissions' => Permission::pluck('name')->all(),
        ])->assertSessionHasNoErrors()->assertSessionMissing('error')->assertSessionHas('success');

        $this->assertSame(['eoffice'], $user->fresh()->permissions->pluck('module')->unique()->all());
    }

    public function test_manually_adjusted_academic_permissions_remain_editable(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->role('dosen'));
        $this->patch(route('superadmin.users.update', $user), [
            'name' => $user->name, 'email' => $user->email,
            'roles' => ['dosen'], 'permissions' => ['banksoal.view'],
        ])->assertSessionHasNoErrors()->assertSessionMissing('error');

        $this->assertSame(['banksoal.view'], $user->fresh()->permissions->pluck('name')->all());
    }

    public function test_academic_defaults_can_be_adjusted_before_the_first_save(): void
    {
        $user = User::factory()->create();
        $this->patch(route('superadmin.users.update', $user), [
            'name' => $user->name, 'email' => $user->email,
            'roles' => [$this->role('mahasiswa')->name],
            'permissions_present' => '1', 'permissions' => ['capstone.view'],
        ])->assertSessionHasNoErrors()->assertSessionMissing('error');

        $this->assertSame(['capstone.view'], $user->fresh()->permissions->pluck('name')->all());
    }

    public function test_role_permission_page_keeps_inactive_module_inputs_enabled_within_admin_scope(): void
    {
        $this->role('admin_capstone');
        $html = $this->get(route('superadmin.permissions.show', ['role' => 'admin_capstone']))->assertOk()->getContent();
        $document = new \DOMDocument;
        @$document->loadHTML($html);
        $xpath = new \DOMXPath($document);

        $this->assertSame(0, $xpath->query('//input[@value="capstone.view"]/@disabled')->length);
        $this->assertSame(1, $xpath->query('//input[@value="banksoal.view"]/@disabled')->length);
        $this->assertSame(1, $xpath->query('//input[@data-module-target="bulk_banksoal"]/@disabled')->length);
    }

    public function test_academic_role_permission_page_prefills_all_inactive_modules(): void
    {
        $this->role('mahasiswa');
        $html = $this->get(route('superadmin.permissions.show', ['role' => 'mahasiswa']))->assertOk()->getContent();
        $document = new \DOMDocument;
        @$document->loadHTML($html);
        $xpath = new \DOMXPath($document);

        $this->assertSame(12, $xpath->query('//input[@class="perm-checkbox"]/@checked')->length);
        $this->assertSame(0, $xpath->query('//input[@class="perm-checkbox"]/@disabled')->length);
    }

    public function test_role_default_bulk_update_filters_scope_and_ignores_unknown_permissions(): void
    {
        $role = $this->role('admin_capstone');
        $this->post(route('superadmin.permissions.bulk-update'), [
            'role' => $role->name,
            'permissions' => ['capstone.view', 'banksoal.view', 'capstone.unknown'],
        ])->assertSessionHasNoErrors()->assertSessionMissing('error')->assertSessionHas('success');

        $this->assertSame(['capstone.view'], $role->fresh()->permissions->pluck('name')->all());
    }

    public function test_superadmin_defaults_remain_unrestricted(): void
    {
        $user = User::factory()->create();
        $user->roles()->sync([$this->role('superadmin')->id, $this->role('admin_capstone')->id]);
        PermissionAssigner::assignByRoles($user, ['superadmin', 'admin_capstone']);

        $this->assertCount(12, $user->fresh()->getAllPermissions());
        $this->assertTrue($user->fresh()->can('banksoal.view'));
    }

    private function role(string $name): Role
    {
        return Role::firstOrCreate(['name' => $name, 'module' => 'global'], ['guard_name' => 'web']);
    }
}
