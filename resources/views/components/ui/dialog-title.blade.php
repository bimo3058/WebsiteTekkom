{{-- resources/views/components/ui/dialog-title.blade.php --}}
@props([])
<h2 {{ $attributes->merge(['class' => 'text-lg font-semibold leading-none tracking-tight']) }}>
    {{ $slot }}
</h2>
