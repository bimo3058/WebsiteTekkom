{{-- resources/views/components/ui/label.blade.php --}}
@props([])

<label {{ $attributes->merge([
    'class' => 'text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70',
]) }}>
    {{ $slot }}
</label>
