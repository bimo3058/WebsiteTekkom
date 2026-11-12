@extends('capstone::layouts.app')
@section('title','Tinjau Bid')
@section('content')
<div x-data="lecturerBids" class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center"><div><h1 class="text-3xl font-bold tracking-tight">Tinjau Bid</h1><p class="text-muted-foreground">Tinjau bid pada judul Anda. Rekomendasi Anda bersifat saran untuk admin.</p></div>@include('capstone::pages.dosen.shared.period')</div>
    <input type="search" x-model.debounce.200ms="search" placeholder="Cari judul, mahasiswa, atau kelompok..." aria-label="Cari bid" class="file:text-on-surface placeholder:text-on-surface/50 selection:bg-primary selection:text-on-primary border-outline h-9 w-full min-w-0 rounded-radius border bg-surface px-3 py-1 text-sm text-on-surface shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/20 max-w-sm" />
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-6">
        <p x-show="!byTitle.length" class="rounded-xl border border-dashed p-12 text-center text-muted-foreground">Belum Ada Bid. Bid pada judul Anda akan muncul di sini.</p>
        <template x-for="entry in byTitle" :key="entry.title.id"><article class="rounded-xl border bg-card p-6 shadow-sm">
            <h2 class="font-semibold" x-text="entry.title.title"></h2><p class="mt-1 text-sm text-muted-foreground" x-text="entry.bids.length+' bid'"></p>
            <div class="mt-5 space-y-3"><template x-for="bid in entry.bids" :key="bid.id"><div class="rounded-lg border p-4" :class="bid.lecturer_recommendation==='ACCEPT'?'border-green-300 bg-green-50':'bg-muted/30'">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"><div class="flex items-center gap-4"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary" x-text="'P'+bid.priority"></span><div><p class="text-sm font-medium" x-text="members(bid.group)"></p><p class="mt-1 text-xs text-muted-foreground" x-text="bid.group?.code||'Kelompok '+bid.group_id"></p><p class="mt-1 flex flex-wrap items-center gap-1.5"><x-badge style="secondary" class="text-xs" x-text="periodName(bid)"></x-badge><x-badge style="secondary" class="bg-primary/10 text-xs text-primary" x-text="priorityLabel(bid)"></x-badge></p></div></div>
                <div class="flex flex-wrap items-center gap-2"><span x-show="bid.lecturer_recommendation" class="rounded-full px-2 py-1 text-xs" :class="statusColor(bid.lecturer_recommendation)" x-text="bid.lecturer_recommendation"></span>
                    <x-btn style="outline" size="sm" @click="recommend(bid,'ACCEPT')" x-bind:disabled="saving || !canRecommend(bid,'ACCEPT')"><x-capstone::icon name="ThumbsUp" class="mr-1 h-3 w-3" />Terima</x-btn>
                    <x-btn style="outline" size="sm" x-show="!bid.lecturer_recommendation" @click="recommend(bid,'REJECT')" x-bind:disabled="saving || !canRecommend(bid,'REJECT')">Tolak</x-btn>
                    <x-btn style="outline" size="sm" x-show="bid.lecturer_recommendation" @click="mutate('/dosen/bids/'+bid.id+'/recommend',{recommendation:'CANCEL'})" x-bind:disabled="saving || locked(bid)"><span x-text="bid.lecturer_recommendation==='ACCEPT'?'Batalkan Penerimaan':'Batalkan penolakan'"></span></x-btn>
                </div></div><p x-show="locked(bid)" class="mt-3 text-xs text-amber-700">Bid dikunci untuk periode ini. Rekomendasi tidak dapat diubah.</p>
            </div></template></div>
        </article></template>
    </div>
</div>
@endsection
