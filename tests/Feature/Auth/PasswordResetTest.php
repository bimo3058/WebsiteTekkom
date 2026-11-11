<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Keep password tests isolated from module migrations and real databases.
        config([
            'database.default' => 'password_change_test',
            'database.connections.password_change_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);
        $paths = array_values(array_filter(
            glob(database_path('migrations/*.php')),
            fn ($path) => preg_match('/(?:users|roles|permissions|spatie_permission|students|lecturers|system_modules)_table/', basename($path))
                && ! str_contains(basename($path), 'optimize_users'),
        ));
        sort($paths);

        foreach ($paths as $path) {
            (require $path)->up();
        }

        // Legacy reset tokens have no migration in this repository.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    private function administrator(string $role = 'superadmin'): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => $role, 'guard_name' => 'web']));

        return $user;
    }

    private function passwordInput(User $user, array $overrides = []): array
    {
        return array_replace([
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'replacement-secret-123!',
            'password_confirmation' => 'replacement-secret-123!',
        ], $overrides);
    }

    public function test_direct_password_change_form_can_be_rendered(): void
    {
        $this->get('/forgot-password')->assertOk()
            ->assertSee('Ubah Password')
            ->assertSee('name="current_password"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertDontSee('Kirim Link Reset Password');
    }

    public static function administratorRoles(): array
    {
        return [['admin'], ['superadmin']];
    }

    #[DataProvider('administratorRoles')]
    public function test_administrator_can_change_password_directly_without_email(string $role): void
    {
        Notification::fake();
        Event::fake([PasswordReset::class]);
        $user = $this->administrator($role);
        $rememberToken = $user->remember_token;
        $sessionVersion = $user->session_version;
        $user->cacheUserData();

        $this->post('/forgot-password', $this->passwordInput($user))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Password berhasil diubah. Silakan login dengan password baru.');

        $user->refresh();
        $this->assertTrue(Hash::check('replacement-secret-123!', $user->password));
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertNotSame($rememberToken, $user->remember_token);
        $this->assertSame((int) $sessionVersion + 1, (int) $user->session_version);
        $this->assertNull(Cache::get("user:{$user->id}:data"));
        $this->assertGuest();
        Notification::assertNothingSent();
        Event::assertDispatched(PasswordReset::class, fn ($event) => $event->user->is($user));
    }

    public static function rejectedAccounts(): array
    {
        return [
            'wrong password' => ['wrong_password'],
            'unknown email' => ['unknown_email'],
            'non administrator' => ['non_admin'],
            'suspended administrator' => ['suspended'],
            'no local password' => ['no_password'],
            'deleted administrator' => ['deleted'],
        ];
    }

    #[DataProvider('rejectedAccounts')]
    public function test_invalid_credentials_or_ineligible_accounts_cannot_change_password(string $scenario): void
    {
        $user = $this->administrator($scenario === 'non_admin' ? 'mahasiswa' : 'superadmin');
        $input = $this->passwordInput($user);
        if ($scenario === 'wrong_password') {
            $input['current_password'] = 'wrong-password';
        } elseif ($scenario === 'unknown_email') {
            $input['email'] = 'unknown@example.com';
        } elseif ($scenario === 'suspended') {
            $user->update(['suspended_at' => now()]);
        } elseif ($scenario === 'no_password') {
            $user->update(['password' => null]);
        } elseif ($scenario === 'deleted') {
            $user->delete();
        }
        $originalPassword = $user->password;

        $this->from('/forgot-password')->post('/forgot-password', $input)
            ->assertRedirect('/forgot-password')->assertSessionHasErrors('email');

        $this->assertSame($originalPassword, User::withTrashed()->findOrFail($user->id)->password);
        $this->assertNull(session('_old_input.current_password'));
        $this->assertNull(session('_old_input.password'));
        $this->assertNull(session('_old_input.password_confirmation'));
    }

    public static function invalidPasswordInputs(): array
    {
        return [
            'missing old password' => [['current_password' => ''], 'current_password'],
            'confirmation mismatch' => [['password_confirmation' => 'different-secret'], 'password'],
            'short password' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
            'same password' => [['password' => 'password', 'password_confirmation' => 'password'], 'password'],
        ];
    }

    #[DataProvider('invalidPasswordInputs')]
    public function test_password_validation_does_not_change_password(array $overrides, string $field): void
    {
        $user = $this->administrator();

        $this->from('/forgot-password')->post('/forgot-password', $this->passwordInput($user, $overrides))
            ->assertRedirect('/forgot-password')->assertSessionHasErrors($field);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_password_change_attempts_are_rate_limited(): void
    {
        $user = $this->administrator();
        $input = $this->passwordInput($user, ['current_password' => 'wrong-password']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/forgot-password', $input)->assertUnprocessable();
        }

        $this->postJson('/forgot-password', $input)->assertStatus(429);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_existing_token_reset_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->get('/reset-password/'.$token)->assertOk();
    }

    public function test_password_can_still_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'replacement-secret-123!',
            'password_confirmation' => 'replacement-secret-123!',
        ])->assertSessionHasNoErrors()->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('replacement-secret-123!', $user->fresh()->password));
    }
}
