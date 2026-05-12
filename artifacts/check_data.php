<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Praktikum;
use App\Models\Pengguna;

echo "Praktikum Count: " . Praktikum::count() . "\n";
echo "Without Koor: " . Praktikum::whereNull('koor_id')->count() . "\n";
echo "With Koor: " . Praktikum::whereNotNull('koor_id')->count() . "\n";

$mhsCount = Pengguna::whereHas('roles', function($q) { $q->where('nama', 'mahasiswa'); })->count();
echo "Mahasiswa Count: " . $mhsCount . "\n";

$koorRoleCount = Pengguna::whereHas('roles', function($q) { $q->where('nama', 'koor_prak'); })->count();
echo "With koor_prak role: " . $koorRoleCount . "\n";

$mhsNoKoorCount = Pengguna::whereHas('roles', function($q) { $q->where('nama', 'mahasiswa'); })
    ->whereDoesntHave('roles', function($q) { $q->where('nama', 'koor_prak'); })
    ->count();
echo "Without koor_prak: " . $mhsNoKoorCount . "\n";
