@props([
    'disabled' => false,
    'error' => false,
])

@php
    $baseClass = 'flex h-10 w-full rounded-lg border bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-0 disabled:cursor-not-allowed disabled:opacity-50';
    
    $errorClass = $error 
        ? 'border-red-300 focus-visible:border-red-500 focus-visible:ring-red-100' 
        : 'border-slate-200 focus-visible:border-primary focus-visible:ring-primary/10';

    $classes = $baseClass . ' ' . $errorClass;
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>
