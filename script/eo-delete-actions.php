<?php

// Extract explicit FK delete actions from migration source without running migrations.
$directory = dirname(__DIR__).'/Modules/EOffice/database/migrations';
$renameSource = file_get_contents($directory.'/2026_11_05_000002_standardize_practicum_table_names.php');
preg_match_all("/'(\w+)'\s*=>\s*'(\w+)'/", $renameSource, $renames, PREG_SET_ORDER);
$mapping = [];
foreach ($renames as $rename) {
    $mapping[$rename[1]] = $rename[2];
}
$actions = [];
foreach (glob($directory.'/*.php') as $path) {
    $source = '';
    foreach (token_get_all(file_get_contents($path)) as $token) {
        if (is_array($token)) {
            if (! in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                $source .= $token[1];
            }
        } else {
            $source .= $token;
        }
    }
    $source = preg_split('/public\s+function\s+down\s*\(/', $source)[0];
    preg_match_all("/Schema::(?:create|table)\(\s*'([^']+)'\s*,(.*?)(?:\}\);)/s", $source, $blocks, PREG_SET_ORDER);
    foreach ($blocks as $block) {
        $table = $mapping[$block[1]] ?? $block[1];
        preg_match_all('/\$\w+->(?:foreign|foreignId)\(\s*\x27([^\x27]+)\x27\s*\)(.*?);/s', $block[2], $chains, PREG_SET_ORDER);
        foreach ($chains as $chain) {
            if (! preg_match("/->(?:on|constrained)\(\s*'([^']+)'/", $chain[2], $target)) {
                continue;
            }
            $parent = $mapping[$target[1]] ?? $target[1];
            $action = 'NO ACTION';
            if (preg_match("/->onDelete\(\s*'([^']+)'/", $chain[2], $delete)) {
                $action = strtoupper($delete[1]);
            } elseif (str_contains($chain[2], 'cascadeOnDelete')) {
                $action = 'CASCADE';
            } elseif (str_contains($chain[2], 'nullOnDelete')) {
                $action = 'SET NULL';
            } elseif (str_contains($chain[2], 'restrictOnDelete')) {
                $action = 'RESTRICT';
            }
            $actions[$table.'.'.$chain[1].'->'.$parent] = ['action'=>$action, 'source'=>basename($path)];
        }
    }
}
return $actions;
