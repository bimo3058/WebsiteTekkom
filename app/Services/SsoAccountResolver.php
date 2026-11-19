<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\UserSsoIdentity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SsoAccountResolver
{
    /**
     * Resolve a verified Microsoft principal, provisioning an enabled account on first login.
     * The configured Azure authority is tenant-specific; its tenant ID is persisted with the identity.
     *
     * @return array{user:?User, linked:bool, ambiguous:bool, match_type:string}
     */
    public function resolve(string $objectId, string $email, string $tenantId, string $name = ''): array
    {
        $email = Str::lower(trim($email));
        if (trim($objectId) === '' || trim($tenantId) === ''
            || ! filter_var($email, FILTER_VALIDATE_EMAIL)
            || ! in_array(Str::afterLast($email, '@'), ['students.undip.ac.id', 'lecturer.undip.ac.id', 'undip.ac.id'], true)) {
            return $this->result(null);
        }

        return DB::transaction(function () use ($objectId, $email, $tenantId, $name) {
            $identity = UserSsoIdentity::query()
                ->where('provider', 'microsoft')
                ->where('tenant_id', $tenantId)
                ->where('object_id', $objectId)
                ->lockForUpdate()
                ->first();

            if ($identity) {
                $user = User::query()->whereKey($identity->user_id)->lockForUpdate()->first();
                if ($user && $identity->email !== $email && ! $user->hasAdministrativeRole()) {
                    $identity->forceFill(['email' => $email])->save();
                }

                return $this->result($user, matchType: 'identity');
            }

            $matches = User::withTrashed()
                ->whereRaw('LOWER(email) = ?', [Str::lower(trim($email))])
                ->lockForUpdate()
                ->limit(2)
                ->get();

            if ($matches->count() > 1) {
                return $this->ambiguousResult();
            }

            $user = $matches->first();
            if ($user?->trashed()) {
                return $this->result(null, matchType: 'email');
            }

            if (! $user) {
                $user = User::create([
                    'external_id' => 'microsoft:'.hash('sha256', $tenantId.':'.$objectId),
                    'name' => trim($name) !== '' ? trim($name) : $email,
                    'email' => $email,
                    'password' => null,
                    'sso_enabled' => true,
                    'sso_enabled_at' => now(),
                    'sso_enabled_by' => null,
                ]);

                $roleName = match (Str::afterLast($email, '@')) {
                    'students.undip.ac.id' => 'mahasiswa',
                    'lecturer.undip.ac.id' => 'dosen',
                    default => null,
                };
                if ($roleName !== null) {
                    $role = Role::firstOrCreate(
                        ['name' => $roleName, 'module' => 'global'],
                        ['guard_name' => 'web', 'is_academic' => true],
                    );
                    $user->assignRole($role);
                    $user->syncPermissionsFromRoles();
                }
            }

            // An existing account's access decision must survive subsequent logins.
            if (! $user->sso_enabled || $user->isSuspended() || $user->hasAdministrativeRole()) {
                return $this->result($user, matchType: 'email');
            }

            $user->ssoIdentity()->create([
                'provider' => 'microsoft',
                'tenant_id' => $tenantId,
                'object_id' => $objectId,
                'email' => Str::lower(trim($email)),
            ]);

            return $this->result($user, linked: true, matchType: 'email');
        }, 3);
    }

    /** @return array{user:?User, linked:bool, ambiguous:bool, match_type:string} */
    private function result(?User $user, bool $linked = false, string $matchType = ''): array
    {
        return [
            'user' => $user,
            'linked' => $linked,
            'ambiguous' => false,
            'match_type' => $matchType,
        ];
    }

    /** @return array{user:null, linked:false, ambiguous:true, match_type:string} */
    private function ambiguousResult(): array
    {
        return ['user' => null, 'linked' => false, 'ambiguous' => true, 'match_type' => 'email'];
    }
}
