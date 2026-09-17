<?php

namespace Modules\Capstone\Http\Controllers\Admin;

use Modules\Capstone\Http\Controllers\ApiResponseTrait;
use Modules\Capstone\Http\Controllers\Controller;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PhaseDocumentRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PhaseDocumentRequirementController extends Controller
{
    use ApiResponseTrait;

    const PHASES = ['PDC1', 'SEMPRO', 'PDC2', 'EXPO', 'TA', 'SIDANG'];

    public function index(Request $request)
    {
        $query = PhaseDocumentRequirement::query();

        if ($request->has('period_id')) {
            $query->where('period_id', $request->period_id);
        }

        if ($request->has('phase')) {
            $query->where('phase', $request->phase);
        }

        $requirements = $query->orderBy('phase')->orderBy('name')->get();

        return $this->successResponse($requirements, 'Document requirements retrieved successfully');
    }

    public function byPeriod($periodId)
    {
        $requirements = PhaseDocumentRequirement::where('period_id', $periodId)
            ->orderBy('phase')
            ->orderBy('name')
            ->get();

        return $this->successResponse($requirements, 'Document requirements retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period_id' => 'required|exists:capstone_periods,id',
            'phase' => 'required|string|in:'.implode(',', self::PHASES),
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $data = $validator->validated();
        return DB::transaction(function () use ($data) {
            $period = Period::whereKey($data['period_id'])->lockForUpdate()->firstOrFail();
            if ($period->is_finalized) return $this->errorResponse('Cannot modify document requirements for a finalized period.', 403);
            return $this->createdResponse(PhaseDocumentRequirement::create($data), 'Document requirement created successfully');
        });
    }

    public function update(Request $request, string $id)
    {
        $requirement = PhaseDocumentRequirement::with('period')->findOrFail($id);

        // Check if period is finalized
        if ($requirement->period && $requirement->period->is_finalized) {
            return $this->errorResponse('Cannot modify document requirements for a finalized period.', 403);
        }

        $validator = Validator::make($request->all(), [
            'period_id' => 'prohibited',
            'phase' => 'sometimes|string|in:'.implode(',', self::PHASES),
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        return DB::transaction(function () use ($requirement, $validator) {
            $period = Period::whereKey($requirement->period_id)->lockForUpdate()->firstOrFail();
            if ($period->is_finalized) return $this->errorResponse('Cannot modify document requirements for a finalized period.', 403);
            $requirement = PhaseDocumentRequirement::whereKey($requirement->id)->lockForUpdate()->firstOrFail();
            $requirement->update($validator->validated());

            return $this->successResponse($requirement, 'Document requirement updated successfully');
        });
    }

    public function destroy(string $id)
    {
        $requirement = PhaseDocumentRequirement::with('period')->findOrFail($id);

        // Check if period is finalized
        if ($requirement->period && $requirement->period->is_finalized) {
            return $this->errorResponse('Cannot delete document requirements for a finalized period.', 403);
        }

        return DB::transaction(function () use ($requirement) {
            $period = Period::whereKey($requirement->period_id)->lockForUpdate()->firstOrFail();
            if ($period->is_finalized) return $this->errorResponse('Cannot delete document requirements for a finalized period.', 403);
            PhaseDocumentRequirement::whereKey($requirement->id)->lockForUpdate()->firstOrFail()->delete();

            return $this->successResponse(null, 'Document requirement deleted successfully');
        });
    }

    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period_id' => 'required|exists:capstone_periods,id',
            'phase' => 'nullable|string|in:'.implode(',', self::PHASES),
            'requirements' => 'present|array',
            'requirements.*.phase' => 'required|string|in:'.implode(',', self::PHASES),
            'requirements.*.name' => 'required|string|max:255',
            'requirements.*.description' => 'nullable|string',
            'requirements.*.is_required' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $data = $validator->validated();
        if (! empty($data['phase']) && collect($data['requirements'])->contains(fn ($item) => $item['phase'] !== $data['phase'])) {
            return $this->errorResponse('All requirements must belong to the selected phase.', 422);
        }

        return DB::transaction(function () use ($data) {
            $period = Period::whereKey($data['period_id'])->lockForUpdate()->firstOrFail();
            if ($period->is_finalized) {
                return $this->errorResponse('Cannot modify document requirements for a finalized period.', 403);
            }
            $query = PhaseDocumentRequirement::where('period_id', $period->id);
            if (! empty($data['phase'])) $query->where('phase', $data['phase']);
            $query->delete();
            $created = [];
            foreach ($data['requirements'] as $req) {
                $created[] = PhaseDocumentRequirement::create([
                    'period_id' => $period->id,
                    'phase' => $req['phase'],
                    'name' => $req['name'],
                    'description' => $req['description'] ?? null,
                    'is_required' => $req['is_required'] ?? false,
                ]);
            }
            return $this->successResponse($created, 'Document requirements updated successfully');
        });
    }

    /**
     * Get summary of document requirements grouped by phase
     * Returns aggregated data for the list view
     */
    public function summary($periodId)
    {
        $period = Period::find($periodId);
        if (! $period) {
            return $this->errorResponse('Period not found', 404);
        }

        $requirements = PhaseDocumentRequirement::where('period_id', $periodId)
            ->orderByRaw("CASE phase WHEN 'PDC1' THEN 1 WHEN 'SEMPRO' THEN 2 WHEN 'PDC2' THEN 3 WHEN 'EXPO' THEN 4 WHEN 'TA' THEN 5 WHEN 'SIDANG' THEN 6 ELSE 7 END")
            ->get();

        // Group by phase and calculate aggregates
        $summary = collect(self::PHASES)->map(function ($phase) use ($requirements, $period) {
            $phaseRequirements = $requirements->where('phase', $phase);

            return [
                'phase' => $phase,
                'document_count' => $phaseRequirements->count(),
                'required_count' => $phaseRequirements->where('is_required', true)->count(),
                'document_names' => $phaseRequirements->pluck('name')->toArray(),
                'has_configured' => $phaseRequirements->count() > 0,
                'period_id' => $period->id,
                'period_name' => $period->name,
                'is_finalized' => $period->is_finalized,
            ];
        })->toArray();

        return $this->successResponse($summary, 'Document requirements summary retrieved successfully');
    }
}
