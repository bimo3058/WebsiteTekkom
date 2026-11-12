@props(['name' => null, 'value' => null, 'placeholder' => 'Pilih...', 'searchPlaceholder' => 'Cari...', 'options' => [], 'required' => false, 'disabled' => false, 'label' => null])
@php
    $menuId = 'cb-'.uniqid();
    $initialOptions = is_array($options) ? $options : [];
    $isAssoc = array_keys($initialOptions) !== range(0, count($initialOptions) - 1);
@endphp
<div x-data="{
    isOpen: false,
    selectedValue: @js((string) ($value ?? '')),
    query: '',
    staticOptions: @js($isAssoc ? $initialOptions : array_combine(array_map('strval', $initialOptions), $initialOptions)),
    resolveLabel(v) {
        if (v === '' || v === null || v === undefined) return '';
        if (this.staticOptions[v] !== undefined) return this.staticOptions[v];
        return String(v);
    },
    filteredKeys() {
        const q = this.query.toLowerCase();
        return Object.keys(this.staticOptions).filter(k => !q || this.staticOptions[k].toLowerCase().includes(q) || k.toLowerCase().includes(q));
    },
    pick(v) { this.selectedValue = String(v); this.query = ''; this.isOpen = false; this.$dispatch('select-menu-change', { name: @js($name), value: this.selectedValue }); }
}" {{ $attributes->class(['relative inline-block w-full']) }} @keydown.escape.window="isOpen = false">
    @if($name)<input type="hidden" name="{{ $name }}" x-model="selectedValue" @if($required) required @endif />@endif
    <div class="hidden">{{ $slot }}</div>
    <button type="button" role="combobox" aria-haspopup="listbox" :aria-expanded="isOpen.toString()" aria-controls="{{ $menuId }}-listbox"
        @click="isOpen = !isOpen" @disabled($disabled) aria-label="{{ $label ?? $placeholder }}"
        class="border-outline bg-surface text-on-surface flex h-9 w-full items-center justify-between gap-2 rounded-radius border px-3 text-sm shadow-xs outline-none focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/20 disabled:opacity-50">
        <span class="truncate" :class="selectedValue === '' ? 'text-on-surface/50' : ''" x-text="selectedValue === '' ? @js($placeholder) : resolveLabel(selectedValue)"></span>
        <span class="shrink-0 text-on-surface/50" :class="isOpen && 'rotate-180'"><x-capstone::icon name="CaretDown" size="14" /></span>
    </button>
    <div x-show="isOpen" x-cloak x-collapse x-trap="isOpen" @click.outside="isOpen = false" class="border-outline bg-surface absolute z-50 mt-2 w-full overflow-hidden rounded-radius border shadow-lg">
        <div class="border-b border-outline p-2">
            <input type="search" x-model.debounce.150ms="query" placeholder="{{ $searchPlaceholder }}" aria-label="{{ $searchPlaceholder }}"
                class="border-outline bg-surface text-on-surface placeholder:text-on-surface/50 h-8 w-full rounded-radius border px-2 text-sm outline-none focus-visible:border-primary" />
        </div>
        <div role="listbox" id="{{ $menuId }}-listbox" aria-label="{{ $label ?? $placeholder }}" class="max-h-56 overflow-y-auto p-1">
            <template x-for="k in filteredKeys()" :key="k">
                <button type="button" role="option" :aria-selected="(k === selectedValue).toString()"
                    @click="pick(k)" :class="k === selectedValue ? 'bg-primary/10 font-medium' : 'hover:bg-surface-alt'"
                    class="text-on-surface flex w-full items-center justify-between gap-2 rounded-radius px-2 py-1.5 text-sm outline-none focus-visible:ring-2 focus-visible:ring-outline-strong">
                    <span class="truncate" x-text="staticOptions[k]"></span>
                    <span x-show="k === selectedValue" class="text-primary"><x-capstone::icon name="Check" size="14" /></span>
                </button>
            </template>
            <div x-show="filteredKeys().length === 0" class="px-2 py-4 text-center text-sm text-on-surface/50">Tidak ada hasil</div>
        </div>
    </div>
    <noscript>
        <select @if($name) name="{{ $name }}" @endif @if($required) required @endif class="border-outline bg-surface mt-1 h-9 w-full rounded-radius border px-3 text-sm">
            <option value="" disabled>{{ $placeholder }}</option>
            @foreach($initialOptions as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
        </select>
    </noscript>
</div>
