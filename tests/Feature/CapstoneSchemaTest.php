<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CapstoneSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_capstone_schema_uses_prefixed_tables_and_global_identities(): void
    {
        foreach ([
            'capstone_periods',
            'capstone_groups',
            'capstone_titles',
            'capstone_period_registrations',
            'capstone_assessment_component_templates',
            'capstone_ta_defense_schedule_student',
            'capstone_expo_student_documents',
            'capstone_sempro_scores',
            'capstone_sidang_ta_scores',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing table {$table}");
        }

        $this->assertTrue(Schema::hasColumns('capstone_groups', [
            'period_id', 'title_id', 'supervisor_1_id', 'supervisor_2_id', 'code', 'finalized_by',
        ]));
        $this->assertTrue(Schema::hasColumns('capstone_ta_defense_schedules', [
            'student_id', 'period_id', 'examiner_1_id', 'examiner_2_id', 'location_id',
        ]));

        $foreignKeys = collect(DB::select("PRAGMA foreign_key_list('capstone_ta_defense_schedules')"));
        $this->assertTrue($foreignKeys->contains(fn ($key) => $key->from === 'student_id' && $key->table === 'students'));
        $this->assertTrue($foreignKeys->contains(fn ($key) => $key->from === 'examiner_1_id' && $key->table === 'lecturers'));

        $indexes = collect(DB::select("PRAGMA index_list('capstone_period_registrations')"));
        $this->assertTrue($indexes->contains(fn ($index) => (bool) $index->unique));

        $this->assertFalse(Schema::hasTable('capstone_users'));
        $this->assertFalse(Schema::hasTable('capstone_roles'));
        $this->assertFalse(Schema::hasTable('capstone_sessions'));
        $this->assertTrue(Schema::hasColumns('users', ['is_online', 'whatsapp', 'avatar_url']));
    }
}
