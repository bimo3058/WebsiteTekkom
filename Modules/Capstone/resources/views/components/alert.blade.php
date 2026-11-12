@props([
    'variant' => 'default',
])
@php
    $base = 'relative w-full rounded-radius border px-4 py-3 text-sm';
    $variants = [
        'default' => 'bg-surface text-on-surface border-outline',
        'destructive' => 'bg-danger/5 text-danger border-danger/30',
        'success' => 'bg-success/5 text-success border-success/30',
        'warning' => 'bg-warning/5 text-warning border-warning/30',
    ];
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['default']);
@endphp
<div role="alert" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</div>
