<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Brick\Math\BigRational;
use Brick\Math\Exception\MathException;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\Nilai;
use Modules\EOffice\Models\NilaiJenisTugas;
use Modules\EOffice\Models\PracticumGradePolicy;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\TaNilai;
use Modules\EOffice\Models\TugasAkhir;

class FinalGradeBook
{
    /** Published reports use frozen results, never the currently active policy. */
    public function report(Praktikum $praktikum, ?string $studentUserId = null, bool $publishedOnly = false): array
    {
        $students = DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->when($studentUserId !== null, fn ($q) => $q->where('user_id', $studentUserId))
            ->with('user.student')->orderBy('shift')->orderBy('kelompok')->orderBy('created_at')->get();
        $published = $this->published($praktikum);
        $flags = Nilai::whereIn('daftar_praktikan_id', $students->pluck('id'))->get()->keyBy('daftar_praktikan_id');
        $frozen = $students->isNotEmpty() && $students->every(fn ($s) => isset($published[$s->id]) && (bool) ($flags->get($s->id)?->dipublikasikan));
        if (! $frozen && ($studentUserId !== null || $publishedOnly)) {
            return ['rows' => [], 'errors' => ['Nilai keseluruhan belum dipublikasikan.'], 'weights' => [], 'published' => false, 'policy' => null];
        }
        if (! $frozen) {
            $book = $this->compute($praktikum);
            $pending = DB::table('eo_prak_grade_batches')->where('id', $praktikum->final_grade_batch_id)->where('status', 'pending')->first();
            if ($pending && ! hash_equals($pending->fingerprint, $book['fingerprint'])) {
                $book['errors'][] = 'Data penilaian atau skema predikat berubah setelah pengajuan. Batalkan persetujuan lalu ajukan kembali.';
            }
            $book['rows'] = $students->mapWithKeys(fn ($s) => [$s->id => $book['rows'][$s->id]])->all();
            $book['published'] = false;

            return $book;
        }
        $batch = DB::table('eo_prak_grade_batches')->where('id', $praktikum->final_grade_batch_id)->first();
        $rows = [];
        foreach ($students as $student) {
            $result = $published[$student->id];
            $calculation = json_decode($result->calculation, true);
            $rows[$student->id] = ['id' => $student->id, 'name' => $student->user?->name, 'nim' => $calculation['nim'], 'group' => $student->kelompok, 'cohort' => $result->cohort_year,
                'module' => $this->rounded(BigRational::of($calculation['module_exact'])), 'ta' => $this->rounded(BigRational::of($calculation['ta_exact'])),
                'final' => number_format((float) $result->final_value, 2, '.', ''), 'letter' => $result->letter, 'points' => $result->points, 'errors' => []];
            $rows[$student->id]['email'] = $student->user?->getRawOriginal('email');
            $rows[$student->id]['shift'] = $student->getRawOriginal('shift');
            $rows[$student->id]['module_values'] = $this->moduleValues($calculation, $praktikum);
            $rows[$student->id]['extras'] = $calculation['extra_scores'] ?? [];
        }

        $weights=json_decode($batch->weights,true);
        return ['rows' => $rows, 'errors' => [], 'weights' => $weights, 'components'=>$weights['components']??[], 'published' => true, 'policy' => PracticumGradePolicy::findOrFail($batch->policy_id)];
    }

