<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserOnlineStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userId = Auth::id();

            // Update status directly in DB to maintain performance and avoid model overhead
            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'is_online' => true,
                    'last_seen_at' => now(),
                ]);
        }

        return $next($request);
    }
}
