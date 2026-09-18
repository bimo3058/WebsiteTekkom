@extends('capstone::layouts.app')
@section('title','My Schedule')
@section('content')
<div x-data="capstoneSchedules" class="space-y-6"><div><h1 class="text-3xl font-bold tracking-tight">My Schedule</h1><p class="text-muted-foreground">View your bimbingan sessions, seminar proposals, expo events, and TA defense schedule.</p></div>@include('capstone::partials.loading')<div x-show="!loading && !error" x-cloak>@include('capstone::partials.schedule-calendar')</div></div>
@endsection
