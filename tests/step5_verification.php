<?php
/**
 * Step 5 — Admin: Praktikum CRUD Verification
 * Jalankan: php tests/step5_verification.php
 */

$base = 'http://127.0.0.1:8000/api';
$results = [];

function apiCall($method, $url, $body = null, $token = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    $headers = ['Content-Type: application/json', 'Accept: application/json'];
    if ($token) $headers[] = "Authorization: Bearer $token";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($body) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['status' => $httpCode, 'body' => json_decode($response, true), 'raw' => $response];
}

function test($name, $pass, $detail = '') {
    global $results;
    $icon = $pass ? '✅' : '❌';
    echo "$icon $name";
    if ($detail) echo " — $detail";
    echo "\n";
    $results[] = ['name' => $name, 'pass' => $pass];
}

echo "\n══════════════════════════════════════════════════\n";
echo "  STEP 5 VERIFICATION — Praktikum CRUD\n";
echo "══════════════════════════════════════════════════\n\n";

// ── Setup: Login users ──
echo "── SETUP ──\n";
$r = apiCall('POST', "$base/auth/login", ['email' => 'admin@praktikum.ac.id', 'password' => 'password123']);
$tokenAdmin = $r['body']['data']['token'];
echo "  Admin token: OK\n";

$r = apiCall('POST', "$base/auth/login", ['email' => 'ahmad.fauzi@praktikum.ac.id', 'password' => 'password123']);
$tokenDosen = $r['body']['data']['token'];
echo "  Dosen token: OK\n";

$r = apiCall('POST', "$base/auth/login", ['email' => 'andi@mhs.ac.id', 'password' => 'password123']);
$tokenMhs = $r['body']['data']['token'];
echo "  Mahasiswa token: OK\n\n";

$r = apiCall('GET', "$base/auth/me", null, $tokenDosen);
$dosenId = $r['body']['data']['user']['id'];
echo "  Dosen ID: $dosenId\n";

$r = apiCall('GET', "$base/auth/me", null, $tokenMhs);
$mhsId = $r['body']['data']['user']['id'];
echo "  Mahasiswa ID: $mhsId\n\n";

// ═══════════════════════════════════════════════════
// 1. NON-ADMIN ACCESS DENIED
// ═══════════════════════════════════════════════════
echo "── 1. Security / Non-Admin ──\n";
$r = apiCall('GET', "$base/admin/praktikum", null, $tokenDosen);
test('Non-admin → 403 (GET List)', $r['status'] === 403, "status={$r['status']}");

