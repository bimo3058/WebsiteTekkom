<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSsoIdentity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SsoAccountResolver
{
    /**
     * Resolve a Microsoft principal to an existing and explicitly provisioned application account.
     * The configured Azure authority is tenant-specific; its tenant ID is persisted with the identity.
     *
     * @return array{user:?User, linked:bool, ambiguous:bool, match_type:string}
     */
    public function resolve(string $objectId, string $email, string $tenantId): array
    {
        return DB::transaction(function () use ($objectId, $email, $tenantId) {
            $identity = UserSsoIdentity::query()
                ->where('provider', 'microsoft')
                ->where('tenant_id', $tenantId)
                ->where('object_id', $objectId)
                ->lockForUpdate()
                ->first();

            if ($identity) {
                $user = User::query()->whereKey($identity->user_id)->lockForUpdate()->first();
                if ($user && $identity->email !== Str::lower(trim($email))) {
                    $identity->forceFill(['email' => Str::lower(trim($email))])->save();
                }

                return $this->result($user, matchType: 'identity');
            }

            $matches = User::query()
                ->whereRaw('LOWER(email) = ?', [Str::lower(trim($email))])
                ->lockForUpdate()
                ->limit(2)
                ->get();

            if ($matches->count() > 1) {
                return $this->ambiguousResult();
            }

            $user = $matches->first();
            if (! $user) {
                return $this->result(null, matchType: 'email');
            }

            // Until cutover, existing users can complete the legacy login path,
            // but never bind a Microsoft principal before the roster grants access.
            if (! $user->sso_enabled || $user->isSuspended()) {
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
