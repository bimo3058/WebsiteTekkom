@extends('capstone::layouts.app')
@section('title','Expo Events')
@section('content')
<div x-data="adminExpo" @keydown.escape.window="closeMenu()" @scroll.window="closeMenu()" @resize.window="closeMenu()" class="space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Expo Events</h1>
            <p class="mt-1 text-sm text-muted-foreground">Kelola event expo, publikasi, dan kapasitas peserta per periode.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <label class="inline-flex items-center gap-2 rounded-lg border border-input bg-white px-4 py-2 text-sm font-semibold shadow-xs">
                <span class="sr-only">Periode</span>
                <select x-model="periodId" @change="page=1;load()" :disabled="saving" aria-label="Pilih periode" class="bg-transparent font-semibold outline-none">
                    <option value="">Semua periode</option>
                    <template x-for="period in periods" :key="period.id">
                        <option :value="String(period.id)" x-text="period.name"></option>
                    </template>
                </select>
                <x-capstone::icon name="ChevronDown" class="size-4 text-muted-foreground" />
            </label>
            <button type="button" @click="edit()" ::disabled="saving" class="inline-flex h-10 items-center gap-2 rounded-lg bg-[#1E2A5A] px-4 text-sm font-semibold text-white shadow-xs hover:bg-[#16204a] disabled:opacity-40"><x-capstone::icon name="Plus" class="size-4" />Tambah Expo</button>
        </div>
    </div>
    @include('capstone::partials.loading')
