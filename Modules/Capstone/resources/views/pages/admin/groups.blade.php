@extends('capstone::layouts.app')
@section('title','Groups')
@section('content')
<div x-data="adminGroups(false)" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Groups</h1>
    </div>

    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error" x-cloak class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2.5 border-b border-slate-100 p-3">
            <h2 class="text-sm font-bold text-slate-900">Total Groups</h2>
            <div class="flex items-center gap-2">
                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-[13px] text-slate-500">
                    <x-capstone::icon name="Search" size="15" />
                    <input x-model.debounce.300ms="search" @input="page=1" type="search" placeholder="Search" class="w-28 bg-transparent outline-none placeholder:text-slate-400 sm:w-44" aria-label="Search groups">
                </label>
                <span x-data="{open:false}" @click.outside="open=false" @keydown.escape.window="open=false" class="relative">
                    <button type="button" @click="open=!open" :aria-expanded="open" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500 hover:bg-slate-50">
                        <x-capstone::icon name="ListFilter" size="15" /> Filter
                    </button>
                    <span x-show="open" x-cloak class="absolute right-0 z-20 mt-1 w-56 rounded-lg border border-slate-200 bg-white p-3 shadow-lg">
                        <label class="mb-2 block text-xs font-medium text-slate-500">Periode
                            <select x-model="periodId" @change="page=1;loadAll()" class="mt-1 w-full rounded-md border border-slate-200 bg-transparent px-2 py-1.5 text-[13px] text-slate-700 outline-none">
                                <option value="">Semua periode</option>
                                <template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name"></option></template>
                            </select>
                        </label>
                        <label class="block text-xs font-medium text-slate-500">Status
                            <select x-model="status" @change="page=1;loadAll()" class="mt-1 w-full rounded-md border border-slate-200 bg-transparent px-2 py-1.5 text-[13px] text-slate-700 outline-none">
                                <option value="">Semua status</option>
                                <template x-for="s in ['FORMING','FORMING_SOLO','READY_FOR_BIDDING','TITLE_PROPOSED','TITLE_APPROVED','READY_FOR_FINALIZATION','KELOMPOK_FINAL','PDC1_ACTIVE','READY_FOR_SEMPRO','SEMPRO_DONE','PDC2_ACTIVE','EXPO_REGISTERED','READY_FOR_TA_INDIVIDUAL','CLOSED','DISSOLVED']"><option :value="s" x-text="s.replace(/_/g,' ')"></option></template>
                            </select>
                        </label>
                    </span>
                </span>
                <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500" title="Urutkan">
                    <x-capstone::icon name="ArrowUpDown" size="15" />
                    <select x-model="sortBy" @change="page=1" class="max-w-24 bg-transparent outline-none" aria-label="Sort groups">
                        <option value="">Sort by</option>
                        <option value="">Terbaru</option>
                        <option value="code">Kode</option>
                        <option value="status">Status</option>
                    </select>
                </label>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-[13px]">
                <thead class="border-b border-slate-100 bg-slate-50/60 text-xs text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 font-medium">No</th>
                        <th class="px-4 py-2.5 font-medium">Kode</th>
                        <th class="px-4 py-2.5 font-medium">Ketua</th>
                        <th class="px-4 py-2.5 font-medium">Anggota</th>
                        <th class="px-4 py-2.5 font-medium">Dosen Pembimbing</th>
                        <th class="px-4 py-2.5 font-medium">Status</th>
                        <th class="px-4 py-2.5 font-medium">Judul Capstone</th>
                        <th class="px-4 py-2.5 text-right font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item,idx) in pagedGroups" :key="item.id">
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-slate-500" x-text="(groupPage-1)*Number(pageSize)+idx+1"></td>
                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-700" x-text="item.code || ('Group '+item.id)"></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 py-0.5 pl-0.5 pr-2.5">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-500" x-text="initials(ketuaName(item))"></span>
                                    <span class="max-w-28 truncate text-xs font-medium text-slate-700" x-text="ketuaName(item)"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="flex items-center">
                                    <template x-for="(m,mi) in (item.members||[]).slice(0,4)" :key="m.id">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-slate-200 text-[10px] font-bold text-slate-500" :class="mi>0 && '-ml-2'" :title="memberName(m)" x-text="initials(memberName(m))"></span>
                                    </template>
                                    <span x-show="(item.members||[]).length>4" class="flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-slate-100 text-[10px] font-bold text-slate-500 -ml-2" x-text="'+'+((item.members||[]).length-4)"></span>
                                    <span x-show="!(item.members||[]).length" class="text-xs text-slate-300">—</span>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="flex max-w-44 flex-col items-start gap-1">
                                    <template x-for="name in supervisorNames(item)" :key="name">
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 py-0.5 pl-0.5 pr-2.5">
                                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-500" x-text="initials(name)"></span>
                                            <span class="max-w-28 truncate text-xs font-medium text-slate-700" x-text="name"></span>
                                        </span>
                                    </template>
                                    <span x-show="!supervisorNames(item).length" class="text-xs text-slate-300">—</span>
                                </span>
                            </td>
                            <td class="px-4 py-3"><span class="whitespace-nowrap rounded-full px-2 py-0.5 text-[11px] font-semibold" :class="groupStatusClass(item.status)" x-text="groupStatusLabel(item)"></span></td>
                            <td class="max-w-56 px-4 py-3 text-slate-700" x-text="item.title?.title || 'Belum ada Judul'"></td>
                            <td class="px-4 py-3 text-right">
                                <span x-data="{menu:false}" @click.outside="menu=false" @keydown.escape.window="menu=false" class="relative inline-block text-left">
                                    <button type="button" @click="menu=!menu" :aria-expanded="menu" aria-label="Aksi group" class="rounded px-1 font-bold tracking-widest text-slate-400 hover:bg-slate-100 hover:text-slate-700">...</button>
                                    <span x-show="menu" x-cloak class="absolute right-0 z-20 min-w-40 rounded-lg border border-slate-200 bg-white p-1 shadow-lg" :class="idx>=pagedGroups.length-2 ? 'bottom-full mb-1' : 'top-full mt-1'">
                                        <a :href="url('/admin/groups/'+item.id)" @click="menu=false" class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-[13px] font-medium text-slate-700 hover:bg-slate-100"><x-capstone::icon name="Eye" size="15" />Lihat Detail</a>
                                        <button type="button" @click="menu=false;openDelete(item)" class="flex w-full items-center gap-2 rounded-md px-2.5 py-1.5 text-[13px] font-medium text-red-600 hover:bg-red-50"><x-capstone::icon name="Trash2" size="15" />Hapus</button>
                                    </span>
                                </span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!pagedGroups.length && !loading"><td colspan="8" class="px-3 py-10 text-center text-sm text-slate-400">Belum ada group.</td></tr>
                    <tr x-show="loading"><td colspan="8" class="px-3 py-10 text-center text-sm text-slate-400">Memuat...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-3 text-[13px]">
            <div class="flex items-center gap-2 text-slate-500">
                <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2 py-1">Per page
                    <select x-model.number="pageSize" @change="page=1" class="bg-transparent font-semibold text-slate-700 outline-none" aria-label="Baris per halaman"><option>10</option><option>25</option><option>50</option></select>
                </label>
                <span x-text="'Showing '+groupFrom+' to '+groupTo+' of, '+groupTotal+' results'"></span>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" @click="page=Math.max(1,groupPage-1)" :disabled="groupPage<=1" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman sebelumnya"><x-capstone::icon name="ChevronLeft" size="15" /></button>
                <template x-for="(p,i) in groupPageList" :key="i+'-'+p">
                    <button type="button" x-show="p!=='…'" @click="page=p" class="min-w-8 rounded-lg border px-2 py-1.5 text-xs font-semibold" :class="p===groupPage ? 'border-[#2f3d8a] bg-[#2f3d8a] text-white' : 'border-slate-200 text-slate-500 hover:bg-slate-50'" x-text="p"></button>
                    <span x-show="p==='…'" class="px-1 text-xs text-slate-400">...</span>
                </template>
                <button type="button" @click="page=Math.min(groupLastPage,groupPage+1)" :disabled="groupPage>=groupLastPage" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman berikutnya"><x-capstone::icon name="ChevronRight" size="15" /></button>
            </div>
        </div>
    </div>

    @include('capstone::partials.group-delete-dialogs')
</div>
@endsection
