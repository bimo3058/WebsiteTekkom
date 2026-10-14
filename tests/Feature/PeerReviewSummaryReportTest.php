<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\SystemModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\PeerReview;
use Modules\Capstone\Models\PeerReviewIndicatorTemplate;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodPeerReviewIndicator;
use Tests\TestCase;

class PeerReviewSummaryReportTest extends TestCase
{
    use RefreshDatabase;

    private Period $period;

    private Group $group;

    /** @var array<string, Student> */
    private array $students = [];

    private PeriodPeerReviewIndicator $indicator60;

    private PeriodPeerReviewIndicator $indicator40;

    protected function setUp(): void
    {
        parent::setUp();

        SystemModule::create([
            'slug' => 'capstone',
            'name' => 'Capstone + TA',
            'is_active' => true,
            'is_maintenance' => false,
        ]);

        $admin = $this->roleUser('admin_capstone');
        Sanctum::actingAs($admin, ['capstone:access']);

        $this->period = Period::create([
            'name' => 'Periode Test',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);
        $this->group = Group::create(['period_id' => $this->period->id, 'status' => 'FORMING']);

        // 4 members: Andi is reviewed by Budi + Citra, Dewi receives nothing.
        $this->students['andi'] = $this->member('Andi Reviewee', 'NIM-001');
        $this->students['budi'] = $this->member('Budi Reviewer', 'NIM-002');
        $this->students['citra'] = $this->member('Citra Reviewer', 'NIM-003');
        $this->students['dewi'] = $this->member('Dewi Kosong', 'NIM-004');

        $template60 = PeerReviewIndicatorTemplate::create([
            'code' => 'PR-IND-1', 'name' => 'Kontribusi', 'weight' => 60,
        ]);
        $template40 = PeerReviewIndicatorTemplate::create([
            'code' => 'PR-IND-2', 'name' => 'Komunikasi', 'weight' => 40,
        ]);
        $this->indicator60 = PeriodPeerReviewIndicator::create([
            'period_id' => $this->period->id, 'template_id' => $template60->id, 'sort_order' => 1,
        ]);
        $this->indicator40 = PeriodPeerReviewIndicator::create([
            'period_id' => $this->period->id, 'template_id' => $template40->id, 'sort_order' => 2,
        ]);

        $andi = $this->students['andi']->id;
        $budi = $this->students['budi']->id;
        $citra = $this->students['citra']->id;

        // Budi -> Andi: raw 4 (ind 60), raw 3 (ind 40)
        $this->review($budi, $andi, $this->indicator60->id, 4);
        $this->review($budi, $andi, $this->indicator40->id, 3);
        // Citra -> Andi: raw 2 (ind 60), raw 4 (ind 40)
        $this->review($citra, $andi, $this->indicator60->id, 2);
        $this->review($citra, $andi, $this->indicator40->id, 4);
    }

    private function review(int $reviewerId, int $revieweeId, int $periodIndicatorId, float $raw): void
    {
        PeerReview::create([
            'group_id' => $this->group->id,
            'reviewer_id' => $reviewerId,
            'reviewee_id' => $revieweeId,
            'indicator_id' => null,
            'period_indicator_id' => $periodIndicatorId,
            'raw_score' => $raw,
            'score' => $raw * 25,
            'comment' => "Komentar {$raw}",
        ]);
    }

    private function member(string $name, string $nim): Student
    {
        $user = $this->roleUser('mahasiswa');
        $user->update(['name' => $name]);
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => $nim,
            'cohort_year' => 2024,
        ]);
        GroupMember::create([
            'period_id' => $this->period->id,
            'group_id' => $this->group->id,
            'student_id' => $student->id,
            'is_leader' => false,
        ]);

