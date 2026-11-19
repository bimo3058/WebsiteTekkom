<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SsoAccountResolver;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as MicrosoftUser;
use Mockery;
use Tests\TestCase;

class SsoAccountProvisioningTest extends TestCase
{
    private const TENANT = '11111111-1111-1111-1111-111111111111';

    protected function setUp(): void
    {
        parent::setUp();

        // Exercise the real account schema without unrelated module migrations.
        config(['database.connections.sso_provisioning_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('sso_provisioning_test');
        foreach ([
            '2026_02_15_111353_create_users_table.php',
            '2026_02_20_085615_add_password_to_users_table.php',
            '2026_10_30_000001_add_sso_access_to_users_table.php',
            '2026_02_20_014817_create_roles_table.php',
            '2026_02_20_014842_create_user_roles_table.php',
            '2026_03_25_123725_create_permissions_table.php',
            '2026_03_25_123732_create_role_permissions_table.php',
            '2026_03_25_123825_create_user_permissions_table.php',
            '2026_03_30_140608_add_is_academic_to_roles_table.php',
            '2026_05_12_000001_migrate_to_spatie_permission_tables.php',
        ] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_online')->default(false);
            $table->unsignedInteger('session_version')->default(0);
            $table->string('avatar_url')->nullable();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('module');
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('description');
            $table->timestamp('created_at');
        });
        $this->mock(NotificationService::class, fn ($mock) => $mock->shouldReceive('notify')->andReturnNull());
    }

    public function test_first_login_creates_enabled_account_and_repeat_login_reuses_identity(): void
    {
        $resolver = app(SsoAccountResolver::class);
        $result = $resolver->resolve('new-object', ' NEW@students.undip.ac.id ', self::TENANT, 'New Student');
        $user = $result['user'];

        $this->assertTrue($result['linked']);
        $this->assertTrue($user->sso_enabled);
        $this->assertNotNull($user->sso_enabled_at);
        $this->assertNull($user->sso_enabled_by);
        $this->assertNull($user->password);
        $this->assertSame('New Student', $user->name);
        $this->assertDatabaseHas('user_sso_identities', [
            'user_id' => $user->id, 'object_id' => 'new-object',
            'tenant_id' => self::TENANT, 'email' => 'new@students.undip.ac.id',
        ]);

        $again = $resolver->resolve('new-object', 'renamed@students.undip.ac.id', self::TENANT, 'Renamed');
        $this->assertSame($user->id, $again['user']->id);
        $this->assertSame('identity', $again['match_type']);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('user_sso_identities', 1);
    }

    public function test_disabled_existing_account_is_not_reenabled_or_linked(): void
    {
        $user = $this->existingUser();

        $result = app(SsoAccountResolver::class)->resolve('new-object', $user->email, self::TENANT);

        $this->assertSame($user->id, $result['user']->id);
        $this->assertFalse($user->fresh()->sso_enabled);
        $this->assertFalse($result['linked']);
        $this->assertDatabaseCount('user_sso_identities', 0);
    }

    public function test_disabling_a_provisioned_account_survives_the_next_login(): void
    {
        $resolver = app(SsoAccountResolver::class);
        $user = $resolver->resolve('new-object', 'new@students.undip.ac.id', self::TENANT)['user'];
        $user->update(['sso_enabled' => false]);

        $result = $resolver->resolve('new-object', $user->email, self::TENANT);

        $this->assertFalse($result['user']->sso_enabled);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_repeat_login_does_not_restore_roles_removed_by_admin(): void
    {
        $resolver = app(SsoAccountResolver::class);
        $user = $resolver->resolve('new-object', 'new@students.undip.ac.id', self::TENANT)['user'];
        $user->syncRoles([]);

        $result = $resolver->resolve('new-object', $user->email, self::TENANT);

        $this->assertCount(0, $result['user']->roles);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_suspended_existing_account_is_not_linked(): void
    {
        $user = $this->existingUser();
        $user->update(['sso_enabled' => true, 'suspended_at' => now()]);

        $result = app(SsoAccountResolver::class)->resolve('new-object', $user->email, self::TENANT);

        $this->assertTrue($result['user']->isSuspended());
        $this->assertFalse($result['linked']);
        $this->assertDatabaseCount('user_sso_identities', 0);
    }

    public function test_deleted_account_is_not_recreated(): void
    {
        $user = $this->existingUser();
        $user->delete();

        $result = app(SsoAccountResolver::class)->resolve('new-object', $user->email, self::TENANT);

        $this->assertNull($result['user']);
        $this->assertSame(1, User::withTrashed()->count());
        $this->assertDatabaseCount('user_sso_identities', 0);
    }

    public function test_invalid_principals_cannot_create_accounts(): void
    {
        foreach ([
            ['object', 'outside@example.com', self::TENANT],
            ['object', 'user@students.undip.ac.id.evil.test', self::TENANT],
            ['object', '@students.undip.ac.id', self::TENANT],
            ['', 'student@students.undip.ac.id', self::TENANT],
            ['object', 'student@students.undip.ac.id', ''],
        ] as $principal) {
            $this->assertNull(app(SsoAccountResolver::class)->resolve(...$principal)['user']);
        }
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('user_sso_identities', 0);
    }

    public function test_new_student_can_sign_in_with_global_role(): void
    {
        Role::create(['name' => 'mahasiswa', 'module' => 'eoffice', 'guard_name' => 'web']);

        $this->signIn('student@students.undip.ac.id')->assertRedirect(route('dashboard'));

        $user = User::firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->sso_enabled);
        $this->assertNotNull($user->sso_enabled_at);
        $this->assertSame('Microsoft Name', $user->name);
        $this->assertSame(['mahasiswa'], $user->roles->pluck('name')->all());
        $this->assertSame(['global'], $user->roles->pluck('module')->all());
        $this->assertDatabaseHas('audit_logs', ['user_id' => $user->id, 'action' => 'LOGIN']);
    }

    public function test_new_lecturer_can_sign_in_with_dosen_role(): void
    {
        $this->signIn('lecturer@lecturer.undip.ac.id')->assertRedirect(route('dashboard'));

        $user = User::firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->sso_enabled);
        $this->assertSame(['dosen'], $user->roles->pluck('name')->all());
    }

    public function test_general_undip_account_is_enabled_but_waits_for_admin_role(): void
    {
        $this->signIn('staff@undip.ac.id')->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $user = User::firstOrFail();
        $this->assertTrue($user->sso_enabled);
        $this->assertCount(0, $user->roles);
        $this->assertGuest();
    }

    public function test_callback_still_denies_disabled_account(): void
    {
        $user = $this->existingUser();
        $this->signIn($user->email)->assertRedirect(route('login', ['sso_error' => 'no_access']));

        $this->assertFalse($user->fresh()->sso_enabled);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('user_sso_identities', 0);
        $this->assertGuest();
    }

    public function test_callback_denies_outside_domain_without_creating_account(): void
    {
        $this->signIn('outside@example.com')->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    private function signIn(string $email)
    {
        Http::fake(['graph.microsoft.com/*' => Http::response('', 404)]);
        $provider = Mockery::mock();
        $provider->shouldReceive('user')->once()->andReturn(MicrosoftUser::fake([
            'id' => 'callback-object', 'email' => $email, 'name' => 'Microsoft Name',
        ]));
        Socialite::shouldReceive('driver')->once()->with('azure')->andReturn($provider);
        config(['services.azure.tenant' => self::TENANT]);

        return $this->get('/auth/microsoft/callback?code=test-code');
    }

    private function existingUser(): User
    {
        return User::create([
            'external_id' => 'existing-student', 'name' => 'Existing Student',
            'email' => 'existing@students.undip.ac.id', 'sso_enabled' => false,
        ]);
    }
}
