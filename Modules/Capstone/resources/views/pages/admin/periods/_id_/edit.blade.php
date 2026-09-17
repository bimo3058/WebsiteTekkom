@extends('capstone::layouts.app')
@section('title','Edit Periode')
@section('content')
@include('capstone::pages.admin.periods.wizard', ['periodId'=>$pageParams['id']])
@endsection
