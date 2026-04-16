{{-- resources/views/components/ui/card-header.blade.php --}}
@props([])
<div {{ $attributes->merge(['class' => 'px-6 pt-6 pb-2 flex flex-col gap-1.5']) }}>
    {{ $slot }}
</div>
