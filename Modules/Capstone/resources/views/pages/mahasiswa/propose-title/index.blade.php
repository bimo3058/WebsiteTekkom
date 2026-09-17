@extends('capstone::layouts.app')
@section('title','Propose Title')
@section('content')
<div x-data="studentProposals" class="space-y-6">@include('capstone::partials.loading')
<div x-show="!loading && !error" x-cloak class="space-y-6"><div class="flex flex-wrap justify-between items-start gap-4"><div><h1 class="text-3xl font-bold tracking-tight">Propose Title</h1><p class="text-muted-foreground">Submit your own capstone title proposal to a supervisor.</p></div><div class="flex items-center gap-3"><x-capstone::badge variant="outline" x-show="isLeader" class="text-sm px-3 py-1"><span x-text="total+'/3 slots used'"></span></x-capstone::badge><x-capstone::button x-show="canCreate && !pending && !approved && !showForm" @click="edit()"><x-capstone::icon name="PenLine" />New Proposal</x-capstone::button></div></div>
@include('capstone::pages.mahasiswa.propose-title.constraints')
@include('capstone::pages.mahasiswa.propose-title.form')
@include('capstone::pages.mahasiswa.propose-title.history')
<div x-show="!proposals.length && !showForm && canCreate" class="text-center py-12 text-muted-foreground border rounded-lg border-dashed"><x-capstone::icon name="PenLine" class="h-12 w-12 mx-auto mb-4 opacity-50" /><p class="text-lg font-medium mb-1">No proposals yet</p><p class="text-sm">Click "New Proposal" to submit your own capstone title.</p></div>
</div></div>
@endsection
