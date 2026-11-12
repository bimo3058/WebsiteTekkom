@props([
    'orientation' => 'horizontal',
])
@php
    $classes = $orientation === 'vertical' ? 'h-full w-px bg-outline' : 'h-px w-full bg-outline';
@endphp
<div role="separator" {{ $attributes->merge(['class' => 'shrink-0 ' . $classes]) }}></div>
