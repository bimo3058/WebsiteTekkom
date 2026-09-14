<?php

namespace Modules\Capstone\Database\Seeders;

use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\AssessmentComponentTemplate;
use Modules\Capstone\Models\DocumentType;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Location;
use Modules\Capstone\Models\PeerReviewIndicatorTemplate;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodAssessmentComponent;
use Modules\Capstone\Models\PeriodPeerReviewIndicator;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Models\Title;

class CapstoneDemoSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::query()->whereHas('roles', fn ($query) => $query->whereIn('name', ['superadmin', 'admin_capstone']))->first();
        $lecturer = Lecturer::query()->first();
        $students = Student::query()->orderBy('id')->limit(3)->get();

        $period = Period::updateOrCreate(
            ['name' => 'Capstone 2026/2027 Ganjil'],
            [
                'start_date' => '2026-08-01',
                'end_date' => '2027-01-31',
                'is_active' => true,
                'bidding_start' => '2026-08-01 00:00:00',
                'bidding_end' => '2026-09-30 23:59:59',
                'pdc1_start' => '2026-09-01',
                'pdc1_end' => '2026-10-15',
                'pdc2_start' => '2026-10-16',
                'pdc2_end' => '2026-12-01',
                'expo_date' => '2026-12-15',
                'ta_start' => '2026-12-16',
                'ta_end' => '2027-01-31',
                'min_group_size' => 2,
                'max_group_size' => 4,
                'max_supervise_load' => 8,
                'max_supervisor_load' => 8,
                'allow_solo' => true,
                'require_all_students_grouped' => true,
                'is_finalized' => false,
            ]
        );

        foreach ([
            ['name' => 'Ruang Capstone A', 'capacity' => 30, 'type' => 'offline'],
            ['name' => 'Ruang Capstone B', 'capacity' => 30, 'type' => 'offline'],
            ['name' => 'Microsoft Teams', 'capacity' => null, 'type' => 'online'],
        ] as $location) {
            Location::updateOrCreate(['name' => $location['name']], $location + ['is_active' => true]);
        }

        foreach ([
            ['name' => 'Proposal PDC1', 'phase' => 'PDC1'],
            ['name' => 'Laporan PDC2', 'phase' => 'PDC2'],
            ['name' => 'Dokumen Tugas Akhir', 'phase' => 'TA'],
        ] as $type) {
            DocumentType::updateOrCreate(['name' => $type['name']], $type + ['is_active' => true]);
        }

        $components = [
            ['code' => 'SUBSTANCE', 'name' => 'Substansi', 'weight' => 40, 'sort_order' => 1],
            ['code' => 'METHOD', 'name' => 'Metodologi', 'weight' => 30, 'sort_order' => 2],
            ['code' => 'PRESENTATION', 'name' => 'Presentasi', 'weight' => 30, 'sort_order' => 3],
        ];

        foreach ($components as $component) {
            $template = AssessmentComponentTemplate::updateOrCreate(
                ['code' => $component['code']],
                $component + ['is_active' => true, 'created_by' => $creator?->id]
            );

            foreach (['SEMPRO', 'EXPO', 'SIDANG_TA'] as $type) {
                PeriodAssessmentComponent::updateOrCreate(
                    ['period_id' => $period->id, 'template_id' => $template->id, 'type' => $type],
                    ['sort_order' => $component['sort_order']]
                );
            }
        }

        foreach ([
            ['name' => 'Kontribusi', 'weight' => 40, 'sort_order' => 1],
            ['name' => 'Kolaborasi', 'weight' => 30, 'sort_order' => 2],
            ['name' => 'Komunikasi', 'weight' => 30, 'sort_order' => 3],
        ] as $indicator) {
            $template = PeerReviewIndicatorTemplate::updateOrCreate(
                ['name' => $indicator['name']],
                $indicator + ['is_active' => true, 'created_by' => $creator?->id]
            );

            PeriodPeerReviewIndicator::updateOrCreate(
                ['period_id' => $period->id, 'template_id' => $template->id],
                ['sort_order' => $indicator['sort_order']]
            );
        }

        if (! $lecturer || $students->count() < 2) {
            return;
        }

        $title = Title::updateOrCreate(
            ['lecturer_id' => $lecturer->id, 'title' => 'Platform Monitoring Capstone Terintegrasi'],
            [
                'period_id' => $period->id,
                'description' => 'Data demo CTMS yang menggunakan identitas global WebsiteTekkom.',
                'problem_statement' => 'Monitoring progres capstone masih tersebar.',
                'scope' => 'Dashboard, dokumen, penilaian, dan jadwal.',
                'specializations' => ['Software Engineering', 'Information System'],
                'quota' => 1,
                'status' => 'OPEN',
                'approved_by_admin' => true,
                'title_source' => 'LECTURER',
            ]
        );

        $group = Group::updateOrCreate(
            ['code' => 'CAP-DEMO-01'],
            [
                'period_id' => $period->id,
                'status' => 'PDC1_ACTIVE',
                'group_mode' => 'GROUP',
                'has_existing_group' => true,
                'supervisor_1_id' => $lecturer->id,
                'readiness_status' => 'READY',
            ]
        );
        $group->assignTitleFromFinalization($title->id);
        $group->save();

        foreach ($students->take(2)->values() as $index => $student) {
            GroupMember::updateOrCreate(
                ['group_id' => $group->id, 'student_id' => $student->id],
                ['period_id' => $period->id, 'is_leader' => $index === 0]
            );
            PeriodRegistration::updateOrCreate(
                ['user_id' => $student->id, 'period_id' => $period->id],
                ['status' => 'active']
            );
        }

        Supervision::updateOrCreate(
            ['group_id' => $group->id, 'role' => 'SUPERVISOR_1'],
            ['supervisor_id' => $lecturer->id, 'assigned_by' => $creator?->id]
        );

        DB::table('system_modules')->updateOrInsert(
            ['slug' => 'capstone'],
            [
                'name' => 'Capstone + TA',
                'icon' => 'graduation-cap',
                'description' => 'Sistem Capstone dan Tugas Akhir terintegrasi SSO.',
                'is_active' => true,
                'is_maintenance' => false,
                'settings' => json_encode(['launch_path' => '/capstone/launch']),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
