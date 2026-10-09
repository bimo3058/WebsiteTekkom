@extends('capstone::layouts.app')
@section('title','Group Progress')
@section('content')
<div x-data="adminProgress" @keydown.escape.window="closeMenu()" @scroll.window="closeMenu()" @resize.window="closeMenu()" class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Group Progress</h1>
            <p class="mt-1 text-sm text-muted-foreground">Pantau progres setiap kelompok beserta status fase pada periode berjalan.</p>
        </div>
        <button type="button" @click="exportCsv()" :disabled="loading || saving" class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-[#1E2A5A] px-4 text-sm font-medium text-white shadow-xs hover:bg-[#16204a] disabled:opacity-40"><x-capstone::icon name="Download" class="size-4" />Export CSV</button>
    </div>
    @include('capstone::pages.admin.shared.toolbar')
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h3 class="text-base font-semibold">Tabel Group Progress</h3>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[860px] text-left text-sm">
                <thead>
                    <tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                        <th class="px-4 py-3 font-medium">No</th>
                        <th class="px-4 py-3 font-medium">Kelompok / Judul</th>
                        <th class="px-4 py-3 font-medium">Periode</th>
                        <th class="px-4 py-3 font-medium">Anggota</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Progress</th>
                        <th class="px-4 py-3 font-medium">Fase</th>
                        <th class="px-5 py-3 text-right font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="(item,index) in visible" :key="item.id">
                        <tr class="bg-white hover:bg-muted/30">
                            <td class="px-4 py-4 text-foreground" x-text="(Math.min(page,pageCount)-1)*Number(pageSize)+index+1"></td>
                            <td class="px-4 py-4"><p class="font-medium text-foreground" x-text="groupName(item)"></p><p class="mt-0.5 text-[13px] text-muted-foreground" x-text="item.title?.title"></p></td>
                            <td class="whitespace-nowrap px-4 py-4 text-foreground" x-text="item.period?.name"></td>
                            <td class="px-4 py-4 tabular-nums text-foreground" x-text="item.members_count"></td>
                            <td class="whitespace-nowrap px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600" x-text="item.status"></span></td>
                            <td class="min-w-36 px-4 py-4"><div class="mb-1.5 text-[13px] tabular-nums text-foreground" x-text="percent(item)+'%'"></div><div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-[#1E2A5A]" :style="'width:'+percent(item)+'%'"></div></div></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap gap-1.5"><template x-for="phase in item.progress?.phases || []" :key="phase.phase"><span :title="phase.status" class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs" :class="phase.status==='completed'?'border-emerald-200 bg-emerald-50 text-emerald-700':phase.status==='locked'?'border-slate-200 text-slate-500':'border-[#1E2A5A]/20 bg-[#1E2A5A]/5 text-[#1E2A5A]'" x-text="phase.phase"></span></template></div></td>
                            <td class="px-5 py-4 text-right"><button type="button" @click="openRowMenu($event,item)" aria-label="Aksi" aria-haspopup="menu" class="rounded-md p-1.5 text-[#666D80] hover:bg-muted"><x-capstone::icon name="Ellipsis" class="size-5" /></button></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <p x-show="!filtered.length" class="p-8 text-center text-sm text-muted-foreground">Belum ada kelompok.</p>
        </div>
        <div x-show="filtered.length" x-cloak class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-0 overflow-hidden rounded-lg border border-input bg-white text-[13px]">
                    <span class="px-2.5 text-muted-foreground">Per page</span>
                    <select x-model.number="pageSize" @change="page=1" aria-label="Baris per halaman" class="border-l border-input bg-transparent px-2 py-1.5 font-medium outline-none"><option>10</option><option>25</option><option>50</option></select>
                </label>
                <span class="text-foreground"><span x-text="'Showing '+showingFrom+' to '+showingTo+' of, '+filtered.length+' results'"></span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="goPage(page-1)" :disabled="page<=1" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                <template x-for="(n,i) in pageNumbers" :key="n">
                    <span class="flex items-center gap-1.5">
                        <span x-show="i>0 && n-pageNumbers[i-1]>1" class="px-1 text-muted-foreground">...</span>
                        <button type="button" @click="goPage(n)" x-text="n" class="inline-flex size-8 items-center justify-center rounded-lg border text-sm" :class="page===n?'border-[#1E2A5A] bg-[#1E2A5A] font-semibold text-white':'border-input bg-white text-foreground hover:bg-accent'"></button>
                    </span>
                </template>
                <button type="button" @click="goPage(page+1)" :disabled="page>=pageCount" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
            </div>
        </div>
    </div>
    <div x-show="openMenuItem" x-cloak @click.outside="closeMenu()" class="fixed z-50 w-48 rounded-lg border border-border bg-white p-1 shadow-lg" :style="menuStyle" role="menu">
        <a :href="url('/admin/groups/'+openMenuItem.id)" class="block rounded-md px-2 py-1.5 text-sm text-foreground hover:bg-accent">Detail</a>
    </div>
</div>
@endsection
