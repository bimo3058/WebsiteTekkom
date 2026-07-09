<?php

namespace Modules\BankSoal\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GpmSessionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasRole('gpm')) {
            if (session('active_banksoal_role') !== 'gpm') {
                return redirect()->route('banksoal.dashboard')
                    ->with('warning', 'Silakan aktifkan sesi GPM Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
