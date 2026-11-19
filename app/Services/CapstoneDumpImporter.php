<?php

namespace App\Services;

use PDO;
use RuntimeException;

class CapstoneDumpImporter
{
    private array $columns = [];
    private array $foreign = [];
    private array $rows = [];
    private array $original = [];
    private array $mapping = [];
    private array $operations = [];
    private array $conflicts = [];
    private array $differences = [];
    private array $roleReview = [];
    private array $nextIds = [];
    private string $lastIssue = '';

    private const KEYS = [
        'capstone_periods' => [['name', 'start_date', 'end_date']],
        'capstone_groups' => [['period_id', 'code']],
        'capstone_titles' => [['period_id', 'lecturer_id', 'title']],
        'capstone_locations' => [['name', 'type']],
        'capstone_document_types' => [['name', 'phase']],
        'capstone_stakeholders' => [['email', 'organization'], ['name', 'organization']],
        'capstone_assessment_component_templates' => [['code']],
        'capstone_peer_review_indicator_templates' => [['code']],
        'capstone_assessment_components' => [['period_id', 'type', 'code']],
        'capstone_peer_review_indicators' => [['period_id', 'name']],
        'capstone_period_assessment_components' => [['period_id', 'type', 'template_id']],
        'capstone_period_peer_review_indicators' => [['period_id', 'template_id']],
        'capstone_phase_document_requirements' => [['period_id', 'phase', 'name']],
        'capstone_period_registrations' => [['user_id', 'period_id']],
        'capstone_group_members' => [['student_id', 'period_id']],
        'capstone_supervisions' => [['group_id', 'role']],
        'capstone_bids' => [['group_id', 'title_id', 'priority', 'created_at']],
        'capstone_documents' => [['group_id', 'student_id', 'phase', 'document_type', 'version']],
        'capstone_document_versions' => [['document_id', 'version']],
        'capstone_group_invitations' => [['group_id', 'student_id']],
        'capstone_join_requests' => [['group_id', 'requester_id']],
        'capstone_group_supervisor_proposals' => [['group_id', 'created_at']],
        'capstone_seminar_schedules' => [['group_id', 'type', 'date', 'start_time']],
        'capstone_seminar_evaluations' => [['schedule_id', 'examiner_id']],
        'capstone_schedules' => [['group_id', 'type', 'date', 'start_time']],
        'capstone_expo_events' => [['period_id', 'name', 'date']],
        'capstone_expo_registrations' => [['expo_event_id', 'group_id']],
        'capstone_expo_self_evaluations' => [['expo_registration_id', 'student_id', 'period_component_id']],
        'capstone_expo_student_documents' => [['expo_registration_id', 'student_id']],
        'capstone_ta_submissions' => [['student_id', 'group_id', 'period_id']],
        'capstone_ta_registrations' => [['student_id', 'group_id']],
        'capstone_ta_defense_schedules' => [['student_id', 'group_id', 'period_id', 'date', 'start_time']],
        'capstone_ta_defense_examiners' => [['schedule_id', 'examiner_id']],
        'capstone_ta_defense_schedule_student' => [['schedule_id', 'student_id']],
        'capstone_ta_defense_evaluations' => [['schedule_id', 'examiner_id', 'student_id']],
        'capstone_student_peer_review_status' => [['student_id', 'period_id']],
        'capstone_peer_reviews' => [['group_id', 'reviewer_id', 'reviewee_id', 'period_indicator_id', 'indicator_id']],
        'capstone_stakeholder_title' => [['stakeholder_id', 'title_id']],
        'capstone_digital_signatures' => [['hash']],
        'capstone_grade_consistency_checks' => [['group_id', 'student_id', 'created_at']],
        'capstone_title_approval_audits' => [['title_id', 'lecturer_id', 'action', 'created_at']],
        'capstone_title_deletion_audits' => [['title_id', 'deleted_by', 'deleted_at']],
        'capstone_finalization_audits' => [['period_id', 'group_id', 'user_id', 'action', 'created_at']],
        'capstone_notifications' => [['user_id', 'type', 'related_type', 'related_id', 'created_at']],
        'capstone_audit_logs' => [['user_id', 'action', 'target_type', 'target_id', 'created_at']],
        'capstone_evaluations' => [['evaluator_id', 'group_id', 'student_id', 'type']],
    ];

