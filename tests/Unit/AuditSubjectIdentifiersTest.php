<?php

namespace Tests\Unit;

use App\Services\AuditLogger;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Modules\EOffice\Models\PracticumSchedulePeriod;
use Tests\TestCase;

class AuditSubjectIdentifiersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'audit_test', 'database.connections.audit_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::purge('audit_test');
        foreach (['audit_logs', 'eo_audit_logs'] as $table) {
            Schema::create($table, function (Blueprint $t) use ($table) {
                $t->id();
                $t->unsignedBigInteger('user_id')->nullable();
                if ($table === 'audit_logs') $t->string('module');
                $t->string('action');
                $t->string('subject_type')->nullable();
                $t->unsignedBigInteger('subject_id')->nullable();
                $t->text('description');
                if ($table === 'eo_audit_logs') { $t->json('old_data')->nullable(); $t->json('new_data')->nullable(); }
                $t->timestamp('created_at');
            });
            DB::table($table)->insert(['action' => 'UPDATE', 'subject_id' => 123, 'description' => 'Legacy', 'created_at' => now()] + ($table === 'audit_logs' ? ['module' => 'eoffice'] : []));
        }
    }

    private function migration(): object
    {
        return require base_path('database/migrations/2026_11_05_000004_allow_uuid_subjects_in_eoffice_audit_logs.php');
    }

    public function test_uuid_subject_is_written_to_both_audits_and_legacy_ids_are_preserved(): void
    {
        $this->migration()->up();
        $subject = new PracticumSchedulePeriod;
        $subject->id = '01a1ab9c-18fd-7197-b43b-0280d55fa6d2';
        AuditLogger::log('eoffice', 'UPDATE', 'Pengaturan periode pelaksanaan praktikum', $subject, null, ['semester' => 'Ganjil'], 1074);
        foreach (['audit_logs', 'eo_audit_logs'] as $table) {
            $this->assertSame('varchar', Schema::getColumnType($table, 'subject_id'));
            $this->assertSame('123', (string) DB::table($table)->where('id', 1)->value('subject_id'));
            $this->assertSame($subject->id, DB::table($table)->where('id', 2)->value('subject_id'));
        }
        try { $this->migration()->down(); $this->fail('UUID rollback must be refused'); }
        catch (\RuntimeException $e) { $this->assertStringContainsString('Rollback ditolak', $e->getMessage()); }
        $this->assertSame(2, DB::table('audit_logs')->count());
        $this->assertSame('varchar', Schema::getColumnType('audit_logs', 'subject_id'));
    }

    public function test_numeric_only_migration_can_be_rolled_back(): void
    {
        $this->migration()->up();
        $this->migration()->down();
        $this->assertSame(123, DB::table('audit_logs')->value('subject_id'));
    }
}
