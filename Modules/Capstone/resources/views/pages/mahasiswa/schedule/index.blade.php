@extends('capstone::layouts.app')
@section('title','My Schedule')
@section('content')
<div x-data="capstoneSchedules" class="space-y-5">
    @include('capstone::partials.schedule-header')
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @include('capstone::partials.schedule-toolbar')
        @include('capstone::partials.schedule-calendar')
        @include('capstone::partials.schedule-table')
    </div>
</div>
@endsection
