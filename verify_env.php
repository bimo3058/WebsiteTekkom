<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '--- 1. Supabase Connection ---' . PHP_EOL;
echo 'Default DB: ' . config('database.default') . PHP_EOL;
try {
    DB::connection()->getPdo();
    echo 'PDO Connection: Success' . PHP_EOL;
} catch (\Exception $e) {
    echo 'PDO Connection: Failed - ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . '--- 2. Migrations Status ---' . PHP_EOL;
Artisan::call('migrate:status');
$output = Artisan::output();
$lines = explode(PHP_EOL, $output);
$pending = false;
foreach ($lines as $line) {
    if (str_contains($line, 'Pending')) {
        $pending = true;
        echo $line . PHP_EOL;
    }
}
if (!$pending) echo 'All migrations are Ran.' . PHP_EOL;

echo PHP_EOL . '--- 3. Data Consistency ---' . PHP_EOL;
try {
    $penggunaCount = DB::table('pengguna')->count();
    echo 'App\Models\Pengguna count: ' . $penggunaCount . PHP_EOL;
} catch (\Exception $e) {
    echo 'App\Models\Pengguna count: Error - ' . $e->getMessage() . PHP_EOL;
}

try {
    $praktikumCount = DB::table('eo_praktikum')->count();
    echo 'App\Models\Praktikum count: ' . $praktikumCount . PHP_EOL;
} catch (\Exception $e) {
    echo 'App\Models\Praktikum count: Error - ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . '--- 4. FK Constraints ---' . PHP_EOL;
$fkCount = DB::table('information_schema.table_constraints')
    ->where('constraint_type', 'FOREIGN KEY')
    ->count();
echo 'Total Foreign Keys: ' . $fkCount . PHP_EOL;

echo PHP_EOL . '--- 5. Git Sync ---' . PHP_EOL;
system('git status');
