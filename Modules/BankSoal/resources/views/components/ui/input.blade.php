@props([
    'disabled' => false,
    'error' => false,
])

@php
    $baseClass = 'flex h-10 w-full rounded-xl border bg-transparent px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-slate-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors';
    
    $errorClass = $error 
        ? 'border-destructive focus-visible:ring-destructive/20 focus-visible:border-destructive' 
        : 'border-input focus-visible:ring-primary/20 focus-visible:border-primary';

    $classes = $baseClass . ' ' . $errorClass;
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>
