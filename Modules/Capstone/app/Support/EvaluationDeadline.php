<?php

namespace Modules\Capstone\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class EvaluationDeadline
{
    /**
     * Evaluation deadline for a schedule date.
     *
     * Convention: schedule date + 2 days (mirrors the TA defense
     * scheduling path). Returns a 'Y-m-d H:i:s' string or null when
     * the date is missing or unparseable.
     */
    public static function fromDate(mixed $date): ?string
    {
        if ($date === null || $date === '') {
            return null;
        }

        try {
            $parsed = $date instanceof CarbonInterface ? $date : Carbon::parse($date);
        } catch (\Throwable) {
            return null;
        }

        return $parsed->copy()->addDays(2)->format('Y-m-d H:i:s');
    }

    /**
     * Whether a deadline string has passed. Null or garbage never counts
     * as passed so readers stay null-safe.
     */
    public static function isPassed(?string $deadline): bool
    {
        if ($deadline === null || $deadline === '') {
            return false;
        }

        try {
            return Carbon::now()->greaterThan(Carbon::parse($deadline));
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Read a stored deadline off a model without triggering
     * MissingAttributeException when the column does not exist (yet).
     *
     * @return ?string Normalized 'Y-m-d H:i:s' or null.
     */
    public static function storedDeadline(object $model, string $key = 'evaluation_deadline'): ?string
    {
        if (! method_exists($model, 'getAttributes')) {
            return null;
        }

        $raw = $model->getAttributes()[$key] ?? null;

        if ($raw === null || $raw === '') {
            return null;
        }

        if ($raw instanceof CarbonInterface) {
            return $raw->copy()->format('Y-m-d H:i:s');
        }

        try {
            return Carbon::parse((string) $raw)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }
}
