@extends('capstone::layouts.app')
@section('title','Konfigurasi Nilai')
@section('content')
<div x-data="adminGradeConfig" @keydown.escape.window="filterMenu=false;sortMenu=false" class="space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Konfigurasi Nilai</h1>
            <p class="mt-1 text-sm text-muted-foreground">Atur bobot nilai PDC 1, PDC 2, dan Tugas Akhir per periode.</p>
        </div>
        <label class="inline-flex items-center gap-2 rounded-lg border border-input bg-white px-4 py-2 text-sm font-semibold shadow-xs">
            <span class="sr-only">Periode</span>
            <select x-model="periodId" @change="load()" :disabled="saving" aria-label="Pilih periode" class="bg-transparent font-semibold outline-none">
                <option value="" disabled x-show="!periodId">Pilih periode</option>
                <template x-for="period in periods" :key="period.id">
                    <option :value="String(period.id)" x-text="period.name" :selected="String(period.id)===String(periodId)"></option>
                </template>
            </select>
            <x-capstone::icon name="ChevronDown" class="size-4 text-muted-foreground" />
        </label>
    </div>

    @include('capstone::partials.loading')

    <div x-show="!loading && !error && periodId" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h2 class="text-base font-semibold">Tabel Konfigurasi Nilai</h2>
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
                    <input type="search" x-model="search" placeholder="Cari..." aria-label="Cari komponen nilai" class="h-9 w-64 rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
                </div>
                <div class="relative">
                    <button type="button" @click="filterMenu=!filterMenu;sortMenu=false" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                        <x-capstone::icon name="ListFilter" class="size-4" />Filter
                    </button>
                    <div x-show="filterMenu" @click.away="filterMenu=false" x-cloak class="absolute right-0 z-20 mt-2 w-44 rounded-lg border bg-white p-1.5 shadow-md">
                        <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Fase</p>
                        <template x-for="opt in [{v:'',l:'Semua'},{v:'pdc1',l:'PDC 1'},{v:'pdc2',l:'PDC 2'},{v:'ta',l:'Tugas Akhir'}]" :key="opt.v">
                            <button type="button" @click="phaseFilter=opt.v;filterMenu=false" class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm hover:bg-accent" :class="String(phaseFilter)===String(opt.v)?'font-semibold text-foreground':'text-muted-foreground'">
                                <span x-text="opt.l"></span><span x-show="String(phaseFilter)===String(opt.v)">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
                <div class="relative">
                    <button type="button" @click="sortMenu=!sortMenu;filterMenu=false" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                        <x-capstone::icon name="ArrowUpDown" class="size-4" />Urutkan
                    </button>
                    <div x-show="sortMenu" @click.away="sortMenu=false" x-cloak class="absolute right-0 z-20 mt-2 w-48 rounded-lg border bg-white p-1.5 shadow-md">
                        <template x-for="opt in [{v:'',l:'Default'},{v:'nama-az',l:'Nama A-Z'},{v:'nama-za',l:'Nama Z-A'},{v:'bobot',l:'Bobot'}]" :key="opt.v">
                            <button type="button" @click="setSort(opt.v)" class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm hover:bg-accent" :class="sortOption===opt.v?'font-semibold text-foreground':'text-muted-foreground'">
                                <span x-text="opt.l"></span><span x-show="sortOption===opt.v">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 px-5 pt-4 lg:grid-cols-3">
            @foreach(['pdc1'=>'PDC 1','pdc2'=>'PDC 2','ta'=>'Tugas Akhir'] as $phase=>$label)
            <div x-show="phaseFilter===''||phaseFilter==='{{ $phase }}'" class="overflow-hidden rounded-xl border border-border">
                <div class="flex items-center justify-between gap-2 bg-[#F8F9FB] px-4 py-3">
                    <h3 class="text-[13px] font-semibold text-[#666D80]">{{ $label }}</h3>
                    <p class="text-xs font-medium" :class="Math.abs(total('{{ $phase }}')-100)<0.001?'text-emerald-600':'text-red-600'" x-text="'Total: '+total('{{ $phase }}')+'%'"></p>
                </div>
                <div class="divide-y divide-border">
                    <template x-for="key in phaseRows('{{ $phase }}')" :key="key">
                        <div class="flex items-center justify-between gap-3 bg-white px-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-foreground" x-text="prettyKey(key)"></p>
                                <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="weightBadgeClass(weights['{{ $phase }}'][key])" x-text="Number(weights['{{ $phase }}'][key]||0)+'%'"></span>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <input type="number" min="0" max="100" step="0.01" x-model.number="weights['{{ $phase }}'][key]" :disabled="saving" :aria-label="'Bobot '+key" class="h-9 w-24 rounded-lg border border-input bg-white px-2.5 text-sm outline-none focus:border-ring">
                                <span class="text-sm text-muted-foreground">%</span>
                            </div>
                        </div>
                    </template>
                </div>
                <p x-show="!phaseRows('{{ $phase }}').length" class="bg-white px-4 py-6 text-center text-sm text-muted-foreground">Tidak ada komponen yang cocok.</p>
            </div>
            @endforeach
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
            <p x-show="!valid" class="text-sm text-red-600">Total bobot setiap fase harus 100%.</p>
            <p x-show="valid" class="text-sm text-muted-foreground">Semua fase berjumlah 100%.</p>
            <div class="flex items-center gap-2.5">
                <button type="button" @click="document.getElementById('reset-grades').showModal()" :disabled="saving" class="inline-flex h-9 items-center rounded-lg border border-input bg-white px-4 text-sm font-semibold shadow-xs hover:bg-accent disabled:opacity-40">Reset ke default</button>
                <button type="button" @click="save()" :disabled="saving || !valid" class="inline-flex h-9 items-center rounded-lg bg-[#1E2A5A] px-4 text-sm font-semibold text-white shadow-xs hover:bg-[#16204a] disabled:opacity-40">Simpan</button>
            </div>
        </div>
    </div>
    <p x-show="!loading && !error && !periodId" class="rounded-xl border border-border bg-white p-8 text-center text-sm text-muted-foreground shadow-xs">Pilih periode untuk menampilkan konfigurasi.</p>
    <p x-show="error" x-text="error" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>

    <dialog id="reset-grades" class="fixed inset-0 m-auto h-fit w-[calc(100%-2rem)] max-w-md rounded-xl border border-border bg-white p-6 backdrop:bg-black/40">
        <h2 class="text-lg font-semibold">Reset bobot nilai?</h2>
        <p class="my-4 text-sm text-muted-foreground">Bobot pada periode yang dipilih akan diganti dengan bobot default sistem.</p>
        <div class="flex gap-2.5">
            <button type="button" @click="reset()" :disabled="saving" class="inline-flex h-9 items-center rounded-lg bg-[#1E2A5A] px-4 text-sm font-semibold text-white hover:bg-[#16204a] disabled:opacity-40">Reset</button>
            <button type="button" @click="document.getElementById('reset-grades').close()" class="inline-flex h-9 items-center rounded-lg border border-input bg-white px-4 text-sm font-semibold shadow-xs hover:bg-accent">Batal</button>
        </div>
    </dialog>
</div>
@endsection
