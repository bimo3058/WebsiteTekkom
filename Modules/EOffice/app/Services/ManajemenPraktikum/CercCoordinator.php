<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use App\Models\{Role,User};
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

class CercCoordinator
{
    public function replace(?User $successor): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['superadmin','admin_praktikum']),403);
        if ($successor) {
            ActiveStudent::ensure($successor);
            abort_if($successor->isSuspended(),422,'Akun yang ditangguhkan tidak dapat menjadi Koordinator CERC.');
            abort_unless($successor->student()->exists(),422,'Pilih akun pribadi mahasiswa untuk Koordinator CERC.');
        }
        DB::transaction(function () use ($successor) {
            $role = Role::where('name','koordinator_cerc')->where('guard_name','web')->lockForUpdate()->firstOrFail();
            $previous = User::withTrashed()->whereHas('roles',fn($q)=>$q->where('roles.id',$role->id))->lockForUpdate()->get();
            if(($successor && $previous->count()===1 && $previous->first()->is($successor)) || (!$successor && $previous->isEmpty()))return;
            foreach ($previous as $user) {
                if ($successor && $user->is($successor)) continue;
                $user->roles()->detach($role->id);
                $user->syncPermissionsFromRoles();
                $user->increment('session_version');
                $user->clearUserCache();
            }
            if ($successor) {
                $successor->roles()->syncWithoutDetaching([$role->id]);
                $successor->syncPermissionsFromRoles();
                $successor->clearUserCache();
            }
            AuditLogger::log('eoffice','UPDATE','Pergantian Koordinator CERC',null,
                ['user_ids'=>$previous->modelKeys(),'users'=>$previous->map(fn($user)=>$user->only(['id','name','email']))->all()],
                ['user_id'=>$successor?->id,'user'=>$successor?->only(['id','name','email'])],auth()->id());
        });
    }
}
