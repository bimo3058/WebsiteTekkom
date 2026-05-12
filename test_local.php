<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

function makeRequest($kernel, $method, $url, $body = null, $token = null) {
    $serverVars = ['CONTENT_TYPE' => 'application/json'];
    $request = Illuminate\Http\Request::create($url, $method, [], [], [], $serverVars, $body ? json_encode($body) : null);
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('Content-Type', 'application/json');
    if ($token) $request->headers->set('Authorization', "Bearer $token");
    return $kernel->handle($request);
}

echo "=== STEP 7 DEBUG ===\n\n";

// 1. Login
$res = makeRequest($kernel, 'POST', '/api/auth/login', ['email' => 'admin@praktikum.ac.id', 'password' => 'password123']);
$data = json_decode($res->getContent(), true);
echo "1. Login: HTTP " . $res->getStatusCode() . "\n";
if ($res->getStatusCode() !== 200) {
    echo "   Error: " . json_encode($data) . "\n";
    exit(1);
}
$token = $data['data']['token'];
$roleAktif = $data['data']['role_aktif'];
echo "   Role aktif: $roleAktif\n";
echo "   Token: ...{$token}\n\n";

// 2. GET praktikum list
$res = makeRequest($kernel, 'GET', '/api/admin/praktikum', null, $token);
$data = json_decode($res->getContent(), true);
echo "2. GET /admin/praktikum: HTTP " . $res->getStatusCode() . "\n";
if ($res->getStatusCode() !== 200) {
    echo "   Error: " . $res->getContent() . "\n";
    exit(1);
}
$praktikumId = $data['data'][0]['id'] ?? null;
echo "   Praktikum ID: $praktikumId\n\n";

// 3. GET asparaks (empty)
$res = makeRequest($kernel, 'GET', "/api/admin/praktikum/$praktikumId/asparaks", null, $token);
echo "3. GET /admin/praktikum/{id}/asparaks: HTTP " . $res->getStatusCode() . "\n";
echo "   Response: " . $res->getContent() . "\n\n";

// 4. GET first user_id from users table
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$userId = \Illuminate\Support\Facades\DB::table('users')->first()?->id;
echo "4. First user id from users table: $userId\n\n";

// 5. POST assign-asprak
$res = makeRequest($kernel, 'POST', "/api/admin/praktikum/$praktikumId/assign-asprak", ['user_id' => $userId, 'role' => 'asprak'], $token);
echo "5. POST /admin/praktikum/{id}/assign-asprak: HTTP " . $res->getStatusCode() . "\n";
echo "   Response: " . $res->getContent() . "\n";
