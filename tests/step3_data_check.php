<?php
/**
 * Step 3 — Data Integrity Check
 * Jalankan: php tests/step3_data_check.php
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pengguna;
use App\Models\Praktikum;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

echo "\n══════════════════════════════════════════════════\n";
echo "  STEP 3 — DATA INTEGRITY CHECK\n";
echo "══════════════════════════════════════════════════\n\n";

$allOk = true;

// ── 1. ROLES ──────────────────────────────────────────────────────────────
echo "── 1. ROLES ──\n";
$roles = Role::all();
$expectedRoles = ['admin', 'dosen', 'mahasiswa', 'asprak', 'koor_prak'];
foreach ($roles as $r) {
    echo "  ✅ {$r->nama} — {$r->deskripsi}\n";
}
$missingRoles = array_diff($expectedRoles, $roles->pluck('nama')->toArray());
if (!empty($missingRoles)) {
    echo "  ❌ MISSING: " . implode(', ', $missingRoles) . "\n";
    $allOk = false;
}
echo "  Total: {$roles->count()}/5 " . ($roles->count() === 5 ? '✅' : '❌') . "\n\n";

// ── 2. PENGGUNA + ROLE ASSIGNMENTS ────────────────────────────────────────
echo "── 2. PENGGUNA + ROLES ──\n";
$users = Pengguna::with('roles')->get();
foreach ($users as $u) {
    $roleNames = $u->roles->pluck('nama')->implode(', ');
    $pad = str_pad($u->nama, 30);
    $nimPad = str_pad($u->nim_nip ?? '-', 10);
    echo "  {$pad} | {$nimPad} | roles: [{$roleNames}]\n";
}

$adminCount = Pengguna::whereHas('roles', fn($q) => $q->where('nama', 'admin'))->count();
$dosenCount = Pengguna::whereHas('roles', fn($q) => $q->where('nama', 'dosen'))->count();
$mhsCount   = Pengguna::whereHas('roles', fn($q) => $q->where('nama', 'mahasiswa'))->count();
$asprakCount = Pengguna::whereHas('roles', fn($q) => $q->where('nama', 'asprak'))->count();
$koorCount  = Pengguna::whereHas('roles', fn($q) => $q->where('nama', 'koor_prak'))->count();

echo "\n  Breakdown:\n";
echo "    admin:     {$adminCount}/1 " . ($adminCount === 1 ? '✅' : '❌') . "\n";
echo "    dosen:     {$dosenCount}/2 " . ($dosenCount === 2 ? '✅' : '❌') . "\n";
echo "    mahasiswa: {$mhsCount}/10 " . ($mhsCount >= 10 ? '✅' : '❌') . "\n";
echo "    asprak:    {$asprakCount} (Andi+Budi) " . ($asprakCount >= 2 ? '✅' : '⚠️') . "\n";
echo "    koor_prak: {$koorCount} (Andi) " . ($koorCount >= 1 ? '✅' : '⚠️') . "\n";
echo "  Total users: {$users->count()}/13\n\n";

// ── 3. PRAKTIKUM ──────────────────────────────────────────────────────────
echo "── 3. PRAKTIKUM ──\n";
$prakts = Praktikum::with(['dosen', 'koordinator'])->get();
foreach ($prakts as $p) {
    $nama = str_pad($p->nama, 35);
    $dosen = $p->dosen->nama ?? 'NULL';
    $koor  = $p->koordinator->nama ?? 'NULL';
    echo "  {$nama} | dosen: {$dosen}\n";
    echo "  " . str_repeat(' ', 35) . " | koor:  {$koor}\n";
    echo "  " . str_repeat(' ', 35) . " | status: {$p->status} | sem: {$p->semester} | thn: {$p->tahun_ajaran}\n\n";
}
echo "  Total: {$prakts->count()}/4 " . ($prakts->count() === 4 ? '✅' : '❌') . "\n";
$withDosen = $prakts->whereNotNull('dosen_id')->count();
$withKoor  = $prakts->whereNotNull('koor_id')->count();
echo "  Has dosen_id: {$withDosen}/4 " . ($withDosen === 4 ? '✅' : '⚠️') . "\n";
echo "  Has koor_id:  {$withKoor} (only Praktikum 1 has koor) " . ($withKoor >= 1 ? '✅' : '❌') . "\n\n";

// ── 4. PENGGUNA_ROLE pivot ────────────────────────────────────────────────
echo "── 4. PENGGUNA_ROLE pivot ──\n";
$pivots = DB::table('pengguna_role')
    ->join('pengguna', 'pengguna_role.pengguna_id', '=', 'pengguna.id')
    ->join('role', 'pengguna_role.role_id', '=', 'role.id')
    ->select('pengguna.nama', 'role.nama as role_nama', 'pengguna_role.status')
    ->orderBy('pengguna.nama')
    ->get();
foreach ($pivots as $pv) {
    $pad = str_pad($pv->nama, 30);
    echo "  {$pad} → {$pv->role_nama} ({$pv->status})\n";
}
echo "  Total pivot rows: {$pivots->count()}\n\n";

// ── 5. TABLES ─────────────────────────────────────────────────────────────
echo "── 5. ALL TABLES ──\n";
$tables = collect(DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT IN ('sqlite_sequence','migrations') ORDER BY name"))
    ->pluck('name');
foreach ($tables as $t) {
    $count = DB::table($t)->count();
    echo "  📋 " . str_pad($t, 30) . " | rows: {$count}\n";
}
echo "\n";

// ── 6. GAP ANALYSIS ──────────────────────────────────────────────────────
echo "── 6. GAP ANALYSIS ──\n";

// Table yang SEHARUSNYA ada berdasarkan ERD tapi BELUM dibuat
$expectedTables = [
    'pengguna', 'role', 'pengguna_role', 'sesi_login',
    'personal_access_tokens', 'activity_logs', 'praktikum',
    // ERD tables belum dibuat:
    'asprak_praktikum', 'daftar_praktikan', 'modul', 'modul_asprak',
    'materi_modul', 'pengumuman', 'tugas', 'pengumpulan_tugas',
    'absensi', 'nilai', 'kode_praktikum',
    'pendaftaran_koordinator', 'pendaftaran_asprak', 'notifikasi',
];

$existingTables = $tables->toArray();
$missingTables  = array_diff($expectedTables, $existingTables);
$createdTables  = array_intersect($expectedTables, $existingTables);

echo "  ✅ Created (" . count($createdTables) . "):\n";
foreach ($createdTables as $t) echo "     ✅ {$t}\n";
echo "  ⏳ Not yet created (" . count($missingTables) . ") — will be created in later steps:\n";
foreach ($missingTables as $t) echo "     ⏳ {$t}\n";
echo "\n";

// Specific checks for Step 4 readiness
echo "── 7. STEP 4 READINESS ──\n";
$ready = true;

$check1 = Role::where('nama', 'dosen')->exists();
echo "  Role 'dosen' exists: " . ($check1 ? '✅' : '❌') . "\n";
$ready = $ready && $check1;

$check2 = Pengguna::whereHas('roles', fn($q) => $q->where('nama', 'dosen'))->count() >= 2;
echo "  At least 2 dosen exist: " . ($check2 ? '✅' : '❌') . "\n";
$ready = $ready && $check2;

$check3 = Praktikum::count() >= 3;
echo "  At least 3 praktikum exist: " . ($check3 ? '✅' : '❌') . "\n";
$ready = $ready && $check3;

$check4 = Pengguna::where('nim_nip', 'ADM001')->whereHas('roles', fn($q) => $q->where('nama', 'admin'))->exists();
echo "  Admin user with role: " . ($check4 ? '✅' : '❌') . "\n";
$ready = $ready && $check4;

// Andi has koor_prak + asprak + mahasiswa
$andi = Pengguna::where('nim_nip', '2021001')->with('roles')->first();
$andiRoles = $andi ? $andi->roles->pluck('nama')->toArray() : [];
$check5 = in_array('koor_prak', $andiRoles) && in_array('asprak', $andiRoles) && in_array('mahasiswa', $andiRoles);
echo "  Andi (2021001) has koor+asprak+mhs: " . ($check5 ? '✅' : '❌') . " [" . implode(', ', $andiRoles) . "]\n";
$ready = $ready && $check5;

// Budi has asprak + mahasiswa
$budi = Pengguna::where('nim_nip', '2021002')->with('roles')->first();
$budiRoles = $budi ? $budi->roles->pluck('nama')->toArray() : [];
$check6 = in_array('asprak', $budiRoles) && in_array('mahasiswa', $budiRoles);
echo "  Budi (2021002) has asprak+mhs: " . ($check6 ? '✅' : '❌') . " [" . implode(', ', $budiRoles) . "]\n";
$ready = $ready && $check6;

echo "\n══════════════════════════════════════════════════\n";
echo "  STEP 4 READY: " . ($ready ? '✅ YES' : '❌ NO — fix issues above') . "\n";
echo "══════════════════════════════════════════════════\n\n";

exit($ready ? 0 : 1);
