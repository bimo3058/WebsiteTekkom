<?php

namespace App\Services;

use App\Models\Lecturer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Throwable;

class AcademicRoleSynchronizer
{
    public function shouldSync(User $user, bool $force = false): bool
    {
        if ($force) {
            return true;
        }

        try {
            return ! Cache::has($this->cacheKey($user));
        } catch (Throwable) {
            $lastSync = $user->last_synced_from_sso;
            $ttl = max(1, (int) config('auth.sso.sync_ttl_seconds', 21_600));

            return ! $lastSync || $lastSync->lt(now()->subSeconds($ttl));
        }
    }

    public function syncIfNeeded(User $user, bool $force = false): bool
    {
        if (! $this->shouldSync($user, $force)) {
            return false;
        }

        $this->sync($user);
        $this->markSynced($user);

        return true;
    }

    public function markSynced(User $user): void
    {
        try {
            Cache::put(
                $this->cacheKey($user),
                true,
                max(1, (int) config('auth.sso.sync_ttl_seconds', 21_600))
            );
        } catch (Throwable) {
            // The database timestamp is the fallback when Redis is unavailable.
        }
    }

    /**
     * Ensure the global academic roles match the profiles linked to the user.
     *
     * Additional administrative and module-specific roles are preserved. Only
     * stale global academic roles are removed. Roles are handled by exact IDs so
     * a same-named module role cannot mask the role required by the SSO session.
     *
     * @return array<int, string>
     */
    public function sync(User $user): array
    {
        $roleNames = [];

        if ($user->lecturer()
            ->where('employee_number', 'not like', Lecturer::LEGACY_CAPSTONE_ACTOR_PREFIX.'%')
            ->exists()) {
            $roleNames[] = 'dosen';
        }

        if ($user->student()->exists()) {
            $roleNames[] = 'mahasiswa';
        }

        if ($roleNames === []) {
            return [];
        }

        $roleIds = [];

        foreach ($roleNames as $roleName) {
            $role = Role::firstOrCreate(
                [
                    'name' => $roleName,
                    'module' => 'global',
                ],
                [
                    'guard_name' => 'web',
                    'is_academic' => true,
                ]
            );

            $roleIds[] = $role->id;
        }

        $staleRoleIds = Role::query()
            ->where('module', 'global')
            ->whereIn('name', ['dosen', 'mahasiswa'])
            ->whereNotIn('name', $roleNames)
            ->pluck('id');

        if ($staleRoleIds->isNotEmpty()) {
            $user->roles()->detach($staleRoleIds);
        }

        $user->roles()->syncWithoutDetaching($roleIds);
        $user->unsetRelation('roles');
        $user->forgetCachedPermissions();
        $user->syncPermissionsFromRoles();

        return $roleNames;
    }

    private function cacheKey(User $user): string
    {
        return "user:{$user->getKey()}:sso-academic-synced";
    }
}
