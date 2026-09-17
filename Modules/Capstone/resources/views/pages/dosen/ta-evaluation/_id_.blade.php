@extends('capstone::layouts.app')
@section('title','TA Evaluation')
@section('content')
@include('capstone::pages.dosen.shared.evaluation-form', ['ta'=>true])
@endsection
