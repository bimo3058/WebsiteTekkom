<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pengguna;
use App\Models\SystemRole;

$budi = Pengguna::where('nim_nip', '2021002')->first();
$roleKoor = SystemRole::firstOrCreate(['nama' => 'koor_prak']);
$budi->roles()->syncWithoutDetaching([
    $roleKoor->id => ['status' => 'aktif', 'dibuat_pada' => now()]
]);
echo "Budi assigned as koor_prak\n";
