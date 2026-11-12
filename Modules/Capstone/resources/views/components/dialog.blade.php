@props(['id', 'title'=>null, 'description'=>null, 'width'=>'max-w-lg'])
{{-- PenguinUI Modern modal: Alpine local state + focus trap + outside/Esc close.
     Canonical: $dispatch('open-{{ $id }}') / $dispatch('close-{{ $id }}').
     Legacy JS (document.getElementById(...).showModal()) keeps working via shim below. --}}
<div x-data="{ modalIsOpen: false }"
    @open-{{ $id }}.window="modalIsOpen = true"
    @close-{{ $id }}.window="modalIsOpen = false"
    x-init="$watch('modalIsOpen', v => { const d = document.getElementById('{{ $id }}'); if (!d) return; if (v && !d.open) d.showModal(); if (!v && d.open) d.close(); })">
<dialog id="{{ $id }}" x-trap="modalIsOpen" @close="modalIsOpen = false" @cancel="modalIsOpen = false" aria-labelledby="{{ $id }}-title" {{ $attributes->class(['fixed inset-0 m-auto w-[calc(100%-2rem)] max-h-[90vh] overflow-y-auto bg-surface text-on-surface gap-4 rounded-radius border border-outline p-6 shadow-lg backdrop:bg-black/50 backdrop:backdrop-blur-xs', $width]) }} @click="if ($event.target === $el) $el.close()">
<div class="flex items-start justify-between gap-4">
@if($title)<h2 id="{{ $id }}-title" class="text-lg leading-none font-semibold text-on-surface-strong pr-6">{{ $title }}</h2>@endif
<button type="button" class="absolute top-4 right-4 rounded-radius p-1 text-on-surface/60 hover:bg-surface-alt hover:text-on-surface focus-visible:ring-2 focus-visible:ring-outline-strong" aria-label="Tutup" @click="$el.closest('dialog').close()"><x-capstone::icon name="X" size="20" /></button>
</div>
@if($description)<p class="text-on-surface/60 mt-2 text-sm">{{ $description }}</p>@endif
{{ $slot }}
</dialog>
</div>
