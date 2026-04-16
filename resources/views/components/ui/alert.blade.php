{{-- resources/views/components/ui/alert.blade.php --}}
@props([
    'variant' => 'default',
])

@php
    $base = 'relative w-full rounded-lg border px-4 py-3 text-sm [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground [&>svg~*]:pl-7';

    $variants = [
        'default'     => 'bg-background text-foreground border-border',
        'destructive' => 'bg-destructive-50 text-destructive-300 border-destructive/30 [&>svg]:text-destructive',
        'success'     => 'bg-success-50 text-success-300 border-success-200/30',
        'warning'     => 'bg-warning-50 text-warning-300 border-warning-200/30',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['default']);
@endphp

<div role="alert" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
