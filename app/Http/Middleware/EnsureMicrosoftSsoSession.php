<?php

namespace App\Http\Middleware;

use App\Services\MicrosoftSsoSession;
use Closure;
use Illuminate\Http\Request;

class EnsureMicrosoftSsoSession
{
    public function __construct(private readonly MicrosoftSsoSession $sso) {}

    public function handle(Request $request, Closure $next)
    {
        // OAuth callbacks must be allowed to finish their own validation. A stale
        // marker from an earlier session must not pre-empt the callback and drop
        // its error redirect back to the login screen.
        if ($request->is('auth/microsoft/redirect', 'auth/microsoft/callback')) {
            return $next($request);
        }

        // Explicit logout must still visit Microsoft's end-session endpoint.
        if ($request->is('logout', 'logout-and-switch')) {
            return $next($request);
        }
        $session = $request->session()->get(MicrosoftSsoSession::KEY);
        if ($session === null) {
            return $next($request);
        } // Local password login.
        try {
            $tenant = $this->sso->tenant();
        } catch (\InvalidArgumentException) {
            $tenant = null;
        }
        if (! $tenant || ! is_array($session) || ($session['tenant'] ?? null) !== $tenant) {
            $this->sso->clear($request);

            // The login page is a public landing page. Clear the stale marker
            // and render the current request so callback query messages survive.
            if ($request->routeIs('login')) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi Microsoft berakhir. Silakan login SSO kembali.'], 401);
            }

            return redirect()->route('login')->with('status', 'Sesi Microsoft berakhir. Silakan login SSO kembali.');
        }

        return $next($request);
    }
}
