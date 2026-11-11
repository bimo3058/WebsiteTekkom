@extends('capstone::layouts.app')
@section('title','Kelompok Bimbingan')
@section('content')
<div x-data="lecturerGroups" class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center"><div><h1 class="text-3xl font-bold tracking-tight">Kelompok Bimbingan</h1><p class="text-muted-foreground">Pantau kemajuan kelompok bimbingan Anda.</p></div>@include('capstone::pages.dosen.shared.period')</div>
    <x-capstone::input type="search" x-model.debounce.200ms="search" @input="page=1" placeholder="Cari kelompok, judul, atau mahasiswa..." aria-label="Cari kelompok" class="max-w-sm" />
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-4">
        <p x-show="!filtered.length" class="rounded-xl border border-dashed p-12 text-center text-muted-foreground">Tidak Ada Kelompok. Anda saat ini tidak membimbing kelompok yang cocok.</p>
        <div class="grid gap-4 md:grid-cols-2"><template x-for="group in visible" :key="group.id"><article class="rounded-xl border bg-card p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-2"><div class="flex flex-wrap items-center gap-2"><h2 class="text-lg font-semibold" x-text="group.code||'Kelompok '+group.id"></h2><span x-show="group.is_dosbing_1" class="rounded bg-primary px-2 py-1 text-xs text-white">Dosbing 1</span><span x-show="group.is_dosbing_2" class="rounded bg-success px-2 py-1 text-xs text-white">Dosbing 2</span></div><span class="rounded-full bg-muted px-2 py-1 text-xs" x-text="group.status"></span></div>
            <div class="mt-5 space-y-4 text-sm"><div class="flex items-center justify-between gap-2"><span class="font-medium">Periode</span><span class="rounded border px-2 py-1 text-xs" x-text="group.period?.name||'-'"></span></div><div><h3 class="font-medium">Judul</h3><p class="mt-1 text-muted-foreground" x-text="group.title?.title||'Belum ada judul'"></p></div><div><h3 class="font-medium">Anggota</h3><p class="mt-1 text-muted-foreground" x-text="members(group)"></p></div>
                <template x-if="latest(group)"><div class="rounded-lg bg-muted/50 p-3"><h3 class="mb-2 text-xs font-medium uppercase text-muted-foreground">Aktivitas Terbaru</h3><div class="flex justify-between gap-2"><span x-text="latest(group).phase+' (v'+latest(group).version+')'"></span><span class="rounded px-2 text-xs" :class="statusColor(latest(group).status)" x-text="latest(group).status"></span></div><p class="mt-2 text-xs text-muted-foreground" x-text="date(latest(group).updated_at,true)"></p></div></template>
                <div class="border-t pt-3"><div class="mb-2 flex justify-between text-xs font-medium"><span>Kemajuan Keseluruhan</span><span x-text="progress(group)+'%'"></span></div><div class="h-2 rounded-full bg-muted" role="progressbar" :aria-valuenow="progress(group)" aria-valuemin="0" aria-valuemax="100" :aria-label="'Kemajuan '+(group.code||group.id)"><div class="h-2 rounded-full bg-primary" :style="{width:progress(group)+'%'}"></div></div></div>
                <a :href="url('/dosen/bimbingan?group_id='+group.id)" class="inline-flex items-center gap-2 font-medium text-primary hover:underline">Tinjau Dokumen <x-capstone::icon name="ArrowRight" class="h-4 w-4" /></a>
            </div>
        </article></template></div>
        @include('capstone::pages.dosen.shared.pagination')
    </div>
</div>
@endsection
