<?php
/**
 * Step 7 — Admin: Assign Asprak & Koor Verification
 * Jalankan: php tests/step7_verification.php
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
echo "  STEP 7 VERIFICATION — Assign Asprak & Koor\n";
echo "══════════════════════════════════════════════════\n\n";

// ── Setup: Login admin ──
echo "── SETUP ──\n";
$r = apiCall('POST', "$base/auth/login", ['email' => 'admin@praktikum.ac.id', 'password' => 'password123']);
if ($r['status'] !== 200) die("❌ Admin login failed: " . $r['raw'] . "\n");
$tokenAdmin = $r['body']['data']['token'];
echo "  Admin token: OK\n";

$r = apiCall('POST', "$base/auth/login", ['email' => 'citra@mhs.ac.id', 'password' => 'password123']);
if ($r['status'] !== 200) die("❌ Non-Admin login failed: " . $r['raw'] . "\n");
$tokenNonAdmin = $r['body']['data']['token'];
echo "  Non-Admin token: OK\n\n";

// Get a praktikum
$r = apiCall('GET', "$base/admin/praktikum", null, $tokenAdmin);
$praktikum = $r['body']['data'][0] ?? null;
if (!$praktikum) {
    die("❌ No praktikum found. Run migrations and seeders first.\n");
}
$praktikumId = $praktikum['id'];
echo "  Praktikum Target ID: $praktikumId\n\n";

// Get a user
$tinkerCmd = "php artisan tinker --execute \"echo \App\Models\User::first()->id;\"";
$out = [];
exec($tinkerCmd, $out);
$userId1 = (int) trim(implode("", $out));

$tinkerCmd2 = "php artisan tinker --execute \"echo \App\Models\User::skip(1)->first()->id;\"";
$out2 = [];
exec($tinkerCmd2, $out2);
$userId2 = (int) trim(implode("", $out2));

echo "  User 1 ID: $userId1\n";
echo "  User 2 ID: $userId2\n\n";

// ═══════════════════════════════════════════════════
// 1. LIST Asparaks (Empty)
// ═══════════════════════════════════════════════════
echo "── 1. List Asparaks ──\n";
$r = apiCall('GET', "$base/admin/praktikum/$praktikumId/asparaks", null, $tokenAdmin);
test('GET → 200 (list asparaks)', $r['status'] === 200, "status={$r['status']}");
test('List is initially an array', is_array($r['body']['data'] ?? null));

// ═══════════════════════════════════════════════════
// 2. ASSIGN Asprak (cleanup first if exists)
// ═══════════════════════════════════════════════════
echo "\n── 2. Assign Asprak ──\n";

// Cleanup: find & delete existing assignment for userId1 to get a clean slate
$existingList = apiCall('GET', "$base/admin/praktikum/$praktikumId/asparaks", null, $tokenAdmin);
foreach (($existingList['body']['data'] ?? []) as $item) {
    if ($item['user']['id'] === $userId1) {
        apiCall('DELETE', "$base/admin/praktikum/$praktikumId/asprak/{$item['id']}", null, $tokenAdmin);
    }
}

$r = apiCall('POST', "$base/admin/praktikum/$praktikumId/assign-asprak", ['user_id' => $userId1, 'role' => 'asprak'], $tokenAdmin);
test('POST → 201 (assign valid asprak)', $r['status'] === 201, "status={$r['status']}");
$asprakRecordId = $r['body']['data']['id'] ?? null;
test('Response format valid', $asprakRecordId !== null);


// ═══════════════════════════════════════════════════
// 3. LIST Asparaks (After Assign)
// ═══════════════════════════════════════════════════
echo "\n── 3. List Asparaks After Assign ──\n";
$r = apiCall('GET', "$base/admin/praktikum/$praktikumId/asparaks", null, $tokenAdmin);
$count = count($r['body']['data'] ?? []);
test('GET → 200 (list has 1+ items)', $count > 0, "count=$count");
test('Includes user details', isset($r['body']['data'][0]['user']['name']));

// ═══════════════════════════════════════════════════
// 4. ASSIGN Koor (Same User - Should Fail)
// ═══════════════════════════════════════════════════
echo "\n── 4. Assign Conflict ──\n";
$r = apiCall('POST', "$base/admin/praktikum/$praktikumId/assign-asprak", ['user_id' => $userId1, 'role' => 'koor'], $tokenAdmin);
test('POST → 409 (duplicate user)', $r['status'] === 409, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// 5. VALIDATION
// ═══════════════════════════════════════════════════
echo "\n── 5. Validation Rules ──\n";
$r = apiCall('POST', "$base/admin/praktikum/$praktikumId/assign-asprak", ['user_id' => 'not-int', 'role' => 'asprak'], $tokenAdmin);
test('POST → 422 (user_id not integer)', $r['status'] === 422, "status={$r['status']}");

$r = apiCall('POST', "$base/admin/praktikum/$praktikumId/assign-asprak", ['user_id' => 999999, 'role' => 'asprak'], $tokenAdmin);
test('POST → 422 (user_id not exist)', $r['status'] === 422, "status={$r['status']}");

$r = apiCall('POST', "$base/admin/praktikum/$praktikumId/assign-asprak", ['user_id' => $userId2, 'role' => 'invalid-role'], $tokenAdmin);
test('POST → 422 (invalid role)', $r['status'] === 422, "status={$r['status']}");

$r = apiCall('POST', "$base/admin/praktikum/not-a-uuid/assign-asprak", ['user_id' => $userId2, 'role' => 'koor'], $tokenAdmin);
test('POST → 404 (invalid praktikum_id format)', $r['status'] === 404, "status={$r['status']}");

$r = apiCall('POST', "$base/admin/praktikum/$praktikumId/assign-asprak", ['user_id' => $userId2, 'role' => 'asprak', 'deskripsi' => str_repeat('A', 501)], $tokenAdmin);
test('POST → 422 (deskripsi too long)', $r['status'] === 422, "status={$r['status']}");

$fakeUuid = '00000000-0000-0000-0000-000000000000';
$r = apiCall('POST', "$base/admin/praktikum/$fakeUuid/assign-asprak", ['user_id' => $userId2, 'role' => 'koor'], $tokenAdmin);
test('POST → 404 (praktikum not found)', $r['status'] === 404, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// 6. PERMISSION CHECK
// ═══════════════════════════════════════════════════
echo "\n── 6. Permission Check ──\n";
$r = apiCall('GET', "$base/admin/praktikum/$praktikumId/asparaks", null, $tokenNonAdmin);
test('GET → 403 (non-admin access)', $r['status'] === 403, "status={$r['status']}");

$r = apiCall('POST', "$base/admin/praktikum/$praktikumId/assign-asprak", ['user_id' => $userId2, 'role' => 'koor'], $tokenNonAdmin);
test('POST → 403 (non-admin assign)', $r['status'] === 403, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// 7. UNASSIGN
// ═══════════════════════════════════════════════════
echo "\n── 7. Unassign Asprak ──\n";
$r = apiCall('DELETE', "$base/admin/praktikum/$praktikumId/asprak/$asprakRecordId", null, $tokenAdmin);
test('DELETE → 200 (valid unassign)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('DELETE', "$base/admin/praktikum/$praktikumId/asprak/$asprakRecordId", null, $tokenAdmin);
test('DELETE → 404 (already deleted/not found)', $r['status'] === 404, "status={$r['status']}");

$r = apiCall('DELETE', "$base/admin/praktikum/$praktikumId/asprak/9999", null, $tokenAdmin);
test('DELETE → 404 (invalid asprak_id)', $r['status'] === 404, "status={$r['status']}");

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
