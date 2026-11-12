<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Services\GradeCalculationService;
use Tests\TestCase;

/** Pure unit test: grading combinators, no database needed. */
class GradingCombinatorsTest extends TestCase
{
    public function test_mean_averages_non_null_inputs(): void
    {
        $this->assertSame(85.0, GradeCalculationService::mean(80.0, 90.0));
        $this->assertSame(90.0, GradeCalculationService::mean(null, 90.0));
        $this->assertSame(80.0, GradeCalculationService::mean(80.0, null, null));
    }

    public function test_mean_returns_null_without_inputs(): void
    {
        $this->assertNull(GradeCalculationService::mean());
        $this->assertNull(GradeCalculationService::mean(null, null));
    }

    public function test_peer_review_weighted_average_uses_indicator_weights(): void
    {
        $reviews = [
            (object) ['periodIndicator' => (object) ['template' => (object) ['weight' => 1]], 'raw_score' => 4, 'score' => 100.0],
            (object) ['periodIndicator' => (object) ['template' => (object) ['weight' => 3]], 'raw_score' => 2, 'score' => 50.0],
        ];

        $this->assertSame(62.5, GradeCalculationService::peerReviewWeightedAverage($reviews));
    }

    public function test_peer_review_weighted_average_falls_back_to_plain_average(): void
    {
        $reviews = [
            (object) ['periodIndicator' => null, 'raw_score' => null, 'score' => 80.0],
            (object) ['periodIndicator' => null, 'raw_score' => null, 'score' => 90.0],
        ];

        $this->assertSame(85.0, GradeCalculationService::peerReviewWeightedAverage($reviews));
    }

    public function test_peer_review_weighted_average_returns_null_without_reviews(): void
    {
        $this->assertNull(GradeCalculationService::peerReviewWeightedAverage(null));
        $this->assertNull(GradeCalculationService::peerReviewWeightedAverage([]));
        $this->assertNull(GradeCalculationService::peerReviewWeightedAverage([(object) ['periodIndicator' => null, 'raw_score' => null, 'score' => null]]));
    }

    public function test_peer_review_weighted_average_ignores_unweighted_rows_when_weights_exist(): void
    {
        $reviews = [
            (object) ['periodIndicator' => (object) ['template' => (object) ['weight' => 2]], 'raw_score' => 4, 'score' => 100.0],
            (object) ['periodIndicator' => null, 'raw_score' => null, 'score' => 0.0],
        ];

        $this->assertSame(100.0, GradeCalculationService::peerReviewWeightedAverage($reviews));
    }

    public function test_peer_review_weighted_average_skips_null_raw_scores(): void
    {
        $reviews = [
            (object) ['periodIndicator' => (object) ['template' => (object) ['weight' => 1]], 'raw_score' => null, 'score' => 70.0],
            (object) ['periodIndicator' => (object) ['template' => (object) ['weight' => 1]], 'raw_score' => 2, 'score' => 50.0],
        ];

        $this->assertSame(50.0, GradeCalculationService::peerReviewWeightedAverage($reviews));
    }
}
