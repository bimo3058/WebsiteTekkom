<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSuspended
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->suspended_at) {
            $reason = Auth::user()->suspension_reason;
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