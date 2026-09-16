<?php

namespace Modules\Capstone\Services;

use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Models\ExpoRegistration;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaSubmission;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ExpoService
{
    protected GroupStateMachine $stateMachine;

    public function __construct(GroupStateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * Register a group to an expo event.
     * Uses lockForUpdate to prevent capacity race condition.
     */
    public function registerGroupToEvent(int $eventId, int $groupId, int $userId): ExpoRegistration
    {
        return DB::transaction(function () use ($eventId, $groupId, $userId) {
            // ⚠ Row lock: prevent capacity race condition
            $event = ExpoEvent::lockForUpdate()->findOrFail($eventId);

            // Guard: event must be published
            if (!$event->is_published) {
                throw new InvalidArgumentException('This expo event is not open for registration.');
            }

            // Guard: capacity check (concurrency-safe with lockForUpdate)
            $currentCount = $event->registrations()->where('status','REGISTERED')->count();
            if ($currentCount >= $event->capacity) {
                throw new InvalidArgumentException('This expo event is full. No remaining capacity.');
            }

            // Guard: group must exist and be in correct state
            $group = Group::lockForUpdate()->findOrFail($groupId);

            // ⚠ Validate state machine transition BEFORE attempting
            if (!$this->stateMachine->canTransition($group->status, 'EXPO_REGISTERED')) {
                throw new InvalidArgumentException(
                    "Group is not eligible for expo registration. Current status: {$group->status}. " .
                    "Required: PDC2_READY_FOR_EXPO."
                );
            }

            // Guard: group must belong to same period as event
            if ($group->period_id !== $event->period_id) {
                throw new InvalidArgumentException('Group does not belong to the same period as this event.');
            }

            // Guard: Must have at least one TA Draft submitted
            $taDraftsCount = TaSubmission::where('group_id', $group->id)->count();
            if ($taDraftsCount < 1) {
                throw new InvalidArgumentException(
                    "Group is not eligible for expo registration. At least 1 member must have submitted a TA draft."
                );
            }

            // Create registration
            $registration = ExpoRegistration::updateOrCreate([
                'expo_event_id' => $event->id,
                'group_id' => $group->id,
            ], [
                'registered_at' => now(),
                'status' => 'REGISTERED',
            ]);

            // Auto-create seminar schedule for expo
            SeminarSchedule::updateOrCreate([
                'group_id' => $group->id,
                'type' => 'EXPO',
            ], [
                'date' => $event->date,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
                'room' => $event->room,
                'status' => 'APPROVED',
            ]);

            // Transition state
            $this->stateMachine->transition($group, 'EXPO_REGISTERED');

            // Audit
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'EXPO_REGISTRATION',
                'target_type' => 'ExpoRegistration',
                'target_id' => $registration->id,
                'payload' => [
                    'event_id' => $event->id,
                    'group_id' => $group->id,
                    'event_name' => $event->name,
                ],
            ]);

            return $registration->load(['expoEvent', 'group']);
        });
    }

    public function withdrawGroupFromEvent(int $eventId, int $groupId, int $userId): void
    {
        DB::transaction(function () use ($eventId,$groupId,$userId) {
            $event=ExpoEvent::lockForUpdate()->findOrFail($eventId);
            $group=Group::lockForUpdate()->findOrFail($groupId);
            $registration=ExpoRegistration::where('expo_event_id',$event->id)->where('group_id',$group->id)->where('status','REGISTERED')->lockForUpdate()->firstOrFail();
            if ($group->status !== 'EXPO_REGISTERED') throw new InvalidArgumentException('This group can no longer withdraw from Expo.');
            if (\Modules\Capstone\Models\ExpoSelfEvaluation::where('expo_registration_id',$registration->id)->exists()
                || \Modules\Capstone\Models\ExpoStudentDocument::where('expo_registration_id',$registration->id)->exists()) {
                throw new InvalidArgumentException('Withdrawal is locked after an evaluation or document has been submitted.');
            }
            $registration->update(['status'=>'WITHDRAWN']);
            SeminarSchedule::where('group_id',$group->id)->where('type','EXPO')->update(['status'=>'CANCELLED']);
            $this->stateMachine->transition($group,'PDC2_READY_FOR_EXPO');
            AuditLog::create(['user_id'=>$userId,'action'=>'EXPO_WITHDRAWAL','target_type'=>'ExpoRegistration','target_id'=>$registration->id,'payload'=>['event_id'=>$event->id,'group_id'=>$group->id]]);
        });
    }
}
