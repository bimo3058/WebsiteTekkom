<?php

// Install only the outstanding mk_ constraints, without changing any row.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
$plan = json_decode(file_get_contents(__DIR__.'/mk-schema-comparison.json'), true, 512, JSON_THROW_ON_ERROR)['operations'];
$operations = [];
foreach ($plan as $operation) {
    if ($operation['kind'] !== 'constraint' || ! preg_match('/^ALTER TABLE public\."(mk_\w+)" ADD CONSTRAINT "(\w+)" (PRIMARY KEY \(\w+\)|FOREIGN KEY \(\w+\) REFERENCES public\.\w+\(\w+\))$/D', $operation['sql'], $match) || $match[1] !== $operation['table']) {
        throw new RuntimeException('Unexpected operation: refusing to execute');
    }
    $operations[] = $operation + ['name'=>$match[2]];
}
$fingerprint = fn () => $db->selectOne(<<<'SQL'
SELECT md5(string_agg(item, E'\n' ORDER BY item)) AS fingerprint FROM (
SELECT 'column:'||c.table_name||':'||c.column_name||':'||c.data_type||':'||coalesce(c.column_default,'')||':'||c.is_nullable AS item
FROM information_schema.columns c WHERE c.table_schema='public' AND left(c.table_name,3)<>'mk_'
UNION ALL SELECT 'constraint:'||t.relname||':'||c.conname||':'||pg_get_constraintdef(c.oid)
FROM pg_constraint c JOIN pg_class t ON t.oid=c.conrelid JOIN pg_namespace n ON n.oid=t.relnamespace WHERE n.nspname='public' AND left(t.relname,3)<>'mk_'
UNION ALL SELECT 'index:'||tablename||':'||indexdef FROM pg_indexes WHERE schemaname='public' AND left(tablename,3)<>'mk_'
) s
SQL)->fingerprint;
$tables = array_unique(array_column($operations, 'table'));
$dataSql = implode(' UNION ALL ', array_map(fn ($table) => "SELECT '$table' AS table_name, count(*) AS rows, md5(string_agg(md5(to_jsonb(t)::text), '' ORDER BY md5(to_jsonb(t)::text))) AS hash FROM public.\"$table\" t", $tables));
$result = ['before_non_mk'=>$fingerprint(), 'validated'=>[], 'not_valid'=>[], 'blocked'=>[], 'existing'=>[]];
$db->beginTransaction();
try {
    $db->statement("SET LOCAL lock_timeout='5s'");
    $db->statement("SET LOCAL statement_timeout='30s'");
    // Stabilize rows during comparison and prevent concurrent writes to these mk_ tables.
    if ($tables) {
        $db->statement('LOCK TABLE '.implode(', ', array_map(fn ($table) => 'public."'.$table.'"', $tables)).' IN SHARE ROW EXCLUSIVE MODE');
    }
    $beforeRows = $dataSql ? $db->select($dataSql) : [];
    foreach ($operations as $operation) {
        $exists = $db->selectOne('SELECT convalidated FROM pg_constraint WHERE conrelid=?::regclass AND conname=?', ['public.'.$operation['table'], $operation['name']]);
        if ($exists) {
            $result['existing'][] = $operation['name'];
            continue;
        }
        $db->statement('SAVEPOINT mk_constraint');
        try {
            $db->statement($operation['sql']);
            $result['validated'][] = $operation['name'];
        } catch (Illuminate\Database\QueryException $error) {
            $db->statement('ROLLBACK TO SAVEPOINT mk_constraint');
            if (($error->errorInfo[0] ?? '') === '23503' && str_contains($operation['sql'], ' FOREIGN KEY ')) {
                $db->statement($operation['sql'].' NOT VALID');
                $result['not_valid'][] = $operation['name'];
            } elseif (in_array($error->errorInfo[0] ?? '', ['23505', '42830'], true)) {
                $result['blocked'][] = ['table'=>$operation['table'], 'name'=>$operation['name'], 'error'=>$error->getMessage()];
            } else {
                throw $error;
            }
        }
        $db->statement('RELEASE SAVEPOINT mk_constraint');
    }
    $result['after_non_mk'] = $fingerprint();
    $afterRows = $dataSql ? $db->select($dataSql) : [];
    if ($result['before_non_mk'] !== $result['after_non_mk'] || $beforeRows != $afterRows) {
        throw new RuntimeException('Verification failed: rolling back all changes');
    }
    $result['rows_unchanged'] = true;
    $result['row_fingerprints'] = $afterRows;
    $db->commit();
} catch (Throwable $error) {
    $db->rollBack();
    throw $error;
}
file_put_contents(__DIR__.'/mk-constraint-install-result.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode(['validated'=>count($result['validated']), 'not_valid'=>count($result['not_valid']), 'blocked'=>count($result['blocked']), 'existing'=>count($result['existing']), 'rows_unchanged'=>$result['rows_unchanged'], 'non_mk_unchanged'=>$result['before_non_mk'] === $result['after_non_mk']], JSON_PRETTY_PRINT).PHP_EOL;
