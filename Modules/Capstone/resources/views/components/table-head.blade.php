@props([])
<th {{ $attributes->merge(['class' => 'h-10 px-4 text-left align-middle font-medium text-on-surface/60 [&:has([role=checkbox])]:pr-0']) }}>{{ $slot }}</th>
