<?php

namespace App\Http\Middleware;

use App\Services\UserPresence;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UpdateUserOnlineStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Presence is non-critical, so refresh it after the response is sent.
     */
    public function terminate(Request $request, Response $response): void
    {
        $userId = $request->user()?->getAuthIdentifier();

        if (! $userId) {
            return;
        }

        try {
            app(UserPresence::class)->touch((int) $userId);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
