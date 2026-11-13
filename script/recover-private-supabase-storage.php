<?php
require __DIR__.'/../vendor/autoload.php';
$root=dirname(__DIR__).'/storage/app/private/supabase-export';
$env=Dotenv\Dotenv::createArrayBacked($root,'source.env')->load();
if(parse_url($env['SUPABASE_URL']??'',PHP_URL_HOST)!=='yrqfpdqanqujxgfcflrj.supabase.co')throw new RuntimeException('Wrong source project');
$dir=$root.'/yrqfpdqanqujxgfcflrj/recovery';
$m=json_decode(file_get_contents($dir.'/manifest.json'),true,512,JSON_THROW_ON_ERROR);
$pending=array_unique(array_merge($m['private_pending'],array_column($m['failures'],'object')));
$c=new GuzzleHttp\Client(['base_uri'=>rtrim($env['SUPABASE_URL'],'/').'/storage/v1/object/authenticated/','timeout'=>90,'connect_timeout'=>20,'http_errors'=>false,'headers'=>['Authorization'=>'Bearer '.$env['SUPABASE_KEY'],'apikey'=>$env['SUPABASE_KEY']]]);
$failed=[];$new=[];
foreach($pending as $p){
$parts=explode('/',$p);
foreach($parts as $part){if($part===''||$part==='.'||$part==='..'||preg_match('/[<>:"\\\\|?*\x00-\x1f]/',$part)||preg_match('/[. ]$/',$part))throw new RuntimeException('Unsafe object path');}
$path=$dir.'/files/'.$p;@mkdir(dirname($path),0700,true);
try{
$r=$c->get(implode('/',array_map('rawurlencode',$parts)),['sink'=>$path.'.partial']);
if($r->getStatusCode()!==200){$failed[]=['object'=>$p,'status'=>$r->getStatusCode(),'reason'=>substr(file_get_contents($path.'.partial'),0,300)];unlink($path.'.partial');continue;}
rename($path.'.partial',$path);clearstatcache(true,$path);
$new[]=['object'=>$p,'bytes'=>filesize($path),'sha256'=>hash_file('sha256',$path)];
}catch(Throwable $e){$failed[]=['object'=>$p,'reason'=>get_class($e)];if(is_file($path.'.partial'))unlink($path.'.partial');}
}
$m['files']=array_merge($m['files'],$new);$m['failures']=$failed;$m['private_pending']=[];
$m['downloaded']=count($m['files']);$m['bytes']=array_sum(array_column($m['files'],'bytes'));$m['updated_at']=gmdate('c');
file_put_contents($dir.'/manifest.json',json_encode($m,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));
echo json_encode(['newly_recovered'=>count($new),'recovery_total'=>$m['downloaded'],'remaining_failures'=>$failed],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
