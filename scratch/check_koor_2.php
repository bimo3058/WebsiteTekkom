<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\PendaftaranKoordinator;

$p = Praktikum::where('nama', 'Praktikum Pengenalan Jaringan Komputer')->first();
$pendaftarAll = PendaftaranKoordinator::with('user')->where('praktikum_id', $p->id)->get();
foreach($pendaftarAll as $pd) {
    echo "Pendaftar: {$pd->user->name} | Status Dosen: {$pd->status_dosen} | Status Admin: {$pd->status}\n";
}
