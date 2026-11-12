@props([
    'variant' => 'default',
    'size' => 'sm',
])
@php
    $base = $size === 'xs'
        ? 'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold transition-colors'
        : 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-outline-strong focus:ring-offset-2';
    $variants = [
        'default' => 'bg-primary text-on-primary hover:bg-primary/80',
        'secondary' => 'bg-surface-alt text-on-surface hover:bg-surface-alt/80',
        'destructive' => 'bg-danger text-on-danger hover:bg-danger/80',
        'outline' => 'text-on-surface border border-outline',
        'success' => 'bg-success/10 text-success border border-success/30',
        'warning' => 'bg-warning/10 text-warning border border-warning/30',
    ];
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['default']);
@endphp
<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>
