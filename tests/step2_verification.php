<?php
/**
 * Step 2 — Middleware Verification Script
 * Jalankan: php tests/step2_verification.php
 *
 * Test ALL middleware scenarios:
 * - IsAdmin
 * - HasRole (CheckRole) with hierarchy
 * - PraktikumAccess (admin, dosen own/other, etc.)
 * - Auth guards (no token, invalid token)
 */

$base = 'http://127.0.0.1:8000/api';
$results = [];
$tokens  = [];
$praktikumIds = [];

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

echo "\n══════════════════════════════════════════════════\n";
echo "  STEP 2 VERIFICATION — Middleware Tests\n";
echo "══════════════════════════════════════════════════\n\n";

// ═══════════════════════════════════════════════════
// SETUP: Login all users
// ═══════════════════════════════════════════════════
echo "── SETUP: Login all users ──\n";

// Admin
$r = apiCall('POST', "$base/auth/login", ['email' => 'admin@praktikum.ac.id', 'password' => 'password123']);
$tokens['admin'] = $r['body']['data']['token'];
echo "  Admin token: OK\n";

// Dosen 1 (Ahmad Fauzi — ampu Praktikum 1, 2)
$r = apiCall('POST', "$base/auth/login", ['email' => 'ahmad.fauzi@praktikum.ac.id', 'password' => 'password123']);
$tokens['dosen1'] = $r['body']['data']['token'];
echo "  Dosen1 token: OK\n";

// Dosen 2 (Siti Rahayu — ampu Praktikum 3, 4)
$r = apiCall('POST', "$base/auth/login", ['email' => 'siti.rahayu@praktikum.ac.id', 'password' => 'password123']);
$tokens['dosen2'] = $r['body']['data']['token'];
echo "  Dosen2 token: OK\n";

// Koor (Andi — koor_prak di Praktikum 1)
$r = apiCall('POST', "$base/auth/login", ['email' => 'andi@mhs.ac.id', 'password' => 'password123']);
$tokens['koor'] = $r['body']['data']['token'];
// Switch ke koor_prak
apiCall('POST', "$base/auth/switch-role", ['role' => 'koor_prak'], $tokens['koor']);
echo "  Koor token: OK (switched to koor_prak)\n";

// Asprak (Budi — asprak)
$r = apiCall('POST', "$base/auth/login", ['email' => 'budi@mhs.ac.id', 'password' => 'password123']);
$tokens['asprak'] = $r['body']['data']['token'];
// Switch ke asprak
apiCall('POST', "$base/auth/switch-role", ['role' => 'asprak'], $tokens['asprak']);
echo "  Asprak token: OK (switched to asprak)\n";

// Mahasiswa (Citra — mahasiswa saja)
$r = apiCall('POST', "$base/auth/login", ['email' => 'citra@mhs.ac.id', 'password' => 'password123']);
$tokens['mahasiswa'] = $r['body']['data']['token'];
echo "  Mahasiswa token: OK\n";

// Ambil praktikum IDs via tinker approach — via DB
echo "\n── SETUP: Get Praktikum IDs ──\n";
$dbPath = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO("sqlite:$dbPath");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->query("SELECT id, nama, dosen_id FROM praktikum ORDER BY nama");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $praktikumIds[$row['nama']] = $row['id'];
    echo "  {$row['nama']}: {$row['id']}\n";
}
$pdo = null;

$pidPemrog  = $praktikumIds['Praktikum Pemrograman Dasar'];  // dosen1
$pidBasisDB = $praktikumIds['Praktikum Basis Data'];          // dosen1
$pidJarkom  = $praktikumIds['Praktikum Jaringan Komputer'];   // dosen2
$pidSisop   = $praktikumIds['Praktikum Sistem Operasi'];      // dosen2

// ═══════════════════════════════════════════════════
// TEST GROUP 1: IsAdmin Middleware
// ═══════════════════════════════════════════════════
echo "\n── 1. IsAdmin Middleware ──\n";

