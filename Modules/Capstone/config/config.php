<?php

return [
    'name' => 'Capstone',
    'frontend_url' => env('CAPSTONE_FRONTEND_URL', 'http://localhost:3000'),
    'token_ttl_hours' => (int) env('CAPSTONE_TOKEN_TTL_HOURS', 8),
    'ott_ttl_seconds' => (int) env('CAPSTONE_OTT_TTL_SECONDS', 60),
    'http_cache' => [
        'enabled' => (bool) env('CAPSTONE_HTTP_CACHE_ENABLED', true),
        'ttl_seconds' => (int) env('CAPSTONE_HTTP_CACHE_TTL', 30),
        'max_bytes' => (int) env('CAPSTONE_HTTP_CACHE_MAX_BYTES', 1_048_576),
    ],
];
