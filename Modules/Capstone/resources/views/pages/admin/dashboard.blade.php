@extends('capstone::layouts.app')
@section('title','Dashboard')
@section('content')
<div x-data="capstoneDashboard">
    <div class="mb-6"><h1 class="text-2xl font-bold tracking-tight">Welcome back, {{ explode(' ', $actor['name'])[0] }}!</h1><p class="text-muted-foreground">Here's an overview of your administration dashboard.</p></div>
    <section class="space-y-4">
        <div><h2 class="text-lg font-semibold tracking-tight">Admin Overview</h2><p class="text-sm text-muted-foreground">System management &amp; monitoring</p></div>
        @include('capstone::partials.loading')
        <div x-show="!loading && !error" x-cloak class="space-y-4">
            <div class="grid grid-cols-2 gap-3"><x-capstone::stat title="Total Users" value="data.total_users ?? 0" icon="Users" variant="primary" /><x-capstone::stat title="Total Periods" value="data.total_periods ?? 0" icon="Calendar" /><x-capstone::stat title="Total Groups" value="data.total_groups ?? 0" icon="Users" /><x-capstone::stat title="Pending Finalization" value="data.pending_finalization ?? 0" icon="ClipboardCheck" /></div>
            @include('capstone::partials.recent-groups',['heading'=>'Recent Groups','link'=>'/admin/groups','empty'=>'No groups yet'])
            @include('capstone::partials.quick-actions',['actions'=>[['Manage Groups','/admin/groups','Users','View and manage'],['TA Defense','/admin/ta-defense','GraduationCap','Schedule defense'],['Schedule Exam','/admin/schedule','Calendar','SEMPRO & Expo'],['Finalization','/admin/finalization','ClipboardCheck','Finalize groups']]])
        </div>
    </section>
</div>
@endsection
