<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\LocalPasswordSession;
use App\Services\MicrosoftSsoSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->withSession([LocalPasswordSession::KEY => true])
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->withSession([LocalPasswordSession::KEY => true])
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }

    public function test_sso_session_cannot_view_or_submit_the_password_form(): void
    {
        $user = User::factory()->create();
        $tenant = '11111111-1111-1111-1111-111111111111';
        config(['services.azure.tenant' => $tenant]);

        $this->actingAs($user)->withSession([
            MicrosoftSsoSession::KEY => ['tenant' => $tenant, 'authenticated_at' => now()->timestamp],
            LocalPasswordSession::KEY => true,
        ]);

        $this->get(route('profile.edit', ['tab' => 'password']))
            ->assertOk()
            ->assertSee(LocalPasswordSession::CHANGE_LOCKED)
            ->assertDontSee('id="form-password"', false);

        $this->putJson(route('password.update'), [
            'current_password' => 'password',
            'password' => 'replacement-secret-123!',
            'password_confirmation' => 'replacement-secret-123!',
        ])->assertForbidden()->assertJsonPath('message', LocalPasswordSession::CHANGE_LOCKED);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
