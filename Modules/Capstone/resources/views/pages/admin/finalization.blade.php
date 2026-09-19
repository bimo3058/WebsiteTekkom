@extends('capstone::layouts.app')
@section('title','Finalisasi')
@section('content')
<div x-data="adminFinalization" x-init="init()" x-cloak class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <div class="mr-auto">
            <h1 class="text-2xl font-bold">Finalisasi Kelompok</h1>
            <p class="text-muted-foreground text-sm">Dashboard finalisasi: kesiapan kelompok, pembimbing, judul, dan penguncian periode.</p>
        </div>
        <label class="text-sm">Periode
            <select x-model="periodId" @change="page=1;load()" :disabled="saving" class="ml-2 rounded-md border bg-background px-3 py-2">
                <option value="">Pilih periode</option>
                <template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name"></option></template>
            </select>
        </label>
        <span x-show="period && period.is_finalized" class="rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-semibold text-emerald-700">Periode difinalisasi</span>
        <x-capstone::button variant="outline" @click="load()" ::disabled="loading || saving"><x-capstone::icon name="RefreshCw" />Refresh</x-capstone::button>
        <x-capstone::button variant="outline" @click="doExport('excel')" ::disabled="loading || saving || !periodId"><x-capstone::icon name="Download" />Excel</x-capstone::button>
        <x-capstone::button variant="outline" @click="doExport('pdf')" ::disabled="loading || saving || !periodId"><x-capstone::icon name="Download" />PDF</x-capstone::button>
        <x-capstone::button variant="outline" @click="openPeriodFlag()" x-show="!period?.is_finalized && !flow?.can_execute_finalization && ((stats?.total_kelompok_final ?? 0) + (stats?.total_pdc1_active ?? 0)) > 0" ::disabled="saving"><x-capstone::icon name="Flag" />Finalisasi Periode</x-capstone::button>
        <x-capstone::button variant="outline" @click="openReopen()" x-show="stats?.can_reopen_finalization" ::disabled="saving"><x-capstone::icon name="RotateCcw" />Buka Kembali</x-capstone::button>
        <x-capstone::button variant="outline" @click="openAutoFix()" ::disabled="!periodId || saving"><x-capstone::icon name="Wrench" />Auto-fix</x-capstone::button>
        <x-capstone::button variant="outline" @click="confirmBidding('lock')" x-show="period && period.is_finalized" ::disabled="saving">Kunci Bidding</x-capstone::button>
        <x-capstone::button variant="outline" @click="confirmBidding('unlock')" x-show="period" ::disabled="saving">Buka Bidding</x-capstone::button>
    </div>

    <x-capstone::card x-show="multiplePeriods" title="Pilih periode aktif" description="Terdapat lebih dari satu periode aktif. Pilih satu periode untuk melanjutkan.">
        <div class="flex flex-wrap gap-2 px-6 pb-6">
            <template x-for="period in periods" :key="period.id">
                <x-capstone::button variant="outline" @click="periodId=String(period.id);multiplePeriods=false;page=1;load()"><span x-text="period.name"></span></x-capstone::button>
            </template>
        </div>
    </x-capstone::card>

    <div x-show="flow && flow.blockers && flow.blockers.length" class="space-y-2">
        <template x-for="blocker in (flow?.blockers || [])" :key="blocker.type">
            <div class="flex flex-wrap items-center gap-3 rounded-xl border px-4 py-3 text-sm" :class="blocker.severity==='error' ? 'border-red-200 bg-red-50 text-red-700' : 'border-amber-200 bg-amber-50 text-amber-700'">
                <x-capstone::icon name="AlertTriangle" />
                <span x-text="blocker.message" class="mr-auto"></span>
                <x-capstone::button x-show="blocker.action==='reopen'" size="sm" variant="outline" @click="openReopen()">Buka Kembali</x-capstone::button>
                <x-capstone::button x-show="blocker.action==='period_flag'" size="sm" @click="openPeriodFlag()">Finalisasi Periode</x-capstone::button>
            </div>
        </template>
    </div>

    <div x-show="error" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <span x-text="error"></span>
        <button type="button" class="ml-2 font-semibold underline" @click="load()">Coba lagi</button>
    </div>

    <div x-show="stats" class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
        <button type="button" @click="setTab('ready')" class="rounded-xl border bg-card p-4 text-left shadow-sm hover:border-primary" :class="tab==='ready' ? 'border-primary ring-1 ring-primary' : ''">
            <p class="text-xs text-muted-foreground">Siap Finalisasi</p>
            <p class="text-2xl font-bold" x-text="stats?.total_ready ?? 0"></p>
        </button>
        <button type="button" @click="setTab('final')" class="rounded-xl border bg-card p-4 text-left shadow-sm hover:border-primary" :class="tab==='final' ? 'border-primary ring-1 ring-primary' : ''">
            <p class="text-xs text-muted-foreground">Kelompok Final</p>
            <p class="text-2xl font-bold" x-text="stats?.total_kelompok_final ?? 0"></p>
        </button>
        <button type="button" @click="setTab('others', 'no_group')" class="rounded-xl border bg-card p-4 text-left shadow-sm hover:border-primary" :class="tab==='others' && subTab==='no_group' ? 'border-primary ring-1 ring-primary' : ''">
            <p class="text-xs text-muted-foreground">Tanpa Kelompok</p>
            <p class="text-2xl font-bold" x-text="stats?.total_no_group ?? 0"></p>
        </button>
        <button type="button" @click="setTab('others', 'no_title')" class="rounded-xl border bg-card p-4 text-left shadow-sm hover:border-primary" :class="tab==='others' && subTab==='no_title' ? 'border-primary ring-1 ring-primary' : ''">
            <p class="text-xs text-muted-foreground">Tanpa Judul</p>
            <p class="text-2xl font-bold" x-text="stats?.total_no_title ?? 0"></p>
        </button>
        <button type="button" @click="setTab('others', 'not_ready')" class="rounded-xl border bg-card p-4 text-left shadow-sm hover:border-primary" :class="tab==='others' && subTab==='not_ready' ? 'border-primary ring-1 ring-primary' : ''">
            <p class="text-xs text-muted-foreground">Belum Siap</p>
            <p class="text-2xl font-bold" x-text="stats?.total_not_ready ?? 0"></p>
        </button>
        <button type="button" @click="setTab('post')" class="rounded-xl border bg-card p-4 text-left shadow-sm hover:border-primary" :class="tab==='post' ? 'border-primary ring-1 ring-primary' : ''">
            <p class="text-xs text-muted-foreground">PDC1 Aktif / Pasca</p>
            <p class="text-2xl font-bold"><span x-text="stats?.total_pdc1_active ?? 0"></span><span class="text-sm font-normal text-muted-foreground"> / <span x-text="stats?.total_post_finalization ?? 0"></span></span></p>
        </button>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <button type="button" @click="setTab('ready')" class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium" :class="tab==='ready' ? 'bg-primary text-primary-foreground' : 'border bg-background shadow-xs'">Ready (<span x-text="stats?.total_ready ?? 0"></span>)</button>
        <button type="button" @click="setTab('final')" class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium" :class="tab==='final' ? 'bg-primary text-primary-foreground' : 'border bg-background shadow-xs'">Final (<span x-text="stats?.total_kelompok_final ?? 0"></span>)</button>
        <button type="button" @click="setTab('post')" class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium" :class="tab==='post' ? 'bg-primary text-primary-foreground' : 'border bg-background shadow-xs'">Pasca (<span x-text="stats?.total_post_finalization ?? 0"></span>)</button>
        <button type="button" @click="setTab('others')" class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium" :class="tab==='others' ? 'bg-primary text-primary-foreground' : 'border bg-background shadow-xs'">Others</button>
        <template x-if="tab==='others'">
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="setSubTab('no_group')" class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium" :class="subTab==='no_group' ? 'bg-primary text-primary-foreground' : 'border bg-background shadow-xs'">Tanpa Kelompok (<span x-text="stats?.total_no_group ?? 0"></span>)</button>
                <button type="button" @click="setSubTab('no_title')" class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium" :class="subTab==='no_title' ? 'bg-primary text-primary-foreground' : 'border bg-background shadow-xs'">Tanpa Judul (<span x-text="stats?.total_no_title ?? 0"></span>)</button>
                <button type="button" @click="setSubTab('not_ready')" class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium" :class="subTab==='not_ready' ? 'bg-primary text-primary-foreground' : 'border bg-background shadow-xs'">Belum Siap (<span x-text="stats?.total_not_ready ?? 0"></span>)</button>
            </div>
        </template>
    </div>

    <div class="flex flex-wrap items-center gap-3 rounded-xl border bg-card p-4">
        <input type="search" x-model="search" @input="onSearch()" placeholder="Cari kelompok, judul, nama, NIM..." aria-label="Cari data" class="min-w-48 flex-1 rounded-md border bg-background px-3 py-2 text-sm">
        <template x-if="isGroupView">
            <label class="text-sm">Pembimbing
                <select x-model="supervisorStatus" @change="page=1;load()" class="ml-2 rounded-md border bg-background px-3 py-2">
                    <option value="all">Semua</option>
                    <option value="missing_sv1">Belum ada SV1</option>
                    <option value="missing_sv2">Belum ada SV2</option>
                    <option value="complete">Lengkap</option>
                </select>
            </label>
        </template>
        <template x-if="isGroupView">
            <label class="text-sm">Anggota
                <select x-model="memberCount" @change="page=1;load()" class="ml-2 rounded-md border bg-background px-3 py-2">
                    <option value="all">Semua</option>
                    <option value="under_min">Kurang</option>
                    <option value="in_range">Sesuai</option>
                    <option value="over_max">Lebih</option>
                </select>
            </label>
        </template>
        <label class="text-sm">Per halaman
            <select x-model.number="pageSize" @change="page=1;load()" class="ml-2 rounded-md border bg-background px-3 py-2">
                <option :value="10">10</option><option :value="20">20</option><option :value="50">50</option>
            </select>
        </label>
    </div>

    <div x-show="isGroupView && tab==='ready' && selectedIds.length" x-cloak class="flex flex-wrap items-center gap-3 rounded-xl border border-primary/30 bg-primary/5 px-4 py-3 text-sm">
        <span><span x-text="selectedIds.length"></span> kelompok dipilih</span>
        <x-capstone::button size="sm" @click="openBatchSv()">Set Pembimbing Massal</x-capstone::button>
        <x-capstone::button size="sm" variant="outline" x-show="tab==='final'" @click="openRollback()">Rollback</x-capstone::button>
        <x-capstone::button size="sm" variant="ghost" @click="selectedIds=[]">Batalkan</x-capstone::button>
    </div>

    <div x-show="!isGroupView && noGroupSelected.length" x-cloak class="flex flex-wrap items-center gap-3 rounded-xl border border-primary/30 bg-primary/5 px-4 py-3 text-sm">
        <span><span x-text="noGroupSelected.length"></span> mahasiswa dipilih</span>
        <x-capstone::button size="sm" @click="openManual()">Buat Grup Manual</x-capstone::button>
        <x-capstone::button size="sm" variant="outline" @click="openAddExisting()">Tambah ke Grup</x-capstone::button>
        <x-capstone::button size="sm" variant="ghost" @click="noGroupSelected=[]">Batalkan</x-capstone::button>
    </div>

    <x-capstone::card>
        @include('capstone::partials.loading')
        <div x-show="!loading && !error && !items.length" x-cloak class="flex flex-col items-center justify-center px-4 py-16 text-center">
            <p class="font-medium text-gray-500">Tidak ada data ditemukan</p>
            <p class="mt-1 text-sm text-gray-400">Coba ubah filter atau kata kunci pencarian</p>
        </div>
        <div x-show="!loading && !error && items.length" x-cloak class="relative w-full overflow-x-auto">
            <table x-show="isGroupView" class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b"><tr class="bg-gray-50/50 border-b">
                    <th class="h-10 px-2 text-left"><input type="checkbox" @change="toggleAll($event.target.checked)" :checked="items.length>0 && selectedIds.length===items.length" aria-label="Pilih semua"></th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Kelompok</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Anggota</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Judul</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Pembimbing</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Status</th>
                    <th x-show="tab==='ready'" class="h-10 px-2 text-left font-semibold text-gray-700">Kesiapan</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Aksi</th>
                </tr></thead>
                <tbody class="[&_tr:last-child]:border-0">
                    <template x-for="item in items" :key="item.id">
                        <tr class="border-b">
                            <td class="px-2 py-2"><input type="checkbox" @change="toggleId(item.id)" :checked="selectedIds.includes(item.id)" :aria-label="'Pilih '+(item.code||item.id)"></td>
                            <td class="px-2 py-2 font-medium" x-text="item.code || ('Kelompok #'+item.id)"></td>
                            <td class="px-2 py-2"><span x-text="(item.members||[]).length + ' anggota'"></span><br><span class="text-xs text-muted-foreground" x-text="memberNames(item)"></span></td>
                            <td class="px-2 py-2"><span x-text="item.title?.title || '—'"></span></td>
                            <td class="px-2 py-2 text-xs"><span x-text="'SV1: '+(item.supervisor1?.name || item.supervisor1?.user?.name || '—')"></span><br><span x-text="'SV2: '+(item.supervisor2?.name || item.supervisor2?.user?.name || '—')"></span></td>
                            <td class="px-2 py-2"><span class="rounded-full border px-2 py-0.5 text-xs" :class="groupStatusClass(item.status)" x-text="item.status_label || item.status"></span></td>
                            <td x-show="tab==='ready'" class="px-2 py-2"><div class="flex flex-wrap gap-1"><template x-for="r in readiness(item)" :key="r.label"><span class="rounded-full border px-2 py-0.5 text-xs" :class="r.ok ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-600 border-red-200'" x-text="(r.ok ? '✓ ' : '✗ ') + r.label"></span></template></div></td>
                            <td class="px-2 py-2"><div class="flex flex-wrap gap-1">
                                <x-capstone::button size="sm" variant="outline" x-show="item.allowed_actions?.can_set_supervisor" @click="openSingleSv(item)">SV</x-capstone::button>
                                <x-capstone::button size="sm" variant="outline" x-show="item.allowed_actions?.can_mark_kelompok_final" @click="openMarkFinal(item)">Final</x-capstone::button>
                                <x-capstone::button size="sm" variant="outline" x-show="item.allowed_actions?.can_assign_title" @click="openAssignTitle(item)">Judul</x-capstone::button>
                                <x-capstone::button size="sm" variant="outline" x-show="item.allowed_actions?.can_promote_to_ready_for_finalization" @click="promote(item)">Promote</x-capstone::button>
                                <x-capstone::button size="sm" variant="outline" x-show="item.allowed_actions?.can_cancel_kelompok_final" @click="openCancel(item)">Batalkan</x-capstone::button>
                                <x-capstone::button size="sm" variant="ghost" x-show="!['READY_FOR_FINALIZATION','KELOMPOK_FINAL','PDC1_ACTIVE','PDC2_ACTIVE','CLOSED'].includes(item.status)" @click="openForceReady(item)">Force</x-capstone::button>
                            </div></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <table x-show="!isGroupView" class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b"><tr class="bg-gray-50/50 border-b">
                    <th class="h-10 px-2 text-left"><input type="checkbox" @change="toggleAllStudents($event.target.checked)" :checked="items.length>0 && noGroupSelected.length===items.length" aria-label="Pilih semua"></th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Nama</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">NIM</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Email</th>
                </tr></thead>
                <tbody class="[&_tr:last-child]:border-0">
                    <template x-for="item in items" :key="item.id">
                        <tr class="border-b">
                            <td class="px-2 py-2"><input type="checkbox" @change="toggleStudent(item.id)" :checked="noGroupSelected.includes(item.id)" :aria-label="'Pilih '+(item.name||item.id)"></td>
                            <td class="px-2 py-2 font-medium" x-text="item.name || item.user?.name || '—'"></td>
                            <td class="px-2 py-2" x-text="item.student_number || item.nim || '—'"></td>
                            <td class="px-2 py-2" x-text="item.email || item.user?.email || '—'"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div x-show="!loading && !error && items.length" x-cloak class="flex flex-wrap items-center justify-between gap-4 border-t px-6 py-4 text-sm">
            <p class="text-muted-foreground">Total <span x-text="pagination.total"></span> data</p>
            <div class="flex items-center gap-2">
                <x-capstone::button variant="outline" size="sm" @click="gotoPage(1)" ::disabled="page<=1" aria-label="Halaman pertama">«</x-capstone::button>
                <x-capstone::button variant="outline" size="sm" @click="gotoPage(page-1)" ::disabled="page<=1" aria-label="Halaman sebelumnya">‹</x-capstone::button>
                <span>Halaman <span x-text="page"></span> dari <span x-text="pagination.last_page"></span></span>
                <x-capstone::button variant="outline" size="sm" @click="gotoPage(page+1)" ::disabled="page>=pagination.last_page" aria-label="Halaman berikutnya">›</x-capstone::button>
                <x-capstone::button variant="outline" size="sm" @click="gotoPage(pagination.last_page)" ::disabled="page>=pagination.last_page" aria-label="Halaman terakhir">»</x-capstone::button>
            </div>
        </div>
    </x-capstone::card>

    <x-capstone::card title="Beban Dosen Pembimbing" description="Kapasitas supervisi per dosen pada periode berjalan.">
        <div class="relative w-full overflow-x-auto px-6 pb-6">
            <table class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b"><tr class="bg-gray-50/50 border-b">
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Dosen</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">NIP</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Beban</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Sisa</th>
                    <th class="h-10 px-2 text-left font-semibold text-gray-700">Status</th>
                </tr></thead>
                <tbody>
                    <template x-for="lecturer in lecturers" :key="lecturer.id">
                        <tr class="border-b">
                            <td class="px-2 py-2 font-medium" x-text="lecturer.name || '—'"></td>
                            <td class="px-2 py-2" x-text="lecturer.nip || lecturer.employee_number || '—'"></td>
                            <td class="px-2 py-2"><span x-text="lecturer.current_load"></span> / <span x-text="lecturer.max_load"></span></td>
                            <td class="px-2 py-2" x-text="lecturer.remaining_capacity"></td>
                            <td class="px-2 py-2"><span class="rounded-full border px-2 py-0.5 text-xs" :class="lecturer.is_overloaded ? 'bg-red-50 text-red-600 border-red-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'" x-text="lecturer.is_overloaded ? 'Overload' : 'Tersedia'"></span></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <p x-show="!lecturers.length && !loading" class="py-6 text-center text-sm text-gray-400">Belum ada data dosen.</p>
        </div>
    </x-capstone::card>

    <x-capstone::dialog id="fin-set-sv" title="Set Pembimbing" description="Tetapkan pembimbing 1 (wajib) dan pembimbing 2 (opsional, harus berbeda).">
        <p class="text-sm text-muted-foreground"><span x-text="svForm.group_ids.length"></span> kelompok dipilih</p>
        <label class="block text-sm">Pembimbing 1<select x-model="svForm.supervisor_1_id" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="">Pilih dosen</option><template x-for="lecturer in lecturers" :key="lecturer.id"><option :value="String(lecturer.id)" x-text="lecturer.name + ' (' + lecturer.current_load + '/' + lecturer.max_load + ')'"></option></template></select></label>
        <p x-show="svForm.svDefaultName" class="text-xs text-muted-foreground">SV1 default: dosen pemilik/pengaju judul (<span x-text="svForm.svDefaultName"></span>) — dapat diganti.</p>
        <label class="block text-sm">Pembimbing 2 (opsional)<select x-model="svForm.supervisor_2_id" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="">Tidak ada</option><template x-for="lecturer in lecturers" :key="lecturer.id"><option :value="String(lecturer.id)" x-text="lecturer.name + ' (' + lecturer.current_load + '/' + lecturer.max_load + ')'"></option></template></select></label>
        <label x-show="svForm.group_ids.length===1 && svForm.isReady" class="flex items-start gap-2 text-sm"><input type="checkbox" x-model="svForm.mark_final" class="mt-1">Tandai sebagai Kelompok Final (wajib SV1 dan SV2 terisi)</label>
        <p x-show="svForm.mark_final && !(svForm.supervisor_1_id && svForm.supervisor_2_id)" class="text-xs text-amber-700">Kelompok Final wajib SV1 dan SV2 terisi.</p>
        <label class="block text-sm">Catatan (opsional)<input type="text" x-model="svForm.notes" class="mt-1 w-full rounded-md border bg-background px-3 py-2"></label>
        <p x-show="svError" x-text="svError" class="text-sm text-red-600"></p>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-set-sv').close()">Batal</x-capstone::button><x-capstone::button @click="saveSupervisors()" ::disabled="saving || (svForm.mark_final && !(svForm.supervisor_1_id && svForm.supervisor_2_id))">Simpan</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-assign-title" title="Tetapkan Judul" description="Pilih judul yang masih memiliki kuota.">
        <p class="text-sm">Kelompok: <strong x-text="titleForm.group_code"></strong></p>
        <label class="block text-sm">Judul<select x-model="titleForm.title_id" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="">Pilih judul</option><template x-for="title in availTitles" :key="title.id"><option :value="String(title.id)" x-text="title.title + ' (sisa ' + title.remaining_quota + ')'"></option></template></select></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-assign-title').close()">Batal</x-capstone::button><x-capstone::button @click="saveAssignTitle()" ::disabled="saving">Simpan</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-manual" title="Buat Grup Manual" description="Buat kelompok dari mahasiswa terpilih.">
        <p class="text-sm text-muted-foreground"><span x-text="noGroupSelected.length"></span> mahasiswa dipilih</p>
        <div class="space-y-2 text-sm">
            <label class="flex items-center gap-2"><input type="radio" value="no_title" x-model="manualForm.option">Tanpa judul</label>
            <label class="flex items-center gap-2"><input type="radio" value="assign_title" x-model="manualForm.option">Tetapkan judul tersedia</label>
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

    <x-capstone::dialog id="fin-add-existing" title="Tambah ke Grup" description="Masukkan mahasiswa terpilih ke grup yang masih bisa menerima anggota.">
        <p class="text-sm text-muted-foreground"><span x-text="noGroupSelected.length"></span> mahasiswa dipilih</p>
        <label class="block text-sm">Grup tujuan<select x-model="addForm.group_id" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="">Pilih grup</option><template x-for="group in availGroups" :key="group.id"><option :value="String(group.id)" x-text="(group.code || ('Kelompok #'+group.id)) + ' (' + (group.members||[]).length + ' anggota)'"></option></template></select></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-add-existing').close()">Batal</x-capstone::button><x-capstone::button @click="saveAddExisting()" ::disabled="saving">Tambahkan</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-period-flag" title="Finalisasi Periode" description="Kunci periode yang seluruh kelompoknya sudah Kelompok Final.">
        <p class="text-sm">Kelompok final: <strong x-text="stats?.total_kelompok_final ?? 0"></strong><span x-show="(stats?.total_pdc1_active ?? 0) > 0">, pasca-final (PDC1): <strong x-text="stats?.total_pdc1_active ?? 0"></strong></span>. Periode akan dikunci sehingga bidding terkunci dan data tidak bisa diubah lagi.</p>
        <label class="flex items-start gap-2 text-sm"><input type="checkbox" x-model="activatePdc1" class="mt-1">Langsung aktifkan seluruh kelompok final ke PDC1.</label>
        <label class="flex items-start gap-2 text-sm"><input type="checkbox" x-model="periodFlagConfirm" class="mt-1">Saya paham finalisasi periode tidak dapat dibatalkan tanpa membuka kembali periode.</label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-period-flag').close()">Batal</x-capstone::button><x-capstone::button @click="doPeriodFlag()" ::disabled="!periodFlagConfirm || saving">Finalisasi Periode</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-rollback" title="Rollback Finalisasi" description="Kembalikan kelompok final ke status siap.">
        <p class="text-sm"><span x-text="rollbackIds.length"></span> kelompok akan di-rollback.</p>
        <label class="block text-sm">Alasan<textarea x-model="reasonForm.reason" required class="mt-1 w-full rounded-md border bg-background px-3 py-2"></textarea></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-rollback').close()">Batal</x-capstone::button><x-capstone::button @click="doRollback()" ::disabled="saving">Rollback</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-cancel" title="Batalkan Kelompok Final" description="Kembalikan satu kelompok ke status siap finalisasi.">
        <p class="text-sm">Kelompok: <strong x-text="cancelTarget?.code"></strong></p>
        <label class="block text-sm">Alasan (opsional)<textarea x-model="reasonForm.reason" class="mt-1 w-full rounded-md border bg-background px-3 py-2"></textarea></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-cancel').close()">Batal</x-capstone::button><x-capstone::button @click="doCancel()" ::disabled="saving">Batalkan Final</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-reopen" title="Buka Kembali Periode" description="Membuka kembali periode yang sudah difinalisasi.">
        <p class="text-sm text-muted-foreground">Kelompok berstatus PDC1 Aktif akan dikembalikan menjadi Kelompok Final.</p>
        <label class="flex items-start gap-2 text-sm"><input type="checkbox" x-model="execConfirm" class="mt-1">Saya paham periode akan kembali bisa diubah.</label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-reopen').close()">Batal</x-capstone::button><x-capstone::button @click="doReopen()" ::disabled="!execConfirm || saving">Buka Kembali</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-autofix" title="Auto-fix Kesiapan" description="Perbaiki otomatis status kesiapan kelompok.">
        <label class="block text-sm">Mode<select x-model="autoFixMode" class="mt-1 w-full rounded-md border bg-background px-3 py-2"><option value="safe">Safe</option><option value="aggressive">Aggressive</option></select></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-autofix').close()">Batal</x-capstone::button><x-capstone::button @click="doAutoFix()" ::disabled="saving">Jalankan</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-force" title="Force Ready" description="Paksa kelompok ke status Ready for Finalization. Syarat tetap berlaku: sudah punya judul dan jumlah anggota dalam rentang periode.">
        <p class="text-sm">Kelompok: <strong x-text="forceTarget?.code"></strong></p>
        <label class="block text-sm">Alasan<textarea x-model="reasonForm.reason" class="mt-1 w-full rounded-md border bg-background px-3 py-2"></textarea></label>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-force').close()">Batal</x-capstone::button><x-capstone::button @click="doForceReady()" ::disabled="saving">Paksa Ready</x-capstone::button></div>
    </x-capstone::dialog>

    <x-capstone::dialog id="fin-bidding" title="Kunci / Buka Bidding" description="Ubah status kunci bidding periode berjalan.">
        <p class="text-sm">Aksi: <strong x-text="biddingAction"></strong></p>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="dialog('fin-bidding').close()">Batal</x-capstone::button><x-capstone::button @click="doBidding()" ::disabled="saving">Terapkan</x-capstone::button></div>
    </x-capstone::dialog>
</div>
@endsection