<div x-show="!loading && !error" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h2 class="text-base font-semibold">Tabel Expo Events</h2>
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
                    <input type="search" x-model="search" @input="page=1" placeholder="Search" aria-label="Cari expo event" class="h-9 w-64 rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
                </div>
                <div class="relative">
                    <button type="button" @click="filterMenu=!filterMenu;sortMenu=false" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                        <x-capstone::icon name="ListFilter" class="size-4" />Filter
                    </button>
                    <div x-show="filterMenu" @click.away="filterMenu=false" x-cloak class="absolute right-0 z-20 mt-2 w-44 rounded-lg border bg-white p-1.5 shadow-md">
                        <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Status</p>
                        <template x-for="opt in [{v:'all',l:'Semua'},{v:'published',l:'Published'},{v:'draft',l:'Draft'}]" :key="opt.v">
                            <button type="button" @click="statusFilter=opt.v;page=1;filterMenu=false" class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm hover:bg-accent" :class="String(statusFilter)===String(opt.v)?'font-semibold text-foreground':'text-muted-foreground'">
                                <span x-text="opt.l"></span><span x-show="String(statusFilter)===String(opt.v)">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
                <div class="relative">
                    <button type="button" @click="sortMenu=!sortMenu;filterMenu=false" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                        <x-capstone::icon name="ArrowUpDown" class="size-4" />Sort by
                    </button>
                    <div x-show="sortMenu" @click.away="sortMenu=false" x-cloak class="absolute right-0 z-20 mt-2 w-48 rounded-lg border bg-white p-1.5 shadow-md">
                        <template x-for="opt in [{v:'',l:'Default'},{v:'name-az',l:'Nama A-Z'},{v:'date-desc',l:'Tanggal terbaru'},{v:'date-asc',l:'Tanggal terlama'}]" :key="opt.v">
                            <button type="button" @click="setSort(opt.v)" class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm hover:bg-accent" :class="sortOption===opt.v?'font-semibold text-foreground':'text-muted-foreground'">
                                <span x-text="opt.l"></span><span x-show="sortOption===opt.v">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 overflow-x-auto"><table class="w-full min-w-[860px] text-left text-sm"><thead><tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]"><th class="px-4 py-3 font-medium">No</th><th class="px-4 py-3 font-medium">Nama Expo</th><th class="px-4 py-3 font-medium">Periode</th><th class="px-4 py-3 font-medium">Waktu</th><th class="px-4 py-3 font-medium">Lokasi</th><th class="px-4 py-3 font-medium">Kapasitas</th><th class="px-4 py-3 font-medium">Status</th><th class="px-5 py-3 text-right font-medium">Action</th></tr></thead><tbody class="divide-y divide-border"><template x-for="(item,index) in visible" :key="item.id"><tr class="bg-white hover:bg-muted/30"><td class="px-4 py-4 text-foreground" x-text="(Math.min(page,pageCount)-1)*Number(pageSize)+index+1"></td><td class="px-4 py-4 font-medium text-foreground" x-text="item.name"></td><td class="whitespace-nowrap px-4 py-4 text-foreground" x-text="item.period?.name"></td><td class="whitespace-nowrap px-4 py-4 text-foreground" x-text="date(item.date)+' '+(item.start_time||'').slice(0,5)"></td><td class="px-4 py-4 text-foreground" x-text="item.room"></td><td class="whitespace-nowrap px-4 py-4 text-foreground" x-text="(item.registrations_count||0)+' / '+item.capacity"></td><td class="whitespace-nowrap px-4 py-4"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusBadgeClass(item)" x-text="item.is_published?'Published':'Draft'"></span></td><td class="px-5 py-4 text-right"><button type="button" @click="openRowMenu($event,item)" aria-label="Aksi" aria-haspopup="menu" class="rounded-md p-1.5 text-[#666D80] hover:bg-muted"><x-capstone::icon name="Ellipsis" class="size-5" /></button></td></tr></template></tbody></table></div><p x-show="!filtered.length" class="p-8 text-center text-sm text-muted-foreground">Belum ada expo event.</p>
        <div x-show="filtered.length" x-cloak class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-0 overflow-hidden rounded-lg border border-input bg-white text-[13px]">
                    <span class="px-2.5 text-muted-foreground">Per page</span>
                    <select x-model.number="pageSize" @change="page=1" aria-label="Baris per halaman" class="border-l border-input bg-transparent px-2 py-1.5 font-medium outline-none">
                        <option>10</option><option>25</option><option>50</option>
                    </select>
                </label>
                <span class="text-foreground"><span x-text="'Showing '+showingFrom+' to '+showingTo+' of, '+filtered.length+' results'"></span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="goPage(Math.min(page,pageCount)-1)" :disabled="page<=1" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                <template x-for="(n,i) in pageNumbers" :key="n">
                    <span class="flex items-center gap-1.5">
                        <span x-show="i>0 && n-pageNumbers[i-1]>1" class="px-1 text-muted-foreground">...</span>
                        <button type="button" @click="goPage(n)" x-text="n" class="inline-flex size-8 items-center justify-center rounded-lg border text-sm" :class="Math.min(page,pageCount)===n?'border-[#1E2A5A] bg-[#1E2A5A] font-semibold text-white':'border-input bg-white text-foreground hover:bg-accent'"></button>
                    </span>
                </template>
                <button type="button" @click="goPage(Math.min(page,pageCount)+1)" :disabled="page>=pageCount" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
            </div>
        </div>
    </div>
    <div x-show="openMenuItem" @click.away="closeMenu()" x-cloak :style="menuStyle" style="position: fixed; z-index: 50;" role="menu" class="w-48 rounded-lg border border-border bg-white p-1.5 text-left shadow-md">
        <button type="button" @click="edit(openMenuItem);closeMenu()" ::disabled="saving" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-foreground hover:bg-accent">Edit</button>
        <button type="button" @click="confirm(openMenuItem,'publish');closeMenu()" ::disabled="saving" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-foreground hover:bg-accent">Publikasi</button>
        <button type="button" @click="confirm(openMenuItem,'delete');closeMenu()" ::disabled="saving" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-red-600 hover:bg-red-50">Hapus</button>
    </div>
    <p x-show="error" x-text="error" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p><x-capstone::dialog id="expo-form" title="Expo Event"><form @submit.prevent="save()" class="mt-5 space-y-4"><label class="block space-y-2 text-sm"><span>Periode</span><select x-model="form.period_id" class="w-full rounded border bg-background p-2" :disabled="!!editing" required><option value="">Pilih...</option><template x-for="option in periods" :key="option.id"><option :value="option.id" x-text="option.name"></option></template></select></label><label class="block space-y-2 text-sm"><span>Nama Expo</span><input type="text" x-model="form.name" class="w-full rounded border bg-background p-2"  required></label><label class="block space-y-2 text-sm"><span>Tanggal</span><input type="date" x-model="form.date" class="w-full rounded border bg-background p-2"  required></label><div class="grid grid-cols-2 gap-3"><label class="block space-y-2 text-sm"><span>Mulai</span><input type="time" x-model="form.start_time" class="w-full rounded border bg-background p-2"  required></label><label class="block space-y-2 text-sm"><span>Selesai</span><input type="time" x-model="form.end_time" class="w-full rounded border bg-background p-2"  required></label></div><label class="block space-y-2 text-sm"><span>Lokasi (Ruangan EOffice)</span><select x-model="form.eoffice_ruangan_id" class="w-full rounded border bg-background p-2" required><option value="">Pilih ruangan...</option><template x-for="room in eofficeRooms" :key="room.id"><option :value="String(room.id)" x-text="room.nama+(room.kapasitas?' ('+room.kapasitas+')':'')"></option></template></select></label><label class="block space-y-2 text-sm"><span>Kapasitas</span><input type="number" x-model="form.capacity" class="w-full rounded border bg-background p-2" min="1" max="200" required></label><template x-for="(messages,key) in errors" :key="key"><p class="text-sm text-destructive" x-text="Array.isArray(messages)?messages.join(' '):messages"></p></template><div class="flex justify-end gap-3"><x-capstone::button type="button" variant="outline" @click="$el.closest('dialog').close()">Batal</x-capstone::button><x-capstone::button type="submit" ::disabled="saving">Simpan</x-capstone::button></div></form></x-capstone::dialog><x-capstone::dialog id="expo-confirm" title="Konfirmasi Expo"><p class="my-5" x-text="action==='publish'?'Ubah status publikasi '+selected?.name+'?':'Hapus '+selected?.name+'?'"></p><x-capstone::button variant="outline" size="sm" @click="apply()" ::disabled="saving">Konfirmasi</x-capstone::button></x-capstone::dialog>
</div>
@endsection
