<?php

namespace Modules\Capstone\Http\Controllers;

use Modules\Capstone\Models\AssessmentComponent;
use Modules\Capstone\Models\AssessmentComponentTemplate;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodAssessmentComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PeriodAssessmentConfigController extends Controller
{
    use ApiResponseTrait;

    private function hasPeriodAssessmentTable(): bool
    {
        return Schema::hasTable('capstone_period_assessment_components');
    }

    /**
     * Get assessment configuration for a period by type.
     */
    public function show(Request $request, $periodId)
    {
        $request->validate([
            'type' => 'required|string|in:SEMPRO,SIDANG_TA,EXPO,BIMBINGAN_SEMPRO,BIMBINGAN_TA,NILAI_DOSEN,MILESTONE',
        ]);

        $period = Period::findOrFail($periodId);
        $type = $request->type;

        // Get all active templates
        $allTemplates = AssessmentComponentTemplate::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($this->hasPeriodAssessmentTable()) {
            $selectedComponents = PeriodAssessmentComponent::with('template')
                ->where('period_id', $periodId)
                ->where('type', $type)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'template_id' => $c->template_id,
                    'code' => $c->template->code,
                    'name' => $c->template->name,
                    'description' => $c->template->description,
                    'weight' => $c->template->weight,
                    'sort_order' => $c->sort_order,
                ]);
        } else {
            $templateIdByCode = $allTemplates->pluck('id', 'code');

            $selectedComponents = AssessmentComponent::query()
                ->where('period_id', $periodId)
                ->where('type', $type)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'template_id' => $templateIdByCode[$c->code] ?? null,
                    'code' => $c->code,
                    'name' => $c->name,
                    'description' => $c->description,
                    'weight' => $c->weight,
                    'sort_order' => $c->sort_order,
                ]);
        }

        return $this->successResponse([
            'period' => $period,
            'type' => $type,
            'all_templates' => $allTemplates,
            'selected_components' => $selectedComponents,
        ]);
    }

    /**
     * Configure assessment components for a period + type.
     */
    public function store(Request $request, $periodId)
    {
        $request->validate([
            'type' => 'required|string|in:SEMPRO,SIDANG_TA,EXPO,BIMBINGAN_SEMPRO,BIMBINGAN_TA,NILAI_DOSEN,MILESTONE',
            'template_ids' => 'present|array',
            'template_ids.*' => 'integer|distinct|exists:capstone_assessment_component_templates,id',
        ]);

        $type = $request->type;

        return DB::transaction(function () use ($periodId, $type, $request) {
            $period = Period::whereKey($periodId)->lockForUpdate()->firstOrFail();
            abort_if($period->is_finalized, 403, 'Periode final tidak dapat diubah.');
            $created = [];

            if ($this->hasPeriodAssessmentTable()) {
                $current = PeriodAssessmentComponent::where('period_id', $periodId)->where('type', $type)->orderBy('sort_order')->pluck('template_id')->map(fn($id)=>(int)$id)->all();
                if ($current !== array_map('intval', $request->template_ids)) abort_if(\Modules\Capstone\Models\Group::where('period_id', $periodId)->exists(), 403, 'Konfigurasi evaluasi tidak dapat diubah setelah kelompok terbentuk.');
                // New schema path
                PeriodAssessmentComponent::where('period_id', $periodId)
                    ->where('type', $type)
                    ->whereNotIn('template_id', $request->template_ids)
                    ->delete();

                foreach ($request->template_ids as $i => $templateId) {
                    $created[] = PeriodAssessmentComponent::updateOrCreate([
                        'period_id' => $periodId,
                        'template_id' => $templateId,
                        'type' => $type,
                    ], ['sort_order' => $i]);
                }
            } else {
                // Legacy schema fallback
                abort_if(\Modules\Capstone\Models\Group::where('period_id', $periodId)->exists(), 403, 'Konfigurasi evaluasi tidak dapat diubah setelah kelompok terbentuk.');
                AssessmentComponent::where('period_id', $periodId)
                    ->where('type', $type)
                    ->delete();

                $templates = AssessmentComponentTemplate::whereIn('id', $request->template_ids)->get()->keyBy('id');

                foreach ($request->template_ids as $i => $templateId) {
                    $template = $templates->get((int) $templateId);
                    if (! $template) {
                        continue;
                    }

                    $created[] = AssessmentComponent::create([
                        'period_id' => $periodId,
                        'type' => $type,
                        'code' => $template->code,
                        'name' => $template->name,
                        'description' => $template->description,
                        'weight' => $template->weight,
                        'sort_order' => $i,
                    ]);
                }
            }

            return $this->createdResponse([
                'count' => count($created),
                'components' => $created,
            ], 'Assessment configuration saved');
        });
    }

    /**
     * Copy assessment configuration from another period.
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
            if ($this->hasPeriodAssessmentTable()) {
                // Get all components from source period (new schema)
                $sourceComponents = PeriodAssessmentComponent::where('period_id', $sourcePeriodId)
                    ->orderBy('sort_order')
                    ->get();

                if ($sourceComponents->isEmpty()) {
                    return $this->errorResponse('Source period has no assessment configuration', 400);
                }

                // Delete existing config for this period (all types)
                $sourceKeys = $sourceComponents->map(fn ($c) => $c->type.':'.$c->template_id);
                $removeIds = PeriodAssessmentComponent::where('period_id', $periodId)->get()->filter(fn ($c) => !$sourceKeys->contains($c->type.':'.$c->template_id))->pluck('id');
                PeriodAssessmentComponent::whereIn('id', $removeIds)->delete();

                // Copy config
                $created = [];
                foreach ($sourceComponents as $component) {
                    $created[] = PeriodAssessmentComponent::updateOrCreate([
                        'period_id' => $periodId,
                        'template_id' => $component->template_id,
                        'type' => $component->type,
                    ], ['sort_order' => $component->sort_order]);
                }

                return $this->createdResponse([
                    'count' => count($created),
                    'components' => $created,
                ], 'Assessment configuration copied');
            }

            // Legacy schema fallback copy
            $sourceComponents = AssessmentComponent::where('period_id', $sourcePeriodId)
                ->orderBy('sort_order')
                ->get();

            if ($sourceComponents->isEmpty()) {
                return $this->errorResponse('Source period has no assessment configuration', 400);
            }

            AssessmentComponent::where('period_id', $periodId)->delete();

            $created = [];
            foreach ($sourceComponents as $component) {
                $created[] = AssessmentComponent::create([
                    'period_id' => $periodId,
                    'type' => $component->type,
                    'code' => $component->code,
                    'name' => $component->name,
                    'description' => $component->description,
                    'weight' => $component->weight,
                    'sort_order' => $component->sort_order,
                ]);
            }

            return $this->createdResponse([
                'count' => count($created),
                'components' => $created,
            ], 'Assessment configuration copied');
        });
    }
}