    private const POLYMORPHIC = [
        'Group' => 'capstone_groups', 'Period' => 'capstone_periods', 'Title' => 'capstone_titles',
        'Student' => 'students', 'User' => 'users', 'Lecturer' => 'lecturers',
        'SeminarSchedule' => 'capstone_seminar_schedules', 'TaDefenseSchedule' => 'capstone_ta_defense_schedules',
        'ExpoEvent' => 'capstone_expo_events', 'ExpoRegistration' => 'capstone_expo_registrations',
        'GroupInvitation' => 'capstone_group_invitations', 'Document' => 'capstone_documents',
        'Bid' => 'capstone_bids', 'TaRegistration' => 'capstone_ta_registrations',
        'documents' => 'capstone_documents', 'seminar_schedules' => 'capstone_seminar_schedules',
        'group_invitations' => 'capstone_group_invitations', 'groups' => 'capstone_groups',
    ];

    public function __construct(private PDO $db, private array $source, private string $timestamp)
    {
        foreach ($this->source as $table => $rows) {
            $counts = array_count_values(array_column($rows, 'id'));
            foreach ($rows as $key => $row) {
                if (isset($row['id']) && $counts[$row['id']] > 1) {
                    unset($this->source[$table][$key]);
                    $this->conflict($table, $row['id'], 'Duplicate ID in source archive.');
                }
            }
        }
        $metadata = $db->query("SELECT table_name, column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_schema='public' ORDER BY table_name, ordinal_position")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($metadata as $column) {
            if (PostgresCopyArchive::allowed($column['table_name']) || in_array($column['table_name'], ['model_has_permissions', 'role_has_permissions'], true)) {
                $this->columns[$column['table_name']][$column['column_name']] = $column;
            }
        }
        foreach ($db->query(<<<'SQL'
SELECT t.relname AS table_name, a.attname AS column_name, rt.relname AS referenced_table
FROM pg_constraint c JOIN pg_class t ON t.oid=c.conrelid
JOIN pg_namespace n ON n.oid=t.relnamespace JOIN pg_class rt ON rt.oid=c.confrelid
JOIN pg_attribute a ON a.attrelid=t.oid AND a.attnum=c.conkey[1]
WHERE n.nspname='public' AND c.contype='f' AND cardinality(c.conkey)=1
SQL)->fetchAll(PDO::FETCH_ASSOC) as $reference) {
            $this->foreign[$reference['table_name']][$reference['column_name']] = $reference['referenced_table'];
        }
        foreach (['capstone_expo_events', 'capstone_seminar_schedules', 'capstone_ta_defense_schedules'] as $table) {
            if (isset($this->columns[$table]['eoffice_peminjaman_id'])) {
                $this->foreign[$table]['eoffice_peminjaman_id'] = 'eo_mr_peminjamans';
            }
        }
        foreach (array_keys($this->columns) as $table) {
            $rows = $db->query('SELECT * FROM public.'.self::quote($table).' ORDER BY '.(isset($this->columns[$table]['id']) ? 'id' : implode(', ', array_map(self::quote(...), array_keys($this->columns[$table])))))->fetchAll(PDO::FETCH_ASSOC);
            $this->rows[$table] = [];
            foreach ($rows as $index => $row) {
                $this->rows[$table][(string) ($row['id'] ?? $index)] = $row;
            }
            $this->nextIds[$table] = isset($this->columns[$table]['id']) ? max([0, ...array_map('intval', array_keys($this->rows[$table]))]) + 1 : 0;
        }
        $this->original = $this->rows;
    }

    public function tables(): array
    {
        return array_keys($this->columns);
    }

    public function fingerprint(): string
    {
        return hash('sha256', json_encode([$this->columns, $this->foreign, $this->original], JSON_THROW_ON_ERROR));
    }

