@props(['options', 'placeholder'=>'Pilih opsi...'])
<x-eoffice::manajemen-praktikum.ui.select-menu-styles />
<div x-data="{ open: false, value: '', options: {{ json_encode($options) }} }" x-modelable="value" {{ $attributes }} @click.outside="open=false" @keydown.escape.stop="open=false" style="position:relative">
    <button type="button" @click="open=!open" :aria-expanded="open" class="ta-select"><span x-text="options[String(value)] || @js($placeholder)"></span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" style="transition:transform .2s" :style="open ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg></button>
    <div x-show="open" x-cloak x-transition.opacity.duration.150ms class="ta-options"><template x-for="(label,key) in options" :key="key"><button type="button" class="ta-option" @click="value = key === 'true' ? true : (key === 'false' ? false : key); open=false" x-text="label" :class="String(value) === key ? 'selected' : ''" :aria-pressed="String(value) === key"></button></template></div>
</div>
