<?php

namespace Modules\Capstone\Services;

use Illuminate\Support\Collection;
use Modules\Capstone\Models\CapstoneBid;
use Modules\Capstone\Models\CapstoneGroup;

class BidService
{
    /**
     * Semua bid dari satu group, urut priority.
     */
    public function getByGroup(int $groupId): Collection
    {
        return CapstoneBid::where('group_id', $groupId)
            ->with(['title.lecturer'])
            ->orderBy('priority')
            ->get();
    }

    /**
     * Semua bid untuk judul tertentu (untuk dosen review).
     */
    public function getByTitle(int $titleId): Collection
    {
        return CapstoneBid::where('title_id', $titleId)
            ->with(['group.members.student'])
            ->orderBy('priority')
            ->get();
    }

    /**
     * Submit bid dari group ke judul dengan priority.
     */
    public function submitBid(int $groupId, int $titleId, int $priority, array $extra = []): CapstoneBid
    {
        return CapstoneBid::updateOrCreate(
            ['group_id' => $groupId, 'title_id' => $titleId],
            array_merge([
                'priority' => $priority,
                'status'   => CapstoneBid::STATUS_PENDING,
            ], $extra)
        );
    }

    /**
     * Admin accept bid — set status ACCEPTED, reject semua bid lain dari group.
     */
    public function acceptBid(CapstoneBid $bid): void
    {
        // Reject bid lain dari group yang sama
        CapstoneBid::where('group_id', $bid->group_id)
            ->where('id', '!=', $bid->id)
            ->update(['status' => CapstoneBid::STATUS_REJECTED]);

        $bid->update(['status' => CapstoneBid::STATUS_ACCEPTED]);

        // Update group — set title
        CapstoneGroup::where('id', $bid->group_id)->update([
            'title_id' => $bid->title_id,
            'status'   => CapstoneGroup::STATUS_TITLE_SELECTED,
        ]);
    }

    public function rejectBid(CapstoneBid $bid): void
    {
        $bid->update(['status' => CapstoneBid::STATUS_REJECTED]);
    }
}