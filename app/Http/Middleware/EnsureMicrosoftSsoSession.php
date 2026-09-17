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
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi Microsoft berakhir. Silakan login SSO kembali.'], 401);
            }

            return redirect()->route('login')->with('status', 'Sesi Microsoft berakhir. Silakan login SSO kembali.');
        }

        return $next($request);
    }
}
