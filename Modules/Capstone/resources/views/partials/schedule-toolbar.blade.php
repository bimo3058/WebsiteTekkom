<div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-3 lg:flex-row lg:items-center lg:justify-between">
    <div class="inline-flex self-start rounded-xl bg-slate-100 p-1" role="group" aria-label="Tampilan jadwal">
        <button type="button" @click="view = 'calendar'" :aria-pressed="view === 'calendar'"
            :class="view === 'calendar' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
            class="rounded-lg px-5 py-1.5 text-sm font-semibold transition-all">Kalender</button>
        <button type="button" @click="view = 'table'" :aria-pressed="view === 'table'"
            :class="view === 'table' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
            class="rounded-lg px-5 py-1.5 text-sm font-semibold transition-all">Tabel</button>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <label class="relative block w-full sm:w-64">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                <x-capstone::icon name="Search" class="h-4 w-4" />
            </span>
                <input x-model.debounce.200ms="search" @input="page = 1" type="search" placeholder="Cari..." aria-label="Cari jadwal"
                class="h-10 w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0B266E] focus:outline-none" />
        </label>
        <x-capstone::dropdown>
            <x-slot:trigger>
                <button type="button" class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 text-sm text-slate-500 transition-colors hover:bg-slate-50 hover:text-slate-700" aria-label="Filter jadwal" title="Filter jadwal">
                    <x-capstone::icon name="Funnel" class="h-4 w-4" /> Filter
                </button>
            </x-slot:trigger>
            <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Periode</p>
            <button type="button" @click="selectedPeriod='all';page=1;open=false" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="String(selectedPeriod)==='all'?'font-semibold text-slate-900':'text-slate-500'" role="menuitem"><span>Semua Periode</span><span x-show="String(selectedPeriod)==='all'"><x-capstone::icon name="Check" size="14" /></span></button>
            <template x-for="period in periods" :key="period.id">
                <button type="button" @click="selectedPeriod=String(period.id);page=1;open=false" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="String(selectedPeriod)===String(period.id)?'font-semibold text-slate-900':'text-slate-500'" role="menuitem"><span class="truncate" x-text="period.name + (period.is_active ? ' (Aktif)' : '')"></span><span x-show="String(selectedPeriod)===String(period.id)"><x-capstone::icon name="Check" size="14" /></span></button>
            </template>
            <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Tipe</p>
            <template x-for="opt in [{v:'all',l:'Semua Tipe'},{v:'BIMBINGAN',l:'Bimbingan'},{v:'SEMPRO',l:'Sempro'},{v:'EXPO',l:'Expo'},{v:'TA_DEFENSE',l:'Sidang TA'}]" :key="'type-'+opt.v">
                <button type="button" @click="typeFilter=opt.v;page=1;open=false" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="typeFilter===opt.v?'font-semibold text-slate-900':'text-slate-500'" role="menuitem"><span x-text="opt.l"></span><span x-show="typeFilter===opt.v"><x-capstone::icon name="Check" size="14" /></span></button>
            </template>
            <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</p>
            <template x-for="opt in [{v:'all',l:'Semua Status'},{v:'PENDING',l:'Menunggu'},{v:'PENDING_APPROVAL',l:'Menunggu persetujuan'},{v:'SCHEDULED',l:'Terjadwal'},{v:'APPROVED',l:'Disetujui'},{v:'COMPLETED',l:'Selesai'},{v:'REJECTED',l:'Ditolak'},{v:'CANCELLED',l:'Dibatalkan'}]" :key="'status-'+opt.v">
                <button type="button" @click="statusFilter=opt.v;page=1;open=false" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="statusFilter===opt.v?'font-semibold text-slate-900':'text-slate-500'" role="menuitem"><span x-text="opt.l"></span><span x-show="statusFilter===opt.v"><x-capstone::icon name="Check" size="14" /></span></button>
            </template>
        </x-capstone::dropdown>
        <span class="hidden text-sm font-medium text-slate-500 md:inline" aria-live="polite" x-text="monthLabel"></span>
        <button type="button" @click="move(-1)" aria-label="Bulan Sebelumnya" :title="'Bulan Sebelumnya (' + monthLabel + ')'"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-slate-50">
            <x-capstone::icon name="ChevronLeft" class="h-4 w-4" />
        </button>
        <button type="button" @click="move(1)" aria-label="Bulan Berikutnya" :title="'Bulan Berikutnya (' + monthLabel + ')'"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-slate-50">
            <x-capstone::icon name="ChevronRight" class="h-4 w-4" />
        </button>
    </div>
</div>
