<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Support\BladeFeatureAccess;
use Tests\TestCase;

/** Pure unit test: no database needed. */
class PhaseGateTest extends TestCase
{
    public function test_sempro_done_group_cannot_upload_pdc2(): void
    {
        $this->assertNull(BladeFeatureAccess::phaseGateReason('PDC1', 'SEMPRO_DONE'));
        $this->assertNull(BladeFeatureAccess::phaseGateReason('SEMPRO', 'SEMPRO_DONE'));
        $this->assertNotNull(BladeFeatureAccess::phaseGateReason('PDC2', 'SEMPRO_DONE'));
        $this->assertNotNull(BladeFeatureAccess::phaseGateReason('TA', 'SEMPRO_DONE'));
        $this->assertNotNull(BladeFeatureAccess::phaseGateReason('EXPO', 'SEMPRO_DONE'));
        $this->assertNotNull(BladeFeatureAccess::phaseGateReason('SIDANG', 'SEMPRO_DONE'));
    }

    public function test_ready_for_sempro_group_cannot_upload_pdc2(): void
    {
        $this->assertNull(BladeFeatureAccess::phaseGateReason('SEMPRO', 'READY_FOR_SEMPRO'));
        $this->assertNotNull(BladeFeatureAccess::phaseGateReason('PDC2', 'READY_FOR_SEMPRO'));
    }

    public function test_pdc2_active_group_can_upload_pdc2_but_not_expo(): void
    {
        $this->assertNull(BladeFeatureAccess::phaseGateReason('PDC1', 'PDC2_ACTIVE'));
        $this->assertNull(BladeFeatureAccess::phaseGateReason('SEMPRO', 'PDC2_ACTIVE'));
        $this->assertNull(BladeFeatureAccess::phaseGateReason('PDC2', 'PDC2_ACTIVE'));
        $this->assertNull(BladeFeatureAccess::phaseGateReason('TA', 'PDC2_ACTIVE'));
        $this->assertNotNull(BladeFeatureAccess::phaseGateReason('EXPO', 'PDC2_ACTIVE'));
        $this->assertNotNull(BladeFeatureAccess::phaseGateReason('SIDANG', 'PDC2_ACTIVE'));
    }

    public function test_unknown_phase_status_and_groups_fail_open(): void
    {
        $this->assertNull(BladeFeatureAccess::phaseGateReason('UNKNOWN_PHASE', 'SEMPRO_DONE'));
        $this->assertNull(BladeFeatureAccess::phaseGateReason('PDC2', null));
        $this->assertNull(BladeFeatureAccess::phaseGateReason('PDC2', 'LEGACY_STATUS'));
    }

    public function test_expo_upload_reason_requires_schedule_like_sempro(): void
    {
        $expo = ['phase' => 'EXPO', 'status' => 'unlocked'];
        $this->assertSame(
            'Expo belum dijadwalkan. Mohon tunggu admin menjadwalkan Expo terlebih dahulu.',
            BladeFeatureAccess::documentUploadReason($expo, true, null, false)
        );
        $this->assertNull(BladeFeatureAccess::documentUploadReason($expo, true, null, true));
        // Missing prerequisites still report first, mirroring SEMPRO precedence.
        $this->assertSame(
            'Complete the previous phase requirements first.',
            BladeFeatureAccess::documentUploadReason(['phase' => 'EXPO', 'status' => 'locked'], true, null, false)
        );
        // SEMPRO behavior unchanged by the new parameter.
        $this->assertStringContainsString(
            'SEMPRO belum dijadwalkan',
            BladeFeatureAccess::documentUploadReason(['phase' => 'SEMPRO', 'status' => 'unlocked'], false)
        );
    }
}