$r = apiCall('POST', "$base/admin/praktikum", ['nama' => 'Test'], $tokenDosen);
test('Non-admin → 403 (POST Create)', $r['status'] === 403, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// 2. POST (CREATE)
// ═══════════════════════════════════════════════════
echo "\n── 2. Create Praktikum ──\n";

$r = apiCall('POST', "$base/admin/praktikum", [], $tokenAdmin);
test('POST → 422 (validation errors)', $r['status'] === 422, "status={$r['status']}");

$r = apiCall('POST', "$base/admin/praktikum", [
    'nama' => 'Praktikum Error',
    'tahun_ajaran' => 2026,
    'semester' => 'ganjil',
    'dosen_id' => $mhsId // mahasiswa acting as dosen
], $tokenAdmin);
test('POST → 422 (invalid dosen role)', $r['status'] === 422, "status={$r['status']}");

$dynKode = 'PML_' . time();
$newPrakData = [
    'nama' => 'Praktikum Machine Learning',
    'kode' => $dynKode,
    'deskripsi' => 'Belajar ML dengan Python',
    'tahun_ajaran' => 2026,
    'semester' => 'genap',
    'status' => 'aktif',
    'dosen_id' => $dosenId
];
$r = apiCall('POST', "$base/admin/praktikum", $newPrakData, $tokenAdmin);
test('POST → 201 (create practical)', $r['status'] === 201, "status={$r['status']}");
test('New praktikum has relationships', isset($r['body']['data']['dosen']));

$newId = $r['body']['data']['id'] ?? '';

// Try duplicate kode
$r = apiCall('POST', "$base/admin/praktikum", [
    'nama' => 'Praktikum Another',
    'kode' => $dynKode, // duplicate
    'tahun_ajaran' => 2026,
    'semester' => 'ganjil'
], $tokenAdmin);
test('Unique validation works (duplicate kode) → 422', $r['status'] === 422, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// 3. GET (LIST & SHOW)
// ═══════════════════════════════════════════════════
echo "\n── 3. Read Praktikum ──\n";

$r = apiCall('GET', "$base/admin/praktikum", null, $tokenAdmin);
test('GET → 200 (list all)', $r['status'] === 200, "status={$r['status']}");
test('List format {success, data}', isset($r['body']['success']) && isset($r['body']['data']));

$r = apiCall('GET', "$base/admin/praktikum?search=Machine", null, $tokenAdmin);
test('GET → 200 (with filters)', $r['status'] === 200 && count($r['body']['data']) >= 1, "found=" . count($r['body']['data'] ?? []));

$r = apiCall('GET', "$base/admin/praktikum/$newId", null, $tokenAdmin);
test('GET {id} → 200 (show single)', $r['status'] === 200 && $r['body']['data']['id'] === $newId, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/praktikum/non-existent-uuid", null, $tokenAdmin);
test('GET {id} → 404 (not found)', $r['status'] === 404, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// 4. PUT (UPDATE)
// ═══════════════════════════════════════════════════
echo "\n── 4. Update Praktikum ──\n";

$r = apiCall('PUT', "$base/admin/praktikum/$newId", [
    'nama' => 'Praktikum ML Advanced',
    'kode' => $dynKode // keep same code (should ignore self for unique check)
], $tokenAdmin);
test('PUT → 200 (update)', $r['status'] === 200, "status={$r['status']}");
test('Update ignores self unique check', $r['body']['data']['nama'] === 'Praktikum ML Advanced');

// ═══════════════════════════════════════════════════
// 5. DELETE (SOFT DELETE)
// ═══════════════════════════════════════════════════
echo "\n── 5. Delete Praktikum ──\n";

$r = apiCall('DELETE', "$base/admin/praktikum/$newId", null, $tokenAdmin);
test('DELETE → 200 (soft delete)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/praktikum/$newId", null, $tokenAdmin);
test('GET after delete → 404', $r['status'] === 404, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// 6. DB DIRECT CHECKS
// ═══════════════════════════════════════════════════
echo "\n── 6. Database Checks ──\n";

$dbPath = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO("sqlite:$dbPath");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check soft delete
$stmt = $pdo->prepare("SELECT deleted_at FROM praktikum WHERE id = :id");
$stmt->execute([':id' => $newId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
test('Soft-deleted praktikum has deleted_at', !empty($row['deleted_at']), "deleted_at={$row['deleted_at']}");

// Check Activity Log
$stmt = $pdo->query("SELECT action FROM activity_logs WHERE model = 'Praktikum'");
$logs = $stmt->fetchAll(PDO::FETCH_COLUMN);
$hasCreate = in_array('create', $logs);
$hasUpdate = in_array('update', $logs);
$hasDelete = in_array('delete', $logs);

test('Activity logs recorded (create/update/delete)', $hasCreate && $hasUpdate && $hasDelete, 
    "create: ".($hasCreate?'Y':'N').", update: ".($hasUpdate?'Y':'N').", delete: ".($hasDelete?'Y':'N'));

$pdo = null;

// ═══════════════════════════════════════════════════
// SUMMARY
// ═══════════════════════════════════════════════════
echo "\n══════════════════════════════════════════════════\n";
$passed = count(array_filter($results, fn($r) => $r['pass']));
$total  = count($results);
$emoji  = $passed === $total ? '🎉' : '⚠️';
echo "  $emoji RESULT: $passed/$total tests passed\n";
if ($passed < $total) {
    echo "\n  FAILURES:\n";
    foreach ($results as $r) {
        if (!$r['pass']) echo "    ❌ {$r['name']}\n";
    }
}
echo "══════════════════════════════════════════════════\n\n";

exit($passed === $total ? 0 : 1);
