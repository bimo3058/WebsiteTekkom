@extends('capstone::layouts.app')
@section('title','Audit Log')
@section('content')
<div x-data="adminAuditLogs" @keydown.escape.window="closeMenu()" @scroll.window="closeMenu()" @resize.window="closeMenu()" class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Audit Log</h1>
            <p class="mt-1 text-sm text-muted-foreground">Jejak aktivitas admin termasuk penghapusan kelompok dan alasannya.</p>
        </div>
        <x-capstone::button variant="outline" @click="load()" ::disabled="loading"><x-capstone::icon name="RefreshCw" size="15" />Refresh</x-capstone::button>
    </div>

    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h3 class="text-base font-semibold">Tabel Audit Log</h3>
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
                    <input x-model.debounce.300ms="search" @input="page=1;load()" type="search" placeholder="Cari aksi / payload..." aria-label="Cari audit log" class="h-9 w-56 rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
                </div>
                <select x-model="action" @change="filter()" aria-label="Filter aksi" class="h-9 rounded-lg border border-input bg-white px-2.5 text-sm text-muted-foreground outline-none">
                    <option value="">Semua aksi</option>
                    <template x-for="a in actionTypes" :key="a"><option :value="a" x-text="a"></option></template>
                </select>
                <select x-model="periodId" @change="filter()" aria-label="Filter periode" class="h-9 rounded-lg border border-input bg-white px-2.5 text-sm text-muted-foreground outline-none">
                    <option value="">Semua periode</option>
                    <template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name"></option></template>
                </select>
                <label class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-input bg-white px-2.5 text-sm text-muted-foreground">Dari <input type="date" x-model="dateFrom" @change="filter()" class="bg-transparent text-foreground outline-none"></label>
                <label class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-input bg-white px-2.5 text-sm text-muted-foreground">Sampai <input type="date" x-model="dateTo" @change="filter()" class="bg-transparent text-foreground outline-none"></label>
                <button type="button" @click="reset()" class="inline-flex h-9 items-center rounded-lg border border-input bg-white px-3 text-sm text-muted-foreground shadow-xs hover:bg-accent">Reset</button>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead>
                    <tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                        <th class="px-4 py-3 font-medium">Waktu</th>
                        <th class="px-4 py-3 font-medium">Aksi</th>
                        <th class="px-4 py-3 font-medium">Target</th>
                        <th class="px-4 py-3 font-medium">Pelaku</th>
                        <th class="px-4 py-3 font-medium">Ringkasan</th>
                        <th class="px-5 py-3 text-right font-medium">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="log in items" :key="log.id">
                        <tr class="bg-white align-top hover:bg-muted/30">
                            <td class="whitespace-nowrap px-4 py-4 text-muted-foreground" x-text="shortDate(log.created_at)"></td>
                            <td class="whitespace-nowrap px-4 py-4"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="actionClass(log.action)" x-text="log.action"></span></td>
                            <td class="whitespace-nowrap px-4 py-4 text-foreground" x-text="(log.target_type||'—')+' #'+(log.target_id??'—')"></td>
                            <td class="px-4 py-4 text-foreground" x-text="actorName(log)"></td>
                            <td class="max-w-72 px-4 py-4 text-muted-foreground" x-text="log.payload?.reason || log.payload?.group_id ? ('Grup #'+(log.payload?.group_id ?? log.target_id)) : '—'"></td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" @click="openRowMenu($event,log)" aria-label="Aksi" aria-haspopup="menu" class="rounded-md p-1.5 text-[#666D80] hover:bg-muted"><x-capstone::icon name="Ellipsis" class="size-5" /></button>
                            </td>
                        </tr>
                        <tr x-show="expanded===log.id" x-cloak class="bg-muted/30">
                            <td colspan="6" class="px-4 py-3">
                                <dl class="grid gap-x-6 gap-y-1.5 sm:grid-cols-2">
                                    <template x-for="[key,value] in payloadEntries(log)" :key="key">
                                        <div class="flex gap-2 text-[13px]">
                                            <dt class="shrink-0 font-medium text-muted-foreground" x-text="key+':'"></dt>
                                            <dd class="break-words text-foreground" x-text="payloadText(value)"></dd>
                                        </div>
                                    </template>
                                    <p x-show="!payloadEntries(log).length" class="text-[13px] text-muted-foreground">Tidak ada payload.</p>
                                </dl>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <p x-show="!items.length" class="p-8 text-center text-sm text-muted-foreground">Belum ada log.</p>
        </div>

        <div x-show="openMenuItem" @click.away="closeMenu()" x-cloak :style="menuStyle" style="position: fixed; z-index: 50;" role="menu" class="w-48 rounded-lg border border-border bg-white p-1.5 text-left shadow-md">
            <button type="button" @click="expanded=expanded===openMenuItem.id?null:openMenuItem.id;closeMenu()" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-foreground hover:bg-accent">Lihat detail</button>
        </div>
        <div x-show="items.length" x-cloak class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-0 overflow-hidden rounded-lg border border-input bg-white text-[13px]">
                    <span class="px-2.5 text-muted-foreground">Per page</span>
                    <select x-model.number="pageSize" @change="page=1;load()" aria-label="Baris per halaman" class="border-l border-input bg-transparent px-2 py-1.5 font-medium outline-none"><option>10</option><option>25</option><option>50</option></select>
                </label>
                <span class="text-foreground"><span x-text="'Showing '+showingFrom+' to '+showingTo+' of, '+(pagination.total ?? 0)+' results'"></span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="gotoPage(page-1)" :disabled="page<=1" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                <template x-for="(n,i) in pageNumbers" :key="n">
                    <span class="flex items-center gap-1.5">
                        <span x-show="i>0 && n-pageNumbers[i-1]>1" class="px-1 text-muted-foreground">...</span>
                        <button type="button" @click="gotoPage(n)" x-text="n" class="inline-flex size-8 items-center justify-center rounded-lg border text-sm" :class="page===n?'border-[#1E2A5A] bg-[#1E2A5A] font-semibold text-white':'border-input bg-white text-foreground hover:bg-accent'"></button>
                    </span>
                </template>
                <button type="button" @click="gotoPage(page+1)" :disabled="page>=(pagination.last_page||1)" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
            </div>
        </div>
    </div>
</div>
@endsection
