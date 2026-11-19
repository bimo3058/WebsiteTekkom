<?php

use App\Services\CapstoneDumpImporter;
use App\Services\PostgresCopyArchive;
use Illuminate\Support\Facades\DB;

require dirname(__DIR__).'/vendor/autoload.php';

$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.query_cache.enabled' => false]);

$options = getopt('', ['dump:', 'dry-run', 'apply', 'plan:', 'output:', 'pg-bin:', 'validate']);
$dump = realpath($options['dump'] ?? dirname(__DIR__).'/backup_DBProd.dump');
$pgBin = $options['pg-bin'] ?? 'C:/Program Files/PostgreSQL/17/bin';
$apply = isset($options['apply']);
$validate = isset($options['validate']);
$output = $options['output'] ?? __DIR__.'/capstone-import-plan.json';
$writeJson = function (string $path, array $value): void {
    $temporary = $path.'.tmp';
    if (file_put_contents($temporary, json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) === false || ! rename($temporary, $path)) {
        throw new RuntimeException('Cannot write report: '.$path);
    }
};

$checkFiles = function (PDO $db, array $source, array $mapping): array {
    $paths = [];
    foreach ($source as $table => $rows) {
        if (! str_starts_with($table, 'capstone_')) {
            continue;
        }
        foreach ($rows as $id => $row) {
            if (! isset($mapping[$table][$id])) {
                continue;
            }
            foreach (['file_path', 'draft_report_path', 'paper_path'] as $column) {
                if (! empty($row[$column])) {
                    $paths[$row[$column]] = ['path' => $row[$column], 'local_file_exists' => false, 'supabase_metadata_exists' => false];
                }
            }
        }
    }
    $bucket = config('services.supabase.bucket');
    $objects = [];
    $catalog = $db->query("SELECT to_regclass('storage.objects')")->fetchColumn();
    if ($catalog && $paths !== []) {
        $statement = $db->prepare('SELECT name FROM storage.objects WHERE bucket_id=? AND name IN ('.implode(', ', array_fill(0, count($paths), '?')).')');
        $statement->execute([$bucket, ...array_keys($paths)]);
        $objects = array_fill_keys($statement->fetchAll(PDO::FETCH_COLUMN), true);
    }
    foreach ($paths as $path => &$check) {
        $local = realpath(storage_path('app/public/'.$path));
        $root = realpath(storage_path('app/public'));
        $check['local_file_exists'] = $root !== false && $local !== false && str_starts_with($local, $root.DIRECTORY_SEPARATOR) && is_file($local);
        $check['supabase_metadata_exists'] = $catalog ? isset($objects[$path]) : null;
    }

    return array_values($paths);
};

