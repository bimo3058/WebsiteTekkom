@extends('layouts.master')

@section('content')
@endsection

@push('styles')
    <style>
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            background: #ffffff;
            border-right: 1px solid #DFE1E7;
            padding: 0;
            display: flex;
            flex-direction: column;
            transition: width 0.25s ease;
        }

        .menu-title {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
        }

        .sidebar a {
            position: relative;
            display: block;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 10px 7px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #353849;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 1px;
            transition: background .12s, color .12s;
            white-space: nowrap;
        }

        .sidebar a:hover {
            background: #F6F8FA;
            color: #1A1C1E;
        }

        .sidebar a.active {
            background: rgba(11, 38, 110, 0.08);
            color: #0B266E;
            font-weight: 600;
            box-shadow: none;
        }

        .sidebar a.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: #0B266E;
            border-radius: 0 3px 3px 0;
        }

        .sidebar-collapsed .sidebar a.active::before {
            display: none;
        }

        .sidebar a svg {
            color: #666D80;
            width: 16px;
            height: 16px;
            transition: color 0.12s;
            flex-shrink: 0;
        }

        .sidebar a.active svg {
            color: #0B266E;
        }

        .sidebar a:hover svg {
            color: #1A1C1E;
        }

        /* Collapsed Sidebar */
        .sidebar-collapsed .sidebar {
            width: 64px;
            padding: 0;
        }

        .sidebar-collapsed .sidebar a {
            justify-content: center;
            padding: 7px 0;
            gap: 0;
        }

        .sidebar-collapsed .nav-label,
        .sidebar-collapsed .sb-section-label,
        .sidebar-collapsed .sb-brand-text,
        .sidebar-collapsed .dropdown-arrow,
        .sidebar-collapsed .sidebar-dropdown-menu {
            display: none !important;
        }

        .btn-logout {
            position: relative;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 10px 7px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #353849;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 1px;
            transition: background .12s, color .12s;
            width: 100%;
            text-align: left;
            border: none;
            background: transparent;
            white-space: nowrap;
            font-family: inherit;
            cursor: pointer;
        }

        .sidebar-collapsed .btn-logout {
            justify-content: center;
            padding: 7px 0;
            gap: 0;
        }

        .btn-logout:hover {
            background: #FEF1F4;
            color: #DF1C41;
        }

        .btn-logout svg {
            color: #666D80;
            width: 16px;
            height: 16px;
            transition: color 0.12s;
            flex-shrink: 0;
        }

        .btn-logout:hover svg {
            color: #DF1C41;
        }

        .bottom-menu {
            margin-top: auto;
            padding-top: 10px;
            width: 100%;
        }

        .navbar-custom {
            margin-left: 240px;
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #DFE1E7;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 28px;
            transition: margin-left 0.25s ease;
        }

        .sidebar-collapsed .navbar-custom {
            margin-left: 64px;
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
            margin-left: 240px;
            padding: 24px 28px 48px;
            transition: margin-left 0.25s ease;
        }

        .sidebar-collapsed .content {
            margin-left: 64px;
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
<div x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false' }" 
     x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))"
     :class="{ 'sidebar-collapsed': !sidebarOpen }">

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
</div>
@endsection