    public function plan(): array
    {
        $this->planUsers();
        $this->planProfiles('students', 'student_number');
        $this->planProfiles('lecturers', 'employee_number');
        $this->mapExternalTables();
        $this->planNewUserRoles();

        $pending = [];
        foreach ($this->source as $table => $rows) {
            if (! str_starts_with($table, 'capstone_')) {
                continue;
            }
            foreach ($rows as $id => $row) {
                $pending[$table.':'.$id] = [$table, (string) $id, $row];
            }
        }
        // Resolve business keys first. Ancillary relations are checked after all IDs are known.
        do {
            $progress = false;
            foreach ($pending as $key => [$table, $id, $row]) {
                if ($this->planCapstoneRow($table, $id, $row)) {
                    unset($pending[$key]);
                    $progress = true;
                }
            }
        } while ($progress && $pending !== []);
        foreach ($pending as [$table, $id, $row]) {
            $missing = [];
            foreach ($this->foreign[$table] ?? [] as $column => $reference) {
                if (isset($row[$column]) && ! isset($this->mapping[$reference][$row[$column]])) {
                    $missing[] = $column.'='.$reference.':'.$row[$column];
                }
            }
            $this->conflict($table, $id, 'Unresolved business key: '.implode(', ', $missing));
        }

        $this->completeOperations();
        $counts = [];
        foreach ($this->operations as $operation) {
            $counts[$operation['table']] = ($counts[$operation['table']] ?? 0) + 1;
        }
        ksort($counts);

        return [
            'version' => 1, 'generated_at' => $this->timestamp,
            'target_fingerprint' => $this->fingerprint(), 'insert_counts' => $counts,
            'operations' => array_values($this->operations), 'mapping' => $this->mapping,
            'conflicts' => $this->conflicts, 'existing_capstone_differences' => $this->differences,
            'new_user_role_review' => $this->roleReview,
            'source_counts' => array_map('count', $this->source),
        ];
    }

    private function planUsers(): void
    {
        foreach ($this->source['users'] ?? [] as $id => $row) {
            $emailMatches = $this->find('users', ['email' => $row['email']]);
            $externalMatches = $this->find('users', ['external_id' => $row['external_id']]);
            if (count($emailMatches) === 1) {
                $target = $emailMatches[0];
                $externalConflict = $externalMatches !== [] && (count($externalMatches) !== 1 || $externalMatches[0]['id'] != $target['id']);
                if ($this->normalize($target['name']) !== $this->normalize($row['name']) || $target['deleted_at'] !== null || $externalConflict) {
                    $this->conflict('users', $id, 'Name/email/external ID conflict or deleted target account.');
                    continue;
                }
                $this->mapping['users'][$id] = (string) $target['id'];
                continue;
            }
            if ($emailMatches !== [] || $externalMatches !== []) {
                $this->conflict('users', $id, 'Ambiguous email or external ID belongs to another account.');
                continue;
            }
            $active = $row['deleted_at'] === null && $row['suspended_at'] === null;
            $new = array_intersect_key($row, array_flip(['external_id', 'name', 'email', 'created_at', 'updated_at', 'deleted_at', 'suspended_at', 'suspension_reason', 'personal_email', 'whatsapp', 'avatar_url', 'avatar_url_format', 'notification_preferences']));
            $new['email'] = mb_strtolower(trim($row['email']));
            $new += ['password' => null, 'remember_token' => null, 'sso_data' => null, 'sso_enabled' => $active ? 't' : 'f', 'sso_enabled_at' => $active ? $this->timestamp : null, 'sso_enabled_by' => null, 'is_online' => 'f', 'session_version' => '0'];
            $this->add('users', (string) $id, $new);
        }
    }

    private function planProfiles(string $table, string $number): void
    {
        foreach ($this->source[$table] ?? [] as $id => $row) {
            $userId = $this->mapping['users'][$row['user_id']] ?? null;
            if ($userId === null) {
                $this->conflict($table, $id, 'User identity is unresolved.');
                continue;
            }
            $matches = $this->find($table, ['user_id' => $userId]);
            $numberMatches = $this->find($table, [$number => $row[$number]]);
            if (count($matches) === 1 && $matches[0][$number] === $row[$number]
                && count($numberMatches) === 1 && $numberMatches[0]['id'] == $matches[0]['id']) {
                $this->mapping[$table][$id] = (string) $matches[0]['id'];
                continue;
            }
            if ($matches !== [] || $numberMatches !== []) {
                $this->conflict($table, $id, 'NIM/NIP or profile ownership conflict.');
                continue;
            }
            $row['user_id'] = $userId;
            $this->add($table, (string) $id, $row);
        }
    }

