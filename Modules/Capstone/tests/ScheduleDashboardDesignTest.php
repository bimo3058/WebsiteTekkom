<?php

namespace Modules\Capstone\Tests;

use Tests\TestCase;

class ScheduleDashboardDesignTest extends TestCase
{
    public function test_admin_header_has_dashboard_title_and_creation_menu(): void
    {
        $html = view('capstone::partials.schedule-header', ['activeRole' => 'admin'])->render();

        $this->assertStringContainsString('Schedule Dashboard', $html);
        $this->assertStringContainsString('Buat Jadwal', $html);
        foreach (['New SEMPRO', 'New EXPO', 'New TA Defense'] as $label) {
            $this->assertStringContainsString($label, $html);
        }
    }

    public function test_dosen_header_has_bimbingan_action_only(): void
    {
        $html = view('capstone::partials.schedule-header', ['activeRole' => 'dosen'])->render();

        $this->assertStringContainsString('My Schedule', $html);
        $this->assertStringContainsString('Buat Jadwal', $html);
        $this->assertStringContainsString('New BIMBINGAN', $html);
        $this->assertStringNotContainsString('New SEMPRO', $html);
    }

    public function test_mahasiswa_header_has_no_creation_action(): void
    {
        $html = view('capstone::partials.schedule-header', ['activeRole' => 'mahasiswa'])->render();

        $this->assertStringContainsString('My Schedule', $html);
        $this->assertStringNotContainsString('Buat Jadwal', $html);
    }

    public function test_toolbar_has_view_toggle_search_filter_and_month_navigation(): void
    {
        $html = view('capstone::partials.schedule-toolbar', ['activeRole' => 'admin'])->render();

        foreach (['Calendar', 'Table', 'Search', 'Period', 'Type', 'Status', 'Previous month', 'Next month'] as $marker) {
            $this->assertStringContainsString($marker, $html);
        }
    }

    public function test_calendar_is_grid_only_with_pill_events_and_more_overflow(): void
    {
        $html = view('capstone::partials.schedule-calendar', ['activeRole' => 'admin'])->render();

        foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day) {
            $this->assertStringContainsString('>'.$day.'<', $html);
        }
        $this->assertStringContainsString('sched-pill', $html);
        $this->assertStringContainsString('pillClass(event.type)', $html);
        $this->assertStringContainsString("' more'", $html);
        $this->assertStringContainsString('openDay(day.key)', $html);
    }

    public function test_table_has_exact_screenshot_columns_and_pagination(): void
    {
        foreach (['admin', 'dosen', 'mahasiswa'] as $role) {
            $html = view('capstone::partials.schedule-table', ['activeRole' => $role])->render();

            foreach (['No', 'Date', 'Time', 'Type', 'Group', 'Anggota', 'Ruangan', 'Status', 'Action'] as $column) {
                $this->assertStringContainsString('>'.$column.'<', $html, $role.': '.$column);
            }
            foreach (['Per page', 'Showing ', 'typePill(event.type)', 'statusPill(event.status)', 'memberNames(event)', 'pageList', 'groupCode(event)', 'dateId(event.date)', 'timeShort(event.start_time)'] as $marker) {
                $this->assertStringContainsString($marker, $html, $role.': '.$marker);
            }
            $this->assertStringNotContainsString('type="checkbox"', $html, $role.': checkbox column');
        }
    }

    public function test_table_actions_match_the_viewer_role(): void
    {
        $admin = view('capstone::partials.schedule-table', ['activeRole' => 'admin'])->render();
        $this->assertStringContainsString('Approve', $admin);
        $this->assertStringContainsString('Reject', $admin);

        $dosen = view('capstone::partials.schedule-table', ['activeRole' => 'dosen'])->render();
        $this->assertStringContainsString('edit(event)', $dosen);
        $this->assertStringContainsString('Delete', $dosen);
        $this->assertStringNotContainsString('Approve', $dosen);

        $mahasiswa = view('capstone::partials.schedule-table', ['activeRole' => 'mahasiswa'])->render();
        $this->assertStringContainsString('Detail', $mahasiswa);
        $this->assertStringNotContainsString('Approve', $mahasiswa);
        $this->assertStringNotContainsString('Delete', $mahasiswa);
    }
}
