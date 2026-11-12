@props([])
<tr {{ $attributes->merge(['class' => 'border-b border-outline transition-colors hover:bg-surface-alt/50 data-[state=selected]:bg-surface-alt']) }}>{{ $slot }}</tr>
