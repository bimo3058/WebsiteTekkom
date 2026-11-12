@props([])
<p {{ $attributes->merge(['class' => 'text-sm text-on-surface/60']) }}>{{ $slot }}</p>
