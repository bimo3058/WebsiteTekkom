<?php

namespace Modules\Capstone\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Capstone\Http\Requests\Grade\UpdateWeightsRequest;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Services\GradeCalculationService;
use Modules\Capstone\Support\CapstoneActor;

class GradeConfigurationController extends Controller
{
    use ApiResponseTrait;

    private const DEFAULT_PDC1_WEIGHTS = [
        'SEMPRO' => 50,
        'BIMBINGAN_SEMPRO' => 50,
    ];

    private const DEFAULT_PDC2_WEIGHTS = [
        'NILAI_DOSEN' => 25,
        'MILESTONE' => 25,
        'EXPO' => 25,
        'PEER_REVIEW' => 25,
    ];

    private const DEFAULT_TA_WEIGHTS = [
        'BIMBINGAN_TA' => 50,
        'SIDANG_TA' => 50,
    ];

    protected $gradeCalculationService;

    public function __construct(GradeCalculationService $gradeCalculationService)
    {
        $this->gradeCalculationService = $gradeCalculationService;
    }

    /**
     * Get grade weight configuration for PDC1
     */
    public function getPDC1Weights(Request $request, int $periodId): JsonResponse
    {
        $period = Period::findOrFail($periodId);

        $weights = $period->grade_configuration['pdc1'] ?? self::DEFAULT_PDC1_WEIGHTS;

        return $this->successResponse([
            'period_id' => $periodId,
            'period_name' => $period->name,
            'pdc1_weights' => $weights,
            'total_weight' => array_sum($weights),
        ]);
    }

    /**
     * Get grade weight configuration for PDC2
     */
    public function getPDC2Weights(Request $request, int $periodId): JsonResponse
    {
        $period = Period::findOrFail($periodId);

        $weights = $period->grade_configuration['pdc2'] ?? self::DEFAULT_PDC2_WEIGHTS;

        return $this->successResponse([
            'period_id' => $periodId,
            'period_name' => $period->name,
            'pdc2_weights' => $weights,
            'total_weight' => array_sum($weights),
        ]);
    }

    /**
     * Get grade weight configuration for TA
     */
    public function getTAWeights(Request $request, int $periodId): JsonResponse
    {
        $period = Period::findOrFail($periodId);

        $weights = $period->grade_configuration['ta'] ?? self::DEFAULT_TA_WEIGHTS;

        return $this->successResponse([
            'period_id' => $periodId,
            'period_name' => $period->name,
            'ta_weights' => $weights,
            'total_weight' => array_sum($weights),
        ]);
    }

    /**
     * Update grade weight configuration
     */
    public function updateWeights(UpdateWeightsRequest $request, int $periodId): JsonResponse
    {
        $period = Period::findOrFail($periodId);

        $validated = $request->validated();

        $gradeConfig = $period->grade_configuration ?? [];

        if (isset($validated['pdc1_weights'])) {
            $gradeConfig['pdc1'] = array_merge(
                $gradeConfig['pdc1'] ?? self::DEFAULT_PDC1_WEIGHTS,
                $validated['pdc1_weights']
            );
        }

        if (isset($validated['pdc2_weights'])) {
            $gradeConfig['pdc2'] = array_merge(
                $gradeConfig['pdc2'] ?? self::DEFAULT_PDC2_WEIGHTS,
                $validated['pdc2_weights']
            );
        }

        if (isset($validated['ta_weights'])) {
            $gradeConfig['ta'] = array_merge(
                $gradeConfig['ta'] ?? self::DEFAULT_TA_WEIGHTS,
                $validated['ta_weights']
            );
        }

        $period->update(['grade_configuration' => $gradeConfig]);

        return $this->successResponse([
            'period_id' => $periodId,
            'grade_configuration' => $gradeConfig,
        ], 'Grade configuration updated successfully');
    }

    /**
     * Calculate PDC1 grade for a group
     */
    public function calculatePDC1Grade(Request $request, int $groupId): JsonResponse
    {
        return $this->phaseGrade($groupId, 'pdc1', self::DEFAULT_PDC1_WEIGHTS, 'pdc1_grades', 'final_pdc1_score');
    }

    /**
     * Calculate PDC2 grade for a group
     */
    public function calculatePDC2Grade(Request $request, int $groupId): JsonResponse
    {
        return $this->phaseGrade($groupId, 'pdc2', self::DEFAULT_PDC2_WEIGHTS, 'pdc2_grades', 'final_pdc2_score');
    }

    private function phaseGrade(int $groupId, string $weightsKey, array $defaults, string $gradesKey, string $finalKey): JsonResponse
    {
        $group = Group::with('period')->findOrFail($groupId);
        $period = $group->period;

        $result = $this->gradeCalculationService->weightedGroupPhaseGrade($group, $weightsKey, $defaults);

        return $this->successResponse([
            'group_id' => $groupId,
            'group_name' => $group->code,
            'period_id' => $period->id,
            $gradesKey => $result['grades'],
            $finalKey => $result['final'],
            'total_weight' => $result['total_weight'],
        ]);
    }

