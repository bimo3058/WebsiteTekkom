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

    public function test_student_sso_callback_creates_profile_and_assigns_mahasiswa_immediately(): void
    {
        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-student-1',
            'name' => 'Student SSO',
            'email' => 'STUDENT@STUDENTS.UNDIP.AC.ID',
            'surname' => '23120029',
            'onPremisesSamAccountName' => '21120123120029',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::where('email', 'student@students.undip.ac.id')->firstOrFail();

        $this->assertDatabaseHas('students', [
            'user_id' => $user->id,
            'student_number' => '21120123120029',
        ]);
        $this->assertTrue(
            $user->roles()
                ->where('roles.name', 'mahasiswa')
                ->where('roles.module', 'global')
                ->exists()
        );
    }

    public function test_lecturer_sso_callback_creates_profile_and_assigns_dosen_immediately(): void
    {
        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-lecturer-1',
            'name' => 'Lecturer SSO',
            'email' => 'lecturer@lecturer.undip.ac.id',
            'surname' => 'Lecturer',
            'employeeId' => '198001012006041001',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::where('email', 'lecturer@lecturer.undip.ac.id')->firstOrFail();

        $this->assertDatabaseHas('lecturers', [
            'user_id' => $user->id,
            'employee_number' => '198001012006041001',
        ]);
        $this->assertTrue(
            $user->roles()
                ->where('roles.name', 'dosen')
                ->where('roles.module', 'global')
                ->exists()
        );
    }

    public function test_warm_sso_login_skips_identity_role_and_avatar_resynchronization(): void
    {
        $lastSynced = now()->subMinute()->startOfSecond();
        $ssoData = [
            'id' => 'microsoft-warm-student',
            'email' => 'warm@students.undip.ac.id',
            'name' => 'Warm Student',
        ];
        $user = User::create([
            'external_id' => $ssoData['id'],
            'name' => $ssoData['name'],
            'email' => $ssoData['email'],
            'password' => bcrypt('not-used-for-sso'),
            'sso_data' => $ssoData,
            'last_synced_from_sso' => $lastSynced,
            'avatar_url' => 'https://cdn.example.test/avatar.jpg',
        ]);
        Student::create([
            'user_id' => $user->id,
            'student_number' => '21120123120031',
            'cohort_year' => 2023,
        ]);
        app(AcademicRoleSynchronizer::class)->sync($user);
        app(AcademicRoleSynchronizer::class)->markSynced($user);

        $response = $this->signInWithMicrosoft([
            'id' => $ssoData['id'],
            'name' => $ssoData['name'],
            'email' => strtoupper($ssoData['email']),
            'onPremisesSamAccountName' => '21120123120031',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue($user->fresh()->last_synced_from_sso->equalTo($lastSynced));
        Http::assertNothingSent();
    }

    public function test_sso_claims_the_matching_neon_student_without_creating_a_duplicate(): void
    {
        $legacyUser = User::create([
            'external_id' => 'neon:user:501',
            'name' => '  Budi   Santoso ',
            'email' => 'neon-user-501@example.invalid',
            'password' => bcrypt('legacy-password'),
        ]);
        $student = Student::create([
            'user_id' => $legacyUser->id,
            'student_number' => 'legacy-501',
            'cohort_year' => 2023,
        ]);
        $originalUserId = $legacyUser->id;
        $originalStudentId = $student->id;

        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-budi-501',
            'name' => 'budi santoso',
            'email' => 'budi.santoso@students.undip.ac.id',
            'onPremisesSamAccountName' => '21120123120051',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $originalUserId,
            'external_id' => 'microsoft-budi-501',
            'email' => 'budi.santoso@students.undip.ac.id',
        ]);
        $this->assertDatabaseHas('students', [
            'id' => $originalStudentId,
            'user_id' => $originalUserId,
            'student_number' => '21120123120051',
        ]);
    }

    public function test_sso_claims_a_neon_student_by_nim_when_the_display_name_changed(): void
    {
        $legacyUser = User::create([
            'external_id' => 'neon:user:502',
            'name' => 'Legacy Display Name',
            'email' => 'neon-user-502@example.invalid',
            'password' => bcrypt('legacy-password'),
        ]);
        Student::create([
            'user_id' => $legacyUser->id,
            'student_number' => '21120123120052',
            'cohort_year' => 2023,
        ]);

        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-student-502',
            'name' => 'Current Display Name',
            'email' => 'current.name@students.undip.ac.id',
            'onPremisesSamAccountName' => '21120123120052',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $legacyUser->id,
            'external_id' => 'microsoft-student-502',
            'name' => 'Current Display Name',
        ]);
    }

    public function test_sso_claims_the_matching_neon_lecturer_without_replacing_its_profile(): void
    {
        $legacyUser = User::create([
            'external_id' => 'neon:user:505',
            'name' => 'Dosen Legacy',
            'email' => 'neon-user-505@example.invalid',
            'password' => bcrypt('legacy-password'),
        ]);
        $lecturer = Lecturer::create([
            'user_id' => $legacyUser->id,
            'employee_number' => '198001012006041005',
        ]);

        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-lecturer-505',
            'name' => 'Nama Dosen SSO',
            'email' => 'nama.dosen@lecturer.undip.ac.id',
            'employeeId' => '198001012006041005',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $legacyUser->id,
            'external_id' => 'microsoft-lecturer-505',
            'name' => 'Nama Dosen SSO',
        ]);
        $this->assertDatabaseHas('lecturers', [
            'id' => $lecturer->id,
            'user_id' => $legacyUser->id,
            'employee_number' => '198001012006041005',
        ]);
    }

    public function test_lecturer_sso_replaces_a_legacy_actor_number_with_the_real_employee_number(): void
    {
        $legacyUser = User::create([
            'external_id' => 'neon:user:506',
            'name' => 'Dosen Aktor Legacy',
            'email' => 'neon-user-506@example.invalid',
            'password' => bcrypt('legacy-password'),
        ]);
        $lecturer = Lecturer::create([
            'user_id' => $legacyUser->id,
            'employee_number' => Lecturer::LEGACY_CAPSTONE_ACTOR_PREFIX.$legacyUser->id,
        ]);

        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-lecturer-506',
            'name' => 'Dosen Aktor Legacy',
            'email' => 'dosen.aktor@lecturer.undip.ac.id',
            'employeeId' => '198001012006041006',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('lecturers', [
            'id' => $lecturer->id,
            'user_id' => $legacyUser->id,
            'employee_number' => '198001012006041006',
        ]);
        $this->assertTrue($legacyUser->fresh()->hasRole('dosen'));
    }

    public function test_sso_does_not_create_or_claim_an_account_when_the_imported_name_is_ambiguous(): void
    {
        foreach ([503, 504] as $legacyId) {
            $user = User::create([
                'external_id' => "neon:user:{$legacyId}",
                'name' => 'Nama Mahasiswa Sama',
                'email' => "neon-user-{$legacyId}@example.invalid",
                'password' => bcrypt('legacy-password'),
            ]);
            Student::create([
                'user_id' => $user->id,
                'student_number' => "legacy-{$legacyId}",
                'cohort_year' => 2023,
            ]);
        }

        $response = $this->signInWithMicrosoft([
            'id' => 'microsoft-ambiguous-student',
            'name' => 'Nama Mahasiswa Sama',
            'email' => 'ambiguous@students.undip.ac.id',
            'onPremisesSamAccountName' => '21120123129999',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseMissing('users', [
            'external_id' => 'microsoft-ambiguous-student',
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

        return $this->get('/auth/microsoft/callback');
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
