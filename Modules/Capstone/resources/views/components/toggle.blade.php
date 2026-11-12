@props([
    'checked' => false,
    'name' => null,
    'size' => 'default',
])
@php
    $sizes = [
        'sm' => ['track' => 'h-[18px] w-8', 'thumb' => 'size-3.5', 'translate' => 'translate-x-3.5'],
        'default' => ['track' => 'h-[22px] w-10', 'thumb' => 'size-4', 'translate' => 'translate-x-[18px]'],
        'lg' => ['track' => 'h-[26px] w-12', 'thumb' => 'size-5', 'translate' => 'translate-x-[22px]'],
    ];
    $s = $sizes[$size] ?? $sizes['default'];
@endphp
<label class="relative inline-flex cursor-pointer items-center">
    <input type="checkbox" @if ($name) name="{{ $name }}" @endif {{ $checked ? 'checked' : '' }} class="peer sr-only" {{ $attributes->except(['class']) }}>
    <span class="{{ $s['track'] }} rounded-full bg-outline transition-colors peer-checked:bg-primary peer-focus-visible:ring-2 peer-focus-visible:ring-outline-strong peer-focus-visible:ring-offset-2 peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
        <span class="{{ $s['thumb'] }} absolute top-[3px] left-[3px] rounded-full bg-white shadow-sm transition-transform peer-checked:{{ $s['translate'] }}"></span>
    </span>
</label>