$r = apiCall('GET', "$base/admin/test", null, $tokens['admin']);
test('Admin → /admin/test → 200', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/test", null, $tokens['dosen1']);
test('Dosen → /admin/test → 403', $r['status'] === 403, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/test", null, $tokens['koor']);
test('Koor → /admin/test → 403', $r['status'] === 403, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/test", null, $tokens['asprak']);
test('Asprak → /admin/test → 403', $r['status'] === 403, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/test", null, $tokens['mahasiswa']);
test('Mahasiswa → /admin/test → 403', $r['status'] === 403, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// TEST GROUP 2: HasRole (CheckRole) with Hierarchy
// ═══════════════════════════════════════════════════
echo "\n── 2. HasRole (Hierarchy) ──\n";

// /asprak/test → min asprak (level 2)
$r = apiCall('GET', "$base/asprak/test", null, $tokens['admin']);
test('Admin → /asprak/test → 200 (admin>asprak)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/asprak/test", null, $tokens['dosen1']);
test('Dosen → /asprak/test → 200 (dosen>asprak)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/asprak/test", null, $tokens['koor']);
test('Koor → /asprak/test → 200 (koor≥asprak)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/asprak/test", null, $tokens['asprak']);
test('Asprak → /asprak/test → 200 (asprak=asprak)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/asprak/test", null, $tokens['mahasiswa']);
test('Mahasiswa → /asprak/test → 403 (mhs<asprak)', $r['status'] === 403, "status={$r['status']}");

// /koor/test → min koor_prak (level 3)
$r = apiCall('GET', "$base/koor/test", null, $tokens['koor']);
test('Koor → /koor/test → 200 (koor=koor)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/koor/test", null, $tokens['asprak']);
test('Asprak → /koor/test → 403 (asprak<koor)', $r['status'] === 403, "status={$r['status']}");

$r = apiCall('GET', "$base/koor/test", null, $tokens['admin']);
test('Admin → /koor/test → 200 (admin>koor)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/koor/test", null, $tokens['dosen1']);
test('Dosen → /koor/test → 200 (dosen>koor)', $r['status'] === 200, "status={$r['status']}");

// /dosen/test → min dosen (level 4)
$r = apiCall('GET', "$base/dosen/test", null, $tokens['dosen1']);
test('Dosen → /dosen/test → 200 (dosen=dosen)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/dosen/test", null, $tokens['admin']);
test('Admin → /dosen/test → 200 (admin>dosen)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/dosen/test", null, $tokens['koor']);
test('Koor → /dosen/test → 403 (koor<dosen)', $r['status'] === 403, "status={$r['status']}");

// /mahasiswa/test → min mahasiswa (level 1) — everyone passes
$r = apiCall('GET', "$base/mahasiswa/test", null, $tokens['mahasiswa']);
test('Mahasiswa → /mahasiswa/test → 200', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/mahasiswa/test", null, $tokens['asprak']);
test('Asprak → /mahasiswa/test → 200', $r['status'] === 200, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// TEST GROUP 3: PraktikumAccess Middleware
// ═══════════════════════════════════════════════════
echo "\n── 3. PraktikumAccess ──\n";

// Admin → akses semua praktikum
$r = apiCall('GET', "$base/admin/praktikum/$pidPemrog/test", null, $tokens['admin']);
test('Admin → praktikum dosen1 → 200 (admin bypass)', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/praktikum/$pidJarkom/test", null, $tokens['admin']);
test('Admin → praktikum dosen2 → 200 (admin bypass)', $r['status'] === 200, "status={$r['status']}");

// Dosen1 → akses own praktikum
$r = apiCall('GET', "$base/dosen/praktikum/$pidPemrog/test", null, $tokens['dosen1']);
test('Dosen1 → own praktikum (PemrogDasar) → 200', $r['status'] === 200, "status={$r['status']}");

$r = apiCall('GET', "$base/dosen/praktikum/$pidBasisDB/test", null, $tokens['dosen1']);
test('Dosen1 → own praktikum (BasisData) → 200', $r['status'] === 200, "status={$r['status']}");

// Dosen1 → akses other dosen's praktikum → 403
$r = apiCall('GET', "$base/dosen/praktikum/$pidJarkom/test", null, $tokens['dosen1']);
test('Dosen1 → other praktikum (Jarkom/dosen2) → 403', $r['status'] === 403, "status={$r['status']}");

$r = apiCall('GET', "$base/dosen/praktikum/$pidSisop/test", null, $tokens['dosen1']);
test('Dosen1 → other praktikum (Sisop/dosen2) → 403', $r['status'] === 403, "status={$r['status']}");

// Dosen2 → akses own praktikum
$r = apiCall('GET', "$base/dosen/praktikum/$pidJarkom/test", null, $tokens['dosen2']);
test('Dosen2 → own praktikum (Jarkom) → 200', $r['status'] === 200, "status={$r['status']}");

// Dosen2 → akses dosen1's praktikum → 403
$r = apiCall('GET', "$base/dosen/praktikum/$pidPemrog/test", null, $tokens['dosen2']);
test('Dosen2 → other praktikum (PemrogDasar/dosen1) → 403', $r['status'] === 403, "status={$r['status']}");

// Non-existent praktikum → 404
$fakeUuid = '00000000-0000-0000-0000-000000000000';
$r = apiCall('GET', "$base/admin/praktikum/$fakeUuid/test", null, $tokens['admin']);
test('Admin → non-existent praktikum → pass (admin bypass)', $r['status'] === 200 || $r['status'] === 404, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// TEST GROUP 4: Auth Guards (no token, invalid token)
// ═══════════════════════════════════════════════════
echo "\n── 4. Auth Guards ──\n";

$r = apiCall('GET', "$base/admin/test");
test('No token → /admin/test → 401', $r['status'] === 401, "status={$r['status']}");

$r = apiCall('GET', "$base/dosen/test");
test('No token → /dosen/test → 401', $r['status'] === 401, "status={$r['status']}");

$r = apiCall('GET', "$base/admin/test", null, 'invalid-token-123');
test('Invalid token → /admin/test → 401', $r['status'] === 401, "status={$r['status']}");

$r = apiCall('GET', "$base/asprak/test", null, 'bad-token');
test('Invalid token → /asprak/test → 401', $r['status'] === 401, "status={$r['status']}");

// ═══════════════════════════════════════════════════
// TEST GROUP 5: Switch-role + re-test hierarchy
// ═══════════════════════════════════════════════════
echo "\n── 5. Switch-role → Re-test ──\n";

// Andi (koor) switch ke mahasiswa → should not access /koor/test
apiCall('POST', "$base/auth/switch-role", ['role' => 'mahasiswa'], $tokens['koor']);
$r = apiCall('GET', "$base/koor/test", null, $tokens['koor']);
test('Koor→switch(mhs) → /koor/test → 403', $r['status'] === 403, "status={$r['status']}");

// Switch back ke koor_prak → should access /koor/test
apiCall('POST', "$base/auth/switch-role", ['role' => 'koor_prak'], $tokens['koor']);
$r = apiCall('GET', "$base/koor/test", null, $tokens['koor']);
test('Koor→switch(koor_prak) → /koor/test → 200', $r['status'] === 200, "status={$r['status']}");

// Budi (asprak) switch ke mahasiswa → should not access /asprak/test
apiCall('POST', "$base/auth/switch-role", ['role' => 'mahasiswa'], $tokens['asprak']);
$r = apiCall('GET', "$base/asprak/test", null, $tokens['asprak']);
test('Asprak→switch(mhs) → /asprak/test → 403', $r['status'] === 403, "status={$r['status']}");

// Switch back ke asprak
apiCall('POST', "$base/auth/switch-role", ['role' => 'asprak'], $tokens['asprak']);
$r = apiCall('GET', "$base/asprak/test", null, $tokens['asprak']);
test('Asprak→switch(asprak) → /asprak/test → 200', $r['status'] === 200, "status={$r['status']}");

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
