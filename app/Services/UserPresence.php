<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
            try {
                Cache::forget($cacheKey);
            } catch (Throwable) {
                // The original database error is more useful to the caller.
            }

            throw $exception;
        }

        return true;
    }
}