    /**
     * Get full grade configuration for admin view
     */
    public function getFullConfiguration(Request $request, int $periodId): JsonResponse
    {
        $period = Period::findOrFail($periodId);

        $config = $period->grade_configuration ?? [];

        return $this->successResponse([
            'period_id' => $periodId,
            'period_name' => $period->name,
            'pdc1' => [
                'weights' => $config['pdc1'] ?? self::DEFAULT_PDC1_WEIGHTS,
                'components' => ['SEMPRO', 'BIMBINGAN_SEMPRO'],
                'total_weight' => array_sum($config['pdc1'] ?? self::DEFAULT_PDC1_WEIGHTS),
            ],
            'pdc2' => [
                'weights' => $config['pdc2'] ?? self::DEFAULT_PDC2_WEIGHTS,
                'components' => ['NILAI_DOSEN', 'MILESTONE', 'EXPO', 'PEER_REVIEW'],
                'total_weight' => array_sum($config['pdc2'] ?? self::DEFAULT_PDC2_WEIGHTS),
            ],
            'ta' => [
                'weights' => $config['ta'] ?? self::DEFAULT_TA_WEIGHTS,
                'components' => ['BIMBINGAN_TA', 'SIDANG_TA'],
                'total_weight' => array_sum($config['ta'] ?? self::DEFAULT_TA_WEIGHTS),
            ],
        ]);
    }

    /**
     * Reset grade configuration to defaults
     */
    public function resetToDefaults(Request $request, int $periodId): JsonResponse
    {
        $period = Period::findOrFail($periodId);

        $gradeConfig = [
            'pdc1' => self::DEFAULT_PDC1_WEIGHTS,
            'pdc2' => self::DEFAULT_PDC2_WEIGHTS,
            'ta' => self::DEFAULT_TA_WEIGHTS,
        ];

        $period->update(['grade_configuration' => $gradeConfig]);

        return $this->successResponse([
            'period_id' => $periodId,
            'grade_configuration' => $gradeConfig,
        ], 'Grade configuration reset to defaults');
    }

    /**
     * Calculate TA grade for a student (per-student, not group-based)
     */
    public function calculateTAGrade(Request $request, int $studentId): JsonResponse
    {
        $student = Student::findOrFail($studentId);

        // Get student's group to access period
        $groupMember = GroupMember::with('group.period')
            ->where('student_id', $studentId)
            ->first();

        if (! $groupMember || ! $groupMember->group) {
            return $this->notFoundResponse('Student is not assigned to any group');
        }

        $group = $groupMember->group;
        $period = $group->period;

        $result = $this->gradeCalculationService->weightedStudentPhaseGrade(
            $student->id,
            $period,
            'ta',
            self::DEFAULT_TA_WEIGHTS
        );

        return $this->successResponse([
            'student_id' => $studentId,
            'student_name' => $student->name,
            'group_id' => $group->id,
            'group_name' => $group->code,
            'period_id' => $period->id,
            'ta_grades' => $result['grades'],
            'final_ta_score' => $result['final'],
            'total_weight' => $result['total_weight'],
        ]);
    }

    /**
     * Get grades for the authenticated student (my grades)
     */
    public function getMyGrades(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = CapstoneActor::student($user);

        // Get student's group
        $groupMember = GroupMember::with('group.period')
            ->where('student_id', $student->id)
            ->whereHas('group', fn ($query) => $query->whereNotIn('status', ['CLOSED', 'DISSOLVED']))
            ->first();

        if (! $groupMember || ! $groupMember->group) {
            return $this->errorResponse('You are not assigned to any group', 404);
        }

        $group = $groupMember->group;

        // Calculate grades using the service
        $grades = $this->gradeCalculationService->calculateFinalGradeForStudent(
            $student->id,
            $group->id
        );

        if (! $grades) {
            return $this->successResponse([
                'grades' => null,
                'group' => [
                    'id' => $group->id,
                    'name' => $group->code ?? 'Group #'.$group->id,
                ],
                'period' => [
                    'id' => $group->period->id,
                    'name' => $group->period->name,
                ],
            ], 'No grades available yet');
        }

        return $this->successResponse([
            'grades' => $grades,
            'group' => [
                'id' => $group->id,
                'name' => $group->code ?? 'Group #'.$group->id,
            ],
            'period' => [
                'id' => $group->period->id,
                'name' => $group->period->name,
            ],
            'student' => [
                'id' => $student->id,
                'name' => $user->name,
                'nim' => $student->student_number,
            ],
        ]);
    }

    /**
     * Get grades for a specific student (admin/dosen only)
     */
    public function getStudentGrades(Request $request, int $studentId): JsonResponse
    {
        $student = Student::findOrFail($studentId);

        // Get student's group
        $groupMember = GroupMember::with('group.period')
            ->where('student_id', $studentId)
            ->first();

        if (! $groupMember || ! $groupMember->group) {
            return $this->errorResponse('Student is not assigned to any group', 404);
        }

        $group = $groupMember->group;

        // Calculate grades using the service
        $grades = $this->gradeCalculationService->calculateFinalGradeForStudent(
            $studentId,
            $group->id
        );

        return $this->successResponse([
            'grades' => $grades,
            'group' => [
                'id' => $group->id,
                'name' => $group->code ?? 'Group #'.$group->id,
            ],
            'period' => [
                'id' => $group->period->id,
                'name' => $group->period->name,
            ],
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nim' => $student->nim,
            ],
        ]);
    }
}
