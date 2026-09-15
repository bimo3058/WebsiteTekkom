<?php

namespace Modules\Capstone\Http\Controllers;

use Modules\Capstone\Models\PeerReviewIndicatorTemplate;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodPeerReviewIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function store(Request $request, $periodId)
    {
        $request->validate([
            'template_ids' => 'present|array',
            'template_ids.*' => 'integer|distinct|exists:capstone_peer_review_indicator_templates,id',
        ]);

        return DB::transaction(function () use ($periodId, $request) {
            $period = Period::whereKey($periodId)->lockForUpdate()->firstOrFail();
            abort_if($period->is_finalized, 403, 'Periode final tidak dapat diubah.');
            $current = PeriodPeerReviewIndicator::where('period_id', $periodId)->orderBy('sort_order')->pluck('template_id')->map(fn($id)=>(int)$id)->all();
            if ($current !== array_map('intval', $request->template_ids)) abort_if(\Modules\Capstone\Models\Group::where('period_id', $periodId)->exists(), 403, 'Konfigurasi evaluasi tidak dapat diubah setelah kelompok terbentuk.');
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
    public function copy(Request $request, $periodId)
    {
        $request->validate([
            'source_period_id' => 'required|integer|exists:capstone_periods,id|not_in:'.$periodId,
        ]);

        $sourcePeriodId = $request->source_period_id;

        return DB::transaction(function () use ($periodId, $sourcePeriodId) {
            $period = Period::whereKey($periodId)->lockForUpdate()->firstOrFail();
            abort_if($period->is_finalized, 403, 'Periode final tidak dapat diubah.');
            abort_if(\Modules\Capstone\Models\Group::where('period_id', $periodId)->exists(), 403, 'Konfigurasi evaluasi tidak dapat disalin setelah kelompok terbentuk.');
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
