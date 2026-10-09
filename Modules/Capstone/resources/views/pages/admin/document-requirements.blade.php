@extends('capstone::layouts.app')
@section('title','Persyaratan Dokumen')
@section('content')
<div x-data="documentRequirements" @keydown.escape.window="closeMenu();filterMenu=false;sortMenu=false" @scroll.window="closeMenu()" @resize.window="closeMenu()" class="space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Persyaratan Dokumen</h1>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <label class="inline-flex items-center gap-2 rounded-lg border border-input bg-white px-4 py-2 text-sm font-semibold shadow-xs">
                <span class="sr-only">Periode</span>
                <select x-model="selectedPeriod" @change="load()" :disabled="saving" aria-label="Pilih periode" class="bg-transparent font-semibold outline-none">
                    <option value="" disabled x-show="!selectedPeriod">Pilih periode</option>
                    <template x-for="period in periods" :key="period.id">
                        <option :value="String(period.id)" x-text="period.name+(period.is_active ? ' (Aktif)' : '')" :selected="String(period.id)===String(selectedPeriod)"></option>
                    </template>
                </select>
                <x-capstone::icon name="ChevronDown" class="size-4 text-muted-foreground" />
            </label>
            <button type="button" @click="$refs.defaults.showModal()" :disabled="!editable" class="inline-flex h-9 items-center gap-2 rounded-lg bg-[#1E2A5A] px-4 text-sm font-semibold text-white shadow-xs hover:bg-[#16204a] disabled:opacity-40">
                <x-capstone::icon name="Settings" class="size-4" />Gunakan konfigurasi bawaan
            </button>
        </div>
    </div>

    @include('capstone::pages.admin.document-requirements.finalized')
    @include('capstone::partials.loading')

    <div x-show="!loading && !error && selectedPeriod" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h2 class="text-base font-semibold">Tabel Dokumen</h2>
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
                    <input type="search" x-model="search" @input="page=1" placeholder="Search" aria-label="Cari dokumen" class="h-9 w-64 rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
                </div>
                <div class="relative">
                    <button type="button" @click="filterMenu=!filterMenu;sortMenu=false" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                        <x-capstone::icon name="ListFilter" class="size-4" />Filter
                    </button>
                    <div x-show="filterMenu" @click.away="filterMenu=false" x-cloak class="absolute right-0 z-20 mt-2 w-52 rounded-lg border bg-white p-1.5 shadow-md">
                        <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Status</p>
                        <template x-for="opt in [{v:'all',l:'Semua'},{v:'configured',l:'Sudah dikonfigurasi'},{v:'unconfigured',l:'Belum dikonfigurasi'}]" :key="opt.v">
                            <button type="button" @click="status=opt.v;page=1;filterMenu=false" class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm hover:bg-accent" :class="status===opt.v?'font-semibold text-foreground':'text-muted-foreground'">
                                <span x-text="opt.l"></span><span x-show="status===opt.v">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
                <div class="relative">
                    <button type="button" @click="sortMenu=!sortMenu;filterMenu=false" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                        <x-capstone::icon name="ArrowUpDown" class="size-4" />Sort by
                    </button>
                    <div x-show="sortMenu" @click.away="sortMenu=false" x-cloak class="absolute right-0 z-20 mt-2 w-48 rounded-lg border bg-white p-1.5 shadow-md">
                        <template x-for="opt in [{v:'',l:'Default'},{v:'fase-az',l:'Fase A-Z'},{v:'fase-za',l:'Fase Z-A'},{v:'jumlah',l:'Jumlah dokumen'}]" :key="opt.v">
                            <button type="button" @click="setSort(opt.v)" class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm hover:bg-accent" :class="sortOption===opt.v?'font-semibold text-foreground':'text-muted-foreground'">
                                <span x-text="opt.l"></span><span x-show="sortOption===opt.v">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead>
                    <tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                        <th class="px-4 py-3 font-medium">No</th>
                        <th class="px-4 py-3 font-medium">Fase</th>
                        <th class="px-4 py-3 font-medium">Syarat Dokumen</th>
                        <th class="px-4 py-3 font-medium">Tipe Dokumen</th>
                        <th class="px-5 py-3 text-right font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="(item,index) in visible" :key="item.phase">
                        <tr class="cursor-pointer bg-white hover:bg-muted/30" @click="window.location.href=phaseUrl(item.phase)">
                            <td class="px-4 py-4 text-foreground" x-text="(Math.min(page,pageCount)-1)*Number(pageSize)+index+1"></td>
                            <td class="px-4 py-4 font-medium uppercase text-foreground" x-text="labels[item.phase]"></td>
                            <td class="px-4 py-4 text-foreground"><span x-text="item.document_count+' Dokumen'"></span><span x-show="item.required_count" class="ml-1 text-xs text-emerald-600" x-text="'('+item.required_count+' required)'"></span></td>
                            <td class="max-w-[280px] truncate px-4 py-4 text-foreground" x-text="dokumenLabel(item)"></td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" @click.stop="openRowMenu($event, item.phase, phaseUrl(item.phase))" aria-label="Aksi" aria-haspopup="menu" class="rounded-md p-1.5 text-[#666D80] hover:bg-muted"><x-capstone::icon name="Ellipsis" class="size-5" /></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <p x-show="!filtered.length" class="p-8 text-center text-sm text-muted-foreground">Tidak ada fase yang cocok.</p>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-0 overflow-hidden rounded-lg border border-input bg-white text-[13px]">
                    <span class="px-2.5 text-muted-foreground">Per page</span>
                    <select x-model.number="pageSize" @change="page=1" aria-label="Baris per halaman" class="border-l border-input bg-transparent px-2 py-1.5 font-medium outline-none">
                        <option>10</option><option>25</option><option>50</option>
                    </select>
                </label>
                <span class="text-foreground"><span x-text="'Showing '+showingFrom+' to '+showingTo+' of, '+filtered.length+' results'"></span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="goPage(Math.min(page,pageCount)-1)" :disabled="page<=1" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                <template x-for="(n,i) in pageNumbers" :key="n">
                    <span class="flex items-center gap-1.5">
                        <span x-show="i>0 && n-pageNumbers[i-1]>1" class="px-1 text-muted-foreground">...</span>
                        <button type="button" @click="goPage(n)" x-text="n" class="inline-flex size-8 items-center justify-center rounded-lg border text-sm" :class="Math.min(page,pageCount)===n?'border-[#1E2A5A] bg-[#1E2A5A] font-semibold text-white':'border-input bg-white text-foreground hover:bg-accent'"></button>
                    </span>
                </template>
                <button type="button" @click="goPage(Math.min(page,pageCount)+1)" :disabled="page>=pageCount" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
            </div>
        </div>
    </div>
    <p x-show="!loading && !error && !selectedPeriod" class="rounded-xl border border-border bg-white p-8 text-center text-sm text-muted-foreground shadow-xs">Pilih periode untuk menampilkan persyaratan dokumen.</p>
    <p x-show="error" x-text="error" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>

    <div x-show="openMenu" @click.away="closeMenu()" x-cloak :style="menuStyle" style="position: fixed; z-index: 50;" role="menu" class="w-44 rounded-lg border border-border bg-white p-1.5 text-left shadow-md">
        <a :href="openMenuHref" class="block rounded-md px-2 py-1.5 text-sm text-foreground hover:bg-accent" x-text="finalized?'Lihat konfigurasi':'Edit konfigurasi'"></a>
    </div>

    <x-capstone::dialog id="defaults-confirm" x-ref="defaults" title="Gunakan konfigurasi bawaan"><p class="py-4 text-sm text-muted-foreground">Konfigurasi dokumen semua fase pada periode ini akan diganti dengan konfigurasi bawaan.</p><div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="$refs.defaults.close()" ::disabled="saving">Batal</x-capstone::button><x-capstone::button @click="save(true)" ::disabled="!editable" x-text="saving ? 'Saving...' : 'Gunakan konfigurasi bawaan'" /></div></x-capstone::dialog>
</div>
@endsection
