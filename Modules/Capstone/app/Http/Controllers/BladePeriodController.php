<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Models\{Period, Group, AssessmentComponentTemplate, PeerReviewIndicatorTemplate, PeriodAssessmentComponent, PeriodPeerReviewIndicator};

class BladePeriodController extends Controller
{
    public const TYPES = ['SIDANG_TA', 'EXPO', 'BIMBINGAN_SEMPRO', 'BIMBINGAN_TA', 'NILAI_DOSEN', 'MILESTONE'];

    public function options()
    {
        return response()->json([
            'periods' => Period::orderByDesc('created_at')->get(['id', 'name']),
            'templates' => AssessmentComponentTemplate::orderBy('sort_order')->get(),
            'peer_templates' => PeerReviewIndicatorTemplate::orderBy('sort_order')->get(),
        ]);
    }

    public function show(Period $period)
    {
        return response()->json(['period' => $period, ...$this->configuration($period)]);
    }

    private function configuration(Period $period): array
    {
        $components = PeriodAssessmentComponent::where('period_id', $period->id)->orderBy('sort_order')->get();
        $assessments = [];
        foreach (self::TYPES as $type) $assessments[$type] = $components->where('type', $type)->pluck('template_id')->values()->all();
        return ['assessments' => $assessments, 'peer_ids' => PeriodPeerReviewIndicator::where('period_id', $period->id)->orderBy('sort_order')->pluck('template_id')->all()];
    }

    public function store(Request $request) { return $this->save($request); }
    public function update(Request $request, Period $period) { return $this->save($request, $period); }

    private function save(Request $request, ?Period $period = null)
    {
        $rules = [
            'name'=>'required|string|max:255', 'start_date'=>'required|date', 'end_date'=>'required|date|after:start_date', 'is_active'=>'required|boolean',
            'min_group_size'=>'required|integer|min:1|max:10', 'max_group_size'=>'required|integer|min:1|max:10|gte:min_group_size', 'max_supervisor_load'=>'required|integer|min:1|max:50',
            'assessments'=>'required|array:'.implode(',', self::TYPES), 'peer_ids'=>'present|array',
            'peer_ids.*'=>'integer|distinct|exists:capstone_peer_review_indicator_templates,id',
        ];
        foreach (self::TYPES as $type) {
            $rules['assessments.'.$type] = 'present|array';
            $rules['assessments.'.$type.'.*'] = 'integer|distinct|exists:capstone_assessment_component_templates,id';
        }
        foreach (['bidding', 'pdc1', 'pdc2', 'ta'] as $phase) {
            $rules[$phase.'_start'] = 'nullable|date';
            $rules[$phase.'_end'] = 'nullable|date|after_or_equal:'.$phase.'_start';
        }
        foreach (['expo_date', 'bidding_reminder_at', 'pdc1_reminder_at', 'pdc2_reminder_at', 'expo_reminder_at', 'ta_reminder_at'] as $field) $rules[$field] = 'nullable|date';
        $data = $request->validate($rules);

        return DB::transaction(function () use ($data, $period) {
            $editing = $period !== null;
            if ($editing) {
                $period = Period::whereKey($period->id)->lockForUpdate()->firstOrFail();
                abort_if($period->is_finalized, 403, 'Periode final tidak dapat diubah.');
            }
            $templates = AssessmentComponentTemplate::query()->lockForUpdate()->get()->keyBy('id');
            $peers = PeerReviewIndicatorTemplate::query()->lockForUpdate()->get()->keyBy('id');
            if (!$templates->contains('is_active', true)) throw ValidationException::withMessages(['assessments'=>'Period Setup harus diselesaikan sebelum menyimpan periode.']);
            $config = ['assessments'=>$data['assessments'], 'peer_ids'=>$data['peer_ids']];
            $old = $editing ? $this->configuration($period) : ['assessments'=>array_fill_keys(self::TYPES, []), 'peer_ids'=>[]];
            foreach ($config['assessments'] as $type => $ids) $this->validateSelection($ids, $templates, $old['assessments'][$type], 'assessments.'.$type);
            $this->validateSelection($config['peer_ids'], $peers, $old['peer_ids'], 'peer_ids');
            // Keep existing component identifiers and their grades intact.
            if ($editing && $config != $old && Group::where('period_id', $period->id)->exists()) {
                throw ValidationException::withMessages(['assessments'=>'Konfigurasi evaluasi tidak dapat diubah setelah kelompok terbentuk.']);
            }
            unset($data['assessments'], $data['peer_ids']);
            $data['max_supervise_load'] = $data['max_supervisor_load'];
            if ($editing) $period->update($data); else $period = Period::create($data);
            foreach ($config['assessments'] as $type => $ids) {
                PeriodAssessmentComponent::where('period_id', $period->id)->where('type', $type)->whereNotIn('template_id', $ids)->delete();
                foreach ($ids as $order => $id) PeriodAssessmentComponent::updateOrCreate(['period_id'=>$period->id, 'type'=>$type, 'template_id'=>$id], ['sort_order'=>$order]);
            }
            PeriodPeerReviewIndicator::where('period_id', $period->id)->whereNotIn('template_id', $config['peer_ids'])->delete();
            foreach ($config['peer_ids'] as $order => $id) PeriodPeerReviewIndicator::updateOrCreate(['period_id'=>$period->id, 'template_id'=>$id], ['sort_order'=>$order]);
            return response()->json(['period'=>$period->fresh(), ...$config], $editing ? 200 : 201);
        });
    }

    private function validateSelection(array $ids, $templates, array $existing, string $field): void
    {
        foreach ($ids as $id) {
            if (!isset($templates[$id]) || (!$templates[$id]->is_active && !in_array($id, $existing))) {
                throw ValidationException::withMessages([$field=>'Template yang dipilih tidak tersedia.']);
            }
        }
        if ($ids && abs($templates->only($ids)->sum(fn($template)=>(float)$template->weight) - 100) > 0.01) {
            throw ValidationException::withMessages([$field=>'Total bobot komponen yang dipilih harus 100%.']);
        }
    }
}
