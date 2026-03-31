<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSessionVersion
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Ambil versi session yang tersimpan di browser saat ini
            $currentSessionVersion = session('session_version');

            // Jika session browser belum punya versi, kasih versi yang ada di DB sekarang
            if ($currentSessionVersion === null) {
                session(['session_version' => $user->session_version]);
            } 
            // JIKA BERBEDA: Artinya Admin baru saja menaikkan versi di DB (Force Logout)
            else if ($currentSessionVersion != $user->session_version) {
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