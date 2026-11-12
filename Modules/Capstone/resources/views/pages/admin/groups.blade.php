@extends('capstone::layouts.app')
@section('title','Kelompok')
@section('content')
<div x-data="adminGroups(false)" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-bold tracking-tight text-foreground">Kelompok</h1>
    </div>

    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error" x-cloak class="rounded-xl border border-border bg-card shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2.5 border-b border-slate-100 p-3">
            <h2 class="text-sm font-bold text-foreground">Total Kelompok</h2>
            <div class="flex items-center gap-2">
                <label class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 px-3 py-1.5 text-[13px] text-slate-500">
                    <x-capstone::icon name="Search" size="15" />
                    <input x-model.debounce.300ms="search" @input="page=1" type="search" placeholder="Cari..." class="w-28 bg-transparent outline-none placeholder:text-slate-400 sm:w-44" aria-label="Cari kelompok">
                </label>
                <x-capstone::dropdown>
                    <x-slot:trigger>
                        <button type="button" class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 text-[13px] text-slate-500 hover:bg-slate-50" aria-label="Filter kelompok">
                            <x-capstone::icon name="ListFilter" size="15" /> Filter
                        </button>
                    </x-slot:trigger>
                    <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Periode</p>
                    <button type="button" @click="periodId='all';page=1;loadAll();open=false" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="String(periodId)==='all'?'font-semibold text-slate-900':'text-slate-500'" role="menuitem"><span>Semua periode</span><span x-show="String(periodId)==='all'"><x-capstone::icon name="Check" size="14" /></span></button>
                    <template x-for="period in periods" :key="period.id">
                        <button type="button" @click="periodId=String(period.id);page=1;loadAll();open=false" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="String(periodId)===String(period.id)?'font-semibold text-slate-900':'text-slate-500'" role="menuitem"><span class="truncate" x-text="period.name"></span><span x-show="String(periodId)===String(period.id)"><x-capstone::icon name="Check" size="14" /></span></button>
                    </template>
                    <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</p>
                    <button type="button" @click="status='all';page=1;loadAll();open=false" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="status==='all'?'font-semibold text-slate-900':'text-slate-500'" role="menuitem"><span>Semua status</span><span x-show="status==='all'"><x-capstone::icon name="Check" size="14" /></span></button>
                    <template x-for="s in ['FORMING','FORMING_SOLO','READY_FOR_BIDDING','TITLE_PROPOSED','TITLE_APPROVED','READY_FOR_FINALIZATION','KELOMPOK_FINAL','PDC1_ACTIVE','READY_FOR_SEMPRO','SEMPRO_DONE','PDC2_ACTIVE','EXPO_REGISTERED','READY_FOR_TA_INDIVIDUAL','CLOSED','DISSOLVED']" :key="s">
                        <button type="button" @click="status=s;page=1;loadAll();open=false" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="status===s?'font-semibold text-slate-900':'text-slate-500'" role="menuitem"><span class="truncate" x-text="s.replace(/_/g,' ')"></span><span x-show="status===s"><x-capstone::icon name="Check" size="14" /></span></button>
                    </template>
                </x-capstone::dropdown>
                <label class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500" title="Urutkan">
                    <x-capstone::icon name="ArrowUpDown" size="15" />
                    <x-capstone::select-menu borderless x-model="sortBy" @select-menu-change.window="page=1" :options="['all'=>'Terbaru','code'=>'Kode','status'=>'Status']" placeholder="Urutkan" aria-label="Urutkan kelompok" size="sm" class="max-w-28 border-0 shadow-none" />
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
                        <th class="px-4 py-2.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item,idx) in pagedGroups" :key="item.id">
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-slate-500" x-text="(groupPage-1)*Number(pageSize)+idx+1"></td>
                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-700" x-text="item.code || ('Kelompok '+item.id)"></td>
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
                            <td class="max-w-56 px-4 py-3 text-slate-700">
                                <span class="flex items-start gap-1">
                                    <span class="flex-1" x-text="item.title?.title || 'Belum ada Judul'"></span>
                                    <button type="button" x-show="item.title" @click="openRename(item)" title="Ubah judul" aria-label="Ubah judul" class="shrink-0 rounded p-1 text-slate-300 hover:bg-slate-100 hover:text-slate-600"><x-capstone::icon name="Pencil" size="13" /></button>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span x-data="{menu:false}" @click.outside="menu=false" @keydown.escape.window="menu=false" class="relative inline-block text-left">
                                    <button type="button" @click="menu=!menu" :aria-expanded="menu" aria-label="Aksi group" class="rounded px-1 font-bold tracking-widest text-slate-400 hover:bg-slate-100 hover:text-slate-700">...</button>
                                    <span x-show="menu" x-cloak class="absolute right-0 z-20 min-w-40 rounded-lg border border-slate-200 bg-white p-1 shadow-lg" :class="idx>=pagedGroups.length-2 ? 'bottom-full mb-1' : 'top-full mt-1'">
                                        <a :href="url('/admin/groups/'+item.id)" @click="menu=false" class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-[13px] font-medium text-slate-700 hover:bg-slate-100"><x-capstone::icon name="Eye" size="15" />Lihat Detail</a>
                                        <button type="button" x-show="item.title" @click="menu=false;openRename(item)" class="flex w-full items-center gap-2 rounded-md px-2.5 py-1.5 text-[13px] font-medium text-slate-700 hover:bg-slate-100"><x-capstone::icon name="Pencil" size="15" />Ubah Judul</button>
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

        @include('capstone::partials.table-footer', ['page' => 'groupPage', 'pageCount' => 'groupLastPage', 'total' => 'groupTotal', 'from' => 'groupFrom', 'to' => 'groupTo', 'pageList' => 'groupPageList', 'prevAction' => 'page=Math.max(1,groupPage-1)', 'nextAction' => 'page=Math.min(groupLastPage,groupPage+1)', 'goAction' => 'page=p'])
    </div>

    <x-capstone::dialog id="group-rename-title" title="Ubah Judul Capstone" description="Hanya teks judul yang diubah. Status kelompok tidak berubah.">
        <div class="mt-4 space-y-4">
            <p class="text-sm text-slate-600">Kelompok <span class="font-semibold text-foreground" x-text="renameTarget ? (renameTarget.code || ('Kelompok '+renameTarget.id)) : ''"></span></p>
            <input x-model="renameText" type="text" maxlength="255" class="w-full rounded-lg border border-slate-200 p-3 text-sm" placeholder="Tulis judul baru..." aria-label="Judul baru">
            <div class="flex justify-end gap-2">
                <x-btn style="outline" @click="$el.closest('dialog').close()">Batal</x-btn>
                <x-btn @click="sendRename()" x-bind:disabled="saving || !renameText.trim() || renameText.trim() === (renameTarget?.title?.title || '')">Simpan</x-btn>
            </div>
        </div>
    </x-capstone::dialog>

    @include('capstone::partials.group-delete-dialogs')
</div>
@endsection