    public function compute(Praktikum $praktikum): array
    {
        $policy = app(GradePolicyManager::class)->active();
        $modules = Modul::where('praktikum_id', $praktikum->id)->orderBy('urutan')->orderBy('id')->get();
        $ta = TugasAkhir::where('praktikum_id', $praktikum->id)->first();
        $students = DaftarPraktikan::where('praktikum_id', $praktikum->id)->with('user.student')->orderBy('id')->get();
        $scores = NilaiJenisTugas::whereIn('modul_id', $modules->map(fn ($m) => $m->getRawOriginal('id')))->orderBy('id')->get()->groupBy(fn ($s) => (string) $s->modul_id.'|'.$s->daftar_praktikan_id);
        $taGrades = $ta ? TaNilai::where('tugas_akhir_id', $ta->id)->get()->keyBy('praktikan_id') : collect();
        $weights = ['modules' => $praktikum->getAttribute('bobot_gabungan_modul'), 'ta' => $praktikum->getAttribute('bobot_gabungan_ta')];
        $extraComponents=$praktikum->final_components??[];
        $extraScores=\Illuminate\Support\Facades\Schema::hasTable('eo_prak_extra_scores') ? DB::table('eo_prak_extra_scores')->where('praktikum_id',$praktikum->id)->get()->keyBy('praktikan_id') : collect();
        $combinedValid = collect($weights)->every(fn ($weight) => $weight !== null && ctype_digit((string) $weight) && (int) $weight <= 100)
            && count($extraComponents)<=3 && (int) $weights['modules'] + (int) $weights['ta'] + array_sum(array_column($extraComponents,'bobot')) === 100;
        if($extraComponents)$weights['components']=$extraComponents;
        $errors = [];
        if (! $combinedValid) {
            $errors[] = 'Atur bobot gabungan modul, tugas akhir, dan kolom tambahan dengan total 100%.';
        }
        if (! $modules->count()) {
            $errors[] = 'Belum ada modul yang dapat dihitung.';
        }
        if (! $praktikum->moduleWeightsValid()) {
            $errors[] = 'Bobot kolom penilaian modul belum valid.';
        }
        if (! $ta || ! $ta->published) {
            $errors[] = 'Tugas akhir wajib tersedia dan sudah dipublikasikan.';
        } elseif (! $ta->validWeights()) {
            $errors[] = 'Bobot seluruh kolom tugas akhir belum valid.';
        }
        $basis = ['formula' => 1, 'policy' => $policy->id, 'weights' => $weights, 'module_weights' => $praktikum->moduleWeights(), 'modules' => $modules->map(fn ($m) => (string) $m->getRawOriginal('id'))->all(), 'ta' => $ta ? ['id' => $ta->id, 'version' => $ta->version, 'components' => $ta->komponen, 'stages' => $ta->tahap, 'published' => $ta->published] : null, 'students' => []];
        $rows = [];
        foreach ($students as $student) {
            $profile = $student->user?->student;
            $row = ['id' => $student->id, 'name' => $student->user?->name ?? 'Mahasiswa', 'nim' => $profile?->student_number, 'group' => $student->kelompok, 'cohort' => null, 'module' => null, 'ta' => null, 'final' => null, 'letter' => null, 'points' => null, 'errors' => $errors];
            $moduleValues = [];
            $row['email'] = $student->user?->getRawOriginal('email');
            $row['shift'] = $student->getRawOriginal('shift');
            $moduleTotal = BigRational::zero();
            $moduleExact = [];
            foreach ($modules as $module) {
                $values = ($scores->get((string) $module->getRawOriginal('id').'|'.$student->id) ?? collect())->mapWithKeys(fn ($s) => [$s->jenis_tugas => $s->getRawOriginal('nilai')])->all();
                $moduleValues[(string) $module->getRawOriginal('id')] = $values;
                $singleTotal = BigRational::zero();
                foreach ($praktikum->moduleWeights() as $key => $weight) {
                    $singleTotal = $singleTotal->plus($this->score($values[$key] ?? 0)->multipliedBy((string) ($weight ?? 0))->dividedBy(100));
                }
                $moduleExact[(string)$module->getRawOriginal('id')] = (string)$singleTotal->simplified();
                $moduleTotal = $moduleTotal->plus($singleTotal);
            }
            $moduleAverage = $modules->count() ? $moduleTotal->dividedBy($modules->count())->simplified() : null;
            $grade = $taGrades->get($student->id);
            $effective = [];
            $taTotal = BigRational::zero();
            if ($ta) {
                foreach ($ta->komponen as $component) {
                    $key = $component['id'];
                    $value = $this->score($grade?->initialScores()[$key] ?? 0);
                    $revisions = $grade?->revisionScores() ?? [];
                    if (array_key_exists($key, $revisions)) {
                        $revision = $this->score($revisions[$key]);
                        $stage = collect($ta->tahap)->firstWhere('id', $component['tahap_id'] ?? $ta->tahap[0]['id']);
                        $value = match ($stage['revisi_metode'] ?? 'rata_rata') {
                            'ganti' => $revision,
                            'tertinggi' => $value->isGreaterThan($revision) ? $value : $revision,
                            'berbobot' => $value->multipliedBy((string) ($stage['revisi_bobot_awal'] ?? 50))->plus($revision->multipliedBy(BigRational::of(100)->minus((string) ($stage['revisi_bobot_awal'] ?? 50))))->dividedBy(100),
                            default => $value->plus($revision)->dividedBy(2),
                        };
                    }
                    $effective[$key] = (string) $value->simplified();
                    $taTotal = $taTotal->plus($value->multipliedBy((string) ($component['bobot'] ?? 0))->dividedBy(100));
                }
            }
            if ($moduleAverage && $praktikum->moduleWeightsValid()) {
                $row['module'] = $this->rounded($moduleAverage);
            }
            if ($ta && $ta->published && $ta->validWeights()) {
                $row['ta'] = $this->rounded($taTotal);
            }
            $storedExtras=$extraScores->get($student->id);
            $extras=$storedExtras?json_decode($storedExtras->scores,true):[];$extraTotal=BigRational::zero();$row['extras']=[];
            foreach($extraComponents as $component){
                $key=$component['id'];$row['extras'][$key]=$extras[$key]??null;
                if(!isset($extras[$key])){$row['errors'][]='Kolom '.$component['nama'].' belum dinilai.';continue;}
                $extraTotal=$extraTotal->plus($this->score($extras[$key])->multipliedBy((string)$component['bobot']));
            }
            $row['extra_version']=$storedExtras->version??0;
            if (! $row['errors'] && $moduleAverage) {
                $row['final'] = $this->rounded($moduleAverage->multipliedBy((string) $weights['modules'])->plus($taTotal->multipliedBy((string) $weights['ta']))->plus($extraTotal)->dividedBy(100));
            }
            try {
                $row['cohort'] = app(StudentCohort::class)->verified($profile);
                if ($row['final'] !== null) {
                    $band = app(GradePolicyManager::class)->predicate($policy, $row['cohort'], $row['final']);
                    $row['letter'] = $band['letter'];
                    $row['points'] = $band['points'];
                }
            } catch (ValidationException $e) {
                $row['errors'][] = collect($e->errors())->flatten()->first();
            }
            $input = ['student' => $student->id, 'nim' => $profile?->student_number, 'cohort' => $profile?->cohort_year, 'module_scores' => $moduleValues, 'ta_initial' => $grade?->initialScores() ?? [], 'ta_revisions' => $grade?->revisionScores() ?? [], 'ta_version' => $grade?->version];
            if($extraComponents)$input+=['extra_scores'=>$row['extras'],'extra_version'=>$row['extra_version']];
            $basis['students'][] = $input;
            $row['calculation'] = $input + ['module_values_exact'=>$moduleExact,'module_weights'=>$praktikum->moduleWeights(),'module_exact' => $moduleAverage ? (string) $moduleAverage : null, 'ta_exact' => (string) $taTotal->simplified(), 'ta_effective' => $effective, 'weights' => $weights];
            $row['module_values'] = $praktikum->moduleWeightsValid() ? array_map(fn($value)=>$this->rounded(BigRational::of($value)),$moduleExact) : [];
            $rows[$student->id] = $row;
        }

        return ['rows' => $rows, 'policy' => $policy, 'weights' => $weights, 'components'=>$extraComponents, 'errors' => $errors, 'fingerprint' => hash('sha256', json_encode($basis, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION))];
    }

