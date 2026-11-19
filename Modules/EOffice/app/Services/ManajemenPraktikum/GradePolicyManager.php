<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Brick\Math\BigRational;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\PracticumGradePolicy;

class GradePolicyManager
{
    public function active(): PracticumGradePolicy
    {
        return PracticumGradePolicy::findOrFail(DB::table('eo_prak_grade_settings')->where('id', 1)->value('active_policy_id'));
    }

    public function create(array $data): PracticumGradePolicy
    {
        $rules = collect($data['rules'])->sortBy('from')->values()->all();
        $next = 2000;
        foreach ($rules as &$rule) {
            $rule['from'] = (int) $rule['from'];
            $rule['to'] = blank($rule['to'] ?? null) ? null : (int) $rule['to'];
            $this->check($next !== null && $rule['from'] === $next && ($rule['to'] === null || $rule['to'] >= $rule['from']), 'Rentang angkatan harus berurutan mulai 2000, tanpa tumpang tindih atau celah.');
            $next = $rule['to'] === null ? null : $rule['to'] + 1;
            $bands = collect($rule['bands'])->sort(fn ($a, $b) => BigRational::of($b['minimum'])->compareTo($a['minimum']))->values()->all();
            $this->check(count($bands) >= 2 && BigRational::of(end($bands)['minimum'])->isEqualTo(0), 'Batas nilai terakhir harus 0 agar seluruh nilai memiliki predikat.');
            $seen = [];
            $last = null;
            foreach ($bands as &$band) {
                $minimum = BigRational::of($band['minimum']);
                $this->check($minimum->isGreaterThanOrEqualTo(0) && $minimum->isLessThanOrEqualTo(100) && ($last === null || $minimum->isLessThan($last)), 'Batas nilai harus unik dan berada antara 0–100.');
                $band['letter'] = strtoupper(trim($band['letter']));
                $this->check(! in_array($band['letter'], $seen, true), 'Predikat pada satu skema tidak boleh sama.');
                $seen[] = $band['letter'];
                $last = $minimum;
                $band['minimum'] = (string) $minimum->toScale(2);
                $band['points'] = (string) BigRational::of($band['points'])->toScale(2);
            }unset($band);
            $rule['bands'] = $bands;
        }unset($rule);
        $this->check($next === null, 'Rentang angkatan terakhir harus berlaku tanpa batas atas.');

        return DB::transaction(function () use ($data, $rules) {
            $settings = DB::table('eo_prak_grade_settings')->where('id', 1)->lockForUpdate()->first();
            $this->check($settings->active_policy_id === $data['expected_policy'], 'Skema telah berubah. Muat ulang halaman sebelum menyimpan.');
            $policy = PracticumGradePolicy::create(['id' => (string) Str::uuid(), 'version' => PracticumGradePolicy::max('version') + 1, 'nama' => $data['nama'], 'rules' => $rules, 'created_by' => auth()->id(), 'created_at' => now()]);
            DB::table('eo_prak_grade_settings')->where('id', 1)->update(['active_policy_id' => $policy->id]);

            return $policy;
        });
    }

    public function predicate(PracticumGradePolicy $policy, int $cohort, string $rounded): array
    {
        foreach ($policy->rules as $rule) {
            if ($cohort >= $rule['from'] && ($rule['to'] === null || $cohort <= $rule['to'])) {
                foreach ($rule['bands'] as $band) {
                    if (BigRational::of($rounded)->isGreaterThanOrEqualTo($band['minimum'])) {
                        return $band;
                    }
                }
            }
        }
        throw ValidationException::withMessages(['predikat' => 'Skema predikat untuk angkatan mahasiswa belum tersedia.']);
    }

    private function check(bool $condition, string $message): void
    {
        if (! $condition) {
            throw ValidationException::withMessages(['skema_predikat' => $message]);
        }
    }
}
