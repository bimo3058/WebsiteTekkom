@extends('capstone::layouts.app')
@section('title','Peer Review Dashboard')
@section('content')
<div x-data="adminPeerDashboard" @keydown.escape.window="closeMenu()" @scroll.window="closeMenu()" @resize.window="closeMenu()" class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Peer Review Dashboard</h1>
            <p class="mt-1 text-sm text-muted-foreground">Pantau kelengkapan peer review setiap kelompok pada periode berjalan.</p>
        </div>
    </div>
    @include('capstone::pages.admin.shared.toolbar')
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-4">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-border bg-white p-5 shadow-xs"><p class="text-sm text-muted-foreground">Kelompok</p><p class="mt-1 text-3xl font-bold tabular-nums text-foreground" x-text="items.length"></p></div>
            <div class="rounded-xl border border-border bg-white p-5 shadow-xs"><p class="text-sm text-muted-foreground">Selesai</p><p class="mt-1 text-3xl font-bold tabular-nums text-foreground" x-text="completed"></p></div>
            <div class="rounded-xl border border-border bg-white p-5 shadow-xs"><p class="text-sm text-muted-foreground">Belum selesai</p><p class="mt-1 text-3xl font-bold tabular-nums text-foreground" x-text="members-completed"></p></div>
        </div>
        <template x-for="item in visible" :key="item.group_id">
            <div class="rounded-xl border border-border bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-foreground" x-text="item.group_code"></h2>
                        <p class="text-sm text-muted-foreground" x-text="item.period_name"></p>
                    </div>
                    <button type="button" @click="openRowMenu($event,item)" aria-label="Aksi" aria-haspopup="menu" class="rounded-md p-1.5 text-[#666D80] hover:bg-muted"><x-capstone::icon name="Ellipsis" class="size-5" /></button>
                </div>
                <p class="mt-3 text-sm text-foreground" x-text="item.completed_count+' / '+item.total_members+' mahasiswa selesai ('+item.completion_percentage+'%)'"></p>
                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-[#1E2A5A]" :style="'width:'+item.completion_percentage+'%'"></div></div>
                <div class="mt-3 divide-y divide-border border-t border-border">
                    <template x-for="member in item.members" :key="member.student_id">
                        <div class="flex flex-wrap items-center justify-between gap-3 py-3 text-sm">
                            <span class="text-foreground" x-text="member.student_name+' - '+member.student_nim"></span>
                            <span class="flex items-center gap-2"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="member.has_completed?'bg-[#DDF2EE] text-[#287F6E]':'bg-[#F9ECCB] text-[#956321]'" x-text="member.has_completed?'Selesai':'Belum selesai'"></span><span class="text-muted-foreground" x-text="member.ta_status"></span></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>
        <p x-show="!filtered.length" class="rounded-xl border border-border bg-white p-8 text-center text-sm text-muted-foreground">Belum ada kelompok pada tahap peer review.</p>
        <div x-show="filtered.length" x-cloak class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border bg-white px-5 py-3.5 text-sm shadow-xs">
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
        <button type="button" @click="confirm(openMenuItem);closeMenu()" :disabled="saving || (openMenuItem||{}).completed_count>=(openMenuItem||{}).total_members" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-foreground hover:bg-accent disabled:opacity-40">Kirim pengingat</button>
    </div>
    <x-capstone::dialog id="peer-reminder" title="Kirim pengingat peer review"><p class="my-4" x-text="'Pengingat akan dikirim kepada anggota '+selected?.group_code+' yang belum menyelesaikan peer review.'"></p><x-capstone::button variant="outline" size="sm" @click="remind()" ::disabled="saving">Kirim Pengingat</x-capstone::button></x-capstone::dialog>
</div>
@endsection
