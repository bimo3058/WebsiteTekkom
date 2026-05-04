<?php
/**
 * Step 4 — Admin: List Dosen Verification
 * Jalankan: php tests/step4_verification.php
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
echo "  STEP 4 VERIFICATION — Admin: List Dosen\n";
echo "══════════════════════════════════════════════════\n\n";

// ── Setup: Login users ──
echo "── SETUP ──\n";
$r = apiCall('POST', "$base/auth/login", ['email' => 'admin@praktikum.ac.id', 'password' => 'password123']);
$tokenAdmin = $r['body']['data']['token'];
echo "  Admin: OK\n";

$r = apiCall('POST', "$base/auth/login", ['email' => 'ahmad.fauzi@praktikum.ac.id', 'password' => 'password123']);
$tokenDosen = $r['body']['data']['token'];
echo "  Dosen: OK\n";

$r = apiCall('POST', "$base/auth/login", ['email' => 'andi@mhs.ac.id', 'password' => 'password123']);
$tokenMhs = $r['body']['data']['token'];
echo "  Mahasiswa: OK\n\n";

// ═══════════════════════════════════════════════════
// 1. AUTH GUARD TESTS
// ═══════════════════════════════════════════════════
echo "── 1. Auth Guards ──\n";

$r = apiCall('GET', "$base/admin/dosen");
test('No token → 401', $r['status'] === 401, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/dosen", null, 'invalid-token-xyz');
test('Invalid token → 401', $r['status'] === 401, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/dosen", null, $tokenMhs);
test('Mahasiswa → 403', $r['status'] === 403, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/dosen", null, $tokenDosen);
test('Dosen → 403 (is_admin strict)', $r['status'] === 403, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// 2. ADMIN ACCESS + RESPONSE FORMAT
// ═══════════════════════════════════════════════════
echo "\n── 2. Admin Access + Response ──\n";

$r = apiCall('GET', "$base/admin/dosen", null, $tokenAdmin);
test('Admin → 200', $r['status'] === 200, "status={$r['status']}");

$body = $r['body'];
test('Has "success" field = true', isset($body['success']) && $body['success'] === true);
test('Has "data" field (array)', isset($body['data']) && is_array($body['data']));
test('Has "pagination" field', isset($body['pagination']));

// ═══════════════════════════════════════════════════
// 3. DATA VALIDATION
// ═══════════════════════════════════════════════════
echo "\n── 3. Data Validation ──\n";

$data = $body['data'] ?? [];
test('Returns 2 dosens', count($data) === 2, "count=" . count($data));

// Check order (by nama ASC)
if (count($data) >= 2) {
    test('Ordered by nama ASC', $data[0]['nama'] < $data[1]['nama'],
        "first={$data[0]['nama']}, second={$data[1]['nama']}");
}

// Check fields per dosen
$dosen1 = $data[0] ?? [];
test('Has field: id', isset($dosen1['id']) && !empty($dosen1['id']));
test('Has field: nama', isset($dosen1['nama']) && !empty($dosen1['nama']));
test('Has field: email', isset($dosen1['email']) && !empty($dosen1['email']));
test('Has field: nim_nip', isset($dosen1['nim_nip']) && !empty($dosen1['nim_nip']));

// Check NO sensitive data
test('No password_hash exposed', !isset($dosen1['password_hash']));
test('No password exposed', !isset($dosen1['password']));
test('No deleted_at exposed', !isset($dosen1['deleted_at']));

// Verify actual data
$names = array_column($data, 'nama');
test('Dr. Ahmad Fauzi in list', in_array('Dr. Ahmad Fauzi, M.T.', $names), implode(', ', $names));
test('Dr. Siti Rahayu in list', in_array('Dr. Siti Rahayu, M.Kom.', $names), implode(', ', $names));

// ═══════════════════════════════════════════════════
// 4. PAGINATION
// ═══════════════════════════════════════════════════
echo "\n── 4. Pagination ──\n";

$pagination = $body['pagination'] ?? [];
test('current_page = 1', ($pagination['current_page'] ?? 0) === 1);
test('per_page = 10 (default)', ($pagination['per_page'] ?? 0) === 10);
test('total = 2', ($pagination['total'] ?? 0) === 2);
test('last_page = 1', ($pagination['last_page'] ?? 0) === 1);

// Custom per_page
$r = apiCall('GET', "$base/admin/dosen?per_page=1", null, $tokenAdmin);
$p = $r['body']['pagination'] ?? [];
test('per_page=1 → returns 1', count($r['body']['data'] ?? []) === 1, "count=" . count($r['body']['data'] ?? []));
test('per_page=1 → last_page=2', ($p['last_page'] ?? 0) === 2, "last_page={$p['last_page']}");

// Page 2
$r = apiCall('GET', "$base/admin/dosen?per_page=1&page=2", null, $tokenAdmin);
test('page=2 → returns 1 dosen', count($r['body']['data'] ?? []) === 1);
test('page=2 → current_page=2', ($r['body']['pagination']['current_page'] ?? 0) === 2);

// ═══════════════════════════════════════════════════
// 5. SEARCH FILTER
// ═══════════════════════════════════════════════════
echo "\n── 5. Search Filter ──\n";

$r = apiCall('GET', "$base/admin/dosen?search=Ahmad", null, $tokenAdmin);
test('Search "Ahmad" → 1 result', count($r['body']['data'] ?? []) === 1,
    "count=" . count($r['body']['data'] ?? []));
$found = $r['body']['data'][0]['nama'] ?? '';
test('Search "Ahmad" → Dr. Ahmad Fauzi', str_contains($found, 'Ahmad'), "found={$found}");

$r = apiCall('GET', "$base/admin/dosen?search=siti", null, $tokenAdmin);
test('Search "siti" (lowercase) → 1 result', count($r['body']['data'] ?? []) === 1);

$r = apiCall('GET', "$base/admin/dosen?search=@praktikum.ac.id", null, $tokenAdmin);
test('Search by email domain → 2 results', count($r['body']['data'] ?? []) === 2,
    "count=" . count($r['body']['data'] ?? []));

$r = apiCall('GET', "$base/admin/dosen?search=nonexistent", null, $tokenAdmin);
test('Search "nonexistent" → 0 results', count($r['body']['data'] ?? []) === 0);

// ═══════════════════════════════════════════════════
// 6. RESPONSE FORMAT CONSISTENCY
// ═══════════════════════════════════════════════════
echo "\n── 6. Response Consistency ──\n";

// Success response shape
test('Success shape: {success:true, data:[], pagination:{}}',
    $body['success'] === true && is_array($body['data']) && is_array($body['pagination']));

// Error response shape (403)
$r = apiCall('GET', "$base/admin/dosen", null, $tokenDosen);
test('Error 403 shape: {success:false, message:...}',
    $r['body']['success'] === false && !empty($r['body']['message']));

// Pretty print final response
echo "\n── SAMPLE RESPONSE ──\n";
$r = apiCall('GET', "$base/admin/dosen", null, $tokenAdmin);
echo json_encode($r['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

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
