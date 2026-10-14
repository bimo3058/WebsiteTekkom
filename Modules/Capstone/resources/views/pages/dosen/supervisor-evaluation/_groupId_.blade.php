@extends('capstone::layouts.app')
@section('title','Supervisor Evaluation')
@section('content')
@if(request()->query('type') === 'EXPO')
    @include('capstone::pages.dosen.shared.expo-review')
@else
    @include('capstone::pages.dosen.shared.evaluation-form', ['supervisor'=>true])
@endif
@endsection