    private function mapExternalTables(): void
    {
        foreach (['eo_mr_ruangans', 'eo_mr_peminjamans'] as $table) {
            $source = $this->source[$table] ?? ($table === 'eo_mr_ruangans' ? ($this->source['eo_ruangan'] ?? []) : []);
            foreach ($source as $id => $row) {
                $keys = $table === 'eo_mr_ruangans' ? ['nama', 'lokasi', 'lantai'] : ['user_id', 'ruangan_id', 'tanggal_pinjam', 'jam_mulai', 'jam_selesai', 'tujuan'];
                $criteria = [];
                foreach ($keys as $key) {
                    if (! array_key_exists($key, $row) || ! isset($this->columns[$table][$key])) {
                        continue 2;
                    }
                    $value = $row[$key];
                    $reference = $this->foreign[$table][$key] ?? null;
                    if ($reference !== null && $value !== null) {
                        $value = $this->mapping[$reference][$value] ?? null;
                        if ($value === null) {
                            continue 2;
                        }
                    }
                    $criteria[$key] = $value;
                }
                $matches = $this->find($table, $criteria);
                if (count($matches) === 1) {
                    $this->mapping[$table][$id] = (string) $matches[0]['id'];
                }
            }
        }
    }

    private function planNewUserRoles(): void
    {
        foreach ($this->operations as $operation) {
            if ($operation['table'] !== 'users') {
                continue;
            }
            $user = $operation['row'];
            if ($user['sso_enabled'] !== 't') {
                continue;
            }
            $sourceId = $operation['source_id'];
            foreach ($this->source['model_has_roles'] ?? [] as $pivot) {
                if ($pivot['model_type'] === 'App\\Models\\User' && $pivot['model_id'] == $sourceId) {
                    $role = $this->source['roles'][$pivot['role_id']] ?? null;
                    if ($role && ! in_array($role['name'], ['mahasiswa', 'dosen'], true)) {
                        $this->roleReview[] = ['source_user_id' => $sourceId, 'target_user_id' => $user['id'], 'role' => $role['name'], 'module' => $role['module']];
                    }
                }
            }
            $hasStudent = false;
            $hasLecturer = false;
            foreach ($this->source['students'] ?? [] as $profile) {
                $hasStudent = $hasStudent || ($profile['user_id'] == $sourceId && isset($this->mapping['students'][$profile['id']]));
            }
            foreach ($this->source['lecturers'] ?? [] as $profile) {
                $hasLecturer = $hasLecturer || ($profile['user_id'] == $sourceId && isset($this->mapping['lecturers'][$profile['id']]) && ! str_starts_with($profile['employee_number'], 'NEON-CAPSTONE-ACTOR-'));
            }
            $domain = substr(strrchr($user['email'], '@') ?: '', 1);
            $names = [];
            if ($hasStudent || (! $hasLecturer && $domain === 'students.undip.ac.id')) {
                $names[] = 'mahasiswa';
            }
            if ($hasLecturer || (! $hasStudent && $domain === 'lecturer.undip.ac.id')) {
                $names[] = 'dosen';
            }
            foreach ($names as $name) {
                $roles = $this->find('roles', ['name' => $name, 'module' => 'global', 'guard_name' => 'web']);
                if (count($roles) !== 1) {
                    throw new RuntimeException('Expected one existing global academic role: '.$name);
                }
                $this->operations['role:'.$user['id'].':'.$name] = ['table' => 'model_has_roles', 'source_id' => 'new-user-'.$sourceId.'-'.$name, 'row' => ['role_id' => (string) $roles[0]['id'], 'model_type' => 'App\\Models\\User', 'model_id' => $user['id']]];
            }
        }
    }

    private function businessKeys(string $table): array
    {
        if (preg_match('/^capstone_(assessment|bimbingan_sempro|bimbingan_ta|expo|milestone|nilai_dosen|sempro|sidang_ta)_scores$/', $table)) {
            $actor = in_array($table, ['capstone_sempro_scores', 'capstone_sidang_ta_scores'], true) ? 'examiner_id' : 'evaluator_id';

            return [['period_component_id', 'component_id', $actor, 'student_id', 'group_id']];
        }

        return self::KEYS[$table] ?? [];
    }

