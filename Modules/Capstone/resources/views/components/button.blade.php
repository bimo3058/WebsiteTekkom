@props([
    'variant' => 'default',
    'size' => 'default',
    'as' => 'button',
])
@php
    $base = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-radius font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-outline-strong focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';
    $variants = [
        'default' => 'bg-primary text-on-primary hover:bg-primary/90 shadow-xs',
        'destructive' => 'bg-danger text-on-danger hover:bg-danger/90 shadow-xs',
        'outline' => 'border border-outline bg-surface hover:bg-surface-alt hover:text-on-surface-strong shadow-xs',
        'secondary' => 'bg-surface-alt text-on-surface hover:bg-surface-alt/80 shadow-xs',
        'ghost' => 'hover:bg-surface-alt hover:text-on-surface-strong',
        'link' => 'text-primary underline-offset-4 hover:underline',
    ];
    $sizes = [
        'default' => 'h-9 px-4 py-2 text-sm',
        'xs' => 'h-7 rounded-radius px-2.5 text-xs',
        'sm' => 'h-8 rounded-radius px-3 text-xs',
        'lg' => 'h-10 rounded-radius px-6 text-sm',
        'icon' => 'size-9',
    ];
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['default']);
@endphp
@if ($as === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
