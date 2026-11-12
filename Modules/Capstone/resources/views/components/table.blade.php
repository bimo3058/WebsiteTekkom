@props([])
<div {{ $attributes->merge(['class' => 'relative w-full overflow-auto']) }}>
    <table class="w-full caption-bottom text-sm text-on-surface">{{ $slot }}</table>
</div>
