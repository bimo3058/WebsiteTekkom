<?php
// Test actual DB write + read-back
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\User;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Praktikum;

$praktikum = Praktikum::where('koor_id', 33)->where('status', 'aktif')->first();
echo "Praktikum: {$praktikum->nama}\n\n";

// Test: hapus dulu jika sudah ada (cleanup)
DaftarPraktikan::where('praktikum_id', $praktikum->id)->delete();
echo "Cleaned up existing records.\n";

// Test create
$nims = ['24060121120012', '24060121140120', '24060122130008'];
foreach ($nims as $nim) {
    $student = Student::where('student_number', $nim)->with('user')->first();
    if ($student && $student->user) {
        $dp = DaftarPraktikan::create([
            'praktikum_id' => $praktikum->id,
            'user_id'      => $student->user->id,
            'status'       => 'terdaftar',
        ]);
        echo "Created: {$student->user->name} | ID: {$dp->id}\n";
    }
}

// Now verify the query that the controller uses
echo "\n=== QUERY DARI CONTROLLER (praktikan() method) ===\n";
$praktikans = DaftarPraktikan::with(['user'])
    ->where('praktikum_id', $praktikum->id)
    ->paginate(20);

echo "Count: {$praktikans->total()}\n";
foreach ($praktikans as $p) {
    echo "  - {$p->user?->name} | {$p->user?->email} | Status: {$p->status} | Created: {$p->created_at}\n";
}
