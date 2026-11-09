{{--
    Dropdown Alpine bergaya SITKOM (lihat resources/views/superadmin/users/_search_filter.blade.php)
    pengganti <select> bawaan di langkah CV Builder.

    Parameter:
      $model    ekspresi Alpine milik cvWizard() yang menyimpan nilai, mis. 'newSkill.level'
      $options  daftar teks pilihan; nilai yang disimpan = teks yang tampil
--}}
<div class="relative" x-data="{ open: false, options: @js($options) }"
     @click.outside="open = false" @keydown.escape="open = false">
    <button type="button" @click="open = !open"
            class="form-control flex items-center justify-between gap-2 text-left"
            style="cursor: pointer;"
            :style="open ? 'border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-subtle);' : ''">
        <span class="truncate" x-text="{{ $model }}"></span>
        <svg class="shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
             width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: var(--c-fg-placeholder);">
            <path d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="absolute left-0 top-full mt-1 w-full rounded-lg overflow-hidden py-1 max-h-48 overflow-y-auto z-[100]"
         style="display: none; background: #fff; border: 1px solid var(--c-border); box-shadow: 0 10px 25px rgba(0,0,0,.1);">
        <template x-for="opt in options" :key="opt">
            <button type="button" @click="{{ $model }} = opt; open = false"
                    class="w-full text-left px-3 py-1.5 text-sm hover:bg-[var(--c-bg)] transition-colors"
                    :class="{{ $model }} === opt ? 'font-semibold' : 'font-normal'"
                    :style="{{ $model }} === opt ? 'color: var(--c-primary); background: rgba(11,38,110,0.04)' : 'color: var(--c-fg-sec)'"
                    style="font-family: inherit; border: none; cursor: pointer; background: none;">
                <span x-text="opt"></span>
            </button>
        </template>
    </div>
</div>
