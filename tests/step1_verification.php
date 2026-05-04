<?php
/**
 * Step 1 — Pre-Step 2 Verification Script
 * Jalankan: php tests/step1_verification.php
 */

$base = 'http://127.0.0.1:8000/api';
$results = [];
$tokens = [];

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
    return ['status' => $httpCode, 'body' => json_decode($response, true)];
}

function test($name, $pass, $detail = '') {
    global $results;
    $icon = $pass ? '✅' : '❌';
    echo "$icon $name";
    if ($detail) echo " — $detail";
    echo "\n";
    $results[] = ['name' => $name, 'pass' => $pass];
}

echo "\n══════════════════════════════════════════════\n";
echo "  STEP 1 VERIFICATION — Pre-Step 2 Tests\n";
echo "══════════════════════════════════════════════\n\n";

// ── Test 1: Login Admin ──────────────────────────────────────────────────
echo "── AUTH LOGIN ──\n";
$r = apiCall('POST', "$base/auth/login", ['email' => 'admin@praktikum.ac.id', 'password' => 'password123']);
$tokens['admin'] = $r['body']['data']['token'] ?? '';
test('Login Admin', $r['status'] === 200 && !empty($tokens['admin']),
    "status={$r['status']}, role_aktif={$r['body']['data']['role_aktif']}");

// ── Test 2: Login Dosen ──────────────────────────────────────────────────
$r = apiCall('POST', "$base/auth/login", ['email' => 'ahmad.fauzi@praktikum.ac.id', 'password' => 'password123']);
$tokens['dosen'] = $r['body']['data']['token'] ?? '';
test('Login Dosen', $r['status'] === 200 && !empty($tokens['dosen']),
    "status={$r['status']}, role_aktif={$r['body']['data']['role_aktif']}");

// ── Test 3: Login Mahasiswa ──────────────────────────────────────────────
$r = apiCall('POST', "$base/auth/login", ['email' => 'andi@mhs.ac.id', 'password' => 'password123']);
$tokens['mahasiswa'] = $r['body']['data']['token'] ?? '';
test('Login Mahasiswa', $r['status'] === 200 && !empty($tokens['mahasiswa']),
    "status={$r['status']}, role_aktif={$r['body']['data']['role_aktif']}");

// ── Test 4: Login invalid password ───────────────────────────────────────
$r = apiCall('POST', "$base/auth/login", ['email' => 'admin@praktikum.ac.id', 'password' => 'wrongpass']);
test('Login invalid password → 401', $r['status'] === 401,
    "status={$r['status']}");

// ── Test 5: Login non-existent email ─────────────────────────────────────
$r = apiCall('POST', "$base/auth/login", ['email' => 'nobody@abc.com', 'password' => 'password123']);
test('Login non-existent email → 401', $r['status'] === 401,
    "status={$r['status']}");

// ── Test 6: Switch-role admin (hanya punya 1 role) ───────────────────────
echo "\n── SWITCH ROLE ──\n";
$r = apiCall('POST', "$base/auth/switch-role", ['role' => 'admin'], $tokens['admin']);
test('Switch admin→admin (1 role only)', $r['status'] === 200,
    "status={$r['status']}, msg={$r['body']['message']}");

// ── Test 7: Switch mahasiswa → asprak (belum punya) ──────────────────────
$r = apiCall('POST', "$base/auth/switch-role", ['role' => 'asprak'], $tokens['mahasiswa']);
test('Switch mahasiswa→asprak (belum punya) → 403', $r['status'] === 403,
    "status={$r['status']}, msg={$r['body']['message']}");

// ── Test 8: Switch ke role yang tidak ada di sistem ──────────────────────
$r = apiCall('POST', "$base/auth/switch-role", ['role' => 'superadmin'], $tokens['admin']);
test('Switch ke role invalid → 422', $r['status'] === 422,
    "status={$r['status']}");

// ── Test 9: GET /api/auth/me ─────────────────────────────────────────────
echo "\n── AUTH ME ──\n";
$r = apiCall('GET', "$base/auth/me", null, $tokens['admin']);
test('GET /auth/me Admin', $r['status'] === 200 && $r['body']['data']['role_aktif'] === 'admin',
    "role_aktif={$r['body']['data']['role_aktif']}, user={$r['body']['data']['user']['nama']}");

$r = apiCall('GET', "$base/auth/me", null, $tokens['dosen']);
test('GET /auth/me Dosen', $r['status'] === 200 && $r['body']['data']['role_aktif'] === 'dosen',
    "role_aktif={$r['body']['data']['role_aktif']}, user={$r['body']['data']['user']['nama']}");

$r = apiCall('GET', "$base/auth/me", null, $tokens['mahasiswa']);
test('GET /auth/me Mahasiswa', $r['status'] === 200 && $r['body']['data']['role_aktif'] === 'mahasiswa',
    "role_aktif={$r['body']['data']['role_aktif']}, user={$r['body']['data']['user']['nama']}");

// ── Test 10: No token → 401 ─────────────────────────────────────────────
echo "\n── AUTH GUARD ──\n";
$r = apiCall('GET', "$base/auth/me");
test('No token → 401', $r['status'] === 401,
    "status={$r['status']}");

// ── Test 11: Invalid token → 401 ────────────────────────────────────────
$r = apiCall('GET', "$base/auth/me", null, 'invalid-token-xxx');
test('Invalid token → 401', $r['status'] === 401,
    "status={$r['status']}");

// ── Test 12: Bearer token works ─────────────────────────────────────────
$r = apiCall('GET', "$base/auth/me", null, $tokens['admin']);
test('Bearer token header works', $r['status'] === 200,
    "status={$r['status']}");

// ── Test 13: ActivityLog (saat seeder tidak ada Auth → skip) ─────────────
echo "\n── ACTIVITY LOG ──\n";
// ActivityLog hanya terisi saat ada Auth::id() (bukan di seeder).
// Test: login lalu update sesuatu → tapi belum ada endpoint update.
// Untuk sekarang, kita cek tabel activity_logs kosong (expected dari seeder).
echo "ℹ️  ActivityLog di-skip saat seeder (no Auth::id()). Akan berfungsi saat CRUD via API.\n";

// ── Summary ──────────────────────────────────────────────────────────────
echo "\n══════════════════════════════════════════════\n";
$passed = count(array_filter($results, fn($r) => $r['pass']));
$total  = count($results);
echo "  RESULT: $passed/$total tests passed\n";
echo "══════════════════════════════════════════════\n\n";

exit($passed === $total ? 0 : 1);
