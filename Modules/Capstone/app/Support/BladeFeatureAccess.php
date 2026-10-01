<?php

namespace Modules\Capstone\Support;

use App\Models\User;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Services\GroupStateMachine;

/**
 * Menu prerequisites ported from CTMS AppSidebar.tsx and RegistrationController.
 * Blade conversion readiness is not an access condition. Admin and lecturer
 * navigation has no student registration/phase gate; policies still apply.
 */
final class BladeFeatureAccess
{
    public const PDC1_STATUSES = [
        'PDC1_ACTIVE', 'READY_FOR_SEMPRO', 'SEMPRO_DONE', 'PDC2_ACTIVE', 'TA_DRAFT',
        'PDC2_READY_FOR_EXPO', 'EXPO_REGISTERED', 'EXPO_DONE', 'PDC2_COMPLETED',
        'READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS', 'CLOSED',
    ];

    public const EXPO_STATUSES = ['PDC2_READY_FOR_EXPO', 'EXPO_REGISTERED', 'EXPO_DONE', 'READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS', 'CLOSED'];

    public const PEER_REVIEW_STATUSES = ['EXPO_REGISTERED', 'EXPO_DONE', 'READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS', 'CLOSED'];

    /**
     * Minimum group status required to upload documents for each phase.
     * Null means no per-phase gate (the page-level PDC1 gate applies).
     * Compared against GroupStateMachine::ALL_STATUSES order; unknown or
     * legacy statuses fail open so existing coursework is never blocked.
     */
    public const PHASE_MIN_STATUS = [
        'PDC1' => null,
        'SEMPRO' => 'READY_FOR_SEMPRO',
        'PDC2' => 'PDC2_ACTIVE',
        'TA' => 'PDC2_ACTIVE',
        'EXPO' => 'PDC2_READY_FOR_EXPO',
        'SIDANG' => 'EXPO_DONE',
    ];

    public static function snapshot(User $user): array
    {
        $studentId = CapstoneActor::student($user)->id;
        $registrationStatus = PeriodRegistration::where('user_id', $studentId)->value('status');
        $registered = $registrationStatus !== null && strtoupper($registrationStatus) === PeriodRegistration::STATUS_APPROVED;
        $pendingRegistration = $registrationStatus !== null && strtoupper($registrationStatus) === PeriodRegistration::STATUS_PENDING;
        $membership = GroupMember::where('student_id', $studentId)
            ->whereHas('group', fn ($query) => $query->whereNotIn('status', ['CLOSED', 'DISSOLVED']))
            ->with('group:id,status,period_id')->first();
        $status = $membership?->group?->status;

        // my-period auto-registers an existing active membership. Do not block
        // that user before its existing registration repair can run.
        return [
            'registered' => $registered || $status !== null,
            'pending_registration' => $pendingRegistration && $status === null,
            'group_status' => $status,
            'pdc1_started' => $membership?->group ? self::hasStartedPdc1($membership->group) : false,
        ];
    }

    public static function hasStartedPdc1(Group $group): bool
    {
        if ($group->status === 'DISSOLVED') {
            return false;
        }
        if (in_array($group->status, self::PDC1_STATUSES, true)) {
            return true;
        }

        // Existing coursework survives legacy status values and reopened bidding.
        // Check this group's persisted work, without changing its approval state.
        return $group->documents()->whereIn('phase', ['PDC1', 'SEMPRO', 'PDC2', 'TA_DRAFT', 'EXPO', 'TA', 'SIDANG'])->exists();
    }

    public static function reason(string $path, array $state): ?string
    {
        if (! str_starts_with($path, '/mahasiswa/') || in_array($path, ['/mahasiswa/dashboard', '/mahasiswa/registration'], true)) {
            return null;
        }
        if (! ($state['registered'] ?? false)) {
            if (! empty($state['pending_registration'])) {
                return 'Your join request is still pending admin approval';
            }

            return 'Register for a period first';
        }
        $feature = explode('/', trim($path, '/'))[1] ?? '';
        if (in_array($feature, ['documents', 'ta-submission', 'schedule', 'ta-defense', 'expo', 'peer-review', 'grades'], true)
            && (! in_array($state['group_status'] ?? null, self::PDC1_STATUSES, true)
                && ! ($state['pdc1_started'] ?? false))) {
            return 'Available after PDC1 starts';
        }
        if ($feature === 'expo' && ! in_array($state['group_status'] ?? null, self::EXPO_STATUSES, true)) {
            return 'Available when your group is ready for Expo';
        }
        if ($feature === 'peer-review' && ! in_array($state['group_status'] ?? null, self::PEER_REVIEW_STATUSES, true)) {
            return 'Available after Expo registration';
        }

        return null;
    }

    /** The same upload conditions used by DocumentsFeature, also checked on POST. */
    public static function documentUploadReason(array $phase, bool $semproScheduled, ?string $documentStatus = null, bool $expoScheduled = true): ?string
    {
        if (($phase['status'] ?? 'locked') === 'locked') {
            return 'Complete the previous phase requirements first.';
        }
        if (($phase['status'] ?? null) === 'completed') {
            return 'This phase is already completed.';
        }
        if (($phase['phase'] ?? null) === 'SEMPRO' && ! $semproScheduled) {
            return 'SEMPRO belum dijadwalkan. Mohon tunggu admin menjadwalkan SEMPRO terlebih dahulu.';
        }
        if (($phase['phase'] ?? null) === 'EXPO' && ! $expoScheduled) {
            return 'Expo belum dijadwalkan. Mohon tunggu admin menjadwalkan Expo terlebih dahulu.';
        }
        if ($documentStatus === 'APPROVED') {
            return 'Approved documents cannot be replaced.';
        }

        return null;
    }

    /**
     * Per-phase group-status gate for document uploads.
     *
     * The document-approval chain alone cannot gate uploads: a group whose
     * SEMPRO documents were approved is still in SEMPRO_DONE, not
     * PDC2_ACTIVE, so PDC2 uploads must stay locked until the group
     * actually transitions. Returns null when the upload is allowed.
     */
    public static function phaseGateReason(string $phase, ?string $groupStatus): ?string
    {
        $min = self::PHASE_MIN_STATUS[$phase] ?? null;
        if ($min === null || $groupStatus === null) {
            return null;
        }

        $statuses = GroupStateMachine::ALL_STATUSES;
        $groupIdx = array_search($groupStatus, $statuses, true);
        $minIdx = array_search($min, $statuses, true);

        // Unknown or legacy statuses fail open; the page-level gate applies.
        if ($groupIdx === false || $minIdx === false) {
            return null;
        }

        if ($groupIdx >= $minIdx) {
            return null;
        }

        return "This phase is not available while your group is in {$groupStatus} stage.";
    }
}
