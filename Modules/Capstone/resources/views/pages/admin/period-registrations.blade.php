@extends('capstone::layouts.app')
@section('title','Approval')
@section('content')
<div x-data="adminPeriodRegistrations" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Approval</h1>
            <p class="text-sm text-slate-500">Review student requests to join an academic period. Approved students can create or join groups.</p>
        </div>
        <x-capstone::button variant="outline" @click="load()" ::disabled="loading"><x-capstone::icon name="RefreshCw" size="15" />Refresh</x-capstone::button>
    </div>

    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error" x-cloak class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center gap-2.5 border-b border-slate-100 p-3">
            <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-[13px] text-slate-500">
                <x-capstone::icon name="Search" size="15" />
                <input x-model.debounce.300ms="search" @input="filter()" type="search" placeholder="Cari nama / NIM..." class="w-44 bg-transparent outline-none placeholder:text-slate-400" aria-label="Cari join request">
            </label>
            <select x-model="status" @change="filter()" class="rounded-lg border border-slate-200 bg-transparent px-2.5 py-1.5 text-[13px] text-slate-600 outline-none" aria-label="Filter status">
                <option value="PENDING">Pending</option>
                <option value="APPROVED">Approved</option>
                <option value="REJECTED">Rejected</option>
                <option value="all">Semua status</option>
            </select>
            <select x-model="periodId" @change="filter()" class="rounded-lg border border-slate-200 bg-transparent px-2.5 py-1.5 text-[13px] text-slate-600 outline-none" aria-label="Filter periode">
                <option value="">Semua periode</option>
                <template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name"></option></template>
            </select>
            <button type="button" @click="reset()" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500 hover:bg-slate-50">Reset</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-[13px]">
                <thead class="border-b border-slate-100 bg-slate-50/60 text-xs text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 font-medium">Mahasiswa</th>
                        <th class="px-4 py-2.5 font-medium">NIM</th>
                        <th class="px-4 py-2.5 font-medium">Periode</th>
                        <th class="px-4 py-2.5 font-medium">Status</th>
                        <th class="px-4 py-2.5 font-medium">Diminta</th>
                        <th class="px-4 py-2.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="item in items" :key="item.id">
                        <tr class="align-top hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-medium text-slate-800" x-text="studentName(item)"></td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600" x-text="studentNim(item)"></td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700" x-text="item.period?.name || ('Period #'+item.period_id)"></td>
                            <td class="whitespace-nowrap px-4 py-3"><span class="rounded-full px-2 py-0.5 text-[11px] font-semibold" :class="statusClass(item.status)" x-text="item.status"></span><p x-show="item.status==='REJECTED' && item.rejection_reason" class="mt-1 max-w-64 whitespace-normal text-xs text-slate-500" x-text="item.rejection_reason"></p></td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500" x-text="shortDate(item.created_at)"></td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <template x-if="canReview(item)">
                                    <div class="inline-flex gap-2">
                                        <x-capstone::button variant="outline" size="sm" @click="review(item,'reject')" ::disabled="saving">Reject</x-capstone::button>
                                        <x-capstone::button size="sm" @click="review(item,'approve')" ::disabled="saving">Approve</x-capstone::button>
                                    </div>
                                </template>
                                <span x-show="!canReview(item)" class="text-xs text-slate-400">Reviewed</span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!items.length"><td colspan="6" class="px-3 py-10 text-center text-sm text-slate-400">Belum ada join request.</td></tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-3 text-[13px]">
            <div class="flex items-center gap-2 text-slate-500">
                <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2 py-1">Per page
                    <select x-model.number="pageSize" @change="page=1;load()" class="bg-transparent font-semibold text-slate-700 outline-none" aria-label="Baris per halaman"><option>10</option><option>25</option><option>50</option></select>
                </label>
                <span x-text="'Total '+ (pagination.total ?? 0) +' request'"></span>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" @click="gotoPage(page-1)" :disabled="page<=1" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman sebelumnya"><x-capstone::icon name="ChevronLeft" size="15" /></button>
                <span class="px-2 text-xs font-semibold text-slate-500" x-text="page+' / '+(pagination.last_page||1)"></span>
                <button type="button" @click="gotoPage(page+1)" :disabled="page>=(pagination.last_page||1)" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman berikutnya"><x-capstone::icon name="ChevronRight" size="15" /></button>
            </div>
        </div>
    </div>

    <x-capstone::dialog id="join-review"><h2 class="text-lg font-semibold" x-text="decision==='approve'?'Approve Join Request':'Reject Join Request'"></h2><p class="my-4 text-sm text-muted-foreground" x-text="selected ? studentName(selected)+' · '+studentNim(selected)+' · '+(selected.period?.name || '') : ''"></p><form @submit.prevent="submit()" class="space-y-4">
        <p x-show="decision==='approve'" class="text-sm">Approve this request? The student will be able to create or join a group in this period.</p>
        <div x-show="decision==='reject'" class="space-y-2"><label for="rejection-reason" class="text-sm font-medium">Rejection Reason</label><textarea id="rejection-reason" x-model="reason" :required="decision==='reject'" maxlength="1000" class="min-h-24 w-full rounded-md border bg-background p-3 text-sm" placeholder="Explain why this request is being rejected..."></textarea></div>
        <p class="text-sm text-destructive" role="alert" x-text="errors.root"></p><div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Cancel</x-capstone::button><x-capstone::button type="submit" ::disabled="saving || (decision==='reject' && !reason.trim())"><span x-text="saving?'Saving...':decision==='approve'?'Approve':'Reject'"></span></x-capstone::button></div>
    </form></x-capstone::dialog>
</div>
@endsection
