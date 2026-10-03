@extends('capstone::layouts.app')
@section('title','Audit Log')
@section('content')
<div x-data="adminAuditLogs" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Audit Log</h1>
            <p class="text-sm text-slate-500">Jejak aktivitas admin termasuk penghapusan kelompok dan alasannya.</p>
        </div>
        <x-capstone::button variant="outline" @click="load()" ::disabled="loading"><x-capstone::icon name="RefreshCw" size="15" />Refresh</x-capstone::button>
    </div>

    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error" x-cloak class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center gap-2.5 border-b border-slate-100 p-3">
            <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-[13px] text-slate-500">
                <x-capstone::icon name="Search" size="15" />
                <input x-model.debounce.300ms="search" @input="page=1;load()" type="search" placeholder="Cari aksi / payload..." class="w-44 bg-transparent outline-none placeholder:text-slate-400" aria-label="Cari audit log">
            </label>
            <select x-model="action" @change="filter()" class="rounded-lg border border-slate-200 bg-transparent px-2.5 py-1.5 text-[13px] text-slate-600 outline-none" aria-label="Filter aksi">
                <option value="">Semua aksi</option>
                <template x-for="a in actionTypes" :key="a"><option :value="a" x-text="a"></option></template>
            </select>
            <select x-model="periodId" @change="filter()" class="rounded-lg border border-slate-200 bg-transparent px-2.5 py-1.5 text-[13px] text-slate-600 outline-none" aria-label="Filter periode">
                <option value="">Semua periode</option>
                <template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name"></option></template>
            </select>
            <label class="inline-flex items-center gap-1.5 text-[13px] text-slate-500">Dari <input type="date" x-model="dateFrom" @change="filter()" class="rounded-lg border border-slate-200 px-2 py-1.5 text-slate-600 outline-none"></label>
            <label class="inline-flex items-center gap-1.5 text-[13px] text-slate-500">Sampai <input type="date" x-model="dateTo" @change="filter()" class="rounded-lg border border-slate-200 px-2 py-1.5 text-slate-600 outline-none"></label>
            <button type="button" @click="reset()" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500 hover:bg-slate-50">Reset</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-[13px]">
                <thead class="border-b border-slate-100 bg-slate-50/60 text-xs text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 font-medium">Waktu</th>
                        <th class="px-4 py-2.5 font-medium">Aksi</th>
                        <th class="px-4 py-2.5 font-medium">Target</th>
                        <th class="px-4 py-2.5 font-medium">Pelaku</th>
                        <th class="px-4 py-2.5 font-medium">Ringkasan</th>
                        <th class="px-4 py-2.5 text-right font-medium">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="log in items" :key="log.id">
                        <tr class="align-top hover:bg-slate-50/60">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500" x-text="shortDate(log.created_at)"></td>
                            <td class="whitespace-nowrap px-4 py-3"><span class="rounded-full px-2 py-0.5 text-[11px] font-semibold" :class="actionClass(log.action)" x-text="log.action"></span></td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700" x-text="(log.target_type||'—')+' #'+(log.target_id??'—')"></td>
                            <td class="px-4 py-3 text-slate-700" x-text="actorName(log)"></td>
                            <td class="max-w-72 px-4 py-3 text-slate-600" x-text="log.payload?.reason || log.payload?.group_id ? ('Grup #'+(log.payload?.group_id ?? log.target_id)) : '—'"></td>
                            <td class="px-4 py-3 text-right">
                                <button type="button" @click="expanded=expanded===log.id?null:log.id" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 hover:bg-slate-50" :aria-label="'Detail log '+log.id"><x-capstone::icon name="ChevronDown" size="15" /></button>
                            </td>
                        </tr>
                        <tr x-show="expanded===log.id" x-cloak class="bg-slate-50/60">
                            <td colspan="6" class="px-4 py-3">
                                <dl class="grid gap-x-6 gap-y-1.5 sm:grid-cols-2">
                                    <template x-for="[key,value] in payloadEntries(log)" :key="key">
                                        <div class="flex gap-2 text-[13px]">
                                            <dt class="shrink-0 font-medium text-slate-500" x-text="key+':'"></dt>
                                            <dd class="break-words text-slate-800" x-text="payloadText(value)"></dd>
                                        </div>
                                    </template>
                                    <p x-show="!payloadEntries(log).length" class="text-[13px] text-slate-400">Tidak ada payload.</p>
                                </dl>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!items.length"><td colspan="6" class="px-3 py-10 text-center text-sm text-slate-400">Belum ada log.</td></tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-3 text-[13px]">
            <div class="flex items-center gap-2 text-slate-500">
                <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2 py-1">Per page
                    <select x-model.number="pageSize" @change="page=1;load()" class="bg-transparent font-semibold text-slate-700 outline-none" aria-label="Baris per halaman"><option>10</option><option>25</option><option>50</option></select>
                </label>
                <span x-text="'Total '+ (pagination.total ?? 0) +' log'"></span>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" @click="gotoPage(page-1)" :disabled="page<=1" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman sebelumnya"><x-capstone::icon name="ChevronLeft" size="15" /></button>
                <span class="px-2 text-xs font-semibold text-slate-500" x-text="page+' / '+(pagination.last_page||1)"></span>
                <button type="button" @click="gotoPage(page+1)" :disabled="page>=(pagination.last_page||1)" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman berikutnya"><x-capstone::icon name="ChevronRight" size="15" /></button>
            </div>
        </div>
    </div>
</div>
@endsection
