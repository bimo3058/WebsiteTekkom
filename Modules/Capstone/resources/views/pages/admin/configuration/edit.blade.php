@php $peer = $peer ?? false; @endphp
@extends('capstone::layouts.app')
@section('title','Edit Tipe Penilaian')
@section('content')
<div x-data="adminAssessmentConfig({{ $peer ? 'true' : 'false' }},true)" class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a :href="url('/admin/period-assessment-config')+'?period_id='+encodeURIComponent(periodId||'')" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-input bg-white px-3.5 text-sm font-medium text-[#666D80] shadow-xs hover:bg-accent">
            <x-capstone::icon name="ChevronLeft" class="size-4" />Kembali
        </a>
        <button type="button" @click="save()" :disabled="saving || period?.is_finalized" class="inline-flex h-9 items-center rounded-lg bg-[#1E2A5A] px-5 text-sm font-semibold text-white shadow-xs hover:bg-[#091958] disabled:opacity-50">Simpan</button>
    </div>

    <div>
        <h1 class="text-2xl font-bold tracking-tight">Edit Tipe Penilaian</h1>
        <p class="mt-1 text-sm text-muted-foreground">Konfigurasikan Tipe penilaian <strong class="font-semibold uppercase text-foreground" x-text="type.replaceAll('_',' ')"></strong> untuk periode <strong class="font-semibold text-foreground" x-text="period?.name || ''"></strong> dengan memilih dari bank komponen penilaian.</p>
    </div>

    @include('capstone::partials.loading')
    <p x-show="period?.is_finalized" x-cloak class="rounded-lg border bg-muted p-4 text-sm">Periode sudah difinalisasi. Konfigurasi hanya dapat dilihat.</p>

    <div x-show="!loading && !error && periodId" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="border-b border-border px-6 py-5">
            <label for="copy-period" class="text-sm font-medium text-[#666D80]">Salin Penilaian</label>
            <div class="relative mt-2 max-w-sm">
                <select id="copy-period" x-model="copyFrom" @change="if(copyFrom)copy()" :disabled="saving || period?.is_finalized" class="h-11 w-full appearance-none rounded-xl border border-input bg-white px-4 pr-10 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-ring">
                    <option value="">Pilih sumber periode</option>
                    <template x-for="p in periods.filter(p=>String(p.id)!==String(periodId))" :key="p.id">
                        <option :value="p.id" x-text="p.name"></option>
                    </template>
                </select>
                <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="ChevronDown" class="size-4" /></span>
            </div>
            <p class="mt-2 text-[13px] text-muted-foreground">Salin seluruh konfigurasi penilaian dari periode lain.</p>
        </div>

        <div class="grid gap-6 px-6 py-6 lg:grid-cols-[300px_minmax(0,1fr)]">
            <div class="space-y-5">
                <div>
                    <h2 class="text-lg font-semibold">Kumpulan Komponen Penilaian</h2>
                    <p class="mt-1 text-sm leading-relaxed text-[#666D80]">Pilih komponen yang akan digunakan dalam <span class="uppercase" x-text="type.replaceAll('_',' ')"></span> untuk periode yang dipilih.</p>
                </div>
                <div class="rounded-xl border border-border bg-white p-5">
                    <h3 class="text-base font-semibold">Ringkasan</h3>
                    <div class="mt-4 space-y-2.5 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-muted-foreground">Total Komponen</span>
                            <span class="font-medium" x-text="selectedTemplates.length"></span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-muted-foreground">Total Bobot</span>
                            <span class="inline-flex items-center rounded-full border border-emerald-300 px-2.5 py-0.5 text-xs font-medium text-emerald-700" x-text="Math.round(totalWeight)+'%'"></span>
                        </div>
                    </div>
                    <h4 class="mt-5 text-base font-semibold">Komponen terpilih :</h4>
                    <div class="mt-3 overflow-hidden rounded-lg border border-border">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-[#F8F9FB] text-[13px] text-[#666D80]">
                                    <th class="px-3.5 py-2.5 font-medium">Kode</th>
                                    <th class="px-3.5 py-2.5 text-right font-medium">Bobot</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                <template x-for="t in selectedTemplates" :key="t.id">
                                    <tr>
                                        <td class="px-3.5 py-3.5">
                                            <label class="flex cursor-pointer items-center gap-2.5">
                                                <input type="checkbox" checked @change="toggle(t.id)" :disabled="saving || period?.is_finalized" aria-label="Hapus komponen" class="size-4 rounded border-[#C1C7CF] accent-[#293C79]">
                                                <span class="font-medium" x-text="t.code"></span>
                                            </label>
                                        </td>
                                        <td class="px-3.5 py-3.5 text-right">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="weightBadgeClass(t.weight)" x-text="Math.round(Number(t.weight||0))+'%'"></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="!selectedTemplates.length">
                                    <td colspan="2" class="px-3.5 py-5 text-center text-[13px] text-muted-foreground">Belum ada komponen dipilih.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-border bg-white">
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
                    <h3 class="text-base font-semibold">Pilih Komponen</h3>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
                            <input type="search" x-model="search" @input="templatePage=1" placeholder="Search" aria-label="Cari komponen" class="h-9 w-56 rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
                        </div>
                        <div class="relative">
                            <button type="button" @click="filterMenu=!filterMenu;sortMenu=false" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                                <x-capstone::icon name="ListFilter" class="size-4" />Filter
                            </button>
                            <div x-show="filterMenu" @click.away="filterMenu=false" x-cloak class="absolute right-0 z-20 mt-2 w-44 rounded-lg border bg-white p-1.5 shadow-md">
                                <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Bobot</p>
                                <template x-for="opt in [{v:'',l:'Semua'},{v:'10',l:'10%'},{v:'25',l:'25%'},{v:'50',l:'50%'},{v:'100',l:'100%'}]" :key="opt.v">
                                    <button type="button" @click="weightFilter=opt.v;templatePage=1;filterMenu=false" class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm hover:bg-accent" :class="String(weightFilter)===String(opt.v)?'font-semibold text-foreground':'text-muted-foreground'">
                                        <span x-text="opt.l"></span><span x-show="String(weightFilter)===String(opt.v)">✓</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="relative">
                            <button type="button" @click="sortMenu=!sortMenu;filterMenu=false" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                                <x-capstone::icon name="ArrowUpDown" class="size-4" />Sort by
                            </button>
                            <div x-show="sortMenu" @click.away="sortMenu=false" x-cloak class="absolute right-0 z-20 mt-2 w-48 rounded-lg border bg-white p-1.5 shadow-md">
                                <template x-for="opt in [{v:'',l:'Default'},{v:'nama-az',l:'Kode A-Z'},{v:'nama-za',l:'Kode Z-A'},{v:'bobot',l:'Bobot'}]" :key="opt.v">
                                    <button type="button" @click="sortOption=opt.v;templatePage=1;sortMenu=false" class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm hover:bg-accent" :class="sortOption===opt.v?'font-semibold text-foreground':'text-muted-foreground'">
                                        <span x-text="opt.l"></span><span x-show="sortOption===opt.v">✓</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[560px] text-left text-sm">
                        <thead>
                            <tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                                <th class="w-10 px-4 py-3"></th>
                                <th class="px-2 py-3 font-medium">Kode</th>
                                <th class="px-4 py-3 font-medium">Deskripsi</th>
                                <th class="px-5 py-3 text-right font-medium">Bobot</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <template x-for="template in templateVisible" :key="template.id">
                                <tr class="hover:bg-muted/30">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" :checked="selectedIds.includes(Number(template.id))" @change="toggle(template.id)" :disabled="saving || period?.is_finalized" aria-label="Pilih komponen" class="size-4 rounded border-[#C1C7CF] accent-[#293C79]">
                                    </td>
                                    <td class="whitespace-nowrap px-2 py-4 font-medium" x-text="template.code"></td>
                                    <td class="px-4 py-4 leading-relaxed" x-text="template.description || template.name"></td>
                                    <td class="px-5 py-4 text-right">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="weightBadgeClass(template.weight)" x-text="Math.round(Number(template.weight||0))+'%'"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <p x-show="!filteredTemplates.length" class="p-8 text-center text-sm text-muted-foreground">Bank komponen masih kosong.</p>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center gap-0 overflow-hidden rounded-lg border border-input bg-white text-[13px]">
                            <span class="px-2.5 text-muted-foreground">Per page</span>
                            <select x-model.number="templatePageSize" @change="templatePage=1" aria-label="Baris per halaman" class="border-l border-input bg-transparent px-2 py-1.5 font-medium outline-none">
                                <option>10</option><option>25</option><option>50</option>
                            </select>
                        </label>
                        <span class="text-foreground" x-text="'Showing '+((filteredTemplates.length?((Math.min(templatePage,templatePageCount)-1)*Number(templatePageSize)+1):0))+' to '+((Math.min(templatePage,templatePageCount)-1)*Number(templatePageSize)+templateVisible.length)+' of, '+filteredTemplates.length+' results'"></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="templatePage=Math.max(1,Math.min(templatePage,templatePageCount)-1)" :disabled="templatePage<=1" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                        <template x-for="(n,i) in templateNumbers" :key="n">
                            <span class="flex items-center gap-1.5">
                                <span x-show="i>0 && n-templateNumbers[i-1]>1" class="px-1 text-muted-foreground">...</span>
                                <button type="button" @click="templatePage=n" x-text="n" class="inline-flex size-8 items-center justify-center rounded-lg border text-sm" :class="Math.min(templatePage,templatePageCount)===n?'border-[#1E2A5A] bg-[#1E2A5A] font-semibold text-white':'border-input bg-white text-foreground hover:bg-accent'"></button>
                            </span>
                        </template>
                        <button type="button" @click="templatePage=Math.min(templatePageCount,Math.min(templatePage,templatePageCount)+1)" :disabled="templatePage>=templatePageCount" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p x-show="error" x-text="error" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>
</div>
@endsection
