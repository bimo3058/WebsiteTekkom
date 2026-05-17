g@props([
    'variant' => 'default',
    'size' => 'default',
    'as' => 'button',
    'href' => null,
])

@php
    $baseClass = 'inline-flex items-center justify-center whitespace-nowrap rounded-lg text-sm font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';
    
    $variants = [
        'default' => 'bg-primary text-white hover:bg-primary/90 shadow-sm',
        'destructive' => 'bg-red-600 text-white hover:bg-red-700 shadow-sm',
        'outline' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
        'secondary' => 'bg-slate-100 text-slate-900 hover:bg-slate-200',
        'ghost' => 'text-slate-700 hover:bg-slate-50',
        'soft' => 'bg-primary/10 text-primary hover:bg-primary/20',
        'link' => 'text-primary underline-offset-4 hover:underline',
    ];
    
    $sizes = [
        'default' => 'h-10 px-5 py-2.5',
        'sm' => 'h-9 px-4 py-2',
        'lg' => 'h-11 px-8 py-3',
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
