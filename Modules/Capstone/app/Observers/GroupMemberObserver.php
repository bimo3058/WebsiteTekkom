<?php

namespace Modules\Capstone\Observers;

use Modules\Capstone\Jobs\RefreshGroupReadiness;
use Modules\Capstone\Models\GroupMember;

class GroupMemberObserver
{
    public function created(GroupMember $member): void
    {
        RefreshGroupReadiness::dispatch($member->group_id);
    }

    public function deleted(GroupMember $member): void
    {
        RefreshGroupReadiness::dispatch($member->group_id);
    }

    public function updated(GroupMember $member): void
    {
        if ($member->isDirty('group_id')) {
            $oldGroupId = $member->getOriginal('group_id');
            $newGroupId = $member->getAttribute('group_id');

            RefreshGroupReadiness::dispatch($oldGroupId);
            RefreshGroupReadiness::dispatch($newGroupId);
        }
    }
}
