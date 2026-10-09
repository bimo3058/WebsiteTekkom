@extends('capstone::layouts.app')
@section('title','Approval')
@section('content')
<div x-data="{tab:'join'}" class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Approval</h1>
            <p class="mt-1 text-sm text-muted-foreground">Review student requests: period join requests and sidang TA registrations.</p>
        </div>
    </div>

    <div class="inline-flex rounded-xl bg-slate-100 p-1" role="tablist" aria-label="Approval types">
        <button type="button" role="tab" :aria-selected="tab==='join'" @click="tab='join'" class="rounded-lg px-5 py-1.5 text-sm font-semibold" :class="tab==='join'?'bg-white text-slate-900 shadow-sm':'text-slate-500 hover:text-slate-700'">Join Requests</button>
        <button type="button" role="tab" :aria-selected="tab==='sidang'" @click="tab='sidang'" class="rounded-lg px-5 py-1.5 text-sm font-semibold" :class="tab==='sidang'?'bg-white text-slate-900 shadow-sm':'text-slate-500 hover:text-slate-700'">Sidang TA</button>
    </div>

    <div x-show="tab==='join'" x-data="adminPeriodRegistrations" @keydown.escape.window="closeMenu()" @scroll.window="closeMenu()" @resize.window="closeMenu()" class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-foreground">Period Join Requests</h2>
            <p class="text-sm text-muted-foreground">Review student requests to join an academic period. Approved students can create or join groups.</p>
        </div>
        <x-capstone::button variant="outline" @click="load()" ::disabled="loading"><x-capstone::icon name="RefreshCw" size="15" />Refresh</x-capstone::button>
    </div>

    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h3 class="text-base font-semibold">Tabel Join Requests</h3>
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
                    <input x-model.debounce.300ms="search" @input="filter()" type="search" placeholder="Cari nama / NIM..." aria-label="Cari join request" class="h-9 w-56 rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
                </div>
                <select x-model="status" @change="filter()" aria-label="Filter status" class="h-9 rounded-lg border border-input bg-white px-2.5 text-sm text-muted-foreground outline-none">
                    <option value="PENDING">Pending</option>
                    <option value="APPROVED">Approved</option>
                    <option value="REJECTED">Rejected</option>
                    <option value="all">Semua status</option>
                </select>
                <select x-model="periodId" @change="filter()" aria-label="Filter periode" class="h-9 rounded-lg border border-input bg-white px-2.5 text-sm text-muted-foreground outline-none">
                    <option value="">Semua periode</option>
                    <template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name"></option></template>
                </select>
                <button type="button" @click="reset()" class="inline-flex h-9 items-center rounded-lg border border-input bg-white px-3 text-sm text-muted-foreground shadow-xs hover:bg-accent">Reset</button>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead>
                    <tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                        <th class="px-4 py-3 font-medium">Mahasiswa</th>
                        <th class="px-4 py-3 font-medium">NIM</th>
                        <th class="px-4 py-3 font-medium">Periode</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Diminta</th>
                        <th class="px-5 py-3 text-right font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="item in items" :key="item.id">
                        <tr class="bg-white hover:bg-muted/30">
                            <td class="px-4 py-4 font-medium text-foreground" x-text="studentName(item)"></td>
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-foreground" x-text="studentNim(item)"></td>
                            <td class="whitespace-nowrap px-4 py-4 text-foreground" x-text="item.period?.name || ('Period #'+item.period_id)"></td>
                            <td class="whitespace-nowrap px-4 py-4"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusClass(item.status)" x-text="item.status"></span><p x-show="item.status==='REJECTED' && item.rejection_reason" class="mt-1 max-w-64 whitespace-normal text-xs text-muted-foreground" x-text="item.rejection_reason"></p></td>
                            <td class="whitespace-nowrap px-4 py-4 text-foreground" x-text="shortDate(item.created_at)"></td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <button type="button" x-show="canReview(item)" @click="openRowMenu($event,item)" aria-label="Aksi" aria-haspopup="menu" class="rounded-md p-1.5 text-[#666D80] hover:bg-muted"><x-capstone::icon name="Ellipsis" class="size-5" /></button>
                                <span x-show="!canReview(item)" class="text-xs text-muted-foreground">Reviewed</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <p x-show="!items.length" class="p-8 text-center text-sm text-muted-foreground">Belum ada join request.</p>
        </div>

        <div x-show="items.length" x-cloak class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-0 overflow-hidden rounded-lg border border-input bg-white text-[13px]">
                    <span class="px-2.5 text-muted-foreground">Per page</span>
                    <select x-model.number="pageSize" @change="page=1;load()" aria-label="Baris per halaman" class="border-l border-input bg-transparent px-2 py-1.5 font-medium outline-none"><option>10</option><option>25</option><option>50</option></select>
                </label>
                <span class="text-foreground"><span x-text="'Showing '+showingFrom+' to '+showingTo+' of, '+(pagination.total ?? 0)+' results'"></span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="gotoPage(page-1)" :disabled="page<=1" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                <template x-for="(n,i) in pageNumbers" :key="n">
                    <span class="flex items-center gap-1.5">
                        <span x-show="i>0 && n-pageNumbers[i-1]>1" class="px-1 text-muted-foreground">...</span>
                        <button type="button" @click="gotoPage(n)" x-text="n" class="inline-flex size-8 items-center justify-center rounded-lg border text-sm" :class="page===n?'border-[#1E2A5A] bg-[#1E2A5A] font-semibold text-white':'border-input bg-white text-foreground hover:bg-accent'"></button>
                    </span>
                </template>
                <button type="button" @click="gotoPage(page+1)" :disabled="page>=(pagination.last_page||1)" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
            </div>
        </div>
    </div>

    <div x-show="openMenuItem" @click.away="closeMenu()" x-cloak :style="menuStyle" style="position: fixed; z-index: 50;" role="menu" class="w-48 rounded-lg border border-border bg-white p-1.5 text-left shadow-md">
        <button type="button" @click="review(openMenuItem,'approve');closeMenu()" ::disabled="saving" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-foreground hover:bg-accent">Approve</button>
        <button type="button" @click="review(openMenuItem,'reject');closeMenu()" ::disabled="saving" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-red-600 hover:bg-red-50">Reject</button>
    </div>
    <x-capstone::dialog id="join-review"><h2 class="text-lg font-semibold" x-text="decision==='approve'?'Approve Join Request':'Reject Join Request'"></h2><p class="my-4 text-sm text-muted-foreground" x-text="selected ? studentName(selected)+' · '+studentNim(selected)+' · '+(selected.period?.name || '') : ''"></p><form @submit.prevent="submit()" class="space-y-4">
        <p x-show="decision==='approve'" class="text-sm">Approve this request? The student will be able to create or join a group in this period.</p>
        <div x-show="decision==='reject'" class="space-y-2"><label for="rejection-reason" class="text-sm font-medium">Rejection Reason</label><textarea id="rejection-reason" x-model="reason" :required="decision==='reject'" maxlength="1000" class="min-h-24 w-full rounded-md border bg-background p-3 text-sm" placeholder="Explain why this request is being rejected..."></textarea></div>
        <p class="text-sm text-destructive" role="alert" x-text="errors.root"></p><div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Cancel</x-capstone::button><x-capstone::button type="submit" ::disabled="saving || (decision==='reject' && !reason.trim())"><span x-text="saving?'Saving...':decision==='approve'?'Approve':'Reject'"></span></x-capstone::button></div>
    </form></x-capstone::dialog>
    </div>

    <div x-show="tab==='sidang'" x-data="adminTaRegistrations" @keydown.escape.window="closeMenu()" @scroll.window="closeMenu()" @resize.window="closeMenu()" class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-foreground">Sidang TA Registrations</h2>
            <p class="text-sm text-muted-foreground">Review student requests to enter the TA document-upload phase. Approved students can upload the required TA documents.</p>
        </div>
        <x-capstone::button variant="outline" @click="load()" ::disabled="loading"><x-capstone::icon name="RefreshCw" size="15" />Refresh</x-capstone::button>
    </div>

    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h3 class="text-base font-semibold">Tabel Sidang TA</h3>
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
                    <input x-model.debounce.300ms="search" @input="filter()" type="search" placeholder="Cari nama / NIM..." aria-label="Cari pendaftaran sidang" class="h-9 w-56 rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
                </div>
                <select x-model="status" @change="filter()" aria-label="Filter status" class="h-9 rounded-lg border border-input bg-white px-2.5 text-sm text-muted-foreground outline-none">
                    <option value="PENDING">Pending</option>
                    <option value="APPROVED">Approved</option>
                    <option value="REJECTED">Rejected</option>
                    <option value="all">Semua status</option>
                </select>
                <select x-model="periodId" @change="filter()" aria-label="Filter periode" class="h-9 rounded-lg border border-input bg-white px-2.5 text-sm text-muted-foreground outline-none">
                    <option value="">Semua periode</option>
                    <template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name"></option></template>
                </select>
                <button type="button" @click="reset()" class="inline-flex h-9 items-center rounded-lg border border-input bg-white px-3 text-sm text-muted-foreground shadow-xs hover:bg-accent">Reset</button>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[860px] text-left text-sm">
                <thead>
                    <tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                        <th class="px-4 py-3 font-medium">Mahasiswa</th>
                        <th class="px-4 py-3 font-medium">NIM</th>
                        <th class="px-4 py-3 font-medium">Grup</th>
                        <th class="px-4 py-3 font-medium">Periode</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Diminta</th>
                        <th class="px-5 py-3 text-right font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="item in items" :key="item.id">
                        <tr class="bg-white hover:bg-muted/30">
                            <td class="px-4 py-4 font-medium text-foreground" x-text="studentName(item)"></td>
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-foreground" x-text="studentNim(item)"></td>
                            <td class="whitespace-nowrap px-4 py-4 text-foreground" x-text="groupName(item)"></td>
                            <td class="whitespace-nowrap px-4 py-4 text-foreground" x-text="item.period?.name || ('Period #'+item.period_id)"></td>
                            <td class="whitespace-nowrap px-4 py-4"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusClass(item.status)" x-text="item.status"></span><p x-show="item.status==='REJECTED' && item.rejection_reason" class="mt-1 max-w-64 whitespace-normal text-xs text-muted-foreground" x-text="item.rejection_reason"></p></td>
                            <td class="whitespace-nowrap px-4 py-4 text-foreground" x-text="shortDate(item.created_at)"></td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <button type="button" x-show="canReview(item)" @click="openRowMenu($event,item)" aria-label="Aksi" aria-haspopup="menu" class="rounded-md p-1.5 text-[#666D80] hover:bg-muted"><x-capstone::icon name="Ellipsis" class="size-5" /></button>
                                <span x-show="!canReview(item)" class="text-xs text-muted-foreground">Reviewed</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <p x-show="!items.length" class="p-8 text-center text-sm text-muted-foreground">Belum ada pendaftaran sidang TA.</p>
        </div>

        <div x-show="items.length" x-cloak class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-0 overflow-hidden rounded-lg border border-input bg-white text-[13px]">
                    <span class="px-2.5 text-muted-foreground">Per page</span>
                    <select x-model.number="pageSize" @change="page=1;load()" aria-label="Baris per halaman" class="border-l border-input bg-transparent px-2 py-1.5 font-medium outline-none"><option>10</option><option>25</option><option>50</option></select>
                </label>
                <span class="text-foreground"><span x-text="'Showing '+showingFrom+' to '+showingTo+' of, '+(pagination.total ?? 0)+' results'"></span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="gotoPage(page-1)" :disabled="page<=1" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                <template x-for="(n,i) in pageNumbers" :key="n">
                    <span class="flex items-center gap-1.5">
                        <span x-show="i>0 && n-pageNumbers[i-1]>1" class="px-1 text-muted-foreground">...</span>
                        <button type="button" @click="gotoPage(n)" x-text="n" class="inline-flex size-8 items-center justify-center rounded-lg border text-sm" :class="page===n?'border-[#1E2A5A] bg-[#1E2A5A] font-semibold text-white':'border-input bg-white text-foreground hover:bg-accent'"></button>
                    </span>
                </template>
                <button type="button" @click="gotoPage(page+1)" :disabled="page>=(pagination.last_page||1)" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
            </div>
        </div>
    </div>

    <div x-show="openMenuItem" @click.away="closeMenu()" x-cloak :style="menuStyle" style="position: fixed; z-index: 50;" role="menu" class="w-48 rounded-lg border border-border bg-white p-1.5 text-left shadow-md">
        <button type="button" @click="review(openMenuItem,'approve');closeMenu()" ::disabled="saving" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-foreground hover:bg-accent">Approve</button>
        <button type="button" @click="review(openMenuItem,'reject');closeMenu()" ::disabled="saving" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-red-600 hover:bg-red-50">Reject</button>
    </div>
    <x-capstone::dialog id="sidang-review"><h2 class="text-lg font-semibold" x-text="decision==='approve'?'Approve Sidang TA Registration':'Reject Sidang TA Registration'"></h2><p class="my-4 text-sm text-muted-foreground" x-text="selected ? studentName(selected)+' · '+studentNim(selected)+' · '+groupName(selected) : ''"></p><form @submit.prevent="submit()" class="space-y-4">
        <p x-show="decision==='approve'" class="text-sm">Approve this request? The student will be able to upload the required TA documents.</p>
        <div x-show="decision==='reject'" class="space-y-2"><label for="sidang-rejection-reason" class="text-sm font-medium">Rejection Reason</label><textarea id="sidang-rejection-reason" x-model="reason" :required="decision==='reject'" maxlength="1000" class="min-h-24 w-full rounded-md border bg-background p-3 text-sm" placeholder="Explain why this request is being rejected..."></textarea></div>
        <p class="text-sm text-destructive" role="alert" x-text="errors.root"></p><div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Cancel</x-capstone::button><x-capstone::button type="submit" ::disabled="saving || (decision==='reject' && !reason.trim())"><span x-text="saving?'Saving...':decision==='approve'?'Approve':'Reject'"></span></x-capstone::button></div>
    </form></x-capstone::dialog>
    </div>
</div>
@endsection
