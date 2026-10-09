@extends('capstone::layouts.app')
@section('title','Finalisasi')
@section('content')
<div x-data="adminFinalization" x-init="init()" x-cloak class="space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Finalisasi</h1>
            <p class="mt-1 text-sm text-muted-foreground">Kelola finalisasi grup dan penentuan supervisor</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <span x-show="period && period.is_finalized" class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700"><span aria-hidden="true">●</span>Periode terkunci</span>
            <label class="inline-flex items-center gap-2 rounded-lg border border-input bg-white px-4 py-2 text-sm font-semibold shadow-xs">
                <span class="sr-only">Periode</span>
                <select x-model="periodId" @change="page=1;loadLecturers();load()" :disabled="saving" aria-label="Pilih periode" class="bg-transparent font-semibold outline-none">
                    <option value="" disabled x-show="!periodId">Pilih periode</option>
                    <template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name" :selected="String(period.id)===String(periodId)"></option></template>
                </select>
                <x-capstone::icon name="ChevronDown" class="size-4 text-muted-foreground" />
            </label>
            <details class="relative">
                <summary class="inline-flex h-9 cursor-pointer list-none items-center gap-2 rounded-lg bg-[#1E2A5A] px-4 text-sm font-semibold text-white shadow-xs hover:bg-[#16204a] [&::-webkit-details-marker]:hidden"><x-capstone::icon name="Download" class="size-4" />Export<x-capstone::icon name="ChevronDown" class="size-4" /></summary>
                <div class="absolute right-0 z-20 mt-2 flex w-44 flex-col gap-1 rounded-lg border border-border bg-white p-1.5 shadow-md">
                    <button type="button" class="rounded-md px-3 py-2 text-left text-sm hover:bg-accent" @click="doExport('excel')" :disabled="loading || saving || !periodId">Unduh Excel</button>
                    <button type="button" class="rounded-md px-3 py-2 text-left text-sm hover:bg-accent" @click="doExport('pdf')" :disabled="loading || saving || !periodId">Unduh PDF</button>
                </div>
            </details>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <x-capstone::button @click="openPeriodFlag()" x-show="!period?.is_finalized && !flow?.can_execute_finalization && ((stats?.total_kelompok_final ?? 0) + (stats?.total_pdc1_active ?? 0)) > 0" ::disabled="saving"><x-capstone::icon name="Flag" />Finalisasi Periode</x-capstone::button>
        <x-capstone::button variant="outline" @click="load()" ::disabled="loading || saving"><x-capstone::icon name="RefreshCw" />Muat Ulang</x-capstone::button>
        <x-capstone::button variant="outline" @click="openAutoFix()" ::disabled="!periodId || saving"><x-capstone::icon name="Wrench" />Perbaiki Otomatis</x-capstone::button>
        <span class="mx-1 hidden h-6 w-px bg-border sm:inline-block" aria-hidden="true"></span>
        <x-capstone::button variant="outline" @click="confirmBidding('lock')" x-show="period && period.is_finalized" ::disabled="saving">Kunci Bidding</x-capstone::button>
        <x-capstone::button variant="outline" @click="confirmBidding('unlock')" x-show="period && !period.is_finalized" ::disabled="saving">Buka Bidding</x-capstone::button>
        <x-capstone::button variant="destructive" @click="openReopen()" x-show="stats?.can_reopen_finalization" ::disabled="saving"><x-capstone::icon name="RotateCcw" />Buka Kembali Periode</x-capstone::button>
    </div>

    <x-capstone::card x-show="multiplePeriods" title="Pilih satu periode" description="Terdapat lebih dari satu periode aktif. Pilih satu periode untuk melanjutkan kerja.">
        <div class="flex flex-wrap gap-2 px-6 pb-6">
            <template x-for="period in periods" :key="period.id">
                <x-capstone::button variant="outline" @click="periodId=String(period.id);multiplePeriods=false;page=1;loadLecturers();load()"><span x-text="period.name"></span></x-capstone::button>
            </template>
        </div>
    </x-capstone::card>

    <div x-show="flow && flow.blockers && flow.blockers.length" class="space-y-2" role="alert">
        <template x-for="blocker in (flow?.blockers || [])" :key="blocker.type">
            <div class="flex flex-wrap items-center gap-3 rounded-xl border px-4 py-3 text-sm" :class="blocker.severity==='error' ? 'border-red-200 bg-red-50 text-red-800' : 'border-amber-500 bg-amber-50 text-amber-700'">
                <x-capstone::icon name="TriangleAlert" />
                <span x-text="blocker.message" class="mr-auto max-w-[65ch]"></span>
                <x-capstone::button x-show="blocker.action==='reopen'" size="sm" variant="outline" @click="openReopen()">Buka Kembali</x-capstone::button>
                <x-capstone::button x-show="blocker.action==='period_flag'" size="sm" @click="openPeriodFlag()">Finalisasi Periode</x-capstone::button>
            </div>
        </template>
    </div>

    <div x-show="error" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <span x-text="error"></span>
        <button type="button" class="ml-2 font-semibold underline" @click="load()">Coba lagi</button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan periode">
        <div @click="setTab('ready')" class="cursor-pointer rounded-xl border border-border bg-white p-4 shadow-xs hover:border-ring">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="flex size-9 items-center justify-center rounded-full bg-[#EEF1FF] text-[#1E2A5A]"><x-capstone::icon name="User" class="size-4" /></span>
                    <p class="text-sm font-medium text-foreground">Siap Finalisasi</p>
                </div>
                <x-capstone::icon name="Info" class="size-4 text-muted-foreground" />
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums" x-text="stats?.total_ready ?? 0"></p>
            <p class="mt-1 text-sm text-muted-foreground">Grup siap difinalisasi</p>
        </div>
        <div @click="setTab('final')" class="cursor-pointer rounded-xl border border-border bg-white p-4 shadow-xs hover:border-ring">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="flex size-9 items-center justify-center rounded-full bg-[#EEF1FF] text-[#1E2A5A]"><x-capstone::icon name="User" class="size-4" /></span>
                    <p class="text-sm font-medium text-foreground">Group Final</p>
                </div>
                <x-capstone::icon name="Info" class="size-4 text-muted-foreground" />
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums" x-text="stats?.total_kelompok_final ?? 0"></p>
            <p class="mt-1 text-sm text-muted-foreground">Sudah di-set supervisor</p>
        </div>
        <div @click="setTab('others', 'no_group')" class="cursor-pointer rounded-xl border border-border bg-white p-4 shadow-xs hover:border-ring">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="flex size-9 items-center justify-center rounded-full bg-[#EEF1FF] text-[#1E2A5A]"><x-capstone::icon name="User" class="size-4" /></span>
                    <p class="text-sm font-medium text-foreground">Tanpa Kelompok</p>
                </div>
                <x-capstone::icon name="Info" class="size-4 text-muted-foreground" />
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums" x-text="stats?.total_no_group ?? 0"></p>
            <p class="mt-1 text-sm text-muted-foreground">Mahasiswa perlu grouping</p>
        </div>
        <div @click="setTab('others', 'not_ready')" class="cursor-pointer rounded-xl border border-border bg-white p-4 shadow-xs hover:border-ring">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="flex size-9 items-center justify-center rounded-full bg-[#EEF1FF] text-[#1E2A5A]"><x-capstone::icon name="User" class="size-4" /></span>
                    <p class="text-sm font-medium text-foreground">Belum Siap</p>
                </div>
                <x-capstone::icon name="Info" class="size-4 text-muted-foreground" />
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums" x-text="stats?.total_not_ready ?? 0"></p>
            <p class="mt-1 text-sm text-muted-foreground">Grup belum ready</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-border bg-[#F1F2F6] p-1.5" role="tablist" aria-label="Filter daftar kerja">
        <button type="button" role="tab" :aria-selected="tab==='final'" @click="setTab('final')" class="inline-flex h-9 items-center gap-1.5 rounded-lg px-4 text-sm font-medium tabular-nums" :class="tab==='final' ? 'bg-white text-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground'">Kelompok Final (<span x-text="stats?.total_kelompok_final ?? 0"></span>)</button>
        <button type="button" role="tab" :aria-selected="tab==='ready'" @click="setTab('ready')" class="inline-flex h-9 items-center gap-1.5 rounded-lg px-4 text-sm font-medium tabular-nums" :class="tab==='ready' ? 'bg-white text-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground'">Siap Finalisasi (<span x-text="stats?.total_ready ?? 0"></span>)</button>
        <button type="button" role="tab" :aria-selected="tab==='others'" @click="setTab('others', 'no_group')" class="inline-flex h-9 items-center gap-1.5 rounded-lg px-4 text-sm font-medium tabular-nums" :class="tab==='others' ? 'bg-white text-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground'">Perlu Perhatian (<span x-text="(stats?.total_no_group ?? 0) + (stats?.total_no_title ?? 0) + (stats?.total_not_ready ?? 0)"></span>)</button>
        <button type="button" role="tab" :aria-selected="tab==='post'" @click="setTab('post')" class="inline-flex h-9 items-center gap-1.5 rounded-lg px-4 text-sm font-medium tabular-nums" :class="tab==='post' ? 'bg-white text-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground'">Pasca (<span x-text="stats?.total_post_finalization ?? 0"></span>)</button>
    </div>
    <div x-show="tab==='others'" class="flex flex-wrap items-center gap-2" role="tablist" aria-label="Rincian perlu perhatian">
        <button type="button" role="tab" :aria-selected="subTab==='no_group'" @click="setSubTab('no_group')" class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium tabular-nums" :class="subTab==='no_group' ? 'bg-primary text-primary-foreground' : 'border bg-background shadow-xs'">Tanpa Kelompok (<span x-text="stats?.total_no_group ?? 0"></span>)</button>
        <button type="button" role="tab" :aria-selected="subTab==='no_title'" @click="setSubTab('no_title')" class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium tabular-nums" :class="subTab==='no_title' ? 'bg-primary text-primary-foreground' : 'border bg-background shadow-xs'">Tanpa Judul (<span x-text="stats?.total_no_title ?? 0"></span>)</button>
        <button type="button" role="tab" :aria-selected="subTab==='not_ready'" @click="setSubTab('not_ready')" class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium tabular-nums" :class="subTab==='not_ready' ? 'bg-primary text-primary-foreground' : 'border bg-background shadow-xs'">Belum Siap (<span x-text="stats?.total_not_ready ?? 0"></span>)</button>
    </div>

    <div x-show="tab==='ready' && selectedIds.length" x-cloak class="flex flex-wrap items-center gap-3 rounded-xl border border-primary/30 bg-primary/5 px-4 py-3 text-sm">
        <span><span x-text="selectedIds.length" class="tabular-nums"></span> kelompok dipilih. Tetapkan SV1 dan SV2 sebelum menandai final.</span>
        <x-capstone::button size="sm" @click="openBatchSv()">Tetapkan Pembimbing Massal</x-capstone::button>
        <x-capstone::button size="sm" variant="ghost" @click="selectedIds=[]">Batalkan Pilihan</x-capstone::button>
    </div>

    <div x-show="tab==='final' && selectedIds.length" x-cloak class="flex flex-wrap items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
        <span><span x-text="selectedIds.length" class="tabular-nums"></span> Kelompok Final dipilih. Rollback mengembalikan ke Siap dan tercatat sebagai audit.</span>
        <x-capstone::button size="sm" variant="destructive" @click="openRollback()">Rollback ke Siap</x-capstone::button>
        <x-capstone::button size="sm" variant="ghost" @click="selectedIds=[]">Batalkan Pilihan</x-capstone::button>
    </div>

    <div x-show="!isGroupView && noGroupSelected.length" x-cloak class="flex flex-wrap items-center gap-3 rounded-xl border border-primary/30 bg-primary/5 px-4 py-3 text-sm">
        <span><span x-text="noGroupSelected.length" class="tabular-nums"></span> mahasiswa dipilih. Bentuk grup baru atau masukkan ke grup yang masih punya kapasitas.</span>
        <x-capstone::button size="sm" @click="openManual()">Buat Grup Manual</x-capstone::button>
        <x-capstone::button size="sm" variant="outline" @click="openAddExisting()">Tambahkan ke Grup</x-capstone::button>
        <x-capstone::button size="sm" variant="ghost" @click="noGroupSelected=[]">Batalkan Pilihan</x-capstone::button>
    </div>

    <x-capstone::card>
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h2 class="text-base font-semibold" x-text="tableTitle"></h2>
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
                    <input type="search" x-model="search" @input="onSearch()" placeholder="Search" aria-label="Cari daftar kerja" class="h-9 w-64 rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
                </div>
                <div class="relative" x-data="{open:false}" @keydown.escape="open=false">
                    <button type="button" @click="open=!open" :aria-expanded="open" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                        <x-capstone::icon name="ListFilter" class="size-4" />Filter
                    </button>
                    <div x-show="open" @click.outside="open=false" x-cloak class="absolute right-0 z-20 mt-2 w-56 space-y-3 rounded-lg border bg-white p-3 shadow-md">
                        <template x-if="isGroupView">
                            <label class="block text-sm">Pembimbing
                                <select x-model="supervisorStatus" @change="page=1;load()" class="mt-1 w-full rounded-md border bg-background px-3 py-2">
                                    <option value="all">Semua</option>
                                    <option value="missing_sv1">Belum ada SV1</option>
                                    <option value="missing_sv2">Belum ada SV2</option>
                                    <option value="complete">Lengkap</option>
                                </select>
                            </label>
                        </template>
                        <template x-if="isGroupView">
                            <label class="block text-sm">Anggota
                                <select x-model="memberCount" @change="page=1;load()" class="mt-1 w-full rounded-md border bg-background px-3 py-2">
                                    <option value="all">Semua</option>
                                    <option value="under_min">Kurang</option>
                                    <option value="in_range">Sesuai</option>
                                    <option value="over_max">Lebih</option>
                                </select>
                            </label>
                        </template>
                        <p x-show="!isGroupView" class="text-sm text-muted-foreground">Tidak ada filter untuk daftar ini.</p>
                    </div>
                </div>
            </div>
        </div>
        @include('capstone::partials.loading')
        <div x-show="!loading && !error && !items.length" x-cloak class="flex flex-col items-center justify-center px-4 py-16 text-center">
            <template x-if="tab==='ready'"><p class="max-w-[60ch] font-medium text-gray-500">Belum ada kelompok siap. Periksa tab Tanpa Judul dan Belum Siap, lengkapi judul dan anggota, lalu kembali ke sini.</p></template>
            <template x-if="tab==='final'"><p class="max-w-[60ch] font-medium text-gray-500">Belum ada Kelompok Final. Tetapkan SV1 dan SV2 pada tab Siap, lalu tandai kelompok sebagai final.</p></template>
            <template x-if="tab==='others' && subTab==='no_group'"><p class="max-w-[60ch] font-medium text-gray-500">Semua mahasiswa sudah punya kelompok. Tidak ada yang perlu dibentuk manual.</p></template>
            <template x-if="tab==='others' && subTab==='no_title'"><p class="max-w-[60ch] font-medium text-gray-500">Semua kelompok sudah punya judul. Tidak ada yang menunggu penetapan judul.</p></template>
            <template x-if="tab==='others' && subTab==='not_ready'"><p class="max-w-[60ch] font-medium text-gray-500">Tidak ada kelompok yang belum siap. Semua memenuhi syarat jumlah anggota dan judul.</p></template>
            <template x-if="tab==='post'"><p class="max-w-[60ch] font-medium text-gray-500">Belum ada data pasca finalisasi. Kunci periode untuk memindahkan Kelompok Final ke PDC1.</p></template>
            <p class="mt-1 text-sm text-gray-400">Ubah filter atau kata kunci bila hasil pencarian kosong.</p>
        </div>
        <div x-show="!loading && !error && items.length" x-cloak class="relative w-full overflow-x-auto">
            <table x-show="isGroupView" class="w-full caption-bottom text-sm tabular-nums">
                <thead class="[&_tr]:border-b"><tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                    <th class="px-4 py-3 text-left" scope="col"><input type="checkbox" @change="toggleAll($event.target.checked)" :checked="items.length>0 && selectedIds.length===items.length" aria-label="Pilih semua kelompok"></th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Kelompok</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Anggota</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Judul</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Pembimbing</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Status</th>
                    <th x-show="tab==='ready'" class="px-4 py-3 text-left font-medium" scope="col">Kesiapan</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Aksi</th>
                </tr></thead>
                <tbody class="[&_tr:last-child]:border-0">
                    <template x-for="item in items" :key="item.id">
                        <tr class="border-b bg-white hover:bg-muted/30">
                            <td class="px-4 py-3"><input type="checkbox" @change="toggleId(item.id)" :checked="selectedIds.includes(item.id)" :aria-label="'Pilih '+(item.code||item.id)"></td>
                            <td class="px-4 py-3 font-medium" x-text="item.code || ('Kelompok #'+item.id)"></td>
                            <td class="px-4 py-3"><span x-text="(item.members||[]).length + ' anggota'"></span><br><span class="text-xs text-muted-foreground" x-text="memberNames(item)"></span></td>
                            <td class="px-4 py-3"><span x-text="item.title?.title || 'Belum ada judul'"></span></td>
                            <td class="px-4 py-3 text-xs"><span x-text="'SV1: '+(item.supervisor1?.name || item.supervisor1?.user?.name || 'Belum ditetapkan')"></span><br><span x-text="'SV2: '+(item.supervisor2?.name || item.supervisor2?.user?.name || 'Belum ditetapkan')"></span></td>
                            <td class="px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-xs" :class="groupStatusClass(item.status)"><span aria-hidden="true">●</span><span x-text="item.status_label || item.status"></span></span></td>
                            <td x-show="tab==='ready'" class="px-2 py-2"><div class="flex flex-wrap gap-1"><template x-for="r in readiness(item)" :key="r.label"><span class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs" :class="r.ok ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800'"><span aria-hidden="true" x-text="r.ok ? '✓' : '!'"></span><span x-text="r.label"></span></span></template></div></td>
                            <td class="px-4 py-3"><div class="flex flex-wrap gap-1">
                                <x-capstone::button size="sm" variant="outline" x-show="item.allowed_actions?.can_set_supervisor" @click="openSingleSv(item)">Atur SV</x-capstone::button>
                                <x-capstone::button size="sm" x-show="item.allowed_actions?.can_mark_kelompok_final" @click="openMarkFinal(item)">Tandai Final</x-capstone::button>
                                <x-capstone::button size="sm" variant="outline" x-show="item.allowed_actions?.can_assign_title" @click="openAssignTitle(item)">Tetapkan Judul</x-capstone::button>
                                <x-capstone::button size="sm" variant="outline" x-show="item.allowed_actions?.can_promote_to_ready_for_finalization" @click="promote(item)">Promosikan ke Siap</x-capstone::button>
                                <x-capstone::button size="sm" variant="outline" x-show="item.allowed_actions?.can_cancel_kelompok_final" @click="openCancel(item)">Batalkan Final</x-capstone::button>
                                <x-capstone::button size="sm" variant="ghost" x-show="item.status==='TITLE_APPROVED'" @click="openForceReady(item)">Paksa Siap</x-capstone::button>
                            </div></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <table x-show="!isGroupView" class="w-full caption-bottom text-sm tabular-nums">
                <thead class="[&_tr]:border-b"><tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                    <th class="px-4 py-3 text-left" scope="col"><input type="checkbox" @change="toggleAllStudents($event.target.checked)" :checked="items.length>0 && noGroupSelected.length===items.length" aria-label="Pilih semua mahasiswa"></th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Nama</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">NIM</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Email</th>
                </tr></thead>
                <tbody class="[&_tr:last-child]:border-0">
                    <template x-for="item in items" :key="item.id">
                        <tr class="border-b bg-white hover:bg-muted/30">
                            <td class="px-4 py-3"><input type="checkbox" @change="toggleStudent(item.id)" :checked="noGroupSelected.includes(item.id)" :aria-label="'Pilih '+(item.name||item.id)"></td>
                            <td class="px-4 py-3 font-medium" x-text="item.name || item.user?.name || 'Tanpa nama'"></td>
                            <td class="px-4 py-3 font-mono text-xs" x-text="item.student_number || item.nim || 'Tanpa NIM'"></td>
                            <td class="px-4 py-3" x-text="item.email || item.user?.email || 'Tanpa email'"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div x-show="!loading && !error && items.length" x-cloak class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-0 overflow-hidden rounded-lg border border-input bg-white text-[13px]">
                    <span class="px-2.5 text-muted-foreground">Per page</span>
                    <select x-model.number="pageSize" @change="page=1;load()" aria-label="Baris per halaman" class="border-l border-input bg-transparent px-2 py-1.5 font-medium outline-none">
                        <option :value="10">10</option><option :value="20">20</option><option :value="50">50</option>
                    </select>
                </label>
                <span class="text-foreground"><span x-text="'Showing '+showingFrom+' to '+showingTo+' of, '+pagination.total+' results'"></span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="gotoPage(page-1)" :disabled="page<=1" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                <template x-for="(n,i) in pageNumbers" :key="n">
                    <span class="flex items-center gap-1.5">
                        <span x-show="i>0 && n-pageNumbers[i-1]>1" class="px-1 text-muted-foreground">...</span>
                        <button type="button" @click="gotoPage(n)" x-text="n" class="inline-flex size-8 items-center justify-center rounded-lg border text-sm" :class="page===n?'border-[#1E2A5A] bg-[#1E2A5A] font-semibold text-white':'border-input bg-white text-foreground hover:bg-accent'"></button>
                    </span>
                </template>
                <button type="button" @click="gotoPage(page+1)" :disabled="page>=pagination.last_page" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
            </div>
        </div>
    </x-capstone::card>

    <x-capstone::card title="Beban Dosen Pembimbing" description="Kapasitas supervisi per dosen pada periode berjalan. Periksa sebelum menetapkan SV massal.">
        <x-capstone::alert variant="warning" x-show="overloadedLecturers.length" class="mx-6 mb-3"><span x-text="overloadedLecturers.length" class="tabular-nums"></span> dosen overload. Hindari menetapkan mereka sebagai SV baru.</x-capstone::alert>
        <div class="relative w-full overflow-x-auto px-6 pb-6">
            <table class="w-full caption-bottom text-sm tabular-nums">
                <thead class="[&_tr]:border-b"><tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                    <th class="px-4 py-3 text-left font-medium" scope="col">Dosen</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">NIP</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Beban</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Sisa</th>
                    <th class="px-4 py-3 text-left font-medium" scope="col">Status</th>
                </tr></thead>
                <tbody>
                    <template x-for="lecturer in visibleLecturers" :key="lecturer.id">
                        <tr class="border-b bg-white hover:bg-muted/30">
                            <td class="px-4 py-3 font-medium" x-text="lecturer.name || 'Tanpa nama'"></td>
                            <td class="px-4 py-3 font-mono text-xs" x-text="lecturer.nip || lecturer.employee_number || 'Tanpa NIP'"></td>
                            <td class="px-4 py-3"><span x-text="lecturer.current_load"></span> dari <span x-text="lecturer.max_load"></span></td>
                            <td class="px-4 py-3" x-text="lecturer.remaining_capacity"></td>
                            <td class="px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-xs" :class="lecturer.is_overloaded ? 'border-red-200 bg-red-50 text-red-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800'"><span aria-hidden="true">●</span><span x-text="lecturer.is_overloaded ? 'Overload' : 'Tersedia'"></span></span></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <p x-show="!lecturers.length && !loading" class="py-6 text-center text-sm text-gray-400">Belum ada data dosen untuk periode ini.</p>
        </div>
        <div x-show="!loading && !error && lecturers.length" x-cloak class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-0 overflow-hidden rounded-lg border border-input bg-white text-[13px]">
                    <span class="px-2.5 text-muted-foreground">Per page</span>
                    <select x-model.number="lecturerPageSize" @change="lecturerPage=1" aria-label="Baris per halaman" class="border-l border-input bg-transparent px-2 py-1.5 font-medium outline-none">
                        <option :value="10">10</option><option :value="20">20</option><option :value="50">50</option>
                    </select>
                </label>
                <span class="text-foreground"><span x-text="'Showing '+lecturerShowingFrom+' to '+lecturerShowingTo+' of, '+lecturerTotal+' results'"></span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="gotoLecturerPage(lecturerPage-1)" :disabled="lecturerPage<=1" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                <template x-for="(n,i) in lecturerPageNumbers" :key="n">
                    <span class="flex items-center gap-1.5">
                        <span x-show="i>0 && n-lecturerPageNumbers[i-1]>1" class="px-1 text-muted-foreground">...</span>
                        <button type="button" @click="gotoLecturerPage(n)" x-text="n" class="inline-flex size-8 items-center justify-center rounded-lg border text-sm" :class="lecturerPage===n?'border-[#1E2A5A] bg-[#1E2A5A] font-semibold text-white':'border-input bg-white text-foreground hover:bg-accent'"></button>
                    </span>
                </template>
                <button type="button" @click="gotoLecturerPage(lecturerPage+1)" :disabled="lecturerPage>=lecturerTotalPages" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
            </div>
        </div>
    </x-capstone::card>

    <x-capstone::dialog id="fin-set-sv" title="Tetapkan Pembimbing" description="SV1 wajib diisi. SV2 opsional, harus berbeda dari SV1. Pilihan overload ditandai agar tidak menambah beban.">
        <p class="text-sm text-muted-foreground"><span x-text="svForm.group_ids.length" class="tabular-nums"></span> kelompok dipilih</p>
        <label class="block text-sm">Pembimbing 1 (wajib)<select x-model="svForm.supervisor_1_id" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="">Pilih dosen</option><template x-for="lecturer in lecturers" :key="lecturer.id"><option :value="String(lecturer.id)" :disabled="lecturer.is_overloaded" x-text="lecturer.name + ' (' + lecturer.current_load + ' dari ' + lecturer.max_load + (lecturer.is_overloaded ? ', overload' : '') + ')'"></option></template></select></label>
        <p x-show="svForm.svDefaultName" class="text-xs text-muted-foreground">SV1 bawaan: dosen pemilik atau pengaju judul (<span x-text="svForm.svDefaultName"></span>). Boleh diganti.</p>
        <label class="block text-sm">Pembimbing 2 (opsional, harus berbeda)<select x-model="svForm.supervisor_2_id" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="">Tidak ada</option><template x-for="lecturer in lecturers" :key="lecturer.id"><option :value="String(lecturer.id)" :disabled="lecturer.is_overloaded" x-text="lecturer.name + ' (' + lecturer.current_load + ' dari ' + lecturer.max_load + (lecturer.is_overloaded ? ', overload' : '') + ')'"></option></template></select></label>
        <label x-show="svForm.group_ids.length===1 && svForm.isReady" class="flex items-start gap-2 text-sm"><input type="checkbox" x-model="svForm.mark_final" class="mt-1">Tandai sebagai Kelompok Final (wajib SV1 dan SV2 terisi)</label>
        <p x-show="svForm.mark_final && !(svForm.supervisor_1_id && svForm.supervisor_2_id)" class="text-xs text-amber-800">Kelompok Final wajib SV1 dan SV2 terisi.</p>
        <p x-show="svOverloadWarning" class="text-xs text-amber-800">Salah satu dosen yang dipilih sedang overload. Pilih dosen lain bila memungkinkan.</p>
        <label class="block text-sm">Catatan (opsional)<input type="text" x-model="svForm.notes" class="mt-1 w-full rounded-md border bg-background px-3 py-2"></label>
        <p x-show="svError" x-text="svError" class="text-sm text-red-700"></p>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-set-sv').close()">Batal</x-capstone::button><x-capstone::button @click="saveSupervisors()" ::disabled="saving || (svForm.mark_final && !(svForm.supervisor_1_id && svForm.supervisor_2_id))">Simpan Pembimbing</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-assign-title" title="Tetapkan Judul" description="Pilih judul yang masih punya kuota untuk kelompok ini.">
        <p class="text-sm">Kelompok: <strong x-text="titleForm.group_code"></strong></p>
        <label class="block text-sm">Judul<select x-model="titleForm.title_id" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="">Pilih judul</option><template x-for="title in availTitles" :key="title.id"><option :value="String(title.id)" x-text="title.title + ' (sisa ' + title.remaining_quota + ')'"></option></template></select></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-assign-title').close()">Batal</x-capstone::button><x-capstone::button @click="saveAssignTitle()" ::disabled="saving">Tetapkan Judul</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-manual" title="Buat Grup Manual" description="Buat kelompok baru dari mahasiswa terpilih pada periode berjalan.">
        <p class="text-sm text-muted-foreground"><span x-text="noGroupSelected.length" class="tabular-nums"></span> mahasiswa dipilih</p>
        <div class="space-y-2 text-sm">
            <label class="flex items-center gap-2"><input type="radio" value="no_title" x-model="manualForm.option">Tanpa judul (tetapkan nanti)</label>
            <label class="flex items-center gap-2"><input type="radio" value="assign_title" x-model="manualForm.option">Tetapkan judul yang tersedia</label>
            <label class="flex items-center gap-2"><input type="radio" value="add_title" x-model="manualForm.option">Buat judul baru</label>
        </div>
        <label x-show="manualForm.option==='assign_title'" class="block text-sm">Judul<select x-model="manualForm.title_id" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="">Pilih judul</option><template x-for="title in availTitles" :key="title.id"><option :value="String(title.id)" x-text="title.title + ' (sisa ' + title.remaining_quota + ')'"></option></template></select></label>
        <div x-show="manualForm.option==='add_title'" class="space-y-2">
            <label class="block text-sm">Judul baru<input type="text" x-model="manualForm.newTitle.title" class="mt-1 w-full rounded-md border bg-background px-3 py-2"></label>
            <label class="block text-sm">Deskripsi<textarea x-model="manualForm.newTitle.description" class="mt-1 w-full rounded-md border bg-background px-3 py-2"></textarea></label>
            <label class="block text-sm">Dosen pemilik judul<select x-model="manualForm.newTitle.lecturer_id" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="">Pilih dosen</option><template x-for="lecturer in lecturers" :key="lecturer.id"><option :value="String(lecturer.id)" x-text="lecturer.name"></option></template></select></label>
        </div>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-manual').close()">Batal</x-capstone::button><x-capstone::button @click="saveManual()" ::disabled="saving">Buat Grup</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-add-existing" title="Tambahkan ke Grup" description="Masukkan mahasiswa terpilih ke grup yang masih punya kapasitas anggota.">
        <p class="text-sm text-muted-foreground"><span x-text="noGroupSelected.length" class="tabular-nums"></span> mahasiswa dipilih</p>
        <label class="block text-sm">Grup tujuan<select x-model="addForm.group_id" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="">Pilih grup</option><template x-for="group in availGroups" :key="group.id"><option :value="String(group.id)" x-text="(group.code || ('Kelompok #'+group.id)) + ' (' + (group.members||[]).length + ' anggota)'"></option></template></select></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-add-existing').close()">Batal</x-capstone::button><x-capstone::button @click="saveAddExisting()" ::disabled="saving">Tambahkan Anggota</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-period-flag" title="Finalisasi Periode" description="Kunci periode setelah seluruh kelompok berstatus Kelompok Final. Bidding berhenti dan data tidak bisa diubah lagi tanpa Buka Kembali.">
        <p class="text-sm">Kelompok final: <strong x-text="stats?.total_kelompok_final ?? 0"></strong><span x-show="(stats?.total_pdc1_active ?? 0) > 0">, pasca final (PDC1): <strong x-text="stats?.total_pdc1_active ?? 0"></strong></span>.</p>
        <label class="flex items-start gap-2 text-sm"><input type="checkbox" x-model="activatePdc1" class="mt-1">Langsung aktifkan seluruh Kelompok Final ke PDC1.</label>
        <label class="flex items-start gap-2 text-sm"><input type="checkbox" x-model="periodFlagConfirm" class="mt-1">Saya paham periode terkunci dan hanya bisa diubah lewat Buka Kembali Periode.</label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-period-flag').close()">Batal</x-capstone::button><x-capstone::button @click="doPeriodFlag()" ::disabled="!periodFlagConfirm || saving">Finalisasi Periode</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-rollback" title="Rollback ke Siap" description="Kembalikan Kelompok Final ke Siap Finalisasi. Tindakan tercatat sebagai audit dan tidak menghapus judul atau pembimbing.">
        <p class="text-sm"><span x-text="rollbackIds.length" class="tabular-nums"></span> kelompok akan dirollback.</p>
        <label class="block text-sm">Alasan (wajib, minimal 10 karakter)<textarea x-model="reasonForm.reason" required minlength="10" aria-describedby="rollback-hint" class="mt-1 w-full rounded-md border bg-background px-3 py-2"></textarea></label>
        <p id="rollback-hint" class="text-xs text-muted-foreground">Tulis penyebab rollback agar jejak audit jelas.</p>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-rollback').close()">Batal</x-capstone::button><x-capstone::button variant="destructive" @click="doRollback()" ::disabled="saving || reasonForm.reason.trim().length < 10">Rollback Kelompok</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-cancel" title="Batalkan Kelompok Final" description="Kembalikan satu kelompok ke Siap Finalisasi. Judul dan pembimbing tetap tersimpan.">
        <p class="text-sm">Kelompok: <strong x-text="cancelTarget?.code"></strong></p>
        <label class="block text-sm">Alasan (opsional, tercatat sebagai audit)<textarea x-model="reasonForm.reason" class="mt-1 w-full rounded-md border bg-background px-3 py-2"></textarea></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-cancel').close()">Batal</x-capstone::button><x-capstone::button variant="destructive" @click="doCancel()" ::disabled="saving">Batalkan Final</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-reopen" title="Buka Kembali Periode" description="Periode terkunci menjadi bisa diubah lagi. Kelompok PDC1 Aktif kembali menjadi Kelompok Final.">
        <p class="text-sm text-muted-foreground">Gunakan hanya bila ada koreksi setelah penguncian. Semua perubahan tercatat.</p>
        <label class="flex items-start gap-2 text-sm"><input type="checkbox" x-model="execConfirm" class="mt-1">Saya paham periode kembali bisa diubah.</label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-reopen').close()">Batal</x-capstone::button><x-capstone::button variant="destructive" @click="doReopen()" ::disabled="!execConfirm || saving">Buka Kembali Periode</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-autofix" title="Perbaiki Otomatis" description="Perbaiki status kesiapan yang bisa diselesaikan sistem. Mode Aman hanya menyentuh kasus jelas, Mode Agresif menyentuh lebih banyak kasus.">
        <label class="block text-sm">Mode<select x-model="autoFixMode" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="safe">Aman</option><option value="aggressive">Agresif</option></select></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-autofix').close()">Batal</x-capstone::button><x-capstone::button @click="doAutoFix()" ::disabled="saving">Jalankan Perbaikan</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-force" title="Paksa Siap" description="Paksa kelompok ke Siap Finalisasi. Syarat tetap berlaku: sudah punya judul dan jumlah anggota sesuai rentang periode.">
        <p class="text-sm">Kelompok: <strong x-text="forceTarget?.code"></strong></p>
        <label class="block text-sm">Alasan (wajib, minimal 10 karakter)<textarea x-model="reasonForm.reason" required minlength="10" class="mt-1 w-full rounded-md border bg-background px-3 py-2"></textarea></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-force').close()">Batal</x-capstone::button><x-capstone::button @click="doForceReady()" ::disabled="saving || reasonForm.reason.trim().length < 10">Paksa Siap</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-bidding" title="Kunci atau Buka Bidding" description="Ubah status kunci bidding periode berjalan. Mengunci menghentikan bid baru dari mahasiswa.">
        <p class="text-sm">Aksi: <strong x-text="biddingAction"></strong></p>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-bidding').close()">Batal</x-capstone::button><x-capstone::button @click="doBidding()" ::disabled="saving">Terapkan Perubahan</x-capstone::button></div>
    </x-capstone::dialog>
</div>
@endsection
