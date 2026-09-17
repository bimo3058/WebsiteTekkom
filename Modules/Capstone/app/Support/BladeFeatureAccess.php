<?php

namespace Modules\Capstone\Support;

use App\Models\User;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\PeriodRegistration;

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

    public static function snapshot(User $user): array
    {
        $studentId = CapstoneActor::student($user)->id;
        $registered = PeriodRegistration::where('user_id', $studentId)->exists();
        $membership = GroupMember::where('student_id', $studentId)
            ->whereHas('group', fn ($query) => $query->whereNotIn('status', ['CLOSED', 'DISSOLVED']))
            ->with('group:id,status,period_id')->first();
        $status = $membership?->group?->status;

        // my-period auto-registers an existing active membership. Do not block
        // that user before its existing registration repair can run.
        return [
            'registered' => $registered || $status !== null,
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
        if (!str_starts_with($path, '/mahasiswa/') || in_array($path, ['/mahasiswa/dashboard','/mahasiswa/registration'], true)) {
            return null;
        }
        if (!($state['registered'] ?? false)) {
            return 'Register for a period first';
        }
        $feature = explode('/', trim($path, '/'))[1] ?? '';
        if (in_array($feature, ['documents','ta-submission','schedule','ta-defense','expo','peer-review','grades'], true)
            && (!in_array($state['group_status'] ?? null, self::PDC1_STATUSES, true)
                && !($state['pdc1_started'] ?? false))) {
            return 'Available after PDC1 starts';
        }
        if ($feature === 'expo' && !in_array($state['group_status'] ?? null, self::EXPO_STATUSES, true)) {
            return 'Available when your group is ready for Expo';
        }
        if ($feature === 'peer-review' && !in_array($state['group_status'] ?? null, self::PEER_REVIEW_STATUSES, true)) {
            return 'Available after Expo registration';
        }

        return null;
    }

    /** The same upload conditions used by DocumentsFeature, also checked on POST. */
    public static function documentUploadReason(array $phase, bool $semproScheduled, ?string $documentStatus = null): ?string
    {
        if (($phase['status'] ?? 'locked') === 'locked') return 'Complete the previous phase requirements first.';
        if (($phase['status'] ?? null) === 'completed') return 'This phase is already completed.';
        if (($phase['phase'] ?? null) === 'SEMPRO' && ! $semproScheduled) return 'SEMPRO belum dijadwalkan. Mohon tunggu admin menjadwalkan SEMPRO terlebih dahulu.';
        if ($documentStatus === 'APPROVED') return 'Approved documents cannot be replaced.';
        return null;
    }
}
