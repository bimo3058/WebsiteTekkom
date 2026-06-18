<?php
// Cleanup test data
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Praktikum;

$praktikum = Praktikum::where('koor_id', 33)->where('status', 'aktif')->first();
$deleted = DaftarPraktikan::where('praktikum_id', $praktikum->id)->delete();
echo "Cleaned up {$deleted} test records from daftar_praktikan.\n";
echo "Current count: " . DaftarPraktikan::where('praktikum_id', $praktikum->id)->count() . "\n";
