<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\User;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Praktikum;

// Simulasikan isi file CSV (NIM-based)
$csvContent = "email_atau_nim\n24060121120012\n24060121140120\n24060122130008\n";

// Ambil praktikum yang dikoordinir oleh user 33 (Mahasiswa 1 yang juga koor)
$koorUser = User::find(33);
$praktikum = Praktikum::where('koor_id', 33)->where('status', 'aktif')->first();

if (!$praktikum) {
    echo "ERROR: Tidak ada praktikum aktif untuk koor user 33\n";
    exit;
}

echo "=== SIMULASI IMPORT ===\n";
echo "Praktikum: {$praktikum->nama} (ID: {$praktikum->id})\n";
echo "Koor: {$koorUser->name} ({$koorUser->email})\n\n";

// Parse CSV
$lines = array_filter(
    array_map('trim', explode("\n", str_replace("\r\n", "\n", $csvContent))),
    fn($l) => $l !== ''
);

// Buang header
$first = mb_strtolower(reset($lines));
if (str_contains($first, 'email') || str_contains($first, 'nim') || str_contains($first, 'identifier')) {
    array_shift($lines);
}

echo "Baris data CSV (setelah header dibuang): " . implode(', ', $lines) . "\n\n";

$added = 0;
$skipped = 0;
$notFound = [];

foreach ($lines as $line) {
    $row = str_getcsv($line);
    $identifier = trim($row[0] ?? '');

    echo "--- Proses: '{$identifier}' ---\n";

    if ($identifier === '') {
        $skipped++;
        echo "  SKIP: identifier kosong\n";
        continue;
    }

    // Cari by email
    $targetUser = User::where('email', $identifier)->first();
    echo "  Cari by email: " . ($targetUser ? "FOUND ({$targetUser->name})" : "not found") . "\n";

    if (!$targetUser) {
        // Cari by NIM
        $student = Student::where('student_number', $identifier)->with('user')->first();
        $targetUser = $student?->user;
        echo "  Cari by NIM student_number='{$identifier}': " . ($student ? "Student found" : "no student record") . "\n";
        if ($student) {
            echo "  Student->user: " . ($targetUser ? "FOUND ({$targetUser->name})" : "null - PROBLEM!") . "\n";
        }
    }

    if (!$targetUser) {
        $notFound[] = $identifier;
        $skipped++;
        echo "  RESULT: NOT FOUND, skip\n";
        continue;
    }

    // Cek sudah terdaftar?
    $existed = DaftarPraktikan::where('praktikum_id', $praktikum->id)
        ->where('user_id', $targetUser->id)
        ->exists();
    echo "  Sudah terdaftar: " . ($existed ? 'YES (skip)' : 'NO') . "\n";

    if (!$existed) {
        // Jangan benar-benar create di sini — hanya simulasi
        $added++;
        echo "  RESULT: AKAN ditambahkan ke daftar_praktikan\n";
    } else {
        $skipped++;
        echo "  RESULT: Skip (sudah terdaftar)\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Akan ditambahkan: {$added}\n";
echo "Dilewati: {$skipped}\n";
echo "Tidak ditemukan: " . implode(', ', $notFound) . "\n";

// Cek DaftarPraktikan tabel untuk kolom apa saja yang required
echo "\n=== TABEL DAFTAR_PRAKTIKAN ===\n";
$existing = DaftarPraktikan::where('praktikum_id', $praktikum->id)->with('user')->get();
echo "Praktikan terdaftar saat ini: {$existing->count()}\n";
foreach ($existing as $dp) {
    echo "  - {$dp->user?->name} ({$dp->user?->email}) | Status: {$dp->status}\n";
}

// Cek fillable DaftarPraktikan
$dp = new DaftarPraktikan();
echo "\nFillable: " . implode(', ', $dp->getFillable()) . "\n";
