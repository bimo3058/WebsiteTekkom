<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SsoAccessManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'superadmin', 'module' => 'global'], ['guard_name' => 'web']);
        $this->admin = User::create([
            'external_id' => 'test-superadmin',
            'name' => 'Super Admin',
            'email' => 'admin@undip.ac.id',
            'password' => bcrypt('password'),
        ]);
        $this->admin->assignRole($role);
    }

    public function test_superadmin_can_filter_users_by_sso_access_state(): void
    {
        $enabled = $this->makeUser('enabled@students.undip.ac.id', true);
        $disabled = $this->makeUser('disabled@students.undip.ac.id', false);

        $this->actingAs($this->admin)->get('/superadmin/users?sso_status=enabled')
            ->assertOk()->assertSee($enabled->email)->assertDontSee($disabled->email);
        $this->actingAs($this->admin)->get('/superadmin/users?sso_status=disabled')
            ->assertOk()->assertSee($disabled->email)->assertDontSee($enabled->email);
    }

    public function test_disabling_sso_writes_audit_and_revokes_existing_sessions(): void
    {
        $user = $this->makeUser('active@students.undip.ac.id', true);
        $oldVersion = (int) $user->session_version;

        $this->actingAs($this->admin)
            ->from('/superadmin/users')
            ->patch(route('superadmin.users.sso-access.update', $user), ['enabled' => '0'])
            ->assertRedirect('/superadmin/users');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'sso_enabled' => false,
            'sso_enabled_by' => $this->admin->id,
        ]);
        $this->assertGreaterThan($oldVersion, (int) $user->fresh()->session_version);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'module' => 'user_management',
            'action' => 'SSO_ACCESS_DISABLED',
            'subject_id' => $user->id,
        ]);
    }

    private function makeUser(string $email, bool $enabled): User
    {
        return User::create([
            'external_id' => $email,
            'name' => 'Test User',
            'email' => $email,
            'password' => bcrypt('password'),
            'sso_enabled' => $enabled,
        ]);
    }
}
