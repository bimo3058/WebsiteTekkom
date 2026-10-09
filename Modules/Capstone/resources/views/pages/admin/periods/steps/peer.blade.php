<div class="p-6 sm:p-8" x-init="$watch('step', v => { if (v === 2) setPeerContext(); })">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Peer Review</h2>
            <p class="mt-1 text-sm text-gray-500">Konfigurasikan komponen penilaian untuk PEER REVIEW pada setiap periode dengan memilih dari bank komponen.</p>
        </div>
        <button type="button" @click="simpanKonfigurasi()" :disabled="simpanDisabled" :class="simpanDisabled ? 'bg-slate-300 text-white' : 'bg-[#1E2A5A] text-white hover:bg-[#162040]'" class="inline-flex h-10 items-center rounded-lg px-5 text-sm font-medium disabled:cursor-not-allowed">Simpan Konfigurasi</button>
    </div>
    <div class="mt-6">
        <label for="peer-copy-period" class="text-sm text-gray-600">Salin Penilaian</label>
        <select id="peer-copy-period" x-model="copyPeriod" @change="copy()" :disabled="!editable" class="mt-2 flex h-12 w-full max-w-md items-center rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-500 outline-none focus:border-[#1E2A5A] sm:w-[420px]" aria-label="Pilih sumber periode">
            <option value="">Pilih sumber periode</option>
            <template x-for="period in periods.filter(p=>String(p.id)!==String(id))" :key="period.id"><option :value="period.id" x-text="period.name"></option></template>
        </select>
        <p class="mt-2 text-sm text-gray-400">Salin seluruh konfigurasi penilaian dari periode lain.</p>
    </div>
</div>
@include('capstone::pages.admin.periods.steps._component-picker')
