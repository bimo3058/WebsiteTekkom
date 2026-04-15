<x-app-layout>
<div class="flex min-h-screen bg-gray-100">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md p-5">
        <h2 class="text-lg font-semibold mb-6">🎓 Portal Mahasiswa</h2>

        <p class="text-sm text-gray-400 mb-2">Main Menu</p>
        <ul class="space-y-3">
            <li class="bg-purple-100 text-purple-600 px-3 py-2 rounded-lg font-medium">
                Pengumuman
            </li>
            <li class="text-gray-600 hover:text-purple-600 cursor-pointer">Direktori Mahasiswa</li>
            <li class="text-gray-600 hover:text-purple-600 cursor-pointer">Kegiatan</li>
            <li class="text-gray-600 hover:text-purple-600 cursor-pointer">Forum Diskusi</li>
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
                <h1 class="text-3xl font-bold">Pengumuman & Informasi</h1>
                <p class="text-gray-500 text-sm">Wadah Informasi untuk Mahasiswa dan Alumni</p>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm">Username</span>
                <img src="https://i.pravatar.cc/40" class="rounded-full">
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="flex gap-3 mb-6">
            <input type="text" placeholder="Search"
                class="flex-1 px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-purple-400">

            <select class="px-4 py-2 rounded-lg border">
                <option>Filter</option>
            </select>
        </div>

        <!-- Cards -->
        @for ($i = 0; $i < 3; $i++)
        <div class="bg-white p-5 rounded-xl shadow mb-5 hover:shadow-lg transition">
            
            <div class="flex justify-between items-start">
                <div class="flex gap-3">
                    
                    <!-- Icon -->
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 flex items-center justify-center rounded-lg">
                        📢
                    </div>

                    <!-- Content -->
                    <div>
                        <h3 class="font-semibold text-lg">
                            {{ $i == 1 ? 'Jadwal KRS Semester Genap 2025/2026' : 'Judul' }}
                        </h3>

                        <p class="text-sm text-gray-600 mt-1">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.
                        </p>

                        <p class="text-xs text-gray-400 mt-3">
                            27 Januari 2026
                        </p>
                    </div>

                </div>

                <!-- Action -->
                <a href="#" class="text-sm text-purple-600 hover:underline">
                    Baca Selengkapnya
                </a>
            </div>

        </div>
        @endfor

    </main>
</div>
</x-app-layout>