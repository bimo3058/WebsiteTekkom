@extends('layouts.master')

@section('content')
@endsection

@push('styles')
    <style>
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .menu-title {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            color: #374151;
        }

        .sidebar a:hover {
            background: #f3f4f6;
        }

        .sidebar a.active {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .bottom-menu {
            margin-top: auto;
            padding-top: 10px;
            width: 100%;
        }

        .navbar-custom {
            margin-left: 260px;
            height: 70px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 25px;
        }

        .user-profile {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .user-profile img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
        }

        .content {
            margin-left: 260px;
            padding: 25px;
        }

        .main-wrapper {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            min-height: calc(100vh - 120px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Global Scrollbar Customization */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
            border: 2px solid #f1f5f9;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('body')
    {{-- Sidebar Dinamis --}}
    <x-manajemenmahasiswa::ui.sidebar />

    {{-- Navbar --}}
    <x-manajemenmahasiswa::ui.navbar-admin />

    {{-- Content --}}
    <div class="content">
        <div class="main-wrapper">
            @yield('content')
        </div>
    </div>
@endsection