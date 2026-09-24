@extends('capstone::layouts.app')
@section('title','User Management')
@section('content')
<div x-data="adminUsers" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-bold tracking-tight text-foreground">User Management</h1>
    </div>

    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error" x-cloak class="rounded-xl border border-border bg-card shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2.5 border-b border-slate-100 p-3">
            <h2 class="text-sm font-bold text-foreground">User Table</h2>
            <div class="flex items-center gap-2">
                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-[13px] text-slate-500">
                    <x-capstone::icon name="Search" size="15" />
                    <input x-model.debounce.300ms="search" @input="filter()" type="search" placeholder="Search" class="w-28 bg-transparent outline-none placeholder:text-slate-400 sm:w-44" aria-label="Search users">
                </label>
                <span x-data="{open:false}" @click.outside="open=false" @keydown.escape.window="open=false" class="relative">
                    <button type="button" @click="open=!open" :aria-expanded="open" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500 hover:bg-slate-50">
                        <x-capstone::icon name="ListFilter" size="15" /> Filter
                    </button>
                    <span x-show="open" x-cloak class="absolute right-0 z-20 mt-1 w-56 rounded-lg border border-slate-200 bg-white p-3 shadow-lg">
                        <label class="mb-2 block text-xs font-medium text-slate-500">Role
                            <select x-model="role" @change="filter()" class="mt-1 w-full rounded-md border border-slate-200 bg-transparent px-2 py-1.5 text-[13px] text-slate-700 outline-none">
                                <option value="all">Semua role</option>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="admin">Admin</option>
                            </select>
                        </label>
                        <label class="block text-xs font-medium text-slate-500">Status
                            <select x-model="status" @change="filter()" class="mt-1 w-full rounded-md border border-slate-200 bg-transparent px-2 py-1.5 text-[13px] text-slate-700 outline-none">
                                <option value="all">Semua status</option>
                                <option value="active">Aktif</option>
                                <option value="suspended">Ditangguhkan</option>
                            </select>
                        </label>
                    </span>
                </span>
                <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500" title="Urutkan">
                    <x-capstone::icon name="ArrowUpDown" size="15" />
                    <select @change="applySort($event.target.value)" class="max-w-24 bg-transparent outline-none" aria-label="Sort users">
                        <option value="name">Sort by</option>
                        <option value="name">Nama</option>
                        <option value="date">Tanggal Daftar</option>
                    </select>
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
                        <th class="px-4 py-2.5 font-medium">Access Role</th>
                        <th class="px-4 py-2.5 font-medium">Tanggal Daftar</th>
                        <th class="px-4 py-2.5 text-right font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(user,idx) in items" :key="user.id">
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
                                    <span x-show="menu" x-cloak class="absolute right-0 z-20 min-w-40 rounded-lg border border-slate-200 bg-white p-1 shadow-lg" :class="idx>=items.length-2 ? 'bottom-full mb-1' : 'top-full mt-1'">
                                        <a :href="userUrl(user)" @click="menu=false" class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-[13px] font-medium text-slate-700 hover:bg-slate-100"><x-capstone::icon name="Eye" size="15" />Lihat Detail</a>
                                    </span>
                                </span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!items.length && !loading"><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">Tidak ada user yang sesuai dengan filter.</td></tr>
                    <tr x-show="loading"><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">Memuat...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-3 text-[13px]">
            <div class="flex items-center gap-2 text-slate-500">
                <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2 py-1">Per page
                    <select x-model.number="pageSize" @change="filter()" class="bg-transparent font-semibold text-slate-700 outline-none" aria-label="Baris per halaman"><option>10</option><option>25</option><option>50</option></select>
                </label>
                <span x-text="'Showing '+userFrom+' to '+userTo+' of, '+userTotal+' results'"></span>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" @click="page=Math.max(1,userPage-1);load()" :disabled="userPage<=1" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman sebelumnya"><x-capstone::icon name="ChevronLeft" size="15" /></button>
                <template x-for="(p,i) in userPageList" :key="i+'-'+p">
                    <button type="button" x-show="p!=='…'" @click="page=p;load()" class="min-w-8 rounded-lg border px-2 py-1.5 text-xs font-semibold" :class="p===userPage ? 'border-primary bg-primary text-white' : 'border-slate-200 text-slate-500 hover:bg-slate-50'" x-text="p"></button>
                    <span x-show="p==='…'" class="px-1 text-xs text-slate-400">...</span>
                </template>
                <button type="button" @click="page=Math.min(userLastPage,userPage+1);load()" :disabled="userPage>=userLastPage" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman berikutnya"><x-capstone::icon name="ChevronRight" size="15" /></button>
            </div>
        </div>
    </div>
</div>
@endsection
