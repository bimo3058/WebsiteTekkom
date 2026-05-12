<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSessionVersion
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('CheckSessionVersion hit', [
            'auth_check'      => Auth::check(),
            'session_version' => session('session_version'),
            'url'             => $request->url(),
        ]);
        
        if (Auth::check()) {
            $user                  = Auth::user();
            $currentSessionVersion = (int) session('session_version', 0);

            if ($currentSessionVersion !== (int) $user->session_version) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah diakhiri oleh administrator.');
            }
        }

        return $next($request);
    }
}