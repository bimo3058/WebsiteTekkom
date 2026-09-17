@extends('capstone::layouts.app')
@section('title','My Group')
@section('content')
<div x-data="studentGroup" class="space-y-6">
@include('capstone::partials.loading')
<div x-show="!loading && !error" x-cloak class="space-y-6">
    <div><h1 class="text-3xl font-bold tracking-tight">My Group</h1><p class="text-muted-foreground" x-text="group ? 'Kelola kelompok dan anggota capstone Anda.' : 'Create a group to start your capstone journey.'"></p></div>
    <x-capstone::alert title="Title Approval Withdrawn" variant="destructive" x-show="withdrawal"><span x-text="withdrawal?.message"></span> <x-capstone::feature-link href="/mahasiswa/titles" class="underline">Browse Titles</x-capstone::feature-link></x-capstone::alert>
    @include('capstone::pages.mahasiswa.group.empty')
    <template x-if="group"><x-capstone::card>
        <div class="px-6 flex flex-wrap justify-between items-start gap-4"><div><h2 class="text-2xl font-semibold">Group Details</h2><p class="text-sm text-muted-foreground">Status: <x-capstone::badge variant="secondary" x-text="group.status_label || group.status?.replaceAll('_',' ')" /></p></div><div class="flex gap-2"><x-capstone::button variant="destructive" size="sm" x-show="actions.can_delete_group" @click="confirm('delete')" ::disabled="saving"><x-capstone::icon name="Trash2" />Delete Group</x-capstone::button><x-capstone::button variant="outline" size="sm" x-show="actions.can_leave_group" @click="confirm('leave')" ::disabled="saving"><x-capstone::icon name="LogOut" />Leave Group</x-capstone::button></div></div>
        <div class="px-6 space-y-6">
            <div x-show="group.title" class="p-4 bg-muted rounded-lg"><div class="flex items-start gap-3"><x-capstone::icon name="BookOpen" class="h-5 w-5 mt-0.5 text-primary" /><div><h3 class="font-medium" x-text="group.title?.title"></h3><p class="text-sm text-muted-foreground">Lecturer: <span x-text="person(group.title?.lecturer)"></span></p></div></div></div>
            <div x-show="!group.title" class="p-4 bg-muted/50 rounded-lg border border-dashed flex items-center gap-3 text-muted-foreground"><x-capstone::icon name="PenLine" /><span>No title assigned yet. Browse the <x-capstone::feature-link href="/mahasiswa/titles" class="underline text-primary">Titles Marketplace</x-capstone::feature-link> to bid on a title.</span></div>
            @include('capstone::pages.mahasiswa.group.members')
            @include('capstone::pages.mahasiswa.group.join-requests')
        </div>
        <div class="px-6 flex flex-col items-start gap-4 border-t pt-6"><x-capstone::alert x-show="members.length < minMembers && !group.title_id" title="Need More Members">You need at least <span x-text="minMembers"></span> members to bid on a title. Current: <span x-text="members.length+'/'+minMembers"></span></x-capstone::alert><x-capstone::button x-show="actions.can_mark_ready_for_finalization" class="w-full" size="lg" @click="confirm('ready')">Mark Ready for Finalization</x-capstone::button><x-capstone::button x-show="actions.can_cancel_ready_for_finalization" variant="outline" class="w-full" size="lg" @click="confirm('cancel')">Cancel Ready for Finalization</x-capstone::button></div>
    </x-capstone::card></template>
</div>
@include('capstone::pages.mahasiswa.group.dialogs')
</div>
@endsection