    private function planCapstoneRow(string $table, string $id, array $row): bool
    {
        if (! isset($this->columns[$table])) {
            $this->conflict($table, $id, 'Table does not exist in destination.');

            return true;
        }
        $keys = $this->businessKeys($table);
        foreach ($keys as $key) {
            $criteria = [];
            foreach ($key as $column) {
                if (! array_key_exists($column, $row) || ! isset($this->columns[$table][$column])) {
                    continue 2;
                }
                $value = $row[$column];
                $reference = $this->foreign[$table][$column] ?? null;
                if ($reference !== null && $value !== null) {
                    $value = $this->mapping[$reference][$value] ?? null;
                    if ($value === null) {
                        return false;
                    }
                }
                $criteria[$column] = $value;
            }
            if ($table === 'capstone_groups' && empty($row['code'])) {
                // Legacy groups without a code are recognized by their full mapped roster.
                $studentIds = [];
                foreach ($this->source['capstone_group_members'] ?? [] as $member) {
                    if ($member['group_id'] == $id && $member['deleted_at'] === null) {
                        $student = $this->mapping['students'][$member['student_id']] ?? null;
                        if ($student === null) {
                            return false;
                        }
                        $studentIds[] = $student;
                    }
                }
                if ($studentIds === []) {
                    return false;
                }
                sort($studentIds);
                $matches = [];
                foreach ($this->rows['capstone_groups'] ?? [] as $group) {
                    if ($group['period_id'] != $criteria['period_id']) {
                        continue;
                    }
                    $roster = array_column($this->find('capstone_group_members', ['group_id' => $group['id'], 'deleted_at' => null]), 'student_id');
                    sort($roster);
                    if ($roster === $studentIds) {
                        $matches[] = $group;
                    }
                }
                if (count($matches) === 1) {
                    $this->mapping[$table][$id] = (string) $matches[0]['id'];
                    return true;
                }
                if ($matches !== []) {
                    $this->conflict($table, $id, 'Legacy group roster matches multiple destination groups.');
                    return true;
                }
                foreach ($studentIds as $studentId) {
                    if ($this->find('capstone_group_members', ['student_id' => $studentId, 'period_id' => $criteria['period_id']]) !== []) {
                        $this->conflict($table, $id, 'Legacy group roster conflicts with existing memberships.');
                        return true;
                    }
                }
                $this->add($table, $id, $row);
                return true;
            }
            if (in_array($table, ['capstone_assessment_component_templates', 'capstone_peer_review_indicator_templates'], true) && empty($row['code'])) {
                return false;
            }
            foreach (['related', 'target'] as $prefix) {
                if (isset($criteria[$prefix.'_id']) && ! $this->mapPolymorphic($criteria, $prefix)) {
                    return false;
                }
            }
            $matches = $this->find($table, $criteria);
            if (count($matches) > 1) {
                $this->conflict($table, $id, 'Business key is ambiguous in destination.');

                return true;
            }
            if (count($matches) === 1) {
                $target = $matches[0];
                if ($table === 'capstone_group_members') {
                    $group = $this->mapping['capstone_groups'][$row['group_id']] ?? null;
                    if ($group === null) {
                        return false;
                    }
                    if ($target['group_id'] != $group || ($target['deleted_at'] !== null && $row['deleted_at'] === null)) {
                        $this->conflict($table, $id, 'Different group or removed membership in this period.');

                        return true;
                    }
                }
                $this->mapping[$table][$id] = (string) $target['id'];

                return true;
            }
            if ($table === 'capstone_group_members' && in_array($row['is_leader'], ['t', 'true', '1', true], true)) {
                $groupId = $this->mapping['capstone_groups'][$row['group_id']] ?? null;
                if ($groupId === null) {
                    return false;
                }
                if ($this->find($table, ['group_id' => $groupId, 'is_leader' => 't']) !== []) {
                    $this->conflict($table, $id, 'Destination group already has a different leader.');
                    return true;
                }
            }
            $this->add($table, $id, $row);

            return true;
        }

        return false;
    }

