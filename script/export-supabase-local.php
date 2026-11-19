<?php

// Run: php script/export-supabase-local.php
// Credentials: storage/app/private/supabase-export/source.env (never committed).
require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Process\Process;

$root = dirname(__DIR__);
$destination = $root.'/storage/app/private/supabase-export';
$app = Dotenv\Dotenv::createArrayBacked($root)->load();
$source = Dotenv\Dotenv::createArrayBacked($destination, 'source.env')->load();
$project = substr($source['DB_USERNAME'], strlen('postgres.'));
if (parse_url($app['SUPABASE_URL'] ?? '', PHP_URL_HOST) !== $project.'.supabase.co') {
    throw new RuntimeException('Database and Storage must belong to the same project.');
}
$dir = $destination.'/'.$project;
@mkdir($dir, 0700, true);
$db = new PDO('pgsql:host='.$source['DB_HOST'].';port='.$source['DB_PORT'].';dbname='.$source['DB_DATABASE'].';sslmode=require', $source['DB_USERNAME'], $source['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 30]);
$buckets = $db->query('SELECT * FROM storage.buckets ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$objects = $db->query('SELECT * FROM storage.objects ORDER BY bucket_id, name')->fetchAll(PDO::FETCH_ASSOC);
file_put_contents($dir.'/buckets.json', json_encode($buckets, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
file_put_contents($dir.'/objects.json', json_encode($objects, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
echo 'Project: '.$project.'; buckets: '.count($buckets).'; objects: '.count($objects).PHP_EOL;

$bin = $app['PG_BIN'] ?? 'C:/Program Files/PostgreSQL/17/bin';
$pgEnv = ['PGHOST'=>$source['DB_HOST'], 'PGPORT'=>$source['DB_PORT'], 'PGDATABASE'=>$source['DB_DATABASE'], 'PGUSER'=>$source['DB_USERNAME'], 'PGPASSWORD'=>$source['DB_PASSWORD'], 'PGSSLMODE'=>'require', 'PGCONNECT_TIMEOUT'=>'30'];
$dump = new Process([$bin.'/pg_dump', '--format=custom', '--no-owner', '--no-acl', '--file='.$dir.'/database.dump.partial'], $root, $pgEnv, null, 1800);
if (!in_array('--storage-only', $argv, true)) {
$dump->run();
if (!$dump->isSuccessful()) {
    file_put_contents($dir.'/database-error.log', $dump->getErrorOutput());
    throw new RuntimeException('Database export failed. See private database-error.log.');
}
rename($dir.'/database.dump.partial', $dir.'/database.dump');
}
$check = new Process([$bin.'/pg_restore', '--list', $dir.'/database.dump']);
$check->mustRun();
file_put_contents($dir.'/database-contents.txt', $check->getOutput());
echo 'Database dump complete; archive readable.'.PHP_EOL;

$client = new GuzzleHttp\Client(['base_uri'=>rtrim($app['SUPABASE_URL'], '/').'/storage/v1/', 'timeout'=>180, 'connect_timeout'=>30, 'headers'=>['Authorization'=>'Bearer '.$app['SUPABASE_KEY'], 'apikey'=>$app['SUPABASE_KEY']]]);
$manifest = [];
$failures = [];
$seen = [];
foreach ($objects as $i => $object) {
    $relative = $object['bucket_id'].'/'.$object['name'];
    $parts = explode('/', $relative);
    // Reject ambiguous Windows paths instead of silently overwriting other objects.
    $invalid = false;
    foreach ($parts as $part) {
        if ($part === '' || $part === '.' || $part === '..' || preg_match('/[<>:"\\\\|?*\x00-\x1f]/', $part) || preg_match('/[. ]$/', $part) || preg_match('/^(CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])(\.|$)/i', $part)) {
            $invalid = true;
        }
    }
    if ($invalid || isset($seen[strtolower($relative)])) {
        $failures[] = ['object'=>$relative, 'reason'=>'Unsafe or colliding Windows filename'];
        continue;
    }
    $seen[strtolower($relative)] = true;
    $path = $dir.'/files/'.$relative;
    @mkdir(dirname($path), 0700, true);
    $metadata = json_decode($object['metadata'] ?? '{}', true);
    $success = false;
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        try {
            $client->get('object/authenticated/'.implode('/', array_map('rawurlencode', $parts)), ['sink'=>$path.'.partial']);
            clearstatcache(true, $path.'.partial');
            $bytes = filesize($path.'.partial');
            if (isset($metadata['size']) && $bytes !== (int) $metadata['size']) {
                throw new RuntimeException('Object size mismatch');
            }
            rename($path.'.partial', $path);
            $manifest[] = ['object'=>$relative, 'bytes'=>$bytes, 'sha256'=>hash_file('sha256', $path)];
            $success = true;
            break;
        } catch (Throwable $e) {
            $reason = get_class($e).' code '.$e->getCode();
            if ($e instanceof GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
                $reason = 'HTTP '.$e->getResponse()->getStatusCode().' '.(string) $e->getResponse()->getBody();
                if (in_array($e->getResponse()->getStatusCode(), [400, 401, 403, 404], true)) { break; }
            }
            if ($attempt < 3) { sleep($attempt); }
        }
    }
    if (!$success) {
        $failures[] = ['object'=>$relative, 'reason'=>$reason];
        if (is_file($path.'.partial')) { unlink($path.'.partial'); }
    }
    if (($i + 1) % 25 === 0 || $i + 1 === count($objects)) {
        echo 'Files processed: '.($i + 1).'/'.count($objects).'; failed: '.count($failures).PHP_EOL;
    }
}
$report = ['project'=>$project, 'exported_at'=>gmdate('c'), 'database_sha256'=>hash_file('sha256', $dir.'/database.dump'), 'buckets'=>count($buckets), 'expected_objects'=>count($objects), 'downloaded_objects'=>count($manifest), 'downloaded_bytes'=>array_sum(array_column($manifest, 'bytes')), 'files'=>$manifest, 'failures'=>$failures];
file_put_contents($dir.'/manifest.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
echo 'Export finished: '.count($manifest).' files; failures: '.count($failures).PHP_EOL;
exit($failures ? 1 : 0);
