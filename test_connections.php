<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check which columns exist and which are missing
$existingColumns = Illuminate\Support\Facades\Schema::getColumnListing('users');
echo "Existing columns in 'users' table:\n";
echo implode(', ', $existingColumns) . "\n\n";

// Columns referenced in User model but might be missing
$modelColumns = ['is_online', 'whatsapp', 'avatar_url', 'avatar_url_format'];
echo "Missing columns check:\n";
foreach ($modelColumns as $col) {
    $exists = in_array($col, $existingColumns) ? 'EXISTS' : 'MISSING';
    echo "  {$col}: {$exists}\n";
}
