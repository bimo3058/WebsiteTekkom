@extends('capstone::layouts.app')
@section('title','Detail User')
@section('content')
<div x-data="adminUsers('detail')" class="space-y-6">
    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <template x-if="user && !loading && !error">
        <div class="space-y-5">
            <div>
                <x-capstone::button href="/admin/users" variant="outline" class="text-slate-500"><x-capstone::icon name="ChevronLeft" size="16" /> Kembali</x-capstone::button>
            </div>

            <div class="flex items-center gap-4">
                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xl font-bold text-slate-500" x-text="initials(user)"></span>
                <div>
                    <h1 class="flex flex-wrap items-center gap-2 text-2xl font-bold tracking-tight text-foreground"><span x-text="user.name"></span><span x-show="user.is_sso" class="rounded-full border border-violet-400 px-2.5 py-0.5 text-xs font-semibold text-violet-600">SSO</span></h1>
                    <p class="mt-1 text-sm text-slate-500" x-text="[user.nim,user.nip].filter(Boolean).join(' / ')||'—'"></p>
                </div>
            </div>

            <div class="grid gap-x-10 gap-y-4 md:grid-cols-2">
                <div class="space-y-4">
                    <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                        <p class="text-sm text-slate-500">Email</p>
                        <p class="break-words text-sm font-medium text-slate-800" x-text="user.email"></p>
                    </div>
                    <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                        <p class="text-sm text-slate-500">Angkatan</p>
                        <p class="text-sm font-medium text-slate-800" x-text="user.cohort_year || '—'"></p>
                    </div>
                    <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                        <p class="text-sm text-slate-500">Access Role</p>
                        <p class="flex flex-wrap gap-1">
                            <template x-for="slug in user.roles" :key="slug">
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold capitalize" :class="rolePillClass(slug)" x-text="roleLabel(slug)"></span>
                            </template>
                        </p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-[150px_1fr] items-center gap-3">
                        <p class="text-sm text-slate-500">Nomor Telepon</p>
                        <p class="text-sm font-medium text-slate-800" x-text="user.whatsapp || '—'"></p>
                    </div>
                    <div class="grid grid-cols-[150px_1fr] items-center gap-3">
                        <p class="text-sm text-slate-500">Tanggal Daftar</p>
                        <p class="text-sm font-medium text-slate-800" x-text="longDate(user.created_at)"></p>
                    </div>
                    <div class="grid grid-cols-[150px_1fr] items-center gap-3">
                        <p class="text-sm text-slate-500">Aktifitas Terakhir</p>
                        <p class="text-sm font-medium text-slate-800" x-text="timeAgo(user.last_seen_at || user.last_login)"></p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-border p-4 sm:p-5">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h2 class="text-base font-bold text-foreground">Role &amp; Permissions</h2>
                        <p class="mt-1.5 text-sm text-slate-500">Manage roles and module permissions for each user</p>
                    </div>
                    <div>
                        <p class="mb-2 text-sm text-slate-500">Acces Role</p>
                        <div class="rounded-xl border border-primary px-3 py-2" aria-label="Access Role (lihat saja)">
                            <span class="flex flex-wrap gap-1.5">
                                <template x-for="slug in user.roles" :key="slug">
                                    <span class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-0.5 text-[13px] font-medium text-slate-800" x-text="roleLabel(slug)"></span>
                                </template>
                            </span>
                        </div>
                        <p x-show="user.other_roles.length" class="mt-2 text-xs text-slate-400" x-text="'Role lainnya: '+user.other_roles.join(', ')"></p>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
