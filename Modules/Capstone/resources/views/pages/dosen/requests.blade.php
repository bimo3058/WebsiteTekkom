@extends('capstone::layouts.app')
@section('title','Supervision Requests')
@section('content')
<div x-data="lecturerRequests" class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center"><div><h1 class="text-3xl font-bold tracking-tight">Supervision Requests</h1><p class="text-muted-foreground">View groups requesting your project titles.</p></div>@include('capstone::pages.dosen.shared.period')</div>
    <div class="flex flex-wrap gap-3"><x-capstone::input type="search" x-model.debounce.200ms="search" @input="page=1" placeholder="Search by title or student..." aria-label="Search supervision requests" class="min-w-48 flex-1" /><x-capstone::button href="/dosen/bids" variant="outline">Bid Review</x-capstone::button><x-capstone::button href="/dosen/title-approvals">Title Approvals</x-capstone::button></div>
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-4"><p class="rounded-lg border bg-muted/30 p-4 text-sm">Tinjau proposal atau berikan rekomendasi bid melalui menu di atas. Penetapan kelompok dilakukan oleh admin.</p><div class="overflow-x-auto rounded-xl border"><table class="w-full min-w-[600px] text-left text-sm"><thead class="border-b bg-muted/40"><tr>@foreach(['Title','Members','Status','Date'] as $label)<th class="p-4">{{ $label }}</th>@endforeach</tr></thead><tbody><template x-for="group in visible" :key="group.id"><tr class="border-b last:border-0"><td class="p-4 font-medium" x-text="group.title?.title||'-'"></td><td class="p-4" x-text="members(group)"></td><td class="p-4"><span class="rounded-full bg-muted px-2 py-1 text-xs" x-text="group.status"></span></td><td class="p-4" x-text="date(group.created_at)"></td></tr></template></tbody></table><p x-show="!filtered.length" class="p-12 text-center text-muted-foreground">No pending requests.</p></div>@include('capstone::pages.dosen.shared.pagination')</div>
</div>
@endsection
