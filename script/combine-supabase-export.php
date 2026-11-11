<?php
$root=dirname(__DIR__).'/storage/app/private/supabase-export';
$target=$root.'/combined';
$sources=['anihtwnhijxcnhaarsmm'=>'anihtwnhijxcnhaarsmm','yrqfpdqanqujxgfcflrj'=>'yrqfpdqanqujxgfcflrj/recovery'];
$files=[];
foreach($sources as $project=>$folder){
$m=json_decode(file_get_contents($root.'/'.$folder.'/manifest.json'),true,512,JSON_THROW_ON_ERROR);
foreach($m['files'] as $f){
$p=$f['object'];
foreach(explode('/',$p) as $part){if($part===''||$part==='.'||$part==='..'||preg_match('/[<>:"\\\\|?*\x00-\x1f]/',$part))throw new RuntimeException('Unsafe path');}
$source=$root.'/'.$folder.'/files/'.$p;$dest=$target.'/files/'.$p;
if(isset($files[strtolower($p)]))throw new RuntimeException('Duplicate object');
if(!hash_equals($f['sha256'],hash_file('sha256',$source)))throw new RuntimeException('Source checksum mismatch');
@mkdir(dirname($dest),0700,true);
if(!copy($source,$dest)||!hash_equals($f['sha256'],hash_file('sha256',$dest)))throw new RuntimeException('Copy verification failed');
$files[strtolower($p)]=array_merge($f,['source_project'=>$project]);
}
}
$original=json_decode(file_get_contents($root.'/anihtwnhijxcnhaarsmm/manifest.json'),true,512,JSON_THROW_ON_ERROR);
$recovery=json_decode(file_get_contents($root.'/yrqfpdqanqujxgfcflrj/recovery/manifest.json'),true,512,JSON_THROW_ON_ERROR);
$dbSource=$root.'/anihtwnhijxcnhaarsmm/database.dump';
if(!copy($dbSource,$target.'/database.dump')||!hash_equals($original['database_sha256'],hash_file('sha256',$target.'/database.dump')))throw new RuntimeException('Database checksum mismatch');
foreach(['buckets.json','objects.json','database-contents.txt'] as $name){if(!copy($root.'/anihtwnhijxcnhaarsmm/'.$name,$target.'/'.$name))throw new RuntimeException('Metadata copy failed');}
$report=['created_at'=>gmdate('c'),'database_source_project'=>'anihtwnhijxcnhaarsmm','database_sha256'=>$original['database_sha256'],'expected_objects'=>$original['expected_objects'],'downloaded_objects'=>count($files),'downloaded_bytes'=>array_sum(array_column($files,'bytes')),'files'=>array_values($files),'failures'=>$recovery['failures'],'checksums_verified'=>true];
file_put_contents($target.'/manifest.json',json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));
file_put_contents($target.'/missing-files.txt',implode(PHP_EOL,array_column($report['failures'],'object')).PHP_EOL);
file_put_contents($target.'/README.txt',"BACKUP GABUNGAN\nDatabase berasal dari anihtwnhijxcnhaarsmm.\nFile berisi 47 objek dari anihtwnhijxcnhaarsmm dan 559 objek pemulihan dari yrqfpdqanqujxgfcflrj.\nTotal 606 dari 607 objek yang tercatat di backup database; 1 PDF masih tidak ditemukan.\nEmpat objek kosong adalah .emptyFolderPlaceholder.\nSemua checksum file dan database telah diverifikasi setelah penyalinan.\nLihat manifest.json untuk asal tiap objek dan missing-files.txt untuk file yang belum tersedia.\nIni gabungan backup lintas proyek/waktu, bukan snapshot atomik.\nJangan publikasikan folder ini: terdapat database dan dokumen privat.\nAplikasi belum dialihkan ke database atau storage lokal.\n");
echo json_encode(['objects'=>count($files),'bytes'=>$report['downloaded_bytes'],'missing'=>count($report['failures']),'checksums_verified'=>true]);
