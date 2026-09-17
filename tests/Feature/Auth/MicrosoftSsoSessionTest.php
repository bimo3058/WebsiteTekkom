<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\EnsureMicrosoftSsoSession;
use App\Models\User;
use App\Services\MicrosoftSsoSession;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Mockery;
use Tests\TestCase;

class MicrosoftSsoSessionTest extends TestCase
{
    private const TENANT = '11111111-1111-1111-1111-111111111111';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.azure.tenant' => self::TENANT, 'services.azure.client_id' => 'test-client',
            'services.azure.client_secret' => 'test-secret', 'services.azure.redirect' => 'https://sitkom.example/auth/microsoft/callback']);
    }

    private function request(array $data, string $path = '/dashboard'): Request
    {
        $request = Request::create($path);
        $store = new Store('sso-test', new ArraySessionHandler(120));
        $store->start();
        $store->put($data);
        $request->setLaravelSession($store);
        $request->setUserResolver(fn () => null);

        return $request;
    }

    private function expectLogout(): void
    {
        $guard = Mockery::mock();
        $guard->shouldReceive('logout')->once();
        Auth::shouldReceive('guard')->with('web')->once()->andReturn($guard);
    }

    public function test_login_and_switch_use_tenant_authority_and_always_show_account_picker(): void
    {
        foreach (['microsoft.redirect', 'microsoft.switch'] as $route) {
            Socialite::forgetDrivers();
            $response = $this->get(route($route));
            $url = $response->headers->get('Location');
            $this->assertSame('login.microsoftonline.com', parse_url($url, PHP_URL_HOST));
            $this->assertSame('/'.self::TENANT.'/oauth2/v2.0/authorize', parse_url($url, PHP_URL_PATH));
            parse_str(parse_url($url, PHP_URL_QUERY), $query);
            $this->assertSame('select_account', $query['prompt']);
            $this->assertNotEmpty($query['state']);
            $this->assertArrayNotHasKey('login_hint', $query);
        }
    }

    public function test_metadata_records_tenant_without_retaining_tokens_or_overriding_session_lifetime(): void
    {
        $issued = now()->timestamp;
        $metadata = (new MicrosoftSsoSession)->metadata($issued);
        $this->assertSame(['tenant' => self::TENANT, 'authenticated_at' => $issued], $metadata);
    }

    public function test_generic_tenant_authorities_are_rejected_for_sign_in(): void
    {
        config(['services.azure.tenant' => 'common']);
        $this->expectException(\InvalidArgumentException::class);
        (new MicrosoftSsoSession)->tenant();
    }

    public function test_logout_erases_session_payload_rotates_id_and_visits_microsoft(): void
    {
        $request = $this->request([MicrosoftSsoSession::KEY => ['tenant' => self::TENANT],
            'state' => 'old-oauth-state', 'sso_verified' => true, 'sso_pending_user_id' => 7,
            'capstone' => ['role' => 'admin'], 'url.intended' => '/private', 'session_version' => 2]);
        $oldId = $request->session()->getId();
        $this->expectLogout();
        $response = (new MicrosoftSsoSession)->logout($request);
        $this->assertNotSame($oldId, $request->session()->getId());
        $this->assertSame(['_token'], array_keys($request->session()->all()));
        $this->assertStringStartsWith('https://login.microsoftonline.com/'.self::TENANT.'/oauth2/v2.0/logout?', $response->getTargetUrl());
        parse_str(parse_url($response->getTargetUrl(), PHP_URL_QUERY), $query);
        $this->assertSame(config('services.azure.redirect'), $query['post_logout_redirect_uri']);
    }

    public function test_password_session_is_local_even_for_previously_linked_sso_user(): void
    {
        $request = $this->request(['auth.local_password' => true]);
        $user = new User(['sso_data' => ['id' => 'linked-account']]);
        $request->setUserResolver(fn () => $user);
        $this->expectLogout();
        $this->assertSame(url('/'), (new MicrosoftSsoSession)->logout($request)->getTargetUrl());
    }

    public function test_malformed_sso_session_is_rejected_and_cleared_for_json_requests(): void
    {
        $request = $this->request([MicrosoftSsoSession::KEY => 'invalid-metadata', 'capstone' => 'old']);
        $request->headers->set('Accept', 'application/json');
        $this->expectLogout();
        $response = (new EnsureMicrosoftSsoSession(new MicrosoftSsoSession))->handle($request, fn () => $this->fail('Malformed session passed'));
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame(['_token'], array_keys($request->session()->all()));
    }

    public function test_tenant_change_invalidates_session_but_valid_session_is_preserved(): void
    {
        $data = ['tenant' => self::TENANT, 'authenticated_at' => now()->timestamp];
        $request = $this->request([MicrosoftSsoSession::KEY => $data]);
        $middleware = new EnsureMicrosoftSsoSession(new MicrosoftSsoSession);
        $this->assertSame('ok', $middleware->handle($request, fn () => 'ok'));
        $this->assertSame($data, $request->session()->get(MicrosoftSsoSession::KEY));
        config(['services.azure.tenant' => '22222222-2222-2222-2222-222222222222']);
        $this->expectLogout();
        $this->assertSame(route('login'), $middleware->handle($request, fn () => $this->fail('Wrong tenant passed'))->getTargetUrl());
    }

    public function test_logout_return_does_not_exchange_tokens_and_switch_continuation_is_consumed(): void
    {
        Socialite::shouldReceive('driver')->never();
        $this->get(route('microsoft.callback'))->assertRedirect(route('login'));
        $this->withSession(['auth.after_microsoft_logout' => 'switch'])->get(route('microsoft.callback'))
            ->assertRedirect(route('microsoft.switch'))->assertSessionMissing('auth.after_microsoft_logout');
    }

    public function test_switch_logout_keeps_only_an_account_picker_continuation(): void
    {
        $request = $this->request(['state' => 'old-state', 'capstone' => ['user_id' => 7]]);
        $this->expectLogout();
        $response = (new MicrosoftSsoSession)->logout($request, switchAccount: true);
        $this->assertStringContainsString('/oauth2/v2.0/logout?', $response->getTargetUrl());
        $this->assertSame('switch', $request->session()->get('auth.after_microsoft_logout'));
        $this->assertFalse($request->session()->has('state'));
        $this->assertFalse($request->session()->has('capstone'));
        $this->assertFalse($request->session()->has(MicrosoftSsoSession::KEY));
    }

    public function test_failed_oauth_callback_clears_pending_authentication_state(): void
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('user')->once()->andThrow(new InvalidStateException);
        Socialite::shouldReceive('driver')->with('azure')->once()->andReturn($provider);
        $this->withSession(['state' => 'old-state', 'sso_pending_user_id' => 7, 'capstone' => ['user_id' => 7]])
            ->get(route('microsoft.callback', ['code' => 'test-code']))
            ->assertRedirect(route('login'))
            ->assertSessionMissing('state')
            ->assertSessionMissing('sso_pending_user_id')
            ->assertSessionMissing('capstone');
        $this->assertGuest();
    }

    public function test_local_session_does_not_require_microsoft_metadata(): void
    {
        $request = $this->request(['auth.local_password' => true]);
        config(['services.azure.tenant' => 'common']);
        $this->assertSame('ok', (new EnsureMicrosoftSsoSession(new MicrosoftSsoSession))->handle($request, fn () => 'ok'));
    }
}
