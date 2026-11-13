<?php
require __DIR__.'/../vendor/autoload.php';
$root=dirname(__DIR__);
$env=Dotenv\Dotenv::createArrayBacked($root)->load();
$target='anihtwnhijxcnhaarsmm';
if(parse_url($env['SUPABASE_URL']??'',PHP_URL_HOST)!==$target.'.supabase.co')throw new RuntimeException('Wrong destination Storage project');
$dir=$root.'/storage/app/private/supabase-export/combined';
$m=json_decode(file_get_contents($dir.'/manifest.json'),true,512,JSON_THROW_ON_ERROR);
$metadata=[];
foreach(json_decode(file_get_contents($dir.'/objects.json'),true) as $o){$metadata[$o['bucket_id'].'/'.$o['name']]=json_decode($o['metadata']??'{}',true);}
// Verify every local file before making any remote changes.
foreach($m['files'] as $f){
foreach(explode('/',$f['object']) as $part){if($part===''||$part==='.'||$part==='..'||str_contains($part,'\\'))throw new RuntimeException('Unsafe path');}
$p=$dir.'/files/'.$f['object'];
if(!is_file($p)||!hash_equals($f['sha256'],hash_file('sha256',$p)))throw new RuntimeException('Local checksum mismatch');
}
$c=new GuzzleHttp\Client(['base_uri'=>rtrim($env['SUPABASE_URL'],'/').'/storage/v1/','timeout'=>180,'connect_timeout'=>30,'http_errors'=>false,'headers'=>['Authorization'=>'Bearer '.$env['SUPABASE_KEY'],'apikey'=>$env['SUPABASE_KEY']]]);
$report=['target_project'=>$target,'started_at'=>gmdate('c'),'expected_local'=>count($m['files']),'unavailable_source'=>$m['failures'],'results'=>[]];
$save=function()use(&$report,$dir){file_put_contents($dir.'/upload-report.json',json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));};
$requests=function()use($m,$dir,$metadata,$c){foreach($m['files'] as $f){yield $f['object']=>function()use($f,$dir,$metadata,$c){return GuzzleHttp\Promise\Coroutine::of(function()use($f,$dir,$metadata,$c){
$p=implode('/',array_map('rawurlencode',explode('/',$f['object'])));
$r=yield $c->getAsync('object/authenticated/'.$p);
if($r->getStatusCode()===200){
if(hash_equals($f['sha256'],hash('sha256',(string)$r->getBody())))return yield ['status'=>'already_present','sha256'=>$f['sha256']];
return yield ['status'=>'conflict','reason'=>'Existing remote content differs; not overwritten'];
}
$err=json_decode((string)$r->getBody(),true);
if(!in_array($r->getStatusCode(),[400,404],true)||!in_array($err['code']??'',['NoSuchKey','not_found'],true))return yield ['status'=>'failed','step'=>'read','http'=>$r->getStatusCode(),'code'=>$err['code']??null];
$stream=fopen($dir.'/files/'.$f['object'],'rb');
try{$upload=yield $c->postAsync('object/'.$p,['headers'=>['x-upsert'=>'true','Content-Type'=>$metadata[$f['object']]['mimetype']??'application/octet-stream'],'body'=>$stream]);}finally{if(is_resource($stream))fclose($stream);}
if($upload->getStatusCode()<200||$upload->getStatusCode()>=300)return yield ['status'=>'failed','step'=>'upload','http'=>$upload->getStatusCode(),'response'=>substr((string)$upload->getBody(),0,400)];
$verify=yield $c->getAsync('object/authenticated/'.$p);
if($verify->getStatusCode()!==200||!hash_equals($f['sha256'],hash('sha256',(string)$verify->getBody())))return yield ['status'=>'failed','step'=>'verify','http'=>$verify->getStatusCode()];
return yield ['status'=>'uploaded_verified','sha256'=>$f['sha256']];
});};}};
$pool=new GuzzleHttp\Pool($c,$requests(),['concurrency'=>4,'fulfilled'=>function($result,$key)use(&$report,$save){$report['results'][$key]=$result;$save();if(count($report['results'])%25===0)echo 'Processed '.count($report['results']).'/'.$report['expected_local'].PHP_EOL;},'rejected'=>function($e,$key)use(&$report,$save){$report['results'][$key]=['status'=>'failed','reason'=>get_class($e)];$save();}]);
$pool->promise()->wait();
$report['finished_at']=gmdate('c');$report['summary']=array_count_values(array_column($report['results'],'status'));$save();
echo json_encode($report['summary']).PHP_EOL;
exit(isset($report['summary']['failed'])||isset($report['summary']['conflict'])?1:0);
