@extends('capstone::layouts.app')
@section('title','My Titles')
@section('content')
<div x-data="lecturerTitles" class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div><h1 class="text-3xl font-bold tracking-tight">Manage Titles</h1><p class="text-muted-foreground">Create and manage your final project titles.</p></div>
        <div class="flex flex-wrap items-center gap-3">@include('capstone::pages.dosen.shared.period')<x-capstone::button @click="edit()"><x-capstone::icon name="Plus" class="mr-2 h-4 w-4" />Add Title</x-capstone::button></div>
    </div>
    <div class="flex flex-wrap items-center gap-4">
        <x-capstone::input type="search" x-model.debounce.200ms="search" @input="page=1" placeholder="Search titles..." aria-label="Search titles" class="min-w-48 flex-1" />
        <div class="flex flex-wrap gap-3"><template x-for="spec in specializations" :key="spec"><label class="flex items-center gap-1.5 text-xs"><input type="checkbox" x-model="filterSpecs" :value="spec" @change="page=1" class="accent-primary"><span x-text="spec"></span></label></template></div>
    </div>
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-4">
        <div class="overflow-x-auto rounded-lg border"><table class="w-full min-w-[680px] text-left text-sm">
            <thead class="border-b bg-muted/40"><tr>
                @foreach(['title'=>'Title','specializations'=>'Specializations','quota'=>'Quota','active_groups_count'=>'Groups','status'=>'Status'] as $key=>$label)<th class="p-4"><button type="button" @click="sort('{{ $key }}')">{{ $label }} <span x-show="sortKey==='{{ $key }}'" x-text="sortDirection===1?'↑':'↓'"></span></button></th>@endforeach
                <th class="p-4 text-right">Actions</th>
            </tr></thead>
            <tbody><template x-for="title in visible" :key="title.id"><tr class="border-b last:border-0 hover:bg-muted/30">
                <td class="max-w-xs p-4 font-medium"><a :href="url('/dosen/titles/'+title.id)" class="hover:underline" x-text="title.title"></a></td>
                <td class="p-4"><div class="flex flex-wrap gap-1"><template x-for="spec in title.specializations||[]" :key="spec"><span class="rounded-md border px-2 py-0.5 text-xs" x-text="spec"></span></template></div></td>
                <td class="p-4" x-text="title.quota"></td><td class="p-4" x-text="title.active_groups_count||0"></td>
                <td class="p-4"><span class="rounded-full px-2 py-1 text-xs" :class="statusColor(title.status)" x-text="title.status"></span></td>
                <td class="p-4"><div class="flex justify-end gap-2"><x-capstone::button variant="outline" size="sm" @click="edit(title)">Edit</x-capstone::button><x-capstone::button variant="destructive" size="sm" @click="confirmDelete(title)" ::disabled="saving || title.active_groups_count>0">Delete</x-capstone::button></div></td>
            </tr></template></tbody>
        </table><p x-show="!filtered.length" class="p-12 text-center text-muted-foreground">No titles found. Create one or adjust your filters.</p></div>
        @include('capstone::pages.dosen.shared.pagination')
    </div>
    <x-capstone::dialog id="title-form" width="max-w-xl">
        <h2 class="text-lg font-semibold" x-text="editing?'Edit Title':'Add New Title'"></h2><p class="mt-2 text-sm text-muted-foreground">Offer a project title for students to bid on.</p>
        <form @submit.prevent="save()" class="mt-4 space-y-4"><fieldset :disabled="saving" class="space-y-4">
            <x-capstone::field name="title" label="Title" maxlength="255" required />
            <x-capstone::field name="description" label="Description" type="textarea" required />
            <x-capstone::field name="problem_statement" label="Problem Statement" type="textarea" required />
            <x-capstone::field name="scope" label="Scope" type="textarea" required />
            <fieldset class="space-y-2"><legend class="text-sm font-medium">Specializations</legend><div class="flex flex-wrap gap-3"><template x-for="spec in specializations" :key="spec"><label class="flex items-center gap-2 text-sm"><input type="checkbox" x-model="form.specializations" :value="spec" class="accent-primary"><span x-text="spec"></span></label></template></div><p class="text-sm text-destructive" x-text="errors.specializations?.[0]"></p></fieldset>
            <x-capstone::field name="quota" label="Quota (Groups)" type="number" min="1" step="1" required />
            <div x-show="editing"><x-capstone::field name="status" label="Status" type="select" :options="['open'=>'Open','closed'=>'Closed']" /></div>
            <p class="text-sm text-destructive" role="alert" x-text="errors.root"></p>
            <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="close('title-form')">Cancel</x-capstone::button><x-capstone::button type="submit" ::disabled="saving"><span x-text="saving?'Saving...':editing?'Update Title':'Create Title'"></span></x-capstone::button></div>
        </fieldset></form>
    </x-capstone::dialog>
    <x-capstone::dialog id="title-delete" title="Delete Title"><p class="my-4 text-sm">Delete <strong x-text="deleting?.title"></strong>?</p><p class="text-sm text-destructive" x-text="errors.root"></p><div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="close('title-delete')">Cancel</x-capstone::button><x-capstone::button variant="destructive" @click="remove()" ::disabled="saving">Delete</x-capstone::button></div></x-capstone::dialog>
</div>
@endsection
