@props([])
<div {{ $attributes->merge(['class' => 'mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end']) }}>{{ $slot }}</div>
