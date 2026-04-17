@props([
    'required' => false,
])

<label {{ $attributes->merge(['class' => 'text-sm font-semibold leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-slate-800']) }}>
    {{ $slot }}
    @if($required)
        <span class="text-destructive ml-0.5">*</span>
    @endif
</label>
