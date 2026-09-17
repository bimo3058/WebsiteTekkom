@extends('capstone::layouts.app')
@section('title','Edit User')
@section('content')
@include('capstone::pages.admin.users.form',['mode'=>'edit'])
@endsection