    private function moduleValues(array $calculation, Praktikum $praktikum): array
    {
        if (isset($calculation['module_values_exact'])) return array_map(fn($value)=>$this->rounded(BigRational::of($value)),$calculation['module_values_exact']);
        // Older publications already freeze component scores; never read live grades here.
        $weights=$calculation['module_weights'] ?? $praktikum->moduleWeights();
        if (array_sum($weights)!==100 && array_sum($weights)!==100.0) return [];
        $result=[];
        foreach($calculation['module_scores'] ?? [] as $id=>$scores){
            $total=BigRational::zero();
            foreach($weights as $key=>$weight) $total=$total->plus($this->score($scores[$key] ?? 0)->multipliedBy((string)$weight)->dividedBy(100));
            $result[$id]=$this->rounded($total);
        }
        return $result;
    }

    public function propose(Praktikum $praktikum): void
    {
        DB::table('eo_prak_grade_settings')->where('id', 1)->lockForUpdate()->first();
        $book = $this->compute($praktikum);
        $this->ready($book);
        $id = (string) Str::uuid();
        DB::table('eo_prak_grade_batches')->where('praktikum_id', $praktikum->id)->where('status', 'pending')->update(['status' => 'withdrawn']);
        DB::table('eo_prak_grade_batches')->insert(['id' => $id, 'praktikum_id' => $praktikum->id, 'policy_id' => $book['policy']->id, 'fingerprint' => $book['fingerprint'], 'status' => 'pending', 'weights' => json_encode($book['weights']), 'created_by' => auth()->id(), 'created_at' => now()]);
        foreach ($book['rows'] as $row) {
            DB::table('eo_prak_final_results')->insert([
                'id' => (string) Str::uuid(), 'batch_id' => $id, 'praktikan_id' => $row['id'], 'cohort_year' => $row['cohort'],
                'module_value' => (string) BigRational::of($row['calculation']['module_exact'])->toScale(8, RoundingMode::HalfUp),
                'ta_value' => (string) BigRational::of($row['calculation']['ta_exact'])->toScale(8, RoundingMode::HalfUp),
                'final_value' => $row['final'], 'letter' => $row['letter'], 'points' => $row['points'], 'calculation' => json_encode($row['calculation']),
            ]);
        }
        DB::table('eo_prak_praktikum')->where('id', $praktikum->id)->update(['final_grade_batch_id' => $id]);
    }

