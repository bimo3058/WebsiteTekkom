<?php

namespace Modules\BankSoal\Services\Komprehensif;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KompreSettingService
{
    public const RULE_CPL = 'cpl_all_min';

    public const RULE_TOTAL = 'total_min';

    public const RULE_LEGACY_IMPORT = 'legacy_import';

    public const DEFAULT_CPL_MIN_SCORE = 6.0;

    public const DEFAULT_TOTAL_MIN_SCORE = 60.0;

    /** @var array<string, string>|null */
    private ?array $values = null;

    public function passingRule(): string
    {
        $rule = $this->values()['kompre_passing_rule'];

        return in_array($rule, [self::RULE_CPL, self::RULE_TOTAL], true)
            ? $rule
            : self::RULE_CPL;
    }

    public function cplMinScore(): float
    {
        $value = (float) $this->values()['kompre_cpl_min_score'];

        return $value >= 0 && $value <= 10 ? $value : self::DEFAULT_CPL_MIN_SCORE;
    }

    public function totalMinScore(): float
    {
        $value = (float) $this->values()['kompre_total_min_score'];

        return $value >= 0 && $value <= 100 ? $value : self::DEFAULT_TOTAL_MIN_SCORE;
    }

    /**
     * @return array{passing_rule: string, cpl_min_score: float, total_min_score: float}
     */
    public function all(): array
    {
        return [
            'passing_rule' => $this->passingRule(),
            'cpl_min_score' => $this->cplMinScore(),
            'total_min_score' => $this->totalMinScore(),
        ];
    }

    public function labelFor(?string $rule): string
    {
        return match ($rule) {
            self::RULE_TOTAL => 'Nilai total minimal 60',
            self::RULE_LEGACY_IMPORT => 'Data historis',
            self::RULE_CPL => 'Semua CPL minimal 6',
            default => 'Semua CPL minimal 6',
        };
    }

    public function updatePassingRule(string $rule): void
    {
        if (! in_array($rule, [self::RULE_CPL, self::RULE_TOTAL], true)) {
            throw new \InvalidArgumentException('Aturan kelulusan tidak valid.');
        }

        DB::table('bs_pengaturan')->updateOrInsert(
            ['kunci' => 'kompre_passing_rule'],
            ['nilai' => $rule, 'updated_at' => now()]
        );

        $this->values['kompre_passing_rule'] = $rule;
    }

    /**
     * @return array<string, string>
     */
    private function values(): array
    {
        if ($this->values !== null) {
            return $this->values;
        }

        $defaults = [
            'kompre_passing_rule' => self::RULE_CPL,
            'kompre_cpl_min_score' => (string) self::DEFAULT_CPL_MIN_SCORE,
            'kompre_total_min_score' => (string) self::DEFAULT_TOTAL_MIN_SCORE,
        ];

        if (! Schema::hasTable('bs_pengaturan')) {
            return $this->values = $defaults;
        }

        $stored = DB::table('bs_pengaturan')
            ->whereIn('kunci', array_keys($defaults))
            ->pluck('nilai', 'kunci')
            ->all();

        return $this->values = array_replace($defaults, $stored);
    }
}
