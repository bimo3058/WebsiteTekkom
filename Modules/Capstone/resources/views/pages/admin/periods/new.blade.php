@extends('capstone::layouts.app')
@section('title','Tambah Periode Baru')
@section('content')
@include('capstone::pages.admin.periods.wizard', ['periodId'=>null])
@endsection
