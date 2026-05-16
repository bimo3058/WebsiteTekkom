@props([
    'required' => false,
])

<label {{ $attributes->merge(['class' => 'text-sm font-semibold text-slate-900 peer-disabled:cursor-not-allowed peer-disabled:opacity-50']) }}>
    {{ $slot }}
    @if($required)
        <span class="text-red-500 ml-0.5">*</span>
    @endif
</label>
