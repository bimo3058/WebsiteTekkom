<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class MicrosoftSsoSession
{
    public const KEY = 'auth.microsoft';

    /** Authority comes from server configuration, never from request parameters or decoded Graph tokens. */
    public function tenant(): string
    {
        $tenant = strtolower(trim((string) config('services.azure.tenant')));
        if (! preg_match('/\A(?:[a-f0-9]{8}(?:-[a-f0-9]{4}){3}-[a-f0-9]{12}|[a-z0-9](?:[a-z0-9.-]*[a-z0-9])?\.[a-z]{2,})\z/', $tenant)) {
            throw new InvalidArgumentException('Microsoft SSO memerlukan tenant ID atau domain tenant yang spesifik.');
        }

        return $tenant;
    }

    public function metadata(int $authenticatedAt): array
    {
        // Laravel controls session lifetime; Graph token expiry is not an application grant.
        // Store only authority metadata, never access/refresh tokens.
        return ['tenant' => $this->tenant(), 'authenticated_at' => $authenticatedAt];
    }

    public function clear(Request $request): void
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function logout(Request $request, bool $switchAccount = false)
    {
        $isSso = $request->session()->has(self::KEY);
        // Also handle browser sessions created before the SSO marker was deployed.
        $isSso = $isSso || (! empty($request->user()?->getAttributes()['sso_data']) && ! $request->session()->get('auth.local_password', false));
        $this->clear($request);
        if (! $isSso && ! $switchAccount) {
            return redirect('/');
        }

        if ($switchAccount) {
            $request->session()->put('auth.after_microsoft_logout', 'switch');
        }
        // Reuse the already registered OAuth callback. A return without code/error
        // is handled there as a signed-out landing, never as a login callback.
        $returnUri = config('services.azure.redirect') ?: route('microsoft.callback');
        try {
            $tenant = $this->tenant();
        } catch (InvalidArgumentException) {
            $tenant = 'common';
        }

        return redirect()->away('https://login.microsoftonline.com/'.$tenant.'/oauth2/v2.0/logout?'.http_build_query([
            'post_logout_redirect_uri' => $returnUri,
        ], '', '&', PHP_QUERY_RFC3986));
    }
}
