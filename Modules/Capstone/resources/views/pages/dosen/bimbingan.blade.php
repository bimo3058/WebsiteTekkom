@extends('capstone::layouts.app')
@section('title','Bimbingan')
@section('content')
<div x-data="@yield('document-controller','lecturerDocuments')" class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center"><div><h1 class="text-3xl font-bold tracking-tight">@yield('document-heading','Bimbingan')</h1><p class="text-muted-foreground">Review documents and provide feedback for your supervised groups.</p></div><div @change="selectedGroup='all';page=1">@include('capstone::pages.dosen.shared.period')</div></div>
    <div class="flex flex-wrap gap-3"><x-capstone::input type="search" x-model.debounce.200ms="search" @input="page=1" placeholder="Search by student, title, or phase..." aria-label="Search documents" class="min-w-48 flex-1" />
        <select x-model="selectedGroup" @change="page=1" aria-label="Filter by group" class="h-9 max-w-full rounded-md border bg-background px-3 text-sm"><option value="all">All Groups</option><template x-for="group in availableGroups" :key="group.id"><option :value="String(group.id)" x-text="group.code||'Group '+group.id"></option></template></select>
        <select x-model="phase" @change="page=1" aria-label="Filter by phase" class="h-9 rounded-md border bg-background px-3 text-sm"><option value="all">All Phases</option><template x-for="item in phases" :key="item"><option :value="item" x-text="item"></option></template></select>
        <select x-model="status" @change="page=1" aria-label="Filter by status" class="h-9 rounded-md border bg-background px-3 text-sm"><option value="all">All Statuses</option><option>SUBMITTED</option><option>APPROVED</option><option>REJECTED</option></select>
    </div>
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-4">
        <p x-show="!filtered.length" class="rounded-xl border border-dashed p-12 text-center text-muted-foreground">No documents found for the selected filters.</p>
        <template x-for="entry in grouped" :key="entry.id"><section class="overflow-hidden rounded-xl border bg-card shadow-sm"><div class="border-b bg-muted/30 p-4"><h2 class="font-semibold" x-text="entry.group?.title?.title||'Group '+entry.id"></h2><p class="mt-1 text-xs text-muted-foreground" x-text="entry.group?.code||'Group '+entry.id"></p></div>
            <div class="overflow-x-auto"><table class="w-full min-w-[760px] text-left text-sm"><thead class="border-b"><tr>@foreach(['Document','Student','Uploaded','Status','Actions'] as $label)<th class="p-4">{{ $label }}</th>@endforeach</tr></thead><tbody><template x-for="doc in entry.documents" :key="doc.id"><tr class="border-b last:border-0">
                <td class="max-w-xs p-4"><p class="font-medium" x-text="doc.document_type||doc.phase"></p><p class="mt-1 text-xs text-muted-foreground" x-text="doc.phase+' · Version '+doc.version"></p><details x-show="doc.feedback" class="mt-2"><summary class="cursor-pointer text-xs text-primary">Review Feedback</summary><p class="mt-2 whitespace-pre-wrap text-sm text-muted-foreground" x-text="doc.feedback"></p></details></td>
                <td class="p-4"><p x-text="doc.student?.name||'-'"></p><p class="mt-1 text-xs text-muted-foreground" x-text="doc.student?.nim"></p></td><td class="p-4 text-xs" x-text="date(doc.created_at,true)"></td><td class="p-4"><span class="rounded-full px-2 py-1 text-xs" :class="statusColor(doc.status)" x-text="doc.status"></span></td><td class="p-4"><div class="flex gap-2"><x-capstone::button variant="outline" size="sm" @click="downloadDocument(doc)" ::disabled="downloading!==null">Download</x-capstone::button><x-capstone::button size="sm" @click="review(doc)" ::disabled="saving">Review</x-capstone::button></div></td>
            </tr></template></tbody></table></div>
        </section></template>
        @include('capstone::pages.dosen.shared.pagination')
    </div>
    <x-capstone::dialog id="document-review" title="Review Document"><p class="mt-3 text-sm text-muted-foreground" x-text="selected?(selected.document_type||selected.phase)+' · Version '+selected.version:''"></p><form @submit.prevent="submit()" class="mt-4 space-y-4">
        <label class="grid gap-2 text-sm font-medium">Review Status<select x-model="reviewStatus" class="h-9 rounded-md border bg-background px-3"><option value="APPROVED">Approved</option><option value="REJECTED">Rejected / Needs Revision</option></select></label>
        <label class="grid gap-2 text-sm font-medium">Feedback<textarea x-model="feedback" maxlength="500" rows="5" class="rounded-md border bg-background p-3 font-normal" placeholder="Enter your feedback here..."></textarea></label><p class="text-right text-xs text-muted-foreground" x-text="feedback.length+' / 500'"></p><p class="text-sm text-destructive" role="alert" x-text="errors.root"></p>
        <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="close('document-review')">Cancel</x-capstone::button><x-capstone::button type="submit" ::disabled="saving"><span x-text="saving?'Saving...':'Submit Review'"></span></x-capstone::button></div>
    </form></x-capstone::dialog>
</div>
@endsection
