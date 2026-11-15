<?php

namespace Modules\EOffice\Services;

use App\Models\Role;
use App\Models\User;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\Praktikum;

class KoorPraktikumService
{
    public function assign(Praktikum $praktikum, User $user): void
    {
        \Modules\EOffice\Services\ManajemenPraktikum\ActiveStudent::ensure($user);
        $roleIds = Role::where('module', 'eoffice')
            ->whereIn('name', ['koor_prak', 'asprak'])
            ->pluck('id')
            ->all();

        if (! empty($roleIds)) {
            $user->roles()->syncWithoutDetaching($roleIds);
            $user->syncPermissionsFromRoles();
            $user->clearUserCache();
        }

        if ((string) $praktikum->koor_id !== (string) $user->id) {
            $praktikum->update(['koor_id' => $user->id]);
        }

        foreach (['koor', 'asprak'] as $role) {
            $membership = AsprakPraktikum::withTrashed()->firstOrNew([
                'praktikum_id' => $praktikum->id,
                'user_id' => $user->id,
                'role' => $role,
            ]);

            if (! $membership->exists) {
                $membership->save();
            } elseif ($membership->trashed()) {
                $membership->restore();
            }
        }
    }
}
