<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Services\NotifikasiService;

class StudentActivityNotification
{
    public static function send(string $practicumId, string $title, string $message, ?array $target=null): void
    {
        $ids = DaftarPraktikan::where('praktikum_id', $practicumId)->pluck('user_id')->unique();
        app(NotifikasiService::class)->kirimBulk($ids, $title, $message, 'mahasiswa', $target);
    }
}
