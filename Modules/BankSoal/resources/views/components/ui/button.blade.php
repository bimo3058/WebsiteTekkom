@props([
    'variant' => 'default',
    'size' => 'default',
    'as' => 'button',
    'href' => null,
])

@php
    $baseClass = 'inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';
    
    $variants = [
        'default' => 'bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm',
        'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/90 shadow-sm',
        'outline' => 'border border-input bg-transparent hover:bg-slate-50 hover:text-slate-900 shadow-sm',
        'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        'ghost' => 'hover:bg-slate-50 hover:text-slate-900',
        'soft' => 'bg-primary/10 text-primary hover:bg-primary/20',
        'link' => 'text-primary underline-offset-4 hover:underline',
    ];
    
    $sizes = [
        'default' => 'h-10 px-4 py-2',
        'sm' => 'h-9 rounded-lg px-3',
        'lg' => 'h-11 rounded-xl px-8',
        'icon' => 'h-10 w-10',
    ];

    $classes = $baseClass . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['default']);
@endphp

@if($as === 'button')
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@endif
