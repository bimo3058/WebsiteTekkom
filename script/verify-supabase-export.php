<?php

$dir = dirname(__DIR__).'/storage/app/private/supabase-export/'.($argv[1] ?? 'anihtwnhijxcnhaarsmm');
$manifest = json_decode(file_get_contents($dir.'/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
$invalid = [];
if (!hash_equals($manifest['database_sha256'], hash_file('sha256', $dir.'/database.dump'))) {
    $invalid[] = 'database.dump';
}
$buckets = [];
foreach ($manifest['files'] as $file) {
    $path = $dir.'/files/'.$file['object'];
    if (!is_file($path) || filesize($path) !== $file['bytes'] || !hash_equals($file['sha256'], hash_file('sha256', $path))) {
        $invalid[] = $file['object'];
    }
    $bucket = explode('/', $file['object'], 2)[0];
    $buckets[$bucket]['downloaded'] = ($buckets[$bucket]['downloaded'] ?? 0) + 1;
}
foreach ($manifest['failures'] as $failure) {
    $bucket = explode('/', $failure['object'], 2)[0];
    $buckets[$bucket]['failed'] = ($buckets[$bucket]['failed'] ?? 0) + 1;
}
$report = ['expected'=>$manifest['expected_objects'], 'downloaded'=>$manifest['downloaded_objects'], 'bytes'=>$manifest['downloaded_bytes'], 'failures'=>count($manifest['failures']), 'checksum_errors'=>$invalid, 'buckets'=>$buckets];
file_put_contents($dir.'/verification.json', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
echo json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
exit($invalid || $manifest['failures'] ? 1 : 0);
