@props([])
<textarea {{ $attributes->merge(['class' => 'flex min-h-24 w-full rounded-radius border border-outline bg-surface px-3 py-2 text-sm text-on-surface shadow-xs outline-none placeholder:text-on-surface/50 focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-50']) }}>{{ $slot }}</textarea>
