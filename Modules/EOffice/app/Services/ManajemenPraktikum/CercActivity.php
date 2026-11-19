<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Modules\EOffice\Models\CercCoordinatorHistory;

class CercActivity
{
    public static function record(string $description, string $context, ?array $before = null, ?array $after = null): void
    {
        if (!auth()->user()?->hasRole('koordinator_cerc')) return;

        CercCoordinatorHistory::create([
            'user_id'=>auth()->id(), 'action'=>'CERC_ACTIVITY',
            'description'=>$description,
            'old_data'=>['context'=>$context, 'summary'=>$before],
            'new_data'=>['context'=>$context, 'summary'=>$after],
            'created_at'=>now(),
        ]);
    }
}
