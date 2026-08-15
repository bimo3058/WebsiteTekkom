<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$a = \Modules\EOffice\Models\PendaftaranAsprak::where('status', 'pending')->where('status_koor', 'disetujui')->first();
echo 'Asprak ID: ' . ($a ? $a->id : 'none') . ', Praktikum ID: ' . ($a ? $a->praktikum_id : 'none') . PHP_EOL;

if ($a) {
    $p = \Modules\EOffice\Models\Praktikum::find($a->praktikum_id);
    echo 'Praktikum Status: ' . ($p ? $p->status : 'none') . PHP_EOL;
}
