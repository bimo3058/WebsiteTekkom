<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class ActiveStudent
{
    public static function apply(Builder $query): Builder
    {
        $query->whereHas('student')->whereNull('users.suspended_at');
        if (Schema::hasTable('mk_kemahasiswaan')) {
            $query->whereExists(fn ($q) => $q->selectRaw('1')->from('mk_kemahasiswaan')
                ->whereColumn('mk_kemahasiswaan.user_id', 'users.id')->where('mk_kemahasiswaan.status', 'aktif'));
        }
        return $query;
    }

    public static function eligible(User $user): bool
    {
        return self::apply(User::whereKey($user->id))->exists();
    }

    public static function ensure(User $user): void
    {
        if (! self::eligible($user)) throw \Illuminate\Validation\ValidationException::withMessages([
            'user_id' => 'Pilih mahasiswa berstatus aktif dengan akun yang tidak ditangguhkan.',
        ]);
    }
}
