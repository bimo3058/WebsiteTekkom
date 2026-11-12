@extends('capstone::layouts.app')
@section('title','Manajemen Pengguna')
@section('content')
<div x-data="adminUsers" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-bold tracking-tight text-foreground">Manajemen Pengguna</h1>
    </div>

    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error" x-cloak class="rounded-xl border border-border bg-card shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2.5 border-b border-slate-100 p-3">
            <h2 class="text-sm font-bold text-foreground">Tabel Pengguna</h2>
            <div class="flex items-center gap-2">
                <label class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 px-3 py-1.5 text-[13px] text-slate-500">
                    <x-capstone::icon name="Search" size="15" />
                    <input x-model.debounce.300ms="search" @input="page=1" type="search" placeholder="Cari..." class="w-28 bg-transparent outline-none placeholder:text-slate-400 sm:w-44" aria-label="Cari pengguna">
                </label>
                <x-capstone::dropdown>
                    <x-slot:trigger>
                        <button type="button" class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 text-[13px] text-slate-500 hover:bg-slate-50" aria-label="Filter pengguna">
                            <x-capstone::icon name="ListFilter" size="15" /> Filter
                        </button>
                    </x-slot:trigger>
                    <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Peran</p>
                    <template x-for="opt in [{v:'all',l:'Semua role'},{v:'mahasiswa',l:'Mahasiswa'},{v:'dosen',l:'Dosen'},{v:'admin',l:'Admin'}]" :key="'role-'+opt.v">
                        <button type="button" @click="role=opt.v;page=1;filter();open=false" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="role===opt.v?'font-semibold text-slate-900':'text-slate-500'" role="menuitem">
                            <span x-text="opt.l"></span><span x-show="role===opt.v"><x-capstone::icon name="Check" size="14" /></span>
                        </button>
                    </template>
                    <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</p>
                    <template x-for="opt in [{v:'all',l:'Semua status'},{v:'active',l:'Aktif'},{v:'suspended',l:'Ditangguhkan'}]" :key="'status-'+opt.v">
                        <button type="button" @click="status=opt.v;page=1;filter();open=false" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="status===opt.v?'font-semibold text-slate-900':'text-slate-500'" role="menuitem">
                            <span x-text="opt.l"></span><span x-show="status===opt.v"><x-capstone::icon name="Check" size="14" /></span>
                        </button>
                    </template>
                </x-capstone::dropdown>
                <label class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500" title="Urutkan">
                    <x-capstone::icon name="ArrowUpDown" size="15" />
                    <x-capstone::select-menu borderless @select-menu-change.window="applySort($event.detail.value)" :options="['name'=>'Nama','date'=>'Tanggal Daftar']" placeholder="Urutkan" aria-label="Urutkan pengguna" size="sm" class="max-w-28 border-0 shadow-none" />
                </label>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-[13px]">
                <thead class="border-b border-slate-100 bg-slate-50/60 text-xs text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 font-medium">No</th>
                        <th class="px-4 py-2.5 font-medium">Nama User</th>
                        <th class="px-4 py-2.5 font-medium">Email</th>
                        <th class="px-4 py-2.5 font-medium">Peran Akses</th>
                        <th class="px-4 py-2.5 font-medium">Tanggal Daftar</th>
                        <th class="px-4 py-2.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(user,idx) in pagedUsers" :key="user.id">
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-slate-500" x-text="(userPage-1)*Number(pageSize)+idx+1"></td>
                            <td class="px-4 py-3">
                                <a :href="userUrl(user)" class="inline-flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-200 text-[11px] font-bold text-slate-500" x-text="initials(user)"></span>
                                    <span class="font-medium text-slate-800 hover:underline" x-text="user.name"></span>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-700" x-text="user.email"></td>
                            <td class="px-4 py-3">
                                <span class="flex flex-wrap gap-1">
                                    <template x-for="slug in user.roles" :key="slug">
                                        <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold capitalize" :class="rolePillClass(slug)" x-text="roleLabel(slug)"></span>
                                    </template>
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700" x-text="usDate(user.created_at)"></td>
                            <td class="px-4 py-3 text-right">
                                <span x-data="{menu:false}" @click.outside="menu=false" @keydown.escape.window="menu=false" class="relative inline-block text-left">
                                    <button type="button" @click="menu=!menu" :aria-expanded="menu" aria-label="Aksi user" class="rounded px-1 font-bold tracking-widest text-slate-400 hover:bg-slate-100 hover:text-slate-700">...</button>
                                    <span x-show="menu" x-cloak class="absolute right-0 z-20 min-w-40 rounded-lg border border-slate-200 bg-white p-1 shadow-lg" :class="idx>=pagedUsers.length-2 ? 'bottom-full mb-1' : 'top-full mt-1'">
                                        <a :href="userUrl(user)" @click="menu=false" class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-[13px] font-medium text-slate-700 hover:bg-slate-100"><x-capstone::icon name="Eye" size="15" />Lihat Detail</a>
                                    </span>
                                </span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!pagedUsers.length && !loading"><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">Tidak ada user yang sesuai dengan filter.</td></tr>
                    <tr x-show="loading"><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">Memuat...</td></tr>
                </tbody>
            </table>
        </div>

        @include('capstone::partials.table-footer', ['page' => 'userPage', 'pageCount' => 'userLastPage', 'total' => 'userTotal', 'from' => 'userFrom', 'to' => 'userTo', 'pageList' => 'userPageList', 'prevAction' => 'page=Math.max(1,userPage-1)', 'nextAction' => 'page=Math.min(userLastPage,userPage+1)', 'goAction' => 'page=p'])
    </div>
</div>
@endsection