try {
    if (! $dump || ! is_file($dump)) {
        throw new RuntimeException('Dump file not found.');
    }
    if ($apply && (! isset($options['plan']) || isset($options['dry-run']))) {
        throw new RuntimeException('--apply requires --plan and cannot be combined with --dry-run.');
    }
    $connection = DB::connection();
    if ($connection->getDriverName() !== 'pgsql') {
        throw new RuntimeException('The importer requires PostgreSQL.');
    }
    $db = $connection->getPdo();
    $db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
    $source = PostgresCopyArchive::read($dump, $pgBin.'/pg_restore.exe');
    $checksum = hash_file('sha256', $dump);
    $target = hash('sha256', json_encode(array_intersect_key($connection->getConfig(), array_flip(['host', 'port', 'database', 'username']))));
    $approved = $apply ? json_decode(file_get_contents($options['plan']), true, 512, JSON_THROW_ON_ERROR) : null;
    $timestamp = $approved['generated_at'] ?? gmdate('Y-m-d H:i:s');

    $connection->beginTransaction();
    $db->exec("SET LOCAL lock_timeout='10s'");
    $db->exec("SET LOCAL statement_timeout='180s'");
    if ($apply || $validate) {
        $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
        $tables = array_values(array_filter($tables, fn ($table) => PostgresCopyArchive::allowed($table) || in_array($table, ['model_has_permissions', 'role_has_permissions'], true)));
        $db->exec('LOCK TABLE '.implode(', ', array_map(fn ($table) => 'public.'.CapstoneDumpImporter::quote($table), $tables)).' IN SHARE ROW EXCLUSIVE MODE');
    } else {
        $db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
    }
    $importer = new CapstoneDumpImporter($db, $source, $timestamp);
    $plan = $importer->plan();
    $fileChecks = $checkFiles($db, $source, $plan['mapping']);
    $plan['dump_sha256'] = $checksum;
    $plan['target_identity'] = $target;
    $plan['implementation_sha256'] = hash('sha256', file_get_contents(__FILE__).file_get_contents(dirname(__DIR__).'/app/Services/CapstoneDumpImporter.php').file_get_contents(dirname(__DIR__).'/app/Services/PostgresCopyArchive.php'));
    if ($approved !== null) {
        // Concurrent activity such as last_seen_at may change a full snapshot.
        // Recheck the actual inserts, identities and conflicts under the write locks.
        $changed = [];
        foreach (['version', 'dump_sha256', 'target_identity', 'implementation_sha256', 'operations', 'mapping', 'conflicts', 'source_counts', 'new_user_role_review'] as $key) {
            if (($approved[$key] ?? null) !== ($plan[$key] ?? null)) {
                $changed[] = $key;
            }
        }
        if ($changed !== []) {
            throw new RuntimeException('Import plan changed in '.implode(', ', $changed).'. Generate a fresh dry-run plan.');
        }
    }
    if (! $apply && ! $validate) {
        $connection->rollBack();
        $writeJson($output, $plan);
        $writeJson($output.'.files.json', ['bucket' => config('services.supabase.bucket'), 'checks' => $fileChecks]);
        echo json_encode(['mode' => 'dry-run', 'report' => $output, 'insert_counts' => $plan['insert_counts'], 'conflicts' => count($plan['conflicts']), 'existing_differences' => count($plan['existing_capstone_differences'])], JSON_PRETTY_PRINT).PHP_EOL;
        exit(0);
    }

    // Keep a complete, transaction-consistent public schema/data snapshot before writing.
    $snapshot = $db->query('SELECT pg_export_snapshot()')->fetchColumn();
    $backup = __DIR__.'/capstone-before-import-'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(3)).'.dump';
    $config = $connection->getConfig();
    $command = [$pgBin.'/pg_dump.exe', '--format=custom', '--schema=public', '--no-owner', '--no-privileges', '--snapshot='.$snapshot, '--file='.$backup, '--host='.$config['host'], '--port='.$config['port'], '--username='.$config['username'], '--dbname='.$config['database']];
    $environment = getenv();
    $environment['PGPASSWORD'] = $config['password'];
    $environment['PGSSLMODE'] = $config['sslmode'] ?? 'require';
    $process = proc_open($command, [1 => ['file', 'php://stdout', 'w'], 2 => ['file', 'php://stderr', 'w']], $pipes, null, $environment);
    if (! is_resource($process) || proc_close($process) !== 0 || ! is_file($backup) || filesize($backup) === 0) {
        throw new RuntimeException('Database backup failed.');
    }
    $process = proc_open([$pgBin.'/pg_restore.exe', '--list', $backup], [1 => ['file', 'NUL', 'w'], 2 => ['file', 'php://stderr', 'w']], $pipes);
    if (! is_resource($process) || proc_close($process) !== 0) {
        throw new RuntimeException('Backup archive verification failed.');
    }

    $tables = $importer->tables();
    $before = [];
    foreach ($tables as $table) {
        $before[$table] = $db->query('SELECT row_to_json(t)::text AS data FROM public.'.CapstoneDumpImporter::quote($table).' t ORDER BY row_to_json(t)::text')->fetchAll(PDO::FETCH_COLUMN);
    }
    $inserted = [];
    $pending = $plan['operations'];
    $deferred = [];
    $references = [];
    foreach ($db->query(<<<'SQL'
SELECT t.relname AS table_name, a.attname AS column_name, rt.relname AS referenced_table, ia.is_nullable
FROM pg_constraint c JOIN pg_class t ON t.oid=c.conrelid
JOIN pg_namespace n ON n.oid=t.relnamespace JOIN pg_class rt ON rt.oid=c.confrelid
JOIN pg_attribute a ON a.attrelid=t.oid AND a.attnum=c.conkey[1]
JOIN information_schema.columns ia ON ia.table_schema='public' AND ia.table_name=t.relname AND ia.column_name=a.attname
WHERE n.nspname='public' AND c.contype='f' AND cardinality(c.conkey)=1
SQL)->fetchAll(PDO::FETCH_ASSOC) as $reference) {
        $references[$reference['table_name']][$reference['column_name']] = $reference;
    }
    $available = [];
    foreach ($tables as $table) {
        $hasId = $db->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema='public' AND table_name=? AND column_name='id'");
        $hasId->execute([$table]);
        if ($hasId->fetchColumn()) {
            $available[$table] = array_fill_keys($db->query('SELECT id FROM public.'.CapstoneDumpImporter::quote($table))->fetchAll(PDO::FETCH_COLUMN), true);
        }
    }
    while ($pending !== []) {
        $progress = false;
        foreach ($pending as $index => $operation) {
            $table = $operation['table'];
            $row = $operation['row'];
            if (! str_starts_with($table, 'capstone_') && ! in_array($table, ['users', 'students', 'lecturers', 'model_has_roles'], true)) {
                throw new RuntimeException('Operation outside allowed import tables.');
            }
            foreach ($references[$table] ?? [] as $column => $reference) {
                if (isset($row[$column]) && ! isset($available[$reference['referenced_table']][$row[$column]])) {
                    if ($reference['is_nullable'] !== 'YES') {
                        continue 2;
                    }
                    $deferred[$table.':'.$row['id']][$column] = $row[$column];
                    $row[$column] = null;
                }
            }
            $columns = array_keys($row);
            $sql = 'INSERT INTO public.'.CapstoneDumpImporter::quote($table).' ('.implode(', ', array_map(CapstoneDumpImporter::quote(...), $columns)).') VALUES ('.implode(', ', array_fill(0, count($columns), '?')).')';
            $db->prepare($sql)->execute(array_values($row));
            if (isset($row['id'])) {
                $available[$table][$row['id']] = true;
            }
            $inserted[] = ['table' => $table, 'source_id' => $operation['source_id'], 'target_id' => $row['id'] ?? null];
            unset($pending[$index]);
            $progress = true;
        }
        if (! $progress) {
            throw new RuntimeException('Unresolvable insert dependency cycle.');
        }
    }
    foreach ($deferred as $key => $values) {
        [$table, $id] = explode(':', $key);
        $set = implode(', ', array_map(fn ($column) => CapstoneDumpImporter::quote($column).'=?', array_keys($values)));
        $db->prepare('UPDATE public.'.CapstoneDumpImporter::quote($table).' SET '.$set.' WHERE id=?')->execute([...array_values($values), $id]);
    }
    foreach ($inserted as $record) {
        if ($record['table'] === 'users') {
            $user = App\Models\User::findOrFail($record['target_id']);
            $user->syncPermissionsFromRoles();
        }
    }
    $db->exec('SET CONSTRAINTS ALL IMMEDIATE');
    foreach ($before as $table => $original) {
        $after = $db->query('SELECT row_to_json(t)::text AS data FROM public.'.CapstoneDumpImporter::quote($table).' t ORDER BY row_to_json(t)::text')->fetchAll(PDO::FETCH_COLUMN);
        $remaining = array_count_values($after);
        foreach ($original as $row) {
            if (($remaining[$row] ?? 0) < 1) {
                throw new RuntimeException('Original row was modified in '.$table);
            }
            $remaining[$row]--;
        }
    }
    // Check idempotency against the prospective committed state before committing.
    $repeat = (new CapstoneDumpImporter($db, $source, $timestamp))->plan();
    if ($repeat['operations'] !== []) {
        throw new RuntimeException('Idempotency verification failed: '.json_encode($repeat['insert_counts']));
    }
    if ($apply) {
        foreach (array_unique(array_column($inserted, 'table')) as $table) {
            if ($table === 'model_has_roles') {
                continue;
            }
            $statement = $db->prepare("SELECT pg_get_serial_sequence(?, 'id')");
            $statement->execute(['public.'.$table]);
            $sequence = $statement->fetchColumn();
            if ($sequence) {
                // Advance only; never rewind an already higher sequence.
                $state = $db->query('SELECT last_value, is_called FROM '.$sequence)->fetch(PDO::FETCH_ASSOC);
                $maximum = (int) $db->query('SELECT max(id) FROM public.'.CapstoneDumpImporter::quote($table))->fetchColumn();
                if ($maximum >= (int) $state['last_value']) {
                    $db->prepare('SELECT setval(?::regclass, ?, true)')->execute([$sequence, $maximum]);
                }
            }
        }
        $connection->commit();
    } else {
        $connection->rollBack();
    }
    $result = ['committed' => $apply, 'backup' => $backup, 'backup_sha256' => hash_file('sha256', $backup), 'dump_sha256' => $checksum, 'target_identity' => $target, 'insert_counts' => $plan['insert_counts'], 'inserted' => $inserted, 'original_rows_verified' => true, 'idempotency_verified' => true, 'conflicts' => $plan['conflicts'], 'existing_capstone_differences' => $plan['existing_capstone_differences'], 'new_user_role_review' => $plan['new_user_role_review'], 'file_checks' => $fileChecks, 'mapping' => $plan['mapping']];
    $output = $options['output'] ?? __DIR__.($apply ? '/capstone-import-result.json' : '/capstone-import-validation.json');
    $writeJson($output, $result);
    echo json_encode(['committed' => $apply, 'report' => $output, 'backup' => $backup, 'insert_counts' => $plan['insert_counts'], 'original_rows_verified' => true, 'idempotency_verified' => true], JSON_PRETTY_PRINT).PHP_EOL;
} catch (Throwable $error) {
    if (isset($connection) && $connection->transactionLevel() > 0) {
        $connection->rollBack();
    }
    fwrite(STDERR, $error->getMessage().PHP_EOL);
    exit(1);
}
