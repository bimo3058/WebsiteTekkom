@extends('capstone::layouts.app')
@section('title','Evaluasi TA')
@section('content')
@include('capstone::pages.dosen.shared.evaluation-form', ['ta'=>true])
@endsection
