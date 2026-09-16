@extends('capstone::layouts.app')
@section('title','Edit Komponen Penilaian')
@section('content')
@include('capstone::pages.admin.assessment-bank.form',['templateId'=>$pageParams['id']])
@endsection
