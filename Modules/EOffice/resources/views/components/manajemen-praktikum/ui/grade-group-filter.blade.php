@props(['model','label','searchPlaceholder'])
<x-eoffice::manajemen-praktikum.ui.select-menu-styles />
<div x-data="{open:false}" @click.outside="open=false" @keydown.escape.stop="open=false" style="position:relative">
    <button type="button" @click="open=!open" :aria-expanded="open" class="ta-select" style="min-height:36px;padding:7px 12px;min-width:140px">
        <span x-text="{{ $model }}==='' ? @js($label) : @js(str_replace('Semua ','',$label).' ') + {{ $model }}"></span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" :style="open?'transform:rotate(180deg)':''"><path d="m6 9 6 6 6-6"/></svg>
    </button>
    <div class="ta-options" x-show="open" x-cloak x-transition.opacity.duration.150ms>
        <div style="padding:8px;border-bottom:1px solid #DFE1E7"><input type="search" :value="{{ $model }}" @input="{{ $model }}=$el.value.trim()" @keydown.enter.prevent="open=false" placeholder="{{ $searchPlaceholder }}" aria-label="{{ $searchPlaceholder }}" style="width:100%;border:1px solid #DFE1E7;border-radius:6px;padding:6px 10px;font-size:12px"></div>
        <button type="button" class="ta-option" :class="{{ $model }}===''?'selected':''" @click="{{ $model }}='';open=false">{{ $label }}</button>
    </div>
</div>
