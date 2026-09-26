@props(['name', 'label', 'options' => [], 'value' => '', 'inputId' => null])

<div class="control-filter" x-data="{
    open: false,
    selected: @js((string) $value),
    options: @js($options),
    get selectedLabel() { return this.options.find(option => option.value === this.selected)?.label || 'Semua'; },
    choose(value) {
        this.selected = value;
        this.open = false;
        this.$refs.trigger.focus();
        this.$nextTick(() => this.$dispatch('banksoal-filter-change', { name: @js($name), value }));
    },
    focusOption(step) {
        const buttons = [...this.$refs.options.querySelectorAll('button')];
        if (!buttons.length) return;
        const index = buttons.indexOf(document.activeElement);
        buttons[(index + step + buttons.length) % buttons.length].focus();
    }
}" x-id="['control-options', 'control-label']"
    @click.outside="open = false"
    @keydown.escape.stop.prevent="open = false; $refs.trigger.focus()"
    @banksoal-filter-options.window="if ($event.detail.name === @js($name)) { options = $event.detail.options; selected = $event.detail.selected; }">
    <span class="control-label" :id="$id('control-label')">{{ $label }}</span>
    <input type="hidden" name="{{ $name }}" @if($inputId) id="{{ $inputId }}" @endif x-model="selected" value="{{ $value }}">
    <button type="button" class="bs-dropdown-trigger control-filter-trigger" x-ref="trigger"
        @click="open = !open" :aria-expanded="open" aria-haspopup="listbox"
        :aria-controls="$id('control-options')" :aria-labelledby="$id('control-label') + ' ' + $id('control-options') + '-value'"
        @keydown.arrow-down.prevent="open = true; $nextTick(() => $refs.options.querySelector('button')?.focus())">
        <span :id="$id('control-options') + '-value'" x-text="selectedLabel"></span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
    </button>
    <div x-show="open" x-cloak x-transition.opacity.duration.100ms class="bs-dropdown-menu control-filter-options"
        role="listbox" :id="$id('control-options')" :aria-labelledby="$id('control-label')" x-ref="options"
        @keydown.arrow-down.prevent="focusOption(1)" @keydown.arrow-up.prevent="focusOption(-1)">
        <template x-for="option in options" :key="option.value">
            <button type="button" role="option" class="bs-dropdown-item" :aria-selected="selected === option.value"
                :class="{ 'is-selected': selected === option.value }" @click="choose(option.value)">
                <span x-text="option.label"></span>
                <svg x-show="selected === option.value" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5 12 4 4L19 6" /></svg>
            </button>
        </template>
    </div>
</div>
