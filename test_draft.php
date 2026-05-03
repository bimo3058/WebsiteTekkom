<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\ManajemenMahasiswa\Models\PengumumanDraft;

$request = Illuminate\Http\Request::create('/manajemen-mahasiswa/pengumuman/drafts', 'POST', [
    'judul' => 'Test',
    'kategori' => '',
    'target_audience' => 'all',
    'konten' => 'Test konten',
    'draft_id' => '',
    '_token' => 'dummy_token'
], [], [
    'lampiran' => [
        ['error' => UPLOAD_ERR_NO_FILE, 'name' => '', 'type' => '', 'tmp_name' => '', 'size' => 0]
    ]
]);
$request->headers->set('Accept', 'application/json');

$controller = $app->make(\Modules\ManajemenMahasiswa\Http\Controllers\PengumumanController::class);
Auth::loginUsingId(1);

try {
    $response = $controller->saveDraft($request);
    echo "Response: " . json_encode($response->getData()) . "\n";
} catch (\Exception $e) {
    if (method_exists($e, 'errors')) {
        echo "Errors: " . json_encode($e->errors()) . "\n";
    } else {
        echo "Exception: " . $e->getMessage() . "\n";
    }
}
