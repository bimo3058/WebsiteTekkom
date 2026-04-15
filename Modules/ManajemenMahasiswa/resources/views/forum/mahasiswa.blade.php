@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md p-5">
        <h2 class="text-lg font-semibold mb-6">🎓 Portal Mahasiswa</h2>

        <p class="text-sm text-gray-400 mb-2">Main Menu</p>
        <ul class="space-y-3">
            <li class="text-gray-600 hover:text-purple-600 cursor-pointer">Pengumuman</li>
            <li class="text-gray-600 hover:text-purple-600 cursor-pointer">Direktori Mahasiswa</li>
            <li class="text-gray-600 hover:text-purple-600 cursor-pointer">Kegiatan</li>
            <li class="bg-purple-100 text-purple-600 px-3 py-2 rounded-lg font-medium">
                Forum Diskusi
            </li>
        </ul>

        <div class="mt-10 text-sm text-gray-500 space-y-2">
            <p>Settings</p>
            <p>Help & Center</p>
            <p class="text-red-500">Logout</p>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold">Forum Diskusi</h1>
                <p class="text-gray-500 text-sm">Wadah komunikasi mahasiswa & alumni</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm">Username</span>
                <img src="https://i.pravatar.cc/40" class="rounded-full">
            </div>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-2 gap-5 mb-6">

            <!-- Leaderboard -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-5 rounded-xl shadow">
                <h3 class="font-semibold mb-3">🏆 Leaderboard</h3>
                <ul class="text-sm space-y-1">
                    <li>1. Lutfi Halimawan - Lv1</li>
                    <li>2. Reza - Lv1</li>
                    <li>3. Surya - Lv1</li>
                </ul>
            </div>

            <!-- Streak -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white p-5 rounded-xl shadow">
                <h3 class="font-semibold mb-2">🔥 Streak Kamu Hari Ini</h3>
                <p class="text-sm">Rank: ...</p>
                <p class="text-sm">Level: ...</p>
                <p class="text-sm">Exp: ...</p>
            </div>

        </div>

        <!-- Search & Action -->
        <div class="flex gap-3 mb-6">
            <input type="text" placeholder="Search"
                class="flex-1 px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-purple-400">

            <select class="px-3 py-2 rounded-lg border">
                <option>Filter</option>
            </select>

            <button class="bg-purple-600 text-white px-5 py-2 rounded-lg hover:bg-purple-700">
                + Post
            </button>
        </div>

        <!-- Post Card -->
        @for ($i = 0; $i < 3; $i++)
        <div class="bg-white p-5 rounded-xl shadow mb-5">
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center">
                        👤
                    </div>
                    <div>
                        <h4 class="font-semibold">Username</h4>
                        <p class="text-xs text-gray-400">2h ago</p>
                    </div>
                </div>

                <button class="bg-purple-500 text-white text-sm px-3 py-1 rounded-lg">
                    Join
                </button>
            </div>

            <p class="text-gray-600 text-sm mb-3">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </p>

            <!-- Labels -->
            <div class="flex gap-2 mb-3">
                <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded">Label</span>
                <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Label</span>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 text-gray-400 text-sm">
                <button>⬆</button>
                <button>⬇</button>
                <button>💬</button>
                <button>🔗</button>
            </div>
        </div>
        @endfor

    </main>
</div>
@endsection