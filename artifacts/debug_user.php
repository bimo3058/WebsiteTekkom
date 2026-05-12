<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$citra = App\Models\Pengguna::where('email', 'citra@mhs.ac.id')->first();
echo "Citra ID: " . $citra->id . "\n";
echo "Roles Count: " . $citra->roles()->count() . "\n";
foreach ($citra->roles as $role) {
    echo "  Role: " . $role->nama . "\n";
}
echo "Role Tertinggi: " . $citra->roleTertinggi() . "\n";
