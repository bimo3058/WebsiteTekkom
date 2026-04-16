{{-- resources/views/components/ui/card.blade.php --}}
@props([])
<div {{ $attributes->merge(['class' => 'rounded-lg border border-border bg-card text-card-foreground shadow-sm']) }}>
    {{ $slot }}
</div>

{{-- 
    Sub-components: Buat file terpisah untuk masing-masing.
    Atau bisa langsung pakai div dengan class di bawah:

    Card Header:  px-6 pt-6 pb-2 flex flex-col gap-1.5
    Card Title:   text-lg font-semibold leading-none tracking-tight
    Card Description: text-sm text-muted-foreground
    Card Content: px-6 py-4
    Card Footer:  px-6 pb-6 pt-0 flex items-center
--}}
