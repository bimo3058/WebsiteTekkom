<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
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
