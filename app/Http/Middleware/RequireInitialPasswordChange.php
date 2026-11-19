<?php

namespace App\Http\Middleware;

use App\Services\LocalPasswordSession;
use App\Services\MicrosoftSsoSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireInitialPasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        // A verified remember cookie also comes from a local password login.
        if (Auth::guard('web')->viaRemember() && ! $request->session()->has(MicrosoftSsoSession::KEY)) {
            $request->session()->put(LocalPasswordSession::KEY, true);
        }

        if (! LocalPasswordSession::isActive($request)
            || $request->routeIs('profile.edit', 'password.update', 'logout', 'logout.switch')
            || ! $user->needsLocalPasswordChange()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => LocalPasswordSession::CHANGE_REQUIRED], 403);
        }

        return LocalPasswordSession::redirectToPasswordChange();
    }
}
