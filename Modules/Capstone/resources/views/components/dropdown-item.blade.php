@props([
    'as' => 'button',
])
@php
    $classes = 'relative flex w-full cursor-pointer items-center gap-2 rounded-radius px-2 py-1.5 text-sm outline-none transition-colors hover:bg-surface-alt hover:text-on-surface-strong focus:bg-surface-alt focus:text-on-surface-strong data-[disabled]:pointer-events-none data-[disabled]:opacity-50';
@endphp
@if ($as === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }} role="menuitem">{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }} role="menuitem">{{ $slot }}</button>
@endif
