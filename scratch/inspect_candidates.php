<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\DaftarPraktikan;

$p = Praktikum::where('status', 'aktif')->whereNotNull('koor_id')->first();
echo "Praktikum: {$p->nama} (ID: {$p->id}) coordinated by Koor User ID: {$p->koor_id}\n";

$registeredUserIds = DaftarPraktikan::where('praktikum_id', $p->id)->pluck('user_id')->toArray();

// Find users who have student profile but not registered in this praktikum
$candidates = Student::with('user')->whereNotIn('user_id', $registeredUserIds)->limit(10)->get();

echo "\nCandidate Students:\n";
foreach ($candidates as $cand) {
    if ($cand->user) {
        echo "NIM: {$cand->student_number} | Email: {$cand->user->email} | Name: {$cand->user->name}\n";
    }
}
