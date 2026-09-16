@extends('capstone::layouts.app')
@section('title','Supervisor Evaluation')
@section('content')
@include('capstone::pages.dosen.shared.evaluation-form', ['supervisor'=>true])
@endsection
