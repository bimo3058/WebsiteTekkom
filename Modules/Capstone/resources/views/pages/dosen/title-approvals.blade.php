@extends('capstone::layouts.app')
@section('title','Title Approvals')
@section('content')
<div x-data="lecturerApprovals" class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center"><div><h1 class="text-3xl font-bold tracking-tight">Title Approvals</h1><p class="text-muted-foreground">Review title proposals submitted by students.</p></div>@include('capstone::pages.dosen.shared.period')</div>
    <x-capstone::input type="search" x-model.debounce.200ms="search" @input="page=1" placeholder="Search titles, students, or groups..." aria-label="Search proposals" class="max-w-sm" />
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-4">
        <div class="rounded-lg border border-border bg-accent p-4 text-sm text-foreground">Persetujuan pembimbing memvalidasi proposal. Penetapan judul dan kelompok dilanjutkan melalui finalisasi admin.</div>
        <p x-show="!filtered.length" class="rounded-xl border border-dashed p-12 text-center text-muted-foreground">No pending proposals. Student proposals will appear here.</p>
        <template x-for="proposal in visible" :key="proposal.id"><article class="rounded-xl border bg-card p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="text-lg font-semibold" x-text="proposal.title"></h2><p class="mt-1 text-sm text-muted-foreground" x-text="'Group '+(proposal.proposed_by_group?.code||proposal.proposed_by_group_id||'-')+' · '+date(proposal.created_at)"></p></div><span class="rounded-full px-2 py-1 text-xs" :class="statusColor(proposal.supervisor_approval_status)" x-text="proposal.supervisor_approval_status"></span></div>
            <p class="mt-4 text-sm" x-text="members(proposal.proposed_by_group)"></p>
            <div class="my-4 flex flex-wrap gap-1"><template x-for="spec in proposal.specializations||[]" :key="spec"><span class="rounded border px-2 py-0.5 text-xs" x-text="spec"></span></template></div>
            <p class="whitespace-pre-wrap text-sm text-muted-foreground" x-text="proposal.description"></p>
            <details class="mt-4 rounded-lg bg-muted/30 p-3"><summary class="cursor-pointer text-sm font-medium">Proposal Details</summary><div class="mt-3 grid gap-4 text-sm sm:grid-cols-2"><div><h3 class="font-medium">Problem Statement</h3><p class="mt-1 whitespace-pre-wrap text-muted-foreground" x-text="proposal.problem_statement||'-'"></p></div><div><h3 class="font-medium">Scope</h3><p class="mt-1 whitespace-pre-wrap text-muted-foreground" x-text="proposal.scope||'-'"></p></div></div></details>
            <div class="mt-4 flex justify-end gap-2"><x-capstone::button variant="outline" @click="review(proposal,'reject')" ::disabled="saving || !canReview(proposal)">Reject</x-capstone::button><x-capstone::button @click="review(proposal,'approve')" ::disabled="saving || !canReview(proposal)">Approve Proposal</x-capstone::button></div>
        </article></template>
        @include('capstone::pages.dosen.shared.pagination')
    </div>
    <x-capstone::dialog id="proposal-review"><h2 class="text-lg font-semibold" x-text="decision==='approve'?'Approve Proposal':'Reject Proposal'"></h2><p class="my-4 text-sm text-muted-foreground" x-text="selected?.title"></p><form @submit.prevent="submit()" class="space-y-4">
        <p x-show="decision==='approve'" class="text-sm">Approve this proposal for admin finalization?</p>
        <div x-show="decision==='reject'" class="space-y-2"><label for="rejection-reason" class="text-sm font-medium">Rejection Reason</label><textarea id="rejection-reason" x-model="reason" :required="decision==='reject'" maxlength="1000" class="min-h-24 w-full rounded-md border bg-background p-3 text-sm" placeholder="Explain why this proposal is being rejected..."></textarea></div>
        <p class="text-sm text-destructive" role="alert" x-text="errors.root"></p><div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="close('proposal-review')">Cancel</x-capstone::button><x-capstone::button type="submit" ::disabled="saving || (decision==='reject' && !reason.trim())"><span x-text="saving?'Saving...':decision==='approve'?'Approve':'Reject'"></span></x-capstone::button></div>
    </form></x-capstone::dialog>
</div>
@endsection