    public function publish(Praktikum $praktikum): void
    {
        DB::table('eo_prak_grade_settings')->where('id', 1)->lockForUpdate()->first();
        $book = $this->compute($praktikum);
        $this->ready($book);
        $batch = DB::table('eo_prak_grade_batches')->where('id', $praktikum->getAttribute('final_grade_batch_id'))->where('praktikum_id', $praktikum->id)->lockForUpdate()->first();
        if (! $batch || $batch->status !== 'pending' || ! hash_equals($batch->fingerprint, $book['fingerprint'])) {
            throw ValidationException::withMessages(['nilai_akhir' => 'Nilai, bobot, data angkatan, atau skema predikat telah berubah. Batalkan persetujuan lalu minta koordinator mengajukan kembali.']);
        }
        DB::table('eo_prak_grade_batches')->where('id', $batch->id)->update(['status' => 'published', 'published_at' => now()]);
    }

    public function published(Praktikum $praktikum): array
    {
        $batch = DB::table('eo_prak_grade_batches')->where('id', $praktikum->getAttribute('final_grade_batch_id'))->where('praktikum_id', $praktikum->id)->where('status', 'published')->first();
        if (! $batch) {
            return [];
        }

        return DB::table('eo_prak_final_results')->where('batch_id', $batch->id)->get()->keyBy('praktikan_id')->all();
    }

    private function ready(array $book): void
    {
        $errors = $book['errors'];
        if (! $book['rows']) {
            $errors[] = 'Belum ada mahasiswa dalam praktikum ini.';
        }
        foreach ($book['rows'] as $row) {
            foreach (array_diff($row['errors'], $book['errors']) as $error) {
                $errors[] = $row['name'].': '.$error;
            }
        }
        if ($errors) {
            throw ValidationException::withMessages(['nilai_akhir' => array_values(array_unique($errors))]);
        }
    }

    private function rounded(BigRational $value): string
    {
        return (string) $value->toScale(2, RoundingMode::HalfUp);
    }

    private function score(mixed $value): BigRational
    {
        try {
            $number = BigRational::of((string) $value);
        } catch (MathException $e) {
            throw ValidationException::withMessages(['nilai' => 'Data nilai tidak valid. Periksa penilaian mahasiswa.']);
        }
        if ($number->isLessThan(0) || $number->isGreaterThan(100)) {
            throw ValidationException::withMessages(['nilai' => 'Nilai harus berada antara 0–100.']);
        }

        return $number;
    }
}
