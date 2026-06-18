<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$cols = DB::select("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = 'daftar_praktikan' ORDER BY ordinal_position");
echo "=== Kolom tabel daftar_praktikan ===\n";
foreach ($cols as $col) {
    echo "  {$col->column_name} | {$col->data_type} | nullable: {$col->is_nullable}\n";
}
