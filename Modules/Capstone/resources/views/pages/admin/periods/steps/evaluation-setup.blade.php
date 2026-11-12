<div class="p-6 sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Tipe Penilaian</h2>
            <p class="mt-1 text-sm text-gray-500">Konfigurasikan komponen penilaian untuk setiap periode dengan memilih dari bank komponen.</p>
        </div>
        <button type="button" @click="simpanKonfigurasi()" :disabled="simpanDisabled" :class="simpanDisabled ? 'bg-slate-300 text-white' : 'bg-primary text-white hover:bg-primary/90'" class="inline-flex h-10 items-center rounded-lg px-5 text-sm font-medium disabled:cursor-not-allowed">Simpan Konfigurasi</button>
    </div>
    <div class="mt-5 flex flex-wrap items-center gap-x-1 gap-y-2 text-sm" role="tablist" aria-label="Tipe penilaian">
        <template x-for="(type, i) in types" :key="type">
            <span class="inline-flex items-center gap-1">
                <button type="button" role="tab" :aria-selected="evaluationType===type" @click="selectType(type)" class="rounded-lg border px-3 py-1.5 font-medium" :class="evaluationType===type ? 'border-[#1E2A5A] bg-indigo-50 text-[#1E2A5A]' : 'border-transparent text-gray-500 hover:text-gray-800'" x-text="typeLabel(type)"></button>
                <span x-show="i < types.length - 1" class="text-xs text-gray-400">›</span>
            </span>
        </template>
    </div>
    <div class="mt-6">
        <label for="eval-copy-period" class="text-sm text-gray-600">Salin Penilaian</label>
        <x-capstone::select-menu x-model="copyPeriod" @select-menu-change.window="copy()" :options="[]" placeholder="Pilih sumber periode" aria-label="Pilih sumber periode" disabled-when="!editable">
            
            <template x-for="period in periods.filter(p=>String(p.id)!==String(id))" :key="period.id"><button type="button" role="option" @click="pick(period.id, period.name)" :aria-selected="(String(selectedValue) === String(period.id)).toString()" class="flex w-full items-center justify-between gap-2 rounded-radius px-2 py-1.5 text-sm hover:bg-surface-alt" :class="String(selectedValue) === String(period.id) ? 'bg-primary/10 font-medium' : ''"><span class="truncate" x-text="period.name"></span><span x-show="String(selectedValue) === String(period.id)" class="text-primary"><x-capstone::icon name="Check" size="14" /></span></button></template>
        </x-capstone::select-menu>
        <p class="mt-2 text-sm text-gray-400">Salin seluruh konfigurasi penilaian dari periode lain.</p>
    </div>
</div>
@include('capstone::pages.admin.periods.steps._component-picker')
