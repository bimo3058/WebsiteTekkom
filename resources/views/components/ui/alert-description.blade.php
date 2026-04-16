{{-- resources/views/components/ui/alert-description.blade.php --}}
@props([])
<div {{ $attributes->merge(['class' => 'text-sm [&_p]:leading-relaxed']) }}>
    {{ $slot }}
</div>
