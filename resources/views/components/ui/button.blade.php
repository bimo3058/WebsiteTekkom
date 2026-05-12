{{-- resources/views/components/ui/button.blade.php --}}
@props([
    'variant' => 'default',
    'size' => 'default',
    'as' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0';

    $variants = [
        'default'     => 'bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm',
        'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/90 shadow-sm',
        'outline'     => 'border border-input bg-background hover:bg-accent hover:text-accent-foreground shadow-xs',
        'secondary'   => 'bg-secondary text-secondary-foreground hover:bg-secondary/80 shadow-xs',
        'ghost'       => 'hover:bg-accent hover:text-accent-foreground',
        'link'        => 'text-primary underline-offset-4 hover:underline',
    ];

    $sizes = [
        'default' => 'h-9 px-4 py-2 text-sm',
        'sm'      => 'h-8 rounded-md px-3 text-xs',
        'lg'      => 'h-10 rounded-md px-6 text-sm',
        'icon'    => 'h-9 w-9',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['default']);
@endphp

@if($as === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