    private function completeOperations(): void
    {
        // A missing dependency invalidates its dependent insert, including through cycles.
        do {
            $removed = false;
            foreach ($this->operations as $key => $operation) {
                $table = $operation['table'];
                if (! str_starts_with($table, 'capstone_')) {
                    continue;
                }
                $row = $this->source[$table][$operation['source_id']];
                $mapped = $this->remap($table, $row);
                if ($mapped === null) {
                    unset($this->mapping[$table][$operation['source_id']], $this->rows[$table][$operation['row']['id']], $this->operations[$key]);
                    $this->conflict($table, $operation['source_id'], $this->lastIssue);
                    $removed = true;
                } else {
                    $mapped['id'] = $operation['row']['id'];
                    $this->operations[$key]['row'] = $mapped;
                    $this->rows[$table][$mapped['id']] = $mapped;
                }
            }
        } while ($removed);
        foreach ($this->mapping as $table => $mapping) {
            if (! str_starts_with($table, 'capstone_')) {
                continue;
            }
            foreach ($mapping as $sourceId => $targetId) {
                if (! isset($this->original[$table][$targetId])) {
                    continue;
                }
                $mapped = $this->remap($table, $this->source[$table][$sourceId]);
                if ($mapped === null) {
                    continue;
                }
                $changed = [];
                foreach ($mapped as $column => $value) {
                    if ($column !== 'id' && ! $this->equal($table, $column, $value, $this->original[$table][$targetId][$column])) {
                        $changed[] = $column;
                    }
                }
                if ($changed !== []) {
                    $this->differences[] = ['table' => $table, 'source_id' => (string) $sourceId, 'target_id' => (string) $targetId, 'columns' => $changed];
                }
            }
        }
    }

    private function remap(string $table, array $row): ?array
    {
        $this->lastIssue = 'Conflicting group membership or unsupported structured reference.';
        foreach ($this->foreign[$table] ?? [] as $column => $reference) {
            if (! isset($row[$column])) {
                continue;
            }
            $sourceId = $row[$column];
            $row[$column] = $this->mapping[$reference][$sourceId] ?? null;
            if ($row[$column] === null) {
                $this->lastIssue = 'Unresolved reference: '.$column.'='.$reference.':'.$sourceId;
                return null;
            }
        }
        foreach (['related', 'target'] as $prefix) {
            if (! $this->mapPolymorphic($row, $prefix)) {
                return null;
            }
        }
        foreach (['payload', 'old_values', 'new_values'] as $column) {
            if (isset($row[$column])) {
                $value = json_decode($row[$column], true, 512, JSON_THROW_ON_ERROR);
                $valid = true;
                $value = $this->remapJson($value, $valid);
                if (! $valid) {
                    return null;
                }
                $row[$column] = json_encode($value, JSON_THROW_ON_ERROR);
            }
        }
        if (! $this->membershipCompatible($table, $row)) {
            return null;
        }
        foreach ($row as $column => $value) {
            if (! isset($this->columns[$table][$column])) {
                if ($value !== null) {
                    $this->lastIssue = 'Destination column is missing: '.$column;
                    return null;
                }
                unset($row[$column]);
            }
        }

        return $row;
    }

    private function membershipCompatible(string $table, array $row): bool
    {
        if ($table === 'capstone_group_members' || ! isset($row['group_id'])) {
            return true;
        }
        $group = $this->rows['capstone_groups'][$row['group_id']] ?? null;
        if ($group === null) {
            return false;
        }
        foreach (['student_id', 'reviewer_id', 'reviewee_id', 'requester_id'] as $column) {
            if (! isset($row[$column]) || ($this->foreign[$table][$column] ?? null) !== 'students') {
                continue;
            }
            $memberships = $this->find('capstone_group_members', ['student_id' => $row[$column], 'period_id' => $group['period_id']]);
            foreach ($memberships as $membership) {
                if ($membership['deleted_at'] === null && $membership['group_id'] != $row['group_id']) {
                    return false;
                }
            }
            if (! in_array($table, ['capstone_group_invitations', 'capstone_join_requests'], true)
                && $this->find('capstone_group_members', ['student_id' => $row[$column], 'group_id' => $row['group_id']]) === []) {
                return false;
            }
        }

        return true;
    }

