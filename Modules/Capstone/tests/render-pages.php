<?php

// Read-only view verification on an isolated testing configuration.
$root = dirname(__DIR__, 3);
foreach (['APP_ENV'=>'testing','APP_CONFIG_CACHE'=>'bootstrap/cache/capstone-blade-testing.php','CACHE_STORE'=>'array','DB_CONNECTION'=>'sqlite','DB_DATABASE'=>':memory:','SESSION_DRIVER'=>'array','TELESCOPE_ENABLED'=>'false','PULSE_ENABLED'=>'false','NIGHTWATCH_ENABLED'=>'false'] as $key=>$value) {
    putenv($key.'='.$value); $_ENV[$key]=$value; $_SERVER[$key]=$value;
}
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$actor = ['id'=>1,'name'=>'Test Administrator','email'=>'test@example.test','roles'=>['admin','dosen'],'active_role'=>'admin','nim'=>null,'nip'=>'TEST-NIP'];
$failures=[];$count=0;
$directory=new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/../resources/views/pages'));
foreach ($directory as $file) {
    if (!$file->isFile() || !str_ends_with($file->getFilename(),'.blade.php') || !str_contains(file_get_contents($file->getPathname()),'@extends')) continue;
    $relative=str_replace('\\','/',substr($file->getPathname(),strlen(__DIR__.'/../resources/views/pages/')));
    $pagePath='/'.str_replace('.blade.php','',$relative);
    if (in_array('--dosen', $argv) && !str_starts_with($pagePath, '/dosen/')) continue;
    $role=str_starts_with($pagePath,'/dosen')?'dosen':(str_starts_with($pagePath,'/mahasiswa')?'mahasiswa':'admin');
    if (in_array('--verbose', $argv)) fwrite(STDERR, $relative.PHP_EOL);
    try {
        $state=['registered'=>true,'group_status'=>'PDC1_ACTIVE'];
        request()->attributes->set('capstone_feature_access', $role==='mahasiswa' ? $state : []);
        $html=view()->file($file->getPathname(),['actor'=>array_merge($actor,['roles'=>[$role],'active_role'=>$role]),'activeRole'=>$role,'featureAccess'=>$state,'lockedReason'=>'Available after PDC1 starts','pagePath'=>$pagePath,'pageParams'=>['id'=>1,'groupId'=>1,'phase'=>'PDC1','studentId'=>1,'evaluationType'=>'PDC1','evaluatorId'=>1,'scheduleId'=>1,'expoId'=>1]])->render();
        if (substr_count($html,'<!DOCTYPE html>') !== 1) throw new RuntimeException('Missing document');
        $count++;
        if (in_array('--write', $argv)) {
            $target=__DIR__.'/artifacts/'.$relative.'.html';
            if (!is_dir(dirname($target))) mkdir(dirname($target),0777,true);
            file_put_contents($target,$html);
        }
    }catch(Throwable $e){$failures[]=$relative.': '.$e->getMessage();}
}
echo json_encode(['rendered'=>$count,'errors'=>$failures],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
exit(count($failures)>0?1:0);
