<div class="p-6 sm:p-8" x-init="$watch('step', v => { if (v === 2) setPeerContext(); })">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Telaah Sejawat</h2>
            <p class="mt-1 text-sm text-gray-500">Konfigurasikan komponen penilaian untuk TELAAH SEJAWAT pada setiap periode dengan memilih dari bank komponen.</p>
        </div>
        <button type="button" @click="simpanKonfigurasi()" :disabled="simpanDisabled" :class="simpanDisabled ? 'bg-slate-300 text-white' : 'bg-primary text-white hover:bg-primary/90'" class="inline-flex h-10 items-center rounded-lg px-5 text-sm font-medium disabled:cursor-not-allowed">Simpan Konfigurasi</button>
    </div>
    <div class="mt-6">
        <label for="peer-copy-period" class="text-sm text-gray-600">Salin Penilaian</label>
        <x-capstone::select-menu x-model="copyPeriod" @select-menu-change.window="copy()" :options="[]" placeholder="Pilih sumber periode" aria-label="Pilih sumber periode" disabled-when="!editable">
            
            <template x-for="period in periods.filter(p=>String(p.id)!==String(id))" :key="period.id"><button type="button" role="option" @click="pick(period.id, period.name)" :aria-selected="(String(selectedValue) === String(period.id)).toString()" class="flex w-full items-center justify-between gap-2 rounded-radius px-2 py-1.5 text-sm hover:bg-surface-alt" :class="String(selectedValue) === String(period.id) ? 'bg-primary/10 font-medium' : ''"><span class="truncate" x-text="period.name"></span><span x-show="String(selectedValue) === String(period.id)" class="text-primary"><x-capstone::icon name="Check" size="14" /></span></button></template>
        </x-capstone::select-menu>
        <p class="mt-2 text-sm text-gray-400">Salin seluruh konfigurasi penilaian dari periode lain.</p>
    </div>
</div>
@include('capstone::pages.admin.periods.steps._component-picker')
