<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use App\Services\LocalPasswordSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('action="'.route('login.store').'"', false)
            ->assertDontSee(route('admin.login'));

        $this->get(route('admin.login'))->assertOk()
            ->assertSee('Masuk Admin SITKOM')
            ->assertSee('action="'.route('admin.login.store').'"', false);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['local_password_initialized_at' => now()])->save();
        $role = Role::create([
            'name' => 'mahasiswa',
            'guard_name' => 'web',
            'module' => 'global',
            'is_academic' => true,
        ]);
        $user->assignRole($role);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    #[DataProvider('administratorRoles')]
    public function test_administrators_can_only_sign_in_on_the_admin_route(string $role, string $module, string $dashboard): void
    {
        $user = User::factory()->create();
        $user->forceFill(['local_password_initialized_at' => now()])->save();
        $user->assignRole(Role::create([
            'name' => $role, 'guard_name' => 'web', 'module' => $module, 'is_academic' => false,
        ]));

        $credentials = ['email' => $user->email, 'password' => 'password'];
        $this->post(route('login.store'), $credentials + ['administrator' => true, 'remember' => true])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Email tidak dikenali.']);
        $this->assertGuest();

        $this->post(route('admin.login.store'), $credentials)->assertRedirect(route($dashboard));
        $this->assertAuthenticatedAs($user);
    }

    public static function administratorRoles(): array
    {
        return [
            'superadmin' => ['superadmin', 'global', 'superadmin.dashboard'],
            'admin' => ['admin', 'global', 'dashboard'],
            'banksoal' => ['admin_banksoal', 'banksoal', 'banksoal.dashboard'],
            'capstone' => ['admin_capstone', 'capstone', 'capstone.dashboard'],
            'eoffice' => ['admin_eoffice', 'eoffice', 'eoffice.dashboard'],
            'kemahasiswaan' => ['admin_kemahasiswaan', 'kemahasiswaan', 'manajemenmahasiswa.dashboard'],
        ];
    }

    #[DataProvider('firstPasswordLoginRoles')]
    public function test_first_form_login_requires_a_new_password(string $role, string $module, string $form, string $dashboard): void
    {
        $user = User::factory()->create(['name' => 'Test Account']);
        $user->assignRole(Role::create([
            'name' => $role, 'guard_name' => 'web', 'module' => $module,
        ]));
        $user->forceFill(['last_login' => now()->subDay(), 'sso_data' => ['id' => 'previous-sso-login']])->save();

        $settingsUrl = route('profile.edit', ['tab' => 'password']);
        $this->post(route($form), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect($settingsUrl)
            ->assertSessionHas('password_change_required', LocalPasswordSession::CHANGE_REQUIRED);
        $this->get($settingsUrl)->assertOk()
            ->assertSee(LocalPasswordSession::CHANGE_REQUIRED)
            ->assertSee('id="form-password"', false);
        $this->get(route($dashboard))->assertRedirect($settingsUrl);

        $this->from($settingsUrl)->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'replacement-secret-123!',
            'password_confirmation' => 'replacement-secret-123!',
        ])->assertRedirect($settingsUrl)->assertSessionHas('status', 'password-updated');
        $this->assertNotNull($user->fresh()->local_password_initialized_at);

        $this->post(route('logout'));
        $this->post(route($form), ['email' => $user->email, 'password' => 'replacement-secret-123!'])
            ->assertRedirect(route($dashboard));
    }

    public static function firstPasswordLoginRoles(): array
    {
        return [
            'regular account' => ['mahasiswa', 'global', 'login.store', 'dashboard'],
            'module administrator' => ['admin_capstone', 'capstone', 'admin.login.store', 'capstone.dashboard'],
        ];
    }

    public function test_admin_login_rejects_an_academic_account(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create([
            'name' => 'mahasiswa', 'guard_name' => 'web', 'module' => 'global', 'is_academic' => true,
        ]));

        $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors(['email' => 'Email atau kata sandi tidak sesuai.']);
        $this->assertGuest();
    }

    public function test_public_login_does_not_distinguish_unknown_emails_from_rejected_admins(): void
    {
        $this->post(route('login.store'), ['email' => 'unregistered@example.test', 'password' => 'password'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Email tidak dikenali.']);
        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_cached_users_can_logout_without_loading_the_raw_remember_token(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['remember_token' => null])->save();
        $user->cacheUserData();

        $cachedUser = Auth::createUserProvider('users')->retrieveById($user->id);

        $this->assertNotNull($cachedUser);
        $this->assertArrayHasKey('remember_token', $cachedUser->getAttributes());
        $this->assertNull($cachedUser->getAttributes()['remember_token']);

        $response = $this->actingAs($cachedUser)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_cached_remember_token_is_rotated_without_caching_its_value(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['remember_token' => 'sensitive-token'])->save();
        $user->cacheUserData();

        $payload = cache()->get("user:{$user->id}:data");
        $cachedUser = Auth::createUserProvider('users')->retrieveById($user->id);

        $this->assertArrayNotHasKey('remember_token', $payload);
        $this->assertTrue($payload[User::AUTH_CACHE_HAS_REMEMBER_TOKEN]);
        $this->assertSame(
            User::AUTH_CACHE_REMEMBER_TOKEN_PLACEHOLDER,
            $cachedUser->getAttributes()['remember_token']
        );

        $this->actingAs($cachedUser)->post('/logout')->assertRedirect('/');

        $this->assertNotSame('sensitive-token', $user->fresh()->getRememberToken());
    }
}
