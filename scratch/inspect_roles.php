<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::with('roles')->whereIn('id', [33, 34, 54])->get();
foreach ($users as $u) {
    echo "User: {$u->name} (Email: {$u->email})\n";
    echo "  Roles: " . implode(', ', $u->roles->pluck('name')->toArray()) . "\n\n";
}
