@props(['id', 'label'])

@once
    <script src="{{ asset('modules/banksoal/js/Banksoal/Dosen/alpine-select.js') }}"></script>
@endonce

<div class="bs-alpine-select" x-data="bankSoalSelect()"
     :class="{ 'is-enhanced': ready, 'is-open': open }"
     @click.outside="open = false"
     @keydown.escape.stop.prevent="open = false; $refs.trigger.focus()"
     @keydown.tab="open = false">
    {{-- Retain the native field for submission, validation and dependent option loaders. --}}
    {{ $slot }}

    <button type="button" id="{{ $id }}-trigger" x-ref="trigger" x-cloak
            class="bs-dropdown-trigger bs-dropdown-select" role="combobox"
            aria-label="{{ $label }}" aria-haspopup="listbox" aria-controls="{{ $id }}-options"
            :aria-expanded="open" :aria-required="required" :aria-invalid="invalid"
            :aria-activedescendant="open && active >= 0 ? '{{ $id }}-option-' + active : null"
            :aria-describedby="invalid ? '{{ $id }}-error' : null"
            :disabled="disabled" :class="{ 'is-open': open, 'is-invalid': invalid }"
            @click="toggle()"
            @keydown.arrow-down.prevent="move(1)" @keydown.arrow-up.prevent="move(-1)"
            @keydown.home.prevent="edge(false)" @keydown.end.prevent="edge(true)"
            @keydown.enter.prevent="open ? choose(active) : toggle()"
            @keydown.space.prevent="open ? choose(active) : toggle()">
        <span x-text="selectedLabel" :class="{ 'bs-select-placeholder': !value }"></span>
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true" :class="{ 'rotate-180': open }"><path d="m6 9 6 6 6-6"/></svg>
    </button>

    <div x-show="open" x-cloak x-transition.opacity.duration.100ms
         id="{{ $id }}-options" role="listbox" aria-label="{{ $label }}"
         class="bs-dropdown-menu bs-dropdown-options bs-select-options">
        <template x-for="(option, index) in options" :key="index">
            <button type="button" :id="'{{ $id }}-option-' + index" role="option" tabindex="-1"
                    class="bs-dropdown-item" :disabled="option.disabled"
                    :aria-selected="value === option.value"
                    :class="{ 'is-selected': value === option.value, 'is-active': active === index }"
                    @mouseenter="active = index" @mousedown.prevent @click="choose(index)">
                <span x-text="option.label"></span>
                <i class="fas fa-check" x-show="value === option.value" aria-hidden="true"></i>
            </button>
        </template>
    </div>
    <p id="{{ $id }}-error" class="bs-select-error" x-show="invalid" x-cloak>Silakan pilih {{ strtolower($label) }}.</p>
</div>
