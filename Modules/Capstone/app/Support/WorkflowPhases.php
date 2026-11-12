<?php

namespace Modules\Capstone\Support;

final class WorkflowPhases
{
    public const PHASES = ['PDC1', 'SEMPRO', 'PDC2', 'TA_DRAFT', 'EXPO'];

    public const UNLOCK_RULES = [
        'PDC1' => null,
        'SEMPRO' => 'PDC1',
        'PDC2' => 'SEMPRO',
        'TA_DRAFT' => 'PDC2',
        'EXPO' => 'PDC2',
    ];

    public const STATUS_GATES = [
        'PDC2' => 'SEMPRO_DONE',
        'TA_DRAFT' => 'PDC2_ACTIVE',
        'EXPO' => 'PDC2_READY_FOR_EXPO',
    ];

    public const LEGACY_ALIASES = [
        'TA' => 'TA_DRAFT',
    ];

    /**
     * UI-facing stepper phases. Pinned 1:1 by StepperPhaseContractTest against
     * the mahasiswa dashboard blade (`TA` displays as "TA Draft", `SIDANG` as
     * "Sidang TA"). Deliberately different from engine PHASES: the stepper
     * needs display codes, the engine needs approval-chain codes.
     */
    public const UI_PHASES = ['PDC1', 'SEMPRO', 'PDC2', 'TA', 'EXPO', 'SIDANG'];

    public static function isValid(string $phase): bool
    {
        return in_array($phase, self::PHASES, true);
    }

    public static function normalize(string $phase): string
    {
        return self::LEGACY_ALIASES[$phase] ?? $phase;
    }

    public static function unlockPrerequisite(string $phase): ?string
    {
        return self::UNLOCK_RULES[self::normalize($phase)] ?? null;
    }

    public static function statusGate(string $phase): ?string
    {
        return self::STATUS_GATES[self::normalize($phase)] ?? null;
    }

    public static function nextPhase(string $currentPhase): ?string
    {
        $phase = self::normalize($currentPhase);
        $index = array_search($phase, self::PHASES, true);

        if ($index === false || $index >= count(self::PHASES) - 1) {
            return null;
        }

        return self::PHASES[$index + 1];
    }
}
