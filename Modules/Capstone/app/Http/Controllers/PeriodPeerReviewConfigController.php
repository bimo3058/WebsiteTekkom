<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Http\Requests\Assessment\CopyPeerReviewConfigRequest;
use Modules\Capstone\Http\Requests\Assessment\StorePeerReviewConfigRequest;
use Modules\Capstone\Models\PeerReviewIndicatorTemplate;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodPeerReviewIndicator;
use Modules\Capstone\Support\PeriodGate;

class PeriodPeerReviewConfigController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get peer review configuration for a period.
     */
    public function show(Request $request, $periodId)
    {
        $period = Period::findOrFail($periodId);

        // Load templates from the table referenced by the period indicator foreign key.
        $allTemplates = PeerReviewIndicatorTemplate::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get selected indicators for this period
        $selectedIndicators = PeriodPeerReviewIndicator::with('template')
            ->where('period_id', $periodId)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($i) => [
                'id' => $i->id,
                'template_id' => $i->template_id,
                'code' => $i->template->code ?? null,
                'name' => $i->template->name,
                'description' => $i->template->description,
                'weight' => $i->template->weight,
                'sort_order' => $i->sort_order,
            ]);

        return $this->successResponse([
            'period' => $period,
            'all_templates' => $allTemplates,
            'selected_indicators' => $selectedIndicators,
        ]);
    }

    /**
     * Configure peer review indicators for a period.
     */
    public function store(StorePeerReviewConfigRequest $request, $periodId)
    {
        $request->validated();

        return DB::transaction(function () use ($periodId, $request) {
            PeriodGate::lockedPeriod($periodId);
            $current = PeriodPeerReviewIndicator::where('period_id', $periodId)->orderBy('sort_order')->pluck('template_id')->map(fn ($id) => (int) $id)->all();
            PeriodGate::assertNoGroupsIfChanged($periodId, $current, $request->template_ids, 'diubah');
            // Delete existing config for this period
            PeriodPeerReviewIndicator::where('period_id', $periodId)->whereNotIn('template_id', $request->template_ids)->delete();

            // Create new config
            $created = [];
            foreach ($request->template_ids as $i => $templateId) {
                $created[] = PeriodPeerReviewIndicator::updateOrCreate([
                    'period_id' => $periodId,
                    'template_id' => $templateId,
                ], ['sort_order' => $i]);
            }

            return $this->createdResponse([
                'count' => count($created),
                'indicators' => $created,
            ], 'Peer review configuration saved');
        });
    }

    /**
     * Copy peer review configuration from another period.
     */
    public function copy(CopyPeerReviewConfigRequest $request, $periodId)
    {
        $request->validated();

        $sourcePeriodId = $request->source_period_id;

        return DB::transaction(function () use ($periodId, $sourcePeriodId) {
            PeriodGate::lockedPeriod($periodId);
            PeriodGate::assertNoGroups($periodId, 'disalin');
            // Get all indicators from source period
            $sourceIndicators = PeriodPeerReviewIndicator::where('period_id', $sourcePeriodId)
                ->orderBy('sort_order')
                ->get();

            if ($sourceIndicators->isEmpty()) {
                return $this->errorResponse('Source period has no peer review configuration', 400);
            }

            // Delete existing config for this period
            PeriodPeerReviewIndicator::where('period_id', $periodId)->whereNotIn('template_id', $sourceIndicators->pluck('template_id'))->delete();

            // Copy config
            $created = [];
            foreach ($sourceIndicators as $indicator) {
                $created[] = PeriodPeerReviewIndicator::updateOrCreate([
                    'period_id' => $periodId,
                    'template_id' => $indicator->template_id,
                ], ['sort_order' => $indicator->sort_order]);
            }

            return $this->createdResponse([
                'count' => count($created),
                'indicators' => $created,
            ], 'Peer review configuration copied');
        });
    }
}
