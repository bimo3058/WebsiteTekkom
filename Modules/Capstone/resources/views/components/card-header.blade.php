@props([])
<div {{ $attributes->merge(['class' => 'flex flex-col gap-1.5 px-6 pt-6 pb-2']) }}>{{ $slot }}</div>
