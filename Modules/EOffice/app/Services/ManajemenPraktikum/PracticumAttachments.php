<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;

class PracticumAttachments
{
    public function publicUrl(string $path, ?string $bucket='eoffice', ?string $name=null): string
    {
        abort_unless(auth()->check() && ($bucket===null || $bucket==='eoffice'),403);
        return URL::temporarySignedRoute('eoffice.manprak.attachment',now()->addHours(2),[
            'path'=>$path,'name'=>self::filename($name ?? basename($path)),'viewer'=>(string)auth()->id(),
        ]);
    }

    public static function filename(string $name): string
    {
        return trim(str_replace(["\r","\n",'"'], '', basename(str_replace('\\','/',$name)))) ?: 'Lampiran';
    }

    public static function response(string $content, string $name, bool $download, string $downloadUrl)
    {
        $name=self::filename($name);
        $mime=(new \finfo(FILEINFO_MIME_TYPE))->buffer($content) ?: 'application/octet-stream';
        // Never execute uploaded HTML/SVG/XML in the application's origin.
        if (in_array($mime,['text/html','application/xhtml+xml','image/svg+xml','application/xml','text/xml'],true)) {
            $mime='text/plain';
        }
        $fallback=preg_replace('/[^A-Za-z0-9._ -]/','_',Str::ascii($name)) ?: 'Lampiran';
        return response($content,200,[
            'Content-Type'=>$download?'application/octet-stream':$mime,
            'Content-Disposition'=>HeaderUtils::makeDisposition($download?'attachment':'inline',$name,$fallback),
            'X-Content-Type-Options'=>'nosniff','Cache-Control'=>'private, no-store',
            'Content-Security-Policy'=>"sandbox; default-src 'none'; base-uri 'none'; frame-ancestors 'self'",
        ]);
    }
}
