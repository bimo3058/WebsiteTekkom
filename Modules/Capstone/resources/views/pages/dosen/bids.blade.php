@extends('capstone::layouts.app')
@section('title','Bid Review')
@section('content')
<div x-data="lecturerBids" class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center"><div><h1 class="text-3xl font-bold tracking-tight">Bid Review</h1><p class="text-muted-foreground">Review bids on your titles. Your recommendation is advisory for admin.</p></div>@include('capstone::pages.dosen.shared.period')</div>
    <x-capstone::input type="search" x-model.debounce.200ms="search" placeholder="Search titles, students, or groups..." aria-label="Search bids" class="max-w-sm" />
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-6">
        <p x-show="!byTitle.length" class="rounded-xl border border-dashed p-12 text-center text-muted-foreground">No Bids Yet. Bids on your titles will appear here.</p>
        <template x-for="entry in byTitle" :key="entry.title.id"><article class="rounded-xl border bg-card p-6 shadow-sm">
            <h2 class="font-semibold" x-text="entry.title.title"></h2><p class="mt-1 text-sm text-muted-foreground" x-text="entry.bids.length+' bid(s)'"></p>
            <div class="mt-5 space-y-3"><template x-for="bid in entry.bids" :key="bid.id"><div class="rounded-lg border p-4" :class="bid.lecturer_recommendation==='ACCEPT'?'border-green-300 bg-green-50':'bg-muted/30'">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"><div class="flex items-center gap-4"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary" x-text="'P'+bid.priority"></span><div><p class="text-sm font-medium" x-text="members(bid.group)"></p><p class="mt-1 text-xs text-muted-foreground" x-text="bid.group?.code||'Group '+bid.group_id"></p></div></div>
                <div class="flex flex-wrap items-center gap-2"><span x-show="bid.lecturer_recommendation" class="rounded-full px-2 py-1 text-xs" :class="statusColor(bid.lecturer_recommendation)" x-text="bid.lecturer_recommendation"></span>
                    <x-capstone::button variant="outline" size="sm" @click="recommend(bid,'ACCEPT')" ::disabled="saving || !canRecommend(bid,'ACCEPT')"><x-capstone::icon name="ThumbsUp" class="mr-1 h-3 w-3" />Accept</x-capstone::button>
                    <x-capstone::button variant="outline" size="sm" @click="recommend(bid,'REJECT')" ::disabled="saving || !canRecommend(bid,'REJECT')"><span x-text="bid.lecturer_recommendation==='ACCEPT'?'Cancel Acceptance':'Reject'"></span></x-capstone::button>
                </div></div><p x-show="locked(bid)" class="mt-3 text-xs text-amber-700">Bidding is locked for this period. Recommendations cannot be changed.</p>
            </div></template></div>
        </article></template>
    </div>
</div>
@endsection
