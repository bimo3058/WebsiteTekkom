@extends('capstone::layouts.app')
@section('title','Dashboard')
@section('content')
<div x-data="capstoneDashboard">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4"><div><h1 class="text-2xl font-bold tracking-tight">Welcome back, {{ explode(' ', $actor['name'])[0] }}!</h1><p class="text-muted-foreground">Here's an overview of your mentoring dashboard.</p></div><select x-model="selectedPeriod" @change="load" class="h-9 rounded-md border px-3 text-sm"><option value="all">All Periods</option><template x-for="period in periods" :key="period.id"><option :value="period.id" x-text="period.name"></option></template></select></div>
    <section class="space-y-4"><div><h2 class="text-lg font-semibold tracking-tight">Dosen Overview</h2><p class="text-sm text-muted-foreground">Teaching &amp; mentoring activities</p></div>
        @include('capstone::partials.loading')
        <div x-show="!loading && !error" x-cloak class="space-y-4"><div class="grid grid-cols-2 gap-3"><x-capstone::stat title="Supervised Groups" value="data.active_groups ?? 0" icon="Users" variant="primary" /><x-capstone::stat title="Pending Evaluations" value="pending" icon="Star" /><x-capstone::stat title="Pending Proposals" value="data.pending_proposals ?? 0" icon="FileText" /><x-capstone::stat title="Available Periods" value="periods.length" icon="Calendar" /></div>
        @include('capstone::partials.recent-groups',['heading'=>'Supervised Groups','link'=>'/dosen/supervised-groups','empty'=>'No supervised groups yet'])
        @include('capstone::partials.quick-actions',['actions'=>[['Review TA','/dosen/ta-review','FileText','Review submissions'],['Evaluate','/dosen/supervisor-evaluation','Star','Score students'],['My Titles','/dosen/titles','BookOpen','Manage titles'],['Bids','/dosen/bids','Gavel','Review bids']]])</div>
    </section>
</div>
@endsection
