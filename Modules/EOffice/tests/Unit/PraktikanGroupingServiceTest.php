<?php

namespace Modules\EOffice\Tests\Unit;

use Illuminate\Support\Collection;
use Modules\EOffice\Services\PraktikanGroupingService;
use PHPUnit\Framework\TestCase;

class PraktikanGroupingServiceTest extends TestCase
{
    public function test_it_builds_balanced_sequential_groups_and_shifts(): void
    {
        $service = new PraktikanGroupingService;
        $ids = new Collection(['p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7', 'p8', 'p9', 'p10']);

        $draft = $service->automatic($ids, 3, 2, 'urutan_sistem');

        $this->assertSame(['1', '1', '2'], array_column($draft['groups'], 'shift'));
        $this->assertSame(['1', '1', '1', '1', '2', '2', '2', '3', '3', '3'], array_values($draft['assignments']));
        $this->assertSame([], $service->validationErrors($draft, $ids));
    }

    public function test_manual_draft_reports_incomplete_assignment(): void
    {
        $service = new PraktikanGroupingService;
        $draft = $service->manual(2, 1);
        $draft['assignments']['p1'] = '1';

        $errors = $service->validationErrors($draft, new Collection(['p1', 'p2']));

        $this->assertContains('1 praktikan belum memiliki kelompok.', $errors);
        $this->assertContains('Kelompok tanpa anggota: 2.', $errors);
    }

    public function test_manual_draft_is_valid_when_every_group_has_members_and_shift(): void
    {
        $service = new PraktikanGroupingService;
        $draft = $service->manual(2, 2);
        $draft['assignments'] = ['p1' => '1', 'p2' => '2'];

        $this->assertSame([], $service->validationErrors($draft, new Collection(['p1', 'p2'])));
    }

    public function test_it_can_copy_active_grouping_into_an_editable_draft(): void
    {
        $service = new PraktikanGroupingService;
        $praktikans = new Collection([
            (object) ['id' => 'p1', 'kelompok' => 'Capstone A', 'shift' => 'Pagi'],
            (object) ['id' => 'p2', 'kelompok' => 'Capstone A', 'shift' => 'Pagi'],
            (object) ['id' => 'p3', 'kelompok' => 'Capstone B', 'shift' => 'Siang'],
        ]);

        $draft = $service->fromActive($praktikans, 'manual');

        $this->assertSame(['Capstone A', 'Capstone B'], array_column($draft['groups'], 'name'));
        $this->assertSame(['Pagi', 'Siang'], array_column($draft['groups'], 'shift'));
        $this->assertSame(['p1' => 'Capstone A', 'p2' => 'Capstone A', 'p3' => 'Capstone B'], $draft['assignments']);
    }
}