    private function mapPolymorphic(array &$row, string $prefix): bool
    {
        if (! isset($row[$prefix.'_id'])) {
            return true;
        }
        $type = class_basename($row[$prefix.'_type'] ?? '');
        $table = self::POLYMORPHIC[$type] ?? null;
        $id = $table === null ? null : ($this->mapping[$table][$row[$prefix.'_id']] ?? null);
        if ($id === null) {
            return false;
        }
        $row[$prefix.'_id'] = $id;

        return true;
    }

    private function remapJson(mixed $value, bool &$valid): mixed
    {
        if (! is_array($value)) {
            return $value;
        }
        $references = ['user_id' => 'users', 'student_id' => 'students', 'lecturer_id' => 'lecturers', 'supervisor_1_id' => 'lecturers', 'supervisor_2_id' => 'lecturers', 'group_id' => 'capstone_groups', 'period_id' => 'capstone_periods', 'title_id' => 'capstone_titles', 'event_id' => 'capstone_expo_events'];
        foreach ($value as $key => &$item) {
            if (in_array($key, ['final_group_ids', 'pdc1_group_ids'], true) && is_array($item)) {
                foreach ($item as &$id) {
                    $id = $this->mapping['capstone_groups'][$id] ?? null;
                    $valid = $valid && $id !== null;
                }
                unset($id);
            } elseif (is_string($key) && preg_match('/^\d+_\d+$/', $key)) {
                $valid = false;
            } elseif (isset($references[$key]) && $item !== null) {
                $item = $this->mapping[$references[$key]][$item] ?? null;
                $valid = $valid && $item !== null;
            } elseif (is_string($key) && (str_ends_with($key, '_id') || str_ends_with($key, '_ids')) && $item !== null) {
                // Unknown JSON IDs require an explicit semantic mapping, never numeric replacement.
                $valid = false;
            } else {
                $item = $this->remapJson($item, $valid);
            }
        }

        return $value;
    }

    private function add(string $table, string $sourceId, array $row): void
    {
        $id = (string) $this->nextIds[$table]++;
        $row['id'] = $id;
        $row = array_intersect_key($row, $this->columns[$table]);
        $this->mapping[$table][$sourceId] = $id;
        $virtual = $row;
        if (str_starts_with($table, 'capstone_')) {
            foreach ($this->foreign[$table] ?? [] as $column => $reference) {
                if (isset($virtual[$column])) {
                    $virtual[$column] = $this->mapping[$reference][$virtual[$column]] ?? null;
                }
            }
        }
        $this->rows[$table][$id] = $virtual;
        $this->operations[$table.':'.$sourceId] = ['table' => $table, 'source_id' => $sourceId, 'row' => $row];
    }

    private function conflict(string $table, string|int $id, string $reason): void
    {
        $this->conflicts[] = ['table' => $table, 'source_id' => (string) $id, 'reason' => $reason];
    }

    private function find(string $table, array $criteria): array
    {
        return array_values(array_filter($this->rows[$table] ?? [], function ($row) use ($table, $criteria) {
            foreach ($criteria as $column => $value) {
                if (! array_key_exists($column, $row) || ! $this->equal($table, $column, $value, $row[$column])) {
                    return false;
                }
            }

            return true;
        }));
    }

    private function equal(string $table, string $column, mixed $left, mixed $right): bool
    {
        if ($left === null || $right === null) {
            return $left === $right;
        }
        $type = $this->columns[$table][$column]['data_type'] ?? '';
        if ($type === 'boolean') {
            $bool = fn ($value) => in_array($value, [true, 1, '1', 't', 'true'], true);

            return $bool($left) === $bool($right);
        }
        if (in_array($type, ['json', 'jsonb'], true)) {
            return json_decode($left, true) == json_decode($right, true);
        }
        if (in_array($type, ['numeric', 'double precision', 'real'], true)) {
            return (float) $left === (float) $right;
        }
        if (in_array($column, ['name', 'email', 'title', 'nama', 'organization'], true)) {
            return $this->normalize($left) === $this->normalize($right);
        }

        return (string) $left === (string) $right;
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($value)));
    }

    public static function quote(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
}
