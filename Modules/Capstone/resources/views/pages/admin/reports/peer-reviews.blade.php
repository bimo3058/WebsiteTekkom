@extends('capstone::layouts.app')
@section('title','Laporan Peer Review')
@section('content')
<div x-data="adminReports('peer-reviews')" class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Laporan Peer Review</h1>
            <p class="mt-1 text-sm text-muted-foreground">Hasil peer review antar anggota kelompok pada periode berjalan.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-capstone::button href="/admin/reports" variant="outline">Reports</x-capstone::button>
            <button type="button" @click="exportReport()" ::disabled="loading || saving || !periodId" class="inline-flex h-9 items-center gap-2 rounded-lg bg-[#1E2A5A] px-4 text-sm font-medium text-white shadow-xs hover:bg-[#16204a] disabled:opacity-40"><x-capstone::icon name="Download" class="size-4" />Export CSV</button>
        </div>
    </div>
    @include('capstone::pages.admin.shared.toolbar')
    @include('capstone::partials.loading')
    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>
    <div x-show="!loading && !error && periodId" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h3 class="text-base font-semibold">Tabel Peer Review</h3>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[860px] text-left text-sm">
                <thead>
                    <tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                        <th class="w-12 px-4 py-3 font-medium">No</th>
                        <template x-for="column in columns" :key="column[0]"><th class="px-4 py-3 font-medium" x-text="column[1]"></th></template>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="(item,index) in items" :key="item.id || index">
                        <tr class="bg-white hover:bg-muted/30">
                            <td class="px-4 py-4 text-muted-foreground" x-text="(page-1)*Number(pageSize)+index+1"></td>
                            <template x-for="column in columns" :key="column[0]"><td class="px-4 py-4 text-foreground" x-text="value(item,column[0])"></td></template>
                        </tr>
                    </template>
                </tbody>
            </table>
            <p x-show="!items.length" class="p-8 text-center text-sm text-muted-foreground">Belum ada data pada periode ini.</p>
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
