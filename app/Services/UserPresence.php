<?php

namespace App\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserPresence
{
    private const UPDATE_INTERVAL_SECONDS = 120;

    /**
     * Refresh a user's presence at most once per interval.
     *
     * Presence is best-effort: a cache outage must not turn every page request
     * into a synchronous database write.
     */
    public function touch(int $userId): bool
    {
        $cacheKey = "user:{$userId}:presence-touch";

        try {
            $shouldUpdate = Cache::add(
                $cacheKey,
                true,
                now()->addSeconds(self::UPDATE_INTERVAL_SECONDS)
            );
        } catch (Throwable) {
            return false;
        }

        if (! $shouldUpdate) {
            return false;
        }

        try {
            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'is_online' => true,
                    'last_seen_at' => now(),
                ]);
        } catch (Throwable $exception) {
            if ($this->isTimeout($exception)) {
                // Best-effort presence: keep the throttle key so a stalled
                // pooler/lock does not cause a retry storm on the next request.
                Log::warning('UserPresence heartbeat skipped (database timeout)', [
                    'user_id' => $userId,
                    'code' => $exception instanceof QueryException ? $exception->getCode() : null,
                ]);

                return false;
            }

            try {
                Cache::forget($cacheKey);
            } catch (Throwable) {
                // The original database error is more useful to the caller.
            }

            throw $exception;
        }

        return true;
    }

    private function isTimeout(Throwable $exception): bool
    {
        $haystack = strtolower($exception->getMessage().' '.(string) $exception->getCode());

        $previous = $exception->getPrevious();
        if ($previous instanceof Throwable) {
            $haystack .= ' '.strtolower($previous->getMessage().' '.(string) $previous->getCode());
        }

        return str_contains($haystack, '57014')
            || str_contains($haystack, 'statement timeout')
            || str_contains($haystack, 'lock timeout')
            || str_contains($haystack, 'canceling statement due to lock timeout')
            || str_contains($haystack, 'deadlock detected');
    }
}
