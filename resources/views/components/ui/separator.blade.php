{{-- resources/views/components/ui/separator.blade.php --}}
@props([
    'orientation' => 'horizontal',
])

@php
    $classes = $orientation === 'vertical'
        ? 'w-px h-full bg-border'
        : 'h-px w-full bg-border';
@endphp

<div role="separator" {{ $attributes->merge(['class' => 'shrink-0 ' . $classes]) }}></div>
