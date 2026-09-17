<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CachedUserPasswordTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Isolate auth migrations from unrelated module migrations and test databases.
        config([
            'database.default' => 'cached_password_test',
            'database.connections.cached_password_test' => [
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
    }

    private function cachedUser(): User
    {
        $user = User::factory()->create(['name' => 'Test Account']);
        $user->cacheUserData();
        $cached = Auth::createUserProvider('users')->retrieveById($user->id);
        $this->assertArrayNotHasKey('password', $cached->getAttributes());

        return $cached;
    }

    public function test_profile_can_be_displayed_with_a_cached_user(): void
    {
        $this->actingAs($this->cachedUser())->get('/profile')->assertOk();
    }

    public function test_password_is_loaded_once_without_becoming_dirty_or_entering_the_auth_cache(): void
    {
        $user = $this->cachedUser();
        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertSame($user->password, $user->getAuthPassword());
        $this->assertCount(1, DB::getQueryLog());
        $this->assertFalse($user->isDirty('password'));
        DB::disableQueryLog();

        $user->cacheUserData();
        $this->assertArrayNotHasKey('password', Cache::get("user:{$user->id}:data"));
        $this->assertArrayNotHasKey('password', $user->toArray());
    }

    public function test_cached_user_can_update_password(): void
    {
        $user = $this->cachedUser();

        $this->actingAs($user)->from('/profile')->put('/password', [
            'current_password' => 'password',
            'password' => 'replacement-secret-123!',
            'password_confirmation' => 'replacement-secret-123!',
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertTrue(Hash::check('replacement-secret-123!', $user->fresh()->password));
    }

    public function test_loaded_or_unsaved_passwords_do_not_trigger_database_reads(): void
    {
        $user = User::factory()->create(['name' => 'Test Account']);
        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->assertTrue(Hash::check('password', $user->getAuthPassword()));
        $user->password = null;
        $this->assertNull($user->password);
        $this->assertTrue($user->isDirty('password'));
        $this->assertNull((new User)->password);
        $this->assertCount(0, DB::getQueryLog());
        DB::disableQueryLog();
    }

    public function test_cached_user_with_no_database_password_does_not_authenticate(): void
    {
        $user = $this->cachedUser();
        User::whereKey($user->id)->update(['password' => null]);

        $this->assertNull($user->getAuthPassword());
        $this->assertFalse(Auth::createUserProvider('users')->validateCredentials($user, [
            'password' => 'password',
        ]));
        $this->assertFalse($user->isDirty('password'));
    }

    public function test_cached_user_cannot_update_password_with_incorrect_current_password(): void
    {
        $user = $this->cachedUser();

        $this->actingAs($user)->from('/profile')->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'replacement-secret-123!',
            'password_confirmation' => 'replacement-secret-123!',
        ])->assertSessionHasErrorsIn('updatePassword', 'current_password')->assertRedirect('/profile');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