        return $student;
    }

    private function roleUser(string $roleName, array $permissionNames = []): User
    {
        $permissionNames = array_values(array_unique(['capstone.view', ...$permissionNames]));
        $permissions = collect($permissionNames)->map(fn (string $name) => Permission::firstOrCreate(
            ['name' => $name, 'guard_name' => 'web'],
            ['display_name' => $name, 'module' => 'capstone']
        ));
        $role = Role::firstOrCreate(
            ['name' => $roleName, 'guard_name' => 'web'],
            [
                'module' => $roleName === 'admin_capstone' ? 'capstone' : 'global',
                'is_academic' => in_array($roleName, ['dosen', 'mahasiswa'], true),
            ]
        );
        $role->givePermissionTo($permissions);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user->fresh(['roles']);
    }

    /** @return array<string, mixed> */
    private function getSummary(array $params = []): array
    {
        return $this->withHeader('X-Capstone-Role', 'admin')
            ->getJson('/api/capstone/admin/reports/peer-review-summary?'.http_build_query(
                ['period_id' => $this->period->id, ...$params]
            ))
            ->assertOk()
            ->json();
    }

    public function test_summary_returns_one_row_per_reviewee(): void
    {
        $body = $this->getSummary(['per_page' => 50]);

        $this->assertSame(4, $body['meta']['total']);
        $this->assertCount(4, $body['data']);

        $andi = collect($body['data'])->firstWhere('reviewee_nim', 'NIM-001');
        $this->assertNotNull($andi);
        $this->assertSame('DINILAI', $andi['status']);
        $this->assertSame(2, $andi['reviewers_count']);
        $this->assertSame(4, $andi['reviews_count']);
        $this->assertEquals(3.25, $andi['avg_raw_score']);
        // Weighted: ((4*25*60)+(2*25*60)+(3*25*40)+(4*25*40)) / (60+60+40+40) = 80
        $this->assertEquals(80.0, $andi['avg_score']);

        $dewi = collect($body['data'])->firstWhere('reviewee_nim', 'NIM-004');
        $this->assertNotNull($dewi);
        $this->assertSame('BELUM_DINILAI', $dewi['status']);
        $this->assertSame(0, $dewi['reviewers_count']);
        $this->assertNull($dewi['avg_raw_score']);
        $this->assertNull($dewi['avg_score']);
    }

    public function test_summary_search_filters_by_name_and_nim(): void
    {
        $byNim = $this->getSummary(['student_search' => 'NIM-001']);
        $this->assertSame(1, $byNim['meta']['total']);
        $this->assertSame('NIM-001', $byNim['data'][0]['reviewee_nim']);

        $byName = $this->getSummary(['student_search' => 'dewi']);
        $this->assertSame(1, $byName['meta']['total']);
        $this->assertSame('Dewi Kosong', $byName['data'][0]['reviewee_name']);
    }

    public function test_summary_sorts_by_name(): void
    {
        $body = $this->getSummary(['sort_by' => 'reviewee', 'sort_order' => 'asc', 'per_page' => 50]);

        $this->assertSame(
            ['Andi Reviewee', 'Budi Reviewer', 'Citra Reviewer', 'Dewi Kosong'],
            array_column($body['data'], 'reviewee_name')
        );
    }

    public function test_detail_shows_reviewers_with_per_indicator_scores(): void
    {
        $andiId = $this->students['andi']->id;

        $body = $this->withHeader('X-Capstone-Role', 'admin')
            ->getJson("/api/capstone/admin/reports/peer-review-summary/{$andiId}?period_id={$this->period->id}")
            ->assertOk()
            ->json('data');

        $this->assertSame('Andi Reviewee', $body['reviewee']['name']);
        $this->assertSame(2, $body['overall']['reviewers_count']);
        $this->assertSame(4, $body['overall']['reviews_count']);
        $this->assertEquals(80.0, $body['overall']['avg_score']);

        $names = array_column($body['reviewers'], 'reviewer_name');
        sort($names);
        $this->assertSame(['Budi Reviewer', 'Citra Reviewer'], $names);

        $budi = collect($body['reviewers'])->firstWhere('reviewer_name', 'Budi Reviewer');
        $this->assertCount(2, $budi['details']);
        $this->assertSame('PR-IND-1', $budi['details'][0]['indicator_code'] ?? $budi['details'][1]['indicator_code']);
    }

    public function test_detail_returns_404_for_student_outside_period(): void
    {
        $this->withHeader('X-Capstone-Role', 'admin')
            ->getJson('/api/capstone/admin/reports/peer-review-summary/999999?period_id='.$this->period->id)
            ->assertNotFound();
    }

    public function test_flat_report_search_now_filters(): void
    {
        $body = $this->withHeader('X-Capstone-Role', 'admin')
            ->getJson('/api/capstone/admin/reports/peer-reviews?'.http_build_query([
                'period_id' => $this->period->id,
                'student_search' => 'NIM-004',
                'per_page' => 50,
            ]))
            ->assertOk()
            ->json();

        $this->assertSame(0, $body['meta']['total']);
    }
}
