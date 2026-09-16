<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\PendaftaranAsprak;

$p = Praktikum::where('nama', 'Praktikum Pengenalan Jaringan Komputer')->first();
echo "Praktikum ID: {$p->id}, koor_id: {$p->koor_id}\n";
$koor = $p->koordinator;
echo "Koordinator Aktif (Praktikum->koordinator): " . ($koor ? $koor->name : 'None') . "\n";
$pendaftar = PendaftaranAsprak::with('user')->where('praktikum_id', $p->id)->whereNotNull('status_koor')->get();
foreach($pendaftar as $pd) {
    echo "Pendaftar Koor: {$pd->user->name} | Status Koor: {$pd->status_koor} | Status Asprak: {$pd->status}\n";
}
