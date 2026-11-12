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
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        if ($this->isSkippedPath($request)) {
            return;
        }

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

    private function isSkippedPath(Request $request): bool
    {
        $path = trim($request->path(), '/');

        if ($path === '' || $path === 'up') {
            return true;
        }

        foreach (['telescope', 'pulse', 'livewire', '_ignition'] as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }
}
