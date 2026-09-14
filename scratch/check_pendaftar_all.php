<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\PendaftaranAsprak;

$p = Praktikum::where('nama', 'Praktikum Pengenalan Jaringan Komputer')->first();
$pendaftarAll = PendaftaranAsprak::with('user')->where('praktikum_id', $p->id)->get();
foreach($pendaftarAll as $pd) {
    echo "Pendaftar: {$pd->user->name} | Status Asprak: {$pd->status} | Status Koor: " . ($pd->status_koor ?? 'NULL') . "\n";
}
