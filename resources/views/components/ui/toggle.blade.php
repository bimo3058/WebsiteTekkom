{{-- resources/views/components/ui/toggle.blade.php --}}
@props([
    'checked' => false,
    'name'    => null,
    'size'    => 'default',
])

@php
    $sizes = [
        'sm'      => ['track' => 'w-8 h-[18px]',  'thumb' => 'size-3.5', 'translate' => 'translate-x-3.5'],
        'default' => ['track' => 'w-10 h-[22px]',  'thumb' => 'size-4',   'translate' => 'translate-x-[18px]'],
        'lg'      => ['track' => 'w-12 h-[26px]',  'thumb' => 'size-5',   'translate' => 'translate-x-[22px]'],
    ];
    $s = $sizes[$size] ?? $sizes['default'];
@endphp

<label class="relative inline-flex items-center cursor-pointer">
    <input type="checkbox"
           @if($name) name="{{ $name }}" @endif
           {{ $checked ? 'checked' : '' }}
           class="sr-only peer"
           {{ $attributes->except(['class']) }}>
    <div class="{{ $s['track'] }} rounded-full bg-grey-200 peer-checked:bg-primary transition-colors peer-focus-visible:ring-2 peer-focus-visible:ring-ring peer-focus-visible:ring-offset-2 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
        <div class="{{ $s['thumb'] }} absolute top-[3px] left-[3px] rounded-full bg-white shadow-sm transition-transform peer-checked:{{ $s['translate'] }}"></div>
    </div>
</label>
