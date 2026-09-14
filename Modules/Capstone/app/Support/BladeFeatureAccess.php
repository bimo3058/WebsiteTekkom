<?php

namespace Modules\Capstone\Support;

use App\Models\User;
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
        'PDC2_READY_FOR_EXPO', 'EXPO_REGISTERED', 'EXPO_DONE', 'READY_FOR_TA_INDIVIDUAL', 'CLOSED',
    ];
    public const EXPO_STATUSES = ['PDC2_READY_FOR_EXPO', 'EXPO_REGISTERED', 'EXPO_DONE', 'READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS', 'CLOSED'];
    public const PEER_REVIEW_STATUSES = ['EXPO_REGISTERED', 'EXPO_DONE', 'READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS', 'CLOSED'];

    public static function snapshot(User $user): array
    {
        $studentId = CapstoneActor::student($user)->id;
        $registered = PeriodRegistration::where('user_id', $studentId)->exists();
        $membership = GroupMember::where('student_id', $studentId)
            ->whereHas('group', fn ($query) => $query->whereNotIn('status', ['CLOSED']))
            ->with('group:id,status,period_id')->first();
        $status = $membership?->group?->status;

        // my-period auto-registers an existing active membership. Do not block
        // that user before its existing registration repair can run.
        return ['registered' => $registered || ($status !== null && !in_array($status, ['CLOSED','DISSOLVED'], true)), 'group_status' => $status];
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
            && !in_array($state['group_status'] ?? null, self::PDC1_STATUSES, true)) {
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
