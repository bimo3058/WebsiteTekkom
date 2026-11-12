@props([])
<p {{ $attributes->merge(['class' => 'mt-1.5 text-sm text-on-surface/60']) }}>{{ $slot }}</p>
