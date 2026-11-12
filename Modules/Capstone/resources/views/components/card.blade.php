@props([])
<div {{ $attributes->merge(['class' => 'rounded-xl border border-outline bg-surface text-on-surface shadow-xs']) }}>{{ $slot }}</div>
