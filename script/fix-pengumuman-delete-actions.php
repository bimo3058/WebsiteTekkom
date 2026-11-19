<?php

require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
$migration = require dirname(__DIR__).'/Modules/ManajemenMahasiswa/database/migrations/2026_11_11_000001_restore_pengumuman_delete_actions.php';
// Apply directly so the non-mk_ migrations table is not modified.
$migration->up();
$tables = ['mk_pengumuman', 'mk_repo_mulmed', 'mk_pengumuman_attachments', 'mk_pengumuman_personal_pins', 'mk_pengumuman_approval_requests'];
$snapshot = function () use ($db, $tables) {
    return $db->select(implode(' UNION ALL ', array_map(fn ($table) => "SELECT '$table' AS table_name, count(*) AS rows, md5(string_agg(md5(to_jsonb(t)::text), '' ORDER BY md5(to_jsonb(t)::text))) AS hash FROM public.\"$table\" t", $tables)));
};
$before = $snapshot();
$db->beginTransaction();
try {
    $db->statement("SET LOCAL lock_timeout = '5s'");
    $ids = $db->table('mk_repo_mulmed')->where('pengumuman_id', 38)->pluck('id')->all();
    $deleted = $db->table('mk_pengumuman')->where('id', 38)->delete();
    if ($deleted !== 1 || $db->table('mk_repo_mulmed')->whereIn('id', $ids)->whereNull('pengumuman_id')->count() !== count($ids)) {
        throw new RuntimeException('Delete behavior verification failed');
    }
    foreach (array_slice($tables, 2) as $table) {
        if ($db->table($table)->where('pengumuman_id', 38)->exists()) {
            throw new RuntimeException('Cascade verification failed: '.$table);
        }
    }
} finally {
    $db->rollBack();
}
if ($before != $snapshot()) {
    throw new RuntimeException('Rollback verification failed');
}
echo 'Delete ID 38 succeeded in rollback-only test; media preserved; all original rows restored.'.PHP_EOL;
