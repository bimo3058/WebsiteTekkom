<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Validation\ValidationException;

class ModuleRevisionAttachments
{
    public static function files(?string $json): array
    {
        $files=json_decode($json ?? '[]',true);
        if (!is_array($files)) return [];
        return collect($files)->map(fn($file)=>is_string($file)?['path'=>$file,'original_name'=>basename($file)]:$file)->filter(fn($file)=>is_array($file) && !empty($file['path']))->values()->all();
    }

    public static function retained(?string $json, array $paths): array
    {
        $files=collect(self::files($json))->keyBy('path');
        if (count($paths)!==count(array_unique($paths)) || array_diff($paths,$files->keys()->all())) {
            throw ValidationException::withMessages(['retained_files'=>'Lampiran revisi telah berubah atau tidak valid. Muat ulang halaman.']);
        }
        return array_map(fn($path)=>$files[$path],$paths);
    }
}
