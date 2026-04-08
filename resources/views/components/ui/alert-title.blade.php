{{-- resources/views/components/ui/alert-title.blade.php --}}
@props([])
<h5 {{ $attributes->merge(['class' => 'mb-1 font-semibold leading-none tracking-tight']) }}>
    {{ $slot }}
</h5>
