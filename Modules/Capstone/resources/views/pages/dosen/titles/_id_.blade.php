@extends('capstone::layouts.app')
@section('title','Title Details')
@section('content')
<div x-data="lecturerTitleDetail" class="space-y-6">
    <a href="{{ url('/capstone/dosen/titles') }}" class="inline-flex items-center gap-2 text-sm hover:underline"><x-capstone::icon name="ArrowLeft" class="h-4 w-4" />Back to My Titles</a>
    @include('capstone::partials.loading')
    <template x-if="title && !loading && !error"><div class="space-y-6">
        <div><div class="mb-3 flex flex-wrap gap-2"><span class="rounded-full px-3 py-1 text-xs" :class="statusColor(title.status)" x-text="title.status"></span><template x-for="spec in title.specializations||[]" :key="spec"><span class="rounded-full border px-3 py-1 text-xs" x-text="spec"></span></template></div><h1 class="text-3xl font-bold tracking-tight" x-text="title.title"></h1><p class="mt-2 text-muted-foreground" x-text="title.lecturer?.name"></p></div>
        <div class="grid gap-6 lg:grid-cols-3"><div class="space-y-6 lg:col-span-2">
            @foreach(['description'=>'Description','problem_statement'=>'Problem Statement','scope'=>'Scope'] as $key=>$label)<section class="rounded-xl border bg-card p-6"><h2 class="mb-3 font-semibold">{{ $label }}</h2><p class="whitespace-pre-wrap text-sm text-muted-foreground" x-text="title.{{ $key }}||'-'"></p></section>@endforeach
        </div><div class="space-y-6"><section class="rounded-xl border bg-card p-6"><h2 class="font-semibold">Group Capacity</h2><p class="my-3 text-3xl font-bold" x-text="(title.groups?.length||0)+' / '+title.quota"></p><p class="text-sm text-muted-foreground">Assigned groups / quota</p></section>
        <section class="rounded-xl border bg-card p-6"><h2 class="mb-4 font-semibold">Assigned Groups</h2><template x-for="group in title.groups||[]" :key="group.id"><div class="border-t py-3"><p class="font-medium" x-text="group.code||'Group '+group.id"></p><p class="mt-1 text-xs" x-text="group.status"></p><p class="mt-2 text-sm text-muted-foreground" x-text="members(group)"></p></div></template><p x-show="!title.groups?.length" class="text-sm text-muted-foreground">No groups assigned yet.</p></section></div></div>
    </div></template>
</div>
@endsection
