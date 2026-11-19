<?php

// Usage: php script/apply-eo-schema.php [--apply]. Default is a rollback-only test.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
$plan = json_decode(file_get_contents(__DIR__.'/eo-schema-comparison.json'), true, 512, JSON_THROW_ON_ERROR);
$operations = $plan['operations'];
$quote = fn ($s) => '"'.str_replace('"', '""', $s).'"';
$literal = fn ($s) => "'".str_replace("'", "''", $s)."'";
$fingerprint = fn () => $db->selectOne(<<<'SQL'
SELECT md5(string_agg(item, E'\n' ORDER BY item)) AS fingerprint FROM (
SELECT 'column:'||c.table_name||':'||c.column_name||':'||c.data_type||':'||coalesce(c.column_default,'')||':'||c.is_nullable||':'||coalesce(c.character_maximum_length::text,'')||':'||coalesce(c.numeric_precision::text,'')||':'||coalesce(c.numeric_scale::text,'') AS item FROM information_schema.columns c WHERE c.table_schema='public' AND left(c.table_name,3)<>'eo_'
UNION ALL SELECT 'constraint:'||t.relname||':'||c.conname||':'||pg_get_constraintdef(c.oid) FROM pg_constraint c JOIN pg_class t ON t.oid=c.conrelid JOIN pg_namespace n ON n.oid=t.relnamespace WHERE n.nspname='public' AND left(t.relname,3)<>'eo_'
UNION ALL SELECT 'index:'||tablename||':'||indexdef FROM pg_indexes WHERE schemaname='public' AND left(tablename,3)<>'eo_'
) s
SQL)->fingerprint;
$columns = $db->select("SELECT table_name,column_name FROM information_schema.columns WHERE table_schema='public' AND left(table_name,3)='eo_' ORDER BY table_name,ordinal_position");
$groups = [];
foreach ($columns as $column) {
    $groups[$column->table_name][] = $column->column_name;
}
$hashSql = [];
foreach ($groups as $table => $names) {
    // Compare the textual value of every original column, including NULLs.
    // This tolerates the intended numeric-ID-to-text widening without hiding data loss.
    $values = implode(', ', array_map(fn ($name) => $quote($name).'::text', $names));
    $hashSql[] = 'SELECT '.$literal($table).' AS table_name, count(*) AS rows, md5(string_agg(md5(jsonb_build_array('.$values.')::text), \'\' ORDER BY md5(jsonb_build_array('.$values.')::text))) AS hash FROM public.'.$quote($table);
}
$hashSql = implode(' UNION ALL ', $hashSql);
$result = ['before_non_eo'=>$fingerprint(), 'applied'=>[], 'not_valid'=>[], 'blocked'=>[]];
$snapshotPath = __DIR__.'/eo-schema-before-apply.json';
if (! file_exists($snapshotPath)) {
    file_put_contents($snapshotPath, json_encode(['columns'=>$columns, 'constraints'=>$db->select("SELECT t.relname, c.conname, c.convalidated, pg_get_constraintdef(c.oid) AS definition FROM pg_constraint c JOIN pg_class t ON t.oid=c.conrelid JOIN pg_namespace n ON n.oid=t.relnamespace WHERE n.nspname='public' AND left(t.relname,3)='eo_'")], JSON_PRETTY_PRINT));
}
$db->beginTransaction();
try {
    $db->statement("SET LOCAL lock_timeout='5s'");
    $db->statement("SET LOCAL statement_timeout='90s'");
    $db->statement('LOCK TABLE '.implode(', ', array_map(fn ($table) => 'public.'.$quote($table), array_keys($groups))).' IN SHARE ROW EXCLUSIVE MODE');
    $before = $db->select($hashSql);
    $db->statement('CREATE TEMP TABLE eo_schema_results (operation_index integer, state text, error text) ON COMMIT DROP');
    $blocks = [];
    foreach ($operations as $index => $operation) {
        if (! str_starts_with($operation['table'], 'eo_') || ! preg_match('/^(ALTER|CREATE) TABLE public\."'.preg_quote($operation['table'], '/').'" /', $operation['sql']) || str_contains($operation['sql'], ';')) {
            throw new RuntimeException('Refusing operation outside allowed eo_ scope');
        }
        $sql = $literal($operation['sql']);
        $fallback = str_contains($operation['sql'], ' FOREIGN KEY ') || str_contains($operation['sql'], ' CHECK ');
        $handler = $fallback
            ? "IF SQLSTATE IN ('23503','23514') THEN EXECUTE ".$literal($operation['sql'].' NOT VALID')."; INSERT INTO pg_temp.eo_schema_results VALUES ($index, 'not_valid', SQLERRM); ELSE INSERT INTO pg_temp.eo_schema_results VALUES ($index, SQLSTATE, SQLERRM); END IF;"
            : "INSERT INTO pg_temp.eo_schema_results VALUES ($index, SQLSTATE, SQLERRM);";
        $blocks[] = "BEGIN EXECUTE $sql; INSERT INTO pg_temp.eo_schema_results VALUES ($index, 'applied', NULL); EXCEPTION WHEN OTHERS THEN $handler END;";
    }
    // Each PL/pgSQL exception block rolls back its failed statement before fallback.
    $db->unprepared('DO $eo_schema_apply$ BEGIN '.implode("\n", $blocks).' END $eo_schema_apply$');
    $fatal = false;
    foreach ($db->select('SELECT * FROM pg_temp.eo_schema_results ORDER BY operation_index') as $row) {
        $operation = $operations[$row->operation_index];
        $key = in_array($row->state, ['applied', 'not_valid'], true) ? $row->state : 'blocked';
        $result[$key][] = $operation + ['state'=>$row->state, 'error'=>$row->error];
        if ($key === 'blocked' && ($operation['kind'] !== 'constraint' || ! in_array($row->state, ['23505', '23502', '42830'], true))) {
            $fatal = true;
        }
    }
    $after = $db->select($hashSql);
    $result['after_non_eo'] = $fingerprint();
    $result['original_values_unchanged'] = $before == $after;
    $result['non_eo_unchanged'] = $result['before_non_eo'] === $result['after_non_eo'];
    $result['row_fingerprints'] = $after;
    if (! $result['original_values_unchanged'] || ! $result['non_eo_unchanged']) {
        throw new RuntimeException('Verification failed: rolling back all changes');
    }
    $result['committed'] = ! $fatal && in_array('--apply', $argv, true);
    if ($result['committed']) {
        $db->commit();
    } else {
        $db->rollBack();
    }
} catch (Throwable $error) {
    $db->rollBack();
    throw $error;
}
$output = __DIR__.($result['committed'] ? '/eo-schema-apply-result.json' : '/eo-schema-preflight.json');
file_put_contents($output, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode(['applied'=>count($result['applied']), 'not_valid'=>count($result['not_valid']), 'blocked'=>count($result['blocked']), 'original_values_unchanged'=>$result['original_values_unchanged'], 'non_eo_unchanged'=>$result['non_eo_unchanged'], 'committed'=>$result['committed']], JSON_PRETTY_PRINT).PHP_EOL;
