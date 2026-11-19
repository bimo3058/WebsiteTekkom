<?php

namespace Tests\Feature\Auth;

use App\Services\CapstoneDumpImporter;
use App\Services\PostgresCopyArchive;
use PDO;
use PHPUnit\Framework\TestCase;

class CapstoneDumpImporterTest extends TestCase
{
    private PDO $db;

    protected function setUp(): void
    {
        $dsn = getenv('CAPSTONE_IMPORT_TEST_DSN');
        if (! $dsn) {
            $this->markTestSkipped('Set CAPSTONE_IMPORT_TEST_DSN to an isolated PostgreSQL test database.');
        }
        $this->db = new PDO($dsn, getenv('CAPSTONE_IMPORT_TEST_USER') ?: 'codex_import_test', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_STRINGIFY_FETCHES => true]);
        $this->db->beginTransaction();
        $this->db->exec(<<<'SQL'
CREATE TABLE users (id bigserial PRIMARY KEY, external_id text, name text, email text, deleted_at timestamp, suspended_at timestamp, password text, sso_enabled boolean, sso_enabled_at timestamp, sso_enabled_by bigint, is_online boolean, session_version integer, sso_data json, remember_token text);
CREATE TABLE students (id bigserial PRIMARY KEY, user_id bigint REFERENCES users(id), student_number text, cohort_year integer);
CREATE TABLE lecturers (id bigserial PRIMARY KEY, user_id bigint REFERENCES users(id), employee_number text);
CREATE TABLE roles (id bigserial PRIMARY KEY, name text, module text, guard_name text);
CREATE TABLE model_has_roles (role_id bigint REFERENCES roles(id), model_type text, model_id bigint, PRIMARY KEY(role_id, model_type, model_id));
CREATE TABLE capstone_periods (id bigserial PRIMARY KEY, name text, start_date date, end_date date);
CREATE TABLE capstone_groups (id bigserial PRIMARY KEY, period_id bigint REFERENCES capstone_periods(id), code text UNIQUE);
CREATE TABLE capstone_group_members (id bigserial PRIMARY KEY, group_id bigint REFERENCES capstone_groups(id), period_id bigint REFERENCES capstone_periods(id), student_id bigint REFERENCES students(id), is_leader boolean, deleted_at timestamp, UNIQUE(student_id, period_id));
CREATE UNIQUE INDEX single_leader ON capstone_group_members(group_id) WHERE is_leader=true;
CREATE TABLE capstone_documents (id bigserial PRIMARY KEY, group_id bigint REFERENCES capstone_groups(id), student_id bigint REFERENCES students(id), phase text, document_type text, version integer, status text, file_path text);
INSERT INTO users (id, external_id, name, email, password, sso_enabled) VALUES (100, 'NIM-1', 'Student Name', 'student@students.undip.ac.id', 'existing-hash', false);
INSERT INTO students VALUES (200, 100, '21120100001', 2023);
INSERT INTO roles VALUES (1, 'mahasiswa', 'global', 'web'), (2, 'dosen', 'global', 'web');
INSERT INTO capstone_periods VALUES (400, 'Period One', '2026-01-01', '2026-12-31');
INSERT INTO capstone_groups VALUES (300, 400, 'GROUP-A');
SQL);
    }

    protected function tearDown(): void
    {
        if (isset($this->db) && $this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

    private function source(): array
    {
        return [
            'users' => [1 => ['id' => '1', 'external_id' => 'NIM-1', 'name' => ' Student   Name ', 'email' => 'STUDENT@students.undip.ac.id', 'deleted_at' => null, 'suspended_at' => null]],
            'students' => [2 => ['id' => '2', 'user_id' => '1', 'student_number' => '21120100001', 'cohort_year' => '2023']],
            'capstone_periods' => [4 => ['id' => '4', 'name' => 'Period One', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']],
            'capstone_groups' => [3 => ['id' => '3', 'period_id' => '4', 'code' => 'GROUP-A']],
            'capstone_group_members' => [5 => ['id' => '5', 'group_id' => '3', 'period_id' => '4', 'student_id' => '2', 'is_leader' => 't', 'deleted_at' => null]],
            'capstone_documents' => [6 => ['id' => '6', 'group_id' => '3', 'student_id' => '2', 'phase' => 'PDC1', 'document_type' => 'REPORT', 'version' => '1', 'status' => 'APPROVED', 'file_path' => 'capstone/report.pdf']],
        ];
    }

    public function test_existing_student_without_capstone_gets_membership_using_destination_ids(): void
    {
        $before = $this->db->query('SELECT row_to_json(u)::text FROM users u')->fetchColumn();
        $source = $this->source();
        $plan = (new CapstoneDumpImporter($this->db, $source, '2026-11-13 00:00:00'))->plan();
        $this->assertSame('100', $plan['mapping']['users'][1]);
        $this->assertSame('200', $plan['mapping']['students'][2]);
        $this->assertSame(['capstone_documents' => 1, 'capstone_group_members' => 1], $plan['insert_counts']);
        foreach ($plan['operations'] as $operation) {
            $row = $operation['row'];
            $this->assertSame('200', $row['student_id']);
            $this->assertSame('300', $row['group_id']);
            $sql = 'INSERT INTO '.$operation['table'].' ('.implode(',', array_keys($row)).') VALUES ('.implode(',', array_fill(0, count($row), '?')).')';
            $this->db->prepare($sql)->execute(array_values($row));
        }
        $this->assertSame($before, $this->db->query('SELECT row_to_json(u)::text FROM users u')->fetchColumn());
        $repeat = (new CapstoneDumpImporter($this->db, $source, '2026-11-13 00:00:00'))->plan();
        $this->assertSame([], $repeat['operations']);
    }

    public function test_different_group_in_same_period_blocks_membership_and_student_document(): void
    {
        $this->db->exec("INSERT INTO capstone_groups VALUES (301,400,'GROUP-B'); INSERT INTO capstone_group_members VALUES (500,301,400,200,false,NULL)");
        $plan = (new CapstoneDumpImporter($this->db, $this->source(), '2026-11-13 00:00:00'))->plan();
        $this->assertSame([], $plan['operations']);
        $this->assertCount(2, $plan['conflicts']);
    }

    public function test_new_group_is_created_with_destination_period_and_existing_student(): void
    {
        $source = $this->source();
        $source['capstone_groups'][3]['code'] = 'GROUP-NEW';
        $plan = (new CapstoneDumpImporter($this->db, $source, '2026-11-13 00:00:00'))->plan();
        $this->assertSame('301', $plan['mapping']['capstone_groups'][3]);
        $groups = array_values(array_filter($plan['operations'], fn ($operation) => $operation['table'] === 'capstone_groups'));
        $this->assertSame('400', $groups[0]['row']['period_id']);
        $members = array_values(array_filter($plan['operations'], fn ($operation) => $operation['table'] === 'capstone_group_members'));
        $this->assertSame('200', $members[0]['row']['student_id']);
        $this->assertSame('301', $members[0]['row']['group_id']);
    }

    public function test_existing_document_status_is_preserved_and_only_missing_version_is_added(): void
    {
        $this->db->exec("INSERT INTO capstone_group_members VALUES (500,300,400,200,true,NULL); INSERT INTO capstone_documents VALUES (600,300,200,'PDC1','REPORT',1,'REJECTED','capstone/current.pdf')");
        $source = $this->source();
        $source['capstone_documents'][7] = array_replace($source['capstone_documents'][6], ['id' => '7', 'version' => '2']);
        $plan = (new CapstoneDumpImporter($this->db, $source, '2026-11-13 00:00:00'))->plan();
        $this->assertSame(['capstone_documents' => 1], $plan['insert_counts']);
        $this->assertSame('2', $plan['operations'][0]['row']['version']);
        $this->assertSame('REJECTED', $this->db->query('SELECT status FROM capstone_documents WHERE id=600')->fetchColumn());
    }

    public function test_uncoded_legacy_group_can_be_created_for_students_without_memberships(): void
    {
        $source = $this->source();
        $source['capstone_groups'][3]['code'] = null;
        $plan = (new CapstoneDumpImporter($this->db, $source, '2026-11-13 00:00:00'))->plan();
        $this->assertSame('301', $plan['mapping']['capstone_groups'][3]);
        $this->assertSame(['capstone_documents' => 1, 'capstone_group_members' => 1, 'capstone_groups' => 1], $plan['insert_counts']);
    }

    public function test_nim_mismatch_blocks_profile_and_dependent_imports(): void
    {
        $source = $this->source();
        $source['students'][2]['student_number'] = 'DIFFERENT-NIM';
        $plan = (new CapstoneDumpImporter($this->db, $source, '2026-11-13 00:00:00'))->plan();
        $this->assertSame([], $plan['operations']);
        $this->assertArrayNotHasKey('students', $plan['mapping']);
    }

    public function test_new_user_and_profile_receive_fresh_ids_and_only_academic_role(): void
    {
        $source = $this->source();
        $source['users'][1] = array_replace($source['users'][1], ['external_id' => 'NIM-NEW', 'name' => 'New Student', 'email' => 'new@students.undip.ac.id']);
        $source['students'][2]['student_number'] = '21120100002';
        $plan = (new CapstoneDumpImporter($this->db, $source, '2026-11-13 00:00:00'))->plan();
        $this->assertSame('101', $plan['mapping']['users'][1]);
        $this->assertSame('201', $plan['mapping']['students'][2]);
        $users = array_values(array_filter($plan['operations'], fn ($operation) => $operation['table'] === 'users'));
        $this->assertSame('t', $users[0]['row']['sso_enabled']);
        $this->assertNull($users[0]['row']['password']);
        $roles = array_values(array_filter($plan['operations'], fn ($operation) => $operation['table'] === 'model_has_roles'));
        $this->assertSame('101', $roles[0]['row']['model_id']);
        $this->assertSame('1', $roles[0]['row']['role_id']);
    }

    public function test_copy_decoder_preserves_escaped_content_and_ignores_sql(): void
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, "DROP TABLE users;\nCOPY public.users (id, name, email) FROM stdin;\n1\tName\\tWith\\nEscapes\\\\N\t\\N\n\\.\n");
        rewind($stream);
        $rows = PostgresCopyArchive::parse($stream);
        fclose($stream);
        $this->assertSame("Name\tWith\nEscapes\\N", $rows['users'][1]['name']);
        $this->assertNull($rows['users'][1]['email']);
        $this->assertSame('100', $this->db->query('SELECT id FROM users')->fetchColumn());
    }
}
