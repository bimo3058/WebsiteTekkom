@props(['name' => null, 'value' => null, 'placeholder' => 'Pilih...', 'options' => [], 'size' => 'md', 'required' => false, 'disabled' => false, 'disabledWhen' => null, 'label' => null, 'borderless' => false, 'dropup' => false])
@php
    $menuId = 'sm-'.uniqid();
    $sizes = ['sm' => 'h-8 text-xs', 'md' => 'h-9 text-sm', 'lg' => 'h-10 text-sm'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $initialOptions = is_array($options) ? $options : [];
    $isAssoc = array_keys($initialOptions) !== range(0, count($initialOptions) - 1);
    $staticOptions = $isAssoc ? $initialOptions : array_combine(array_map('strval', $initialOptions), $initialOptions);
@endphp
<div x-data="{
    isOpen: false,
    selectedValue: @js((string) ($value ?? '')),
    highlightedIndex: -1,
    staticOptions: @js($staticOptions),
    customLabels: {},
    resolveLabel(v) {
        if (v === '' || v === null || v === undefined) return '';
        const key = String(v);
        if (this.staticOptions[key] !== undefined) return this.staticOptions[key];
        if (this.customLabels[key] !== undefined) return this.customLabels[key];
        return String(v);
    },
    pick(v, label = null) {
        this.selectedValue = String(v);
        if (label !== null && label !== undefined) this.customLabels[String(v)] = String(label);
        this.isOpen = false; this.highlightedIndex = -1;
        this.$dispatch('select-menu-change', { name: @js($name), value: this.selectedValue });
    },
    optionKeys() { return Object.keys(this.staticOptions); }
}" x-modelable="selectedValue" {{ $attributes->class(['relative inline-block min-w-0']) }} @keydown.escape.window="isOpen = false">
    @if($name)<input type="hidden" name="{{ $name }}" x-model="selectedValue" @if($required) required @endif />@endif
    <button type="button" role="combobox" aria-haspopup="listbox" :aria-expanded="isOpen.toString()" aria-controls="{{ $menuId }}-listbox"
        @click="isOpen = !isOpen" @keydown.arrow-down.prevent="isOpen = true; highlightedIndex = 0" @keydown.arrow-up.prevent="isOpen = true; highlightedIndex = optionKeys().length - 1"
        @disabled($disabled) @if($disabledWhen) :disabled="{{ $disabledWhen }}" @endif aria-label="{{ $label ?? $placeholder }}" title="{{ $label ?? $placeholder }}"
        class="bg-surface text-on-surface {{ $sizeClass }} flex w-full items-center justify-between gap-2 rounded-radius outline-none transition focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-50 {{ $borderless ? 'border-0 shadow-none bg-transparent px-1' : 'border-outline border px-3 shadow-xs' }}">
        <span class="truncate" :class="selectedValue === '' ? 'text-on-surface/50' : ''" x-text="selectedValue === '' ? @js($placeholder) : resolveLabel(selectedValue)"></span>
        <span class="shrink-0 text-on-surface/50 transition-transform" :class="isOpen && 'rotate-180'"><x-capstone::icon name="CaretDown" size="14" /></span>
    </button>
    <div x-show="isOpen" x-cloak x-collapse x-trap="isOpen" @click.outside="isOpen = false" role="listbox" id="{{ $menuId }}-listbox" aria-label="{{ $label ?? $placeholder }}"
        class="border-outline bg-surface text-on-surface absolute z-[70] max-h-64 min-w-full w-max max-w-[240px] overflow-y-auto rounded-xl border p-1.5 shadow-lg {{ $dropup ? 'bottom-full mb-2 top-auto origin-bottom' : 'mt-2 origin-top' }}">
        <template x-for="(optLabel, optValue) in staticOptions" :key="optValue">
            <button type="button" role="option" :aria-selected="(String(optValue) === String(selectedValue)).toString()"
                @click="pick(optValue)" @mouseenter="highlightedIndex = optionKeys().indexOf(String(optValue))"
                :class="String(optValue) === String(selectedValue) ? 'bg-primary/10 text-on-surface-strong font-semibold' : 'text-slate-500 hover:bg-slate-50'"
                class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-sm outline-none focus-visible:ring-2 focus-visible:ring-outline-strong">
                <span class="truncate" x-text="optLabel"></span>
                <span x-show="String(optValue) === String(selectedValue)" class="text-primary"><x-capstone::icon name="Check" size="14" /></span>
            </button>
        </template>
        {{ $slot }}
        <div x-show="Object.keys(staticOptions).length === 0" class="px-2 py-4 text-center text-sm text-on-surface/50">Tidak ada pilihan</div>
    </div>
    <noscript>
        <select @if($name) name="{{ $name }}" @endif @if($required) required @endif class="border-outline bg-surface mt-1 h-9 w-full rounded-radius border px-3 text-sm">
            <option value="" disabled>{{ $placeholder }}</option>
            @foreach($staticOptions as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
        </select>
    </noscript>
</div>
