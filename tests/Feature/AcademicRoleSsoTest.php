<?php

namespace Tests\Feature;

use App\Models\Lecturer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\SystemModule;
use App\Models\User;
use App\Services\AcademicRoleSynchronizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as MicrosoftUser;
use Mockery;
use Tests\TestCase;

class AcademicRoleSsoTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_profile_gets_exact_global_mahasiswa_role_and_keeps_other_roles(): void
    {
        $this->createCapstoneViewPermission();

        $user = $this->newUser('student@students.undip.ac.id');
        $moduleRole = Role::create([
            'name' => 'mahasiswa',
            'guard_name' => 'web',
            'module' => 'eoffice',
            'is_academic' => true,
        ]);
        $user->roles()->attach($moduleRole->id);

        Student::create([
            'user_id' => $user->id,
            'student_number' => '21120123120029',
            'cohort_year' => 2023,
        ]);

        app(AcademicRoleSynchronizer::class)->sync($user);

        $globalRole = Role::query()
            ->where('name', 'mahasiswa')
            ->where('module', 'global')
            ->firstOrFail();

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $globalRole->id,
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $moduleRole->id,
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
        $this->assertTrue($user->fresh()->can('capstone.view'));
    }

    public function test_lecturer_profile_gets_global_dosen_role(): void
    {
        $user = $this->newUser('lecturer@lecturer.undip.ac.id');
        $staleRole = Role::create([
            'name' => 'mahasiswa',
            'guard_name' => 'web',
            'module' => 'global',
            'is_academic' => true,
        ]);
        $user->roles()->attach($staleRole->id);

        Lecturer::create([
            'user_id' => $user->id,
            'employee_number' => '198001012006041001',
        ]);

        app(AcademicRoleSynchronizer::class)->sync($user);

        $role = Role::query()
            ->where('name', 'dosen')
            ->where('module', 'global')
            ->firstOrFail();

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('model_has_roles', [
            'role_id' => $staleRole->id,
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
    }

    public function test_legacy_capstone_lecturer_profile_does_not_elevate_a_student_role(): void
    {
        $user = $this->newUser('legacy-actor@students.undip.ac.id');
        Student::create([
            'user_id' => $user->id,
            'student_number' => '21120123120079',
            'cohort_year' => 2023,
        ]);
        Lecturer::create([
            'user_id' => $user->id,
            'employee_number' => Lecturer::LEGACY_CAPSTONE_ACTOR_PREFIX.$user->id,
        ]);

        app(AcademicRoleSynchronizer::class)->sync($user);

        $this->assertTrue($user->fresh()->hasRole('mahasiswa'));
        $this->assertFalse($user->fresh()->hasRole('dosen'));
    }

    public function test_sso_rejects_unknown_account_without_creating_it(): void
    {
        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-unknown-1', 'name' => 'Unknown',
            'email' => 'unknown@students.undip.ac.id',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'unknown@students.undip.ac.id']);
        $this->assertGuest();
    }

    public function test_sso_requires_enabled_account_and_rejects_suspended_user(): void
    {
        $user = $this->newUser('disabled@students.undip.ac.id');
        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-disabled-1', 'name' => 'Disabled', 'email' => $user->email,
        ]);

        $response->assertRedirect(route('login', ['sso_error' => 'no_access']));
        $response->assertSessionHas('sso_access_error', 'Anda tidak memiliki akses ke sistem, silahkan hubungi admin.');
        $this->get(route('login', ['sso_error' => 'no_access']))
            ->assertOk()
            ->assertSee('Anda tidak memiliki akses ke sistem, silahkan hubungi admin.');
        $this->assertDatabaseMissing('user_sso_identities', ['object_id' => 'microsoft-disabled-1']);

        $user->forceFill(['sso_enabled' => true])->save();
        $user->update(['suspended_at' => now()]);
        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-suspended-1', 'name' => 'Suspended', 'email' => $user->email,
        ]);
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_allowed_sso_links_verified_identity_without_changing_roles_or_password(): void
    {
        $user = $this->newUser('student@students.undip.ac.id');
        $user->forceFill(['sso_enabled' => true, 'avatar_url' => 'https://cdn.example.test/avatar.jpg'])->save();
        $password = $user->getRawOriginal('password');
        $role = Role::firstOrCreate([
            'name' => 'mahasiswa', 'module' => 'global',
        ], ['guard_name' => 'web', 'is_academic' => true]);
        $user->assignRole($role);

        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-student-1', 'name' => 'Student SSO', 'email' => strtoupper($user->email),
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('user_sso_identities', [
            'user_id' => $user->id,
            'tenant_id' => '11111111-1111-1111-1111-111111111111',
            'object_id' => 'microsoft-student-1',
        ]);
        $this->assertSame($password, $user->fresh()->getRawOriginal('password'));
        $this->assertTrue($user->fresh()->hasRole('mahasiswa'));
        $this->assertSame(1, User::count());
    }

    public function test_linked_identity_survives_microsoft_email_change(): void
    {
        $user = $this->newUser('old@students.undip.ac.id');
        $user->forceFill(['sso_enabled' => true, 'avatar_url' => 'https://cdn.example.test/avatar.jpg'])->save();
        $role = Role::firstOrCreate([
            'name' => 'mahasiswa', 'module' => 'global',
        ], ['guard_name' => 'web', 'is_academic' => true]);
        $user->assignRole($role);
        $user->ssoIdentity()->create([
            'provider' => 'microsoft',
            'tenant_id' => '11111111-1111-1111-1111-111111111111',
            'object_id' => 'microsoft-student-email-change',
            'email' => $user->email,
        ]);

        $this->signInWithMicrosoft([
            'id' => 'microsoft-student-email-change', 'name' => 'Student', 'email' => 'new@students.undip.ac.id',
        ])->assertRedirect(route('dashboard'));

        $this->assertSame('old@students.undip.ac.id', $user->fresh()->email);
        $this->assertDatabaseHas('user_sso_identities', [
            'user_id' => $user->id, 'object_id' => 'microsoft-student-email-change', 'email' => 'new@students.undip.ac.id',
        ]);
    }

    public function test_cached_auth_user_does_not_store_password_or_remember_token(): void
    {
        $user = $this->newUser('cache-safe@example.test');
        $user->forceFill(['remember_token' => 'sensitive-token'])->save();

        $user->cacheUserData();

        $cached = Cache::get("user:{$user->id}:data");
        $this->assertIsArray($cached);
        $this->assertArrayNotHasKey('password', $cached);
        $this->assertArrayNotHasKey('remember_token', $cached);
        $this->assertTrue($cached[User::AUTH_CACHE_HAS_REMEMBER_TOKEN]);
        $this->assertSame($user->id, $cached['id']);
    }

    public function test_capstone_launch_repairs_an_existing_student_without_a_role(): void
    {
        SystemModule::updateOrCreate(
            ['slug' => 'capstone'],
            ['name' => 'Capstone + TA', 'is_active' => true, 'is_maintenance' => false]
        );
        $this->createCapstoneViewPermission();

        $user = $this->newUser('legacy@students.undip.ac.id');
        Student::create([
            'user_id' => $user->id,
            'student_number' => '21120123120030',
            'cohort_year' => 2023,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['session_version' => 0])
            ->withHeader('User-Agent', 'AcademicRoleRepair/1.0')
            ->get('/capstone/launch');

        $response->assertRedirectContains('/auth/exchange?ott=');
        $this->assertTrue(
            $user->roles()
                ->where('roles.name', 'mahasiswa')
                ->where('roles.module', 'global')
                ->exists()
        );
        $this->assertTrue($user->fresh()->can('capstone.view'));
    }

    private function signInWithMicrosoft(array $attributes)
    {
        Http::fake([
            'graph.microsoft.com/*' => Http::response('', 404),
        ]);

        $provider = Mockery::mock();
        $provider->shouldReceive('user')
            ->once()
            ->andReturn(MicrosoftUser::fake($attributes));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('azure')
            ->andReturn($provider);

        config(['services.azure.tenant' => '11111111-1111-1111-1111-111111111111']);
        return $this->get('/auth/microsoft/callback?code=test-code');
    }

    private function createCapstoneViewPermission(): void
    {
        Permission::create([
            'name' => 'capstone.view',
            'guard_name' => 'web',
            'display_name' => 'Lihat Capstone',
            'module' => 'capstone',
        ]);
    }

    private function newUser(string $email): User
    {
        return User::create([
            'external_id' => (string) Str::uuid(),
            'name' => 'Academic Role Test',
            'email' => $email,
            'password' => bcrypt('not-used-for-sso'),
        ]);
    }
}
