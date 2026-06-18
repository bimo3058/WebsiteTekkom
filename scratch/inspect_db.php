<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\DaftarPraktikan;

echo "--- TOTAL USERS ---\n";
echo User::count() . "\n\n";

echo "--- KOOR USERS ---\n";
// Let's see how koor is determined, maybe by role or something. Let's list some users with their roles or roles table if using spatie
$koorUsers = User::whereHas('roles', function($q) { $q->where('name', 'like', '%koor%'); })->get();
if ($koorUsers->isEmpty()) {
    // If no roles, let's just find users with praktikum assigned
    $koorUsers = User::whereIn('id', Praktikum::pluck('koor_id'))->get();
}
foreach ($koorUsers as $ku) {
    echo "ID: {$ku->id} | Name: {$ku->name} | Email: {$ku->email}\n";
}

echo "\n--- PRAKTIKUM LIST ---\n";
$praktikums = Praktikum::all();
foreach ($praktikums as $p) {
    echo "ID: {$p->id} | Nama: {$p->nama} | Status: {$p->status} | Koor ID: {$p->koor_id}\n";
}

echo "\n--- DAFTAR PRAKTIKAN FOR FIRST PRAKTIKUM ---\n";
if ($p = Praktikum::first()) {
    echo "Praktikum: {$p->nama}\n";
    $daftar = DaftarPraktikan::where('praktikum_id', $p->id)->with('user')->get();
    foreach ($daftar as $d) {
        echo "User ID: {$d->user_id} | Name: {$d->user?->name} | Email: {$d->user?->email} | Status: {$d->status}\n";
    }
}
