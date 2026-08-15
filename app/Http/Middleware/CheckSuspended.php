<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSuspended
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('telescope', 'telescope/*', 'vendor/telescope/*')) {
            return $next($request);
        }
        
        $user = Auth::user();

        // Some authentication flows deliberately select only the identity
        // columns they need. Reading a missing attribute through Eloquent can
        // throw when missing-attribute protection is enabled, so inspect the
        // loaded attributes directly here.
        $attributes = $user?->getAttributes() ?? [];
        if ($user && ($attributes['suspended_at'] ?? null)) {
            $reason = $attributes['suspension_reason'] ?? null;
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = 'Akun Anda telah ditangguhkan.';
            if ($reason) {
                $message .= ' Alasan: ' . $reason;
            }

            return redirect()->route('login')->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
