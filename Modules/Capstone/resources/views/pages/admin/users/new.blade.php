@extends('capstone::layouts.app')
@section('title','Tambah User')
@section('content')
@include('capstone::pages.admin.users.form',['mode'=>'new'])
@endsection
