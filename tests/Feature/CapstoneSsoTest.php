<?php

namespace Tests\Feature;

use App\Models\Lecturer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\SystemModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Capstone\Models\Period;
use Tests\TestCase;

class CapstoneSsoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SystemModule::updateOrCreate(
            ['slug' => 'capstone'],
            ['name' => 'Capstone + TA', 'is_active' => true, 'is_maintenance' => false]
        );
    }

    public function test_launch_exchanges_one_time_ticket_for_current_student(): void
    {
        $user = $this->capstoneUser('mahasiswa');
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => '24060120120001',
            'cohort_year' => 2020,
        ]);
        $launch = $this->actingAs($user)
            ->withSession(['session_version' => 0])
            ->withHeader('User-Agent', 'CapstoneTestBrowser/1.0')
            ->get('/capstone/launch');

        $launch->assertRedirectContains('http://localhost:3000/auth/exchange?ott=');
        parse_str((string) parse_url($launch->headers->get('Location'), PHP_URL_QUERY), $query);

        $exchange = $this->withHeader('User-Agent', 'CapstoneTestBrowser/1.0')
            ->postJson('/api/capstone/auth/exchange', ['ott' => $query['ott']]);

        $exchange->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.active_role', 'mahasiswa')
            ->assertJsonPath('data.user.student_id', $student->id)
            ->assertJsonPath('data.user.nim', '24060120120001')
            ->assertJsonPath('data.logout.url', route('capstone.logout'));

        $this->assertNotEmpty($exchange->json('data.access_token'));
        $this->assertSame(1, PersonalAccessToken::where('tokenable_id', $user->id)->where('name', 'capstone-fe')->count());

        $this->withHeader('User-Agent', 'CapstoneTestBrowser/1.0')
            ->postJson('/api/capstone/auth/exchange', ['ott' => $query['ott']])
            ->assertUnauthorized();
    }

    public function test_ticket_is_bound_to_browser_and_expires(): void
    {
        $user = $this->capstoneUser('dosen');
        Lecturer::create([
            'user_id' => $user->id,
            'employee_number' => '198001012006041001',
        ]);

        $mismatchTicket = $this->launchTicket($user, 'Browser-A/1.0');
        $this->withHeader('User-Agent', 'Browser-B/1.0')
            ->postJson('/api/capstone/auth/exchange', ['ott' => $mismatchTicket])
            ->assertUnauthorized();

        $expiredTicket = $this->launchTicket($user, 'Browser-A/1.0');
        $this->travel(61)->seconds();
        $this->withHeader('User-Agent', 'Browser-A/1.0')
            ->postJson('/api/capstone/auth/exchange', ['ott' => $expiredTicket])
            ->assertUnauthorized();
    }

    public function test_user_without_capstone_permission_is_rejected_before_ticket_creation(): void
    {
        $role = Role::create(['name' => 'mahasiswa', 'guard_name' => 'web', 'module' => 'global', 'is_academic' => true]);
        $user = $this->newUser('blocked@example.test');
        $user->assignRole($role);
        Student::create(['user_id' => $user->id, 'student_number' => 'BLOCKED-01', 'cohort_year' => 2020]);

        $this->actingAs($user)
            ->withSession(['session_version' => 0])
            ->get('/capstone/launch')
            ->assertForbidden();

        $this->assertFalse(Cache::has('capstone:ott:*'));
    }

    public function test_student_without_capstone_data_can_enter_see_empty_dashboard_and_register(): void
    {
        // Pastikan users.id dan students.id tidak kebetulan sama. Seluruh tabel
        // capstone_* harus menyimpan ID profil students, bukan ID auth users.
        $this->newUser('student-profile-id-offset@example.test');
        $user = $this->capstoneUser('mahasiswa');
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => 'NOT-IN-CAPSTONE',
            'cohort_year' => 2020,
        ]);
        $this->assertNotSame($user->id, $student->id);
        $ticket = $this->launchTicket($user, 'NewStudentBrowser/1.0');

        $exchange = $this->withHeader('User-Agent', 'NewStudentBrowser/1.0')
            ->postJson('/api/capstone/auth/exchange', ['ott' => $ticket]);

        $exchange->assertOk();
        $token = $exchange->json('data.access_token');

        $this->withToken($token)
            ->withHeader('X-Capstone-Role', 'mahasiswa')
            ->getJson('/api/capstone/mahasiswa/dashboard')
            ->assertOk()
            ->assertJsonPath('has_group', false)
            ->assertJsonPath('group_status', null)
            ->assertJsonPath('title', null)
            ->assertJsonPath('group_period', null);

        $this->withToken($token)
            ->withHeader('X-Capstone-Role', 'mahasiswa')
            ->getJson('/api/capstone/mahasiswa/my-period')
            ->assertOk()
            ->assertJsonPath('data.period', null);

        $period = Period::create([
            'name' => 'Periode mahasiswa baru',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'is_active' => true,
            'is_finalized' => false,
        ]);

        $this->withToken($token)
            ->withHeader('X-Capstone-Role', 'mahasiswa')
            ->postJson('/api/capstone/mahasiswa/periods/register', ['period_id' => $period->id])
            ->assertCreated();

        $this->assertDatabaseHas('capstone_period_registrations', [
            'user_id' => $student->id,
            'period_id' => $period->id,
        ]);
    }

    public function test_student_without_database_profile_is_returned_to_global_dashboard(): void
    {
        $user = $this->capstoneUser('mahasiswa');
        $user->createToken('capstone-fe', ['capstone:access'], now()->addHour());

        $response = $this->actingAs($user)
            ->withSession(['session_version' => 0])
            ->get('/capstone/launch');

        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Data mahasiswa Anda belum terdaftar di SICATA. Silakan hubungi administrator.');
        $this->assertAuthenticatedAs($user);
        $this->assertSame(
            0,
            PersonalAccessToken::where('tokenable_id', $user->id)
                ->where('name', 'capstone-fe')
                ->count()
        );
    }

    public function test_exchange_returns_global_redirect_if_student_profile_disappears(): void
    {
        $user = $this->capstoneUser('mahasiswa');
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => 'MISSING-AFTER-LAUNCH',
            'cohort_year' => 2020,
        ]);
        $user->createToken('capstone-fe', ['capstone:access'], now()->addHour());
        $ticket = $this->launchTicket($user, 'MissingStudentBrowser/1.0');

        $student->delete();

        $response = $this->withHeader('User-Agent', 'MissingStudentBrowser/1.0')
            ->postJson('/api/capstone/auth/exchange', ['ott' => $ticket]);

        $response->assertForbidden()
            ->assertJsonPath('code', 'SICATA_STUDENT_NOT_REGISTERED')
            ->assertJsonPath('redirect_url', route('dashboard'));
        $this->assertSame(
            0,
            PersonalAccessToken::where('tokenable_id', $user->id)
                ->where('name', 'capstone-fe')
                ->count()
        );
    }

    public function test_existing_sicata_session_is_revoked_when_student_profile_is_missing(): void
    {
        $user = $this->capstoneUser('mahasiswa');
        $token = $user->createToken('capstone-fe', ['capstone:access'], now()->addHour());

        $response = $this->withToken($token->plainTextToken)
            ->withHeader('X-Capstone-Role', 'mahasiswa')
            ->getJson('/api/capstone/auth/user');

        $response->assertForbidden()
            ->assertJsonPath('code', 'SICATA_STUDENT_NOT_REGISTERED')
            ->assertJsonPath('redirect_url', route('dashboard'));
        $this->assertSame(
            0,
            PersonalAccessToken::where('tokenable_id', $user->id)
                ->where('name', 'capstone-fe')
                ->count()
        );
    }

    public function test_fast_logout_revokes_capstone_tokens_and_web_session(): void
    {
        $user = $this->capstoneUser('mahasiswa');
        Student::create(['user_id' => $user->id, 'student_number' => 'LOGOUT-01', 'cohort_year' => 2020]);
        $user->createToken('capstone-fe', ['capstone:access'], now()->addHours(8));
        $user->createToken('another-client', ['*']);

        $response = $this->actingAs($user)
            ->withSession(['session_version' => 0])
            ->post('/capstone/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertSame(0, PersonalAccessToken::where('tokenable_id', $user->id)->where('name', 'capstone-fe')->count());
        $this->assertSame(1, PersonalAccessToken::where('tokenable_id', $user->id)->where('name', 'another-client')->count());
        $this->assertDatabaseHas('capstone_audit_logs', ['user_id' => $user->id, 'action' => 'LOGOUT']);
    }

    private function launchTicket(User $user, string $userAgent): string
    {
        $launch = $this->actingAs($user)
            ->withSession(['session_version' => 0])
            ->withHeader('User-Agent', $userAgent)
            ->get('/capstone/launch');

        parse_str((string) parse_url($launch->headers->get('Location'), PHP_URL_QUERY), $query);

        return $query['ott'];
    }

    private function capstoneUser(string $roleName): User
    {
        $permission = Permission::firstOrCreate(
            ['name' => 'capstone.view', 'guard_name' => 'web'],
            ['display_name' => 'Lihat Capstone', 'module' => 'capstone']
        );
        $role = Role::firstOrCreate(
            ['name' => $roleName, 'guard_name' => 'web'],
            ['module' => $roleName === 'admin_capstone' ? 'capstone' : 'global', 'is_academic' => in_array($roleName, ['dosen', 'mahasiswa'], true)]
        );
        $role->givePermissionTo($permission);

        $user = $this->newUser($roleName.'@example.test');
        $user->assignRole($role);

        return $user->fresh(['roles']);
    }

    private function newUser(string $email): User
    {
        return User::create([
            'external_id' => (string) Str::uuid(),
            'name' => 'Capstone Test User',
            'email' => $email,
            'password' => bcrypt('not-used-for-ctms-login'),
        ]);
    }
}
