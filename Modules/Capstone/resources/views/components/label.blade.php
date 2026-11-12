@props([])
<label {{ $attributes->merge(['class' => 'text-sm font-medium leading-none text-on-surface peer-disabled:cursor-not-allowed peer-disabled:opacity-70']) }}>{{ $slot }}</label>
