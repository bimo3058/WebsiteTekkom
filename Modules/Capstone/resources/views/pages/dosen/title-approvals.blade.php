@extends('capstone::layouts.app')
@section('title','Persetujuan Judul')
@section('content')
<div x-data="lecturerApprovals" class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center"><div><h1 class="text-3xl font-bold tracking-tight">Persetujuan Judul</h1><p class="text-muted-foreground">Tinjau proposal judul yang diajukan mahasiswa.</p></div>@include('capstone::pages.dosen.shared.period')</div>
    <input type="search" x-model.debounce.200ms="search" @input="page=1" placeholder="Cari judul, mahasiswa, atau kelompok..." aria-label="Cari proposal" class="file:text-on-surface placeholder:text-on-surface/50 selection:bg-primary selection:text-on-primary border-outline h-9 w-full min-w-0 rounded-radius border bg-surface px-3 py-1 text-sm text-on-surface shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/20 max-w-sm" />
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-4">
        <div class="rounded-lg border border-border bg-accent p-4 text-sm text-foreground">Persetujuan pembimbing memvalidasi proposal. Penetapan judul dan kelompok dilanjutkan melalui finalisasi admin.</div>
        <p x-show="!filtered.length" class="rounded-xl border border-dashed p-12 text-center text-muted-foreground">Tidak ada proposal tertunda. Proposal mahasiswa akan muncul di sini.</p>
        <template x-for="proposal in visible" :key="proposal.id"><article class="rounded-xl border bg-card p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="text-lg font-semibold" x-text="proposal.title"></h2><p class="mt-1 text-sm text-muted-foreground" x-text="'Kelompok '+(proposal.proposed_by_group?.code||proposal.proposed_by_group_id||'-')+' · '+date(proposal.created_at)"></p></div><span class="rounded-full px-2 py-1 text-xs" :class="statusColor(proposal.supervisor_approval_status)" x-text="proposal.supervisor_approval_status"></span></div>
            <p class="mt-4 text-sm" x-text="members(proposal.proposed_by_group)"></p>
            <div class="my-4 flex flex-wrap gap-1"><template x-for="spec in proposal.specializations||[]" :key="spec"><span class="rounded border px-2 py-0.5 text-xs" x-text="spec"></span></template></div>
            <p class="whitespace-pre-wrap text-sm text-muted-foreground" x-text="proposal.description"></p>
            <details class="mt-4 rounded-lg bg-muted/30 p-3"><summary class="cursor-pointer text-sm font-medium">Detail Proposal</summary><div class="mt-3 grid gap-4 text-sm sm:grid-cols-2"><div><h3 class="font-medium">Rumusan Masalah</h3><p class="mt-1 whitespace-pre-wrap text-muted-foreground" x-text="proposal.problem_statement||'-'"></p></div><div><h3 class="font-medium">Ruang Lingkup</h3><p class="mt-1 whitespace-pre-wrap text-muted-foreground" x-text="proposal.scope||'-'"></p></div></div></details>
            <div class="mt-4 flex justify-end gap-2"><x-btn style="outline" @click="review(proposal,'reject')" x-bind:disabled="saving || !canReview(proposal)">Tolak</x-btn><x-btn @click="review(proposal,'approve')" x-bind:disabled="saving || !canReview(proposal)">Setujui Proposal</x-btn></div>
        </article></template>
        @include('capstone::pages.dosen.shared.pagination')
    </div>
    <x-capstone::dialog id="proposal-review"><h2 class="text-lg font-semibold" x-text="decision==='approve'?'Setujui Proposal':'Tolak Proposal'"></h2><p class="my-4 text-sm text-muted-foreground" x-text="selected?.title"></p><form @submit.prevent="submit()" class="space-y-4">
        <p x-show="decision==='approve'" class="text-sm">Setujui proposal ini untuk finalisasi admin?</p>
        <div x-show="decision==='reject'" class="space-y-2"><label for="rejection-reason" class="text-sm font-medium">Alasan Penolakan</label><textarea id="rejection-reason" x-model="reason" :required="decision==='reject'" maxlength="1000" class="min-h-24 w-full rounded-md border bg-background p-3 text-sm" placeholder="Jelaskan mengapa proposal ini ditolak..."></textarea></div>
        <p class="text-sm text-destructive" role="alert" x-text="errors.root"></p><div class="flex justify-end gap-2"><x-btn style="outline" @click="close('proposal-review')">Batal</x-btn><x-btn type="submit" x-bind:disabled="saving || (decision==='reject' && !reason.trim())"><span x-text="saving?'Menyimpan...':decision==='approve'?'Setujui':'Tolak'"></span></x-btn></div>
    </form></x-capstone::dialog>
</div>
@endsection
