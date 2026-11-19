<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use App\Models\{EoAuditLog, User};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{AsprakPraktikum, Praktikum};

/** Status changes and assignment revocation must succeed or fail together. */
class PracticumLifecycle
{
    public function save(string $id, array $attributes, ?array $lecturers, bool $confirmed, ?string $expectedStatus): Praktikum
    {
        return DB::transaction(function () use ($id, $attributes, $lecturers, $confirmed, $expectedStatus) {
            $practicum = Praktikum::lockForUpdate()->findOrFail($id);
            $active = ($attributes['status'] ?? $practicum->status) === 'aktif';
            $changed = (bool)$practicum->is_active !== $active;
            if (($expectedStatus !== null && $expectedStatus !== $practicum->status) || ($changed && (!$confirmed || $expectedStatus === null))) {
                throw ValidationException::withMessages(['status' => 'Konfirmasikan perubahan status praktikum. Jika status telah berubah, muat ulang halaman terlebih dahulu.']);
            }
            $before = ['status'=>$practicum->status, 'koor_id'=>$practicum->koor_id,
                'dosen_ids'=>$practicum->dosens()->pluck('users.id')->all()];
            $memberships = AsprakPraktikum::where('praktikum_id', $id)->get();
            $before['penugasan'] = $memberships->map->only(['id','user_id','role'])->all();
            $userIds = $memberships->pluck('user_id')->merge($before['dosen_ids'])->push($practicum->koor_id)->filter()->unique();
            if (!$active) {
                // Soft deletion keeps module allocations, submissions and their authors intact.
                foreach ($memberships as $membership) $membership->delete();
                $practicum->dosens()->detach();
                $attributes['koor_id'] = null;
                if (\Illuminate\Support\Facades\Schema::hasColumn($practicum->getTable(),'dosen_id')) $practicum->dosen_id = null;
            } elseif ($lecturers !== null) {
                $practicum->dosens()->sync($lecturers);
            }
            $attributes['is_active'] = $active;
            $attributes['status'] = $active ? 'aktif' : 'nonaktif';
            $practicum->update($attributes);
            if (!$active) {
                foreach (User::whereIn('id', $userIds)->get() as $user) {
                    foreach (['koor'=>'koor_prak','asprak'=>'asprak'] as $membershipRole=>$accountRole) {
                        $remaining = AsprakPraktikum::where('user_id',$user->id)->where('role',$membershipRole)
                            ->whereHas('praktikum',fn($q)=>$q->where('is_active',true))->exists();
                        if ($membershipRole === 'koor') $remaining = $remaining || Praktikum::where('koor_id',$user->id)->where('is_active',true)->exists();
                        if (!$remaining && $user->hasRole($accountRole)) $user->removeRole($accountRole);
                    }
                    // Never remove the global lecturer role: it is shared by other SITKOM modules.
                    $user->syncPermissionsFromRoles();
                    $user->clearUserCache();
                }
            }
            if ($changed) EoAuditLog::create([
                'user_id'=>auth()->id(), 'action'=>'update', 'subject_type'=>'Praktikum', 'subject_id'=>$id,
                'description'=>$active?'Praktikum diaktifkan kembali; penugasan tidak dipulihkan otomatis.':'Praktikum diarsipkan dan penugasan pengelola dicabut.',
                'old_data'=>$before, 'new_data'=>['status'=>$practicum->status,'id'=>$id], 'created_at'=>now(),
            ]);
            return $practicum;
        });
    }
}
