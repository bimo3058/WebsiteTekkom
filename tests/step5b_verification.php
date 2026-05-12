<?php
/**
 * Step 5B — Admin: Assign Koor Verification
 * Jalankan: php tests/step5b_verification.php
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
echo "  STEP 5B VERIFICATION — Assign Koor\n";
echo "══════════════════════════════════════════════════\n\n";

// ── Setup: Login users ──
echo "── SETUP ──\n";
$r = apiCall('POST', "$base/auth/login", ['email' => 'admin@praktikum.ac.id', 'password' => 'password123']);
if ($r['status'] !== 200) die("❌ Admin login failed: " . $r['raw'] . "\n");
$tokenAdmin = $r['body']['data']['token'];
echo "  Admin token: OK\n";

$r = apiCall('POST', "$base/auth/login", ['email' => 'ahmad.fauzi@praktikum.ac.id', 'password' => 'password123']);
if ($r['status'] !== 200) die("❌ Dosen login failed: " . $r['raw'] . "\n");
$tokenDosen = $r['body']['data']['token'];

$r = apiCall('POST', "$base/auth/login", ['email' => 'citra@mhs.ac.id', 'password' => 'password123']);
if ($r['status'] !== 200) die("❌ Mahasiswa login failed: " . $r['raw'] . "\n");
$tokenMhs = $r['body']['data']['token'];

$r = apiCall('GET', "$base/auth/me", null, $tokenMhs);
$mhsId = $r['body']['data']['user']['id'];
echo "  Mahasiswa (Citra) ID: $mhsId\n";

$r = apiCall('POST', "$base/auth/login", ['email' => 'andi@mhs.ac.id', 'password' => 'password123']);
if ($r['status'] !== 200) die("❌ Koor login failed: " . $r['raw'] . "\n");
$tokenAndi = $r['body']['data']['token'];
$r = apiCall('GET', "$base/auth/me", null, $tokenAndi);
$andiId = $r['body']['data']['user']['id'];
echo "  Koor (Andi) ID: $andiId\n\n";

// Get a praktikum
$r = apiCall('GET', "$base/admin/praktikum", null, $tokenAdmin);
$praktikum = $r['body']['data'][0] ?? null;
if (!$praktikum) {
    die("❌ No praktikum found. Run migrations and seeders first.\n");
}
$praktikumId = $praktikum['id'];
echo "  Praktikum Target ID: $praktikumId\n\n";

// ═══════════════════════════════════════════════════
// 1. AUTH & SECURITY
// ═══════════════════════════════════════════════════
echo "── 1. Security & Auth ──\n";

$r = apiCall('PUT', "$base/admin/praktikum/$praktikumId/assign-koor");
test('PUT → 401 (no token)', $r['status'] === 401, "status={$r['status']}");

$r = apiCall('PUT', "$base/admin/praktikum/$praktikumId/assign-koor", null, 'invalid-token-123');
test('PUT → 401 (invalid token)', $r['status'] === 401, "status={$r['status']}");

$r = apiCall('PUT', "$base/admin/praktikum/$praktikumId/assign-koor", ['pengguna_id' => $andiId], $tokenDosen);
test('PUT → 403 (non-admin)', $r['status'] === 403, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// 2. VALIDATION
// ═══════════════════════════════════════════════════
echo "\n── 2. Validation ──\n";

$r = apiCall('PUT', "$base/admin/praktikum/$praktikumId/assign-koor", [], $tokenAdmin);
test('PUT → 422 (missing pengguna_id)', $r['status'] === 422, "status={$r['status']}");

$r = apiCall('PUT', "$base/admin/praktikum/non-existent-uuid/assign-koor", ['pengguna_id' => $andiId], $tokenAdmin);
test('PUT → 404 (invalid praktikum_id)', $r['status'] === 404, "status={$r['status']}");

$r = apiCall('PUT', "$base/admin/praktikum/$praktikumId/assign-koor", ['pengguna_id' => 'not-a-uuid'], $tokenAdmin);
test('PUT → 422 (invalid pengguna_id format)', $r['status'] === 422, "status={$r['status']}");

$fakeUuid = '00000000-0000-0000-0000-000000000000';
$r = apiCall('PUT', "$base/admin/praktikum/$praktikumId/assign-koor", ['pengguna_id' => $fakeUuid], $tokenAdmin);
test('PUT → 422 (pengguna_id not found)', $r['status'] === 422, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// 3. SUCCESS ASSIGNMENT
// ═══════════════════════════════════════════════════
echo "\n── 3. Assignment ──\n";

$r = apiCall('PUT', "$base/admin/praktikum/$praktikumId/assign-koor", ['pengguna_id' => $andiId], $tokenAdmin);
test('PUT → 200 (valid assign existing koor)', $r['status'] === 200, "status={$r['status']}");
test('Response has koordinator assigned', ($r['body']['data']['koordinator']['id'] ?? '') === $andiId);

// Test Auto-assign role (Mahasiswa -> Koor)
$r = apiCall('PUT', "$base/admin/praktikum/$praktikumId/assign-koor", ['pengguna_id' => $mhsId], $tokenAdmin);
test('PUT → 200 (replace existing koor & auto-grant role)', $r['status'] === 200, "status={$r['status']}");
test('Response has NEW koordinator assigned', ($r['body']['data']['koordinator']['id'] ?? '') === $mhsId);

// Verify role in DB (Via Tinker)
$tinkerCmd = "php artisan tinker --execute \"echo 'HasRole: ' . (\App\Models\Pengguna::find('$mhsId')->hasRole('koor_prak') ? 'YES' : 'NO');\"";
$out = [];
exec($tinkerCmd, $out);
$hasRole = str_contains(implode("\n", $out), 'HasRole: YES');
test('Database: User auto-granted koor_prak role', $hasRole);

$tinkerCmd = "php artisan tinker --execute \"echo 'KoorID: ' . \App\Models\Praktikum::find('$praktikumId')->koor_id;\"";
$out = [];
exec($tinkerCmd, $out);
$dbKoorId = str_contains(implode("\n", $out), "KoorID: $mhsId");
test('Database: koor_id updated', $dbKoorId);

// ═══════════════════════════════════════════════════
// 4. READ & RESPONSE CONSISTENCY
// ═══════════════════════════════════════════════════
echo "\n── 4. Read Verification ──\n";

$r = apiCall('GET', "$base/admin/praktikum/$praktikumId", null, $tokenAdmin);
test('GET {id}: shows updated koor', ($r['body']['data']['koordinator']['id'] ?? '') === $mhsId);

$r = apiCall('GET', "$base/admin/praktikum", null, $tokenAdmin);
$listPrak = collect($r['body']['data'] ?? [])->firstWhere('id', $praktikumId);
test('GET list: koor info correct', ($listPrak['koordinator']['id'] ?? '') === $mhsId);

test('Response format consistent', isset($r['body']['success']) && $r['body']['success'] === true);

// Clean up: Re-assign to Andi to keep seed data mostly clean
apiCall('PUT', "$base/admin/praktikum/$praktikumId/assign-koor", ['pengguna_id' => $andiId], $tokenAdmin);

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

// Polyfill for array_first equivalent
function collect($array) {
    return new class($array) {
        public $data;
        public function __construct($data) { $this->data = $data; }
        public function firstWhere($key, $val) {
            foreach($this->data as $item) if(($item[$key]??null) === $val) return $item;
            return null;
        }
    };
}
exit($passed === $total ? 0 : 1);
