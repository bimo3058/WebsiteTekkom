<x-app-layout>
    <x-sidebar :user="auth()->user()">
        <div class="min-h-screen bg-slate-50 p-6">
            <div class="max-w-full mx-auto">
                <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Kategori: {{ $category }}</h1>
                        <p class="text-slate-500 text-xs mt-0.5">Menampilkan semua user dalam grup ini</p>
                    </div>

                    <form action="{{ url()->current() }}" method="GET" class="flex items-center gap-2">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari nama atau email..." 
                                class="text-xs border-slate-200 rounded-lg pl-8 pr-4 py-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                            <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <select name="per_page" onchange="this.form.submit()" 
                            class="text-xs border-slate-200 rounded-lg py-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach([10, 20, 50, 100] as $limit)
                                <option value="{{ $limit }}" {{ request('per_page') == $limit ? 'selected' : '' }}>
                                    Show {{ $limit }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="bg-blue-600 text-white text-xs px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">
                            Filter
                        </button>
                        <a href="{{ route('superadmin.permissions') }}" class="text-xs font-bold text-blue-600 ml-2">Kembali</a>
                    </form>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @php
                        // Mengurutkan koleksi: Superadmin di atas (true/1 didahulukan), 
                        // lalu bisa diikuti urutan nama (opsional)
                        $sortedUsers = $users->getCollection()->sortByDesc(function($user) {
                            return $user->roles->pluck('name')->contains('superadmin');
                        });

                        // Set kembali koleksi yang sudah diurutkan ke paginator
                        $users->setCollection($sortedUsers);
                    @endphp

                    @forelse($users as $user)
                        @include('superadmin.permission._user_card', ['user' => $user])
                    @empty
                        <div class="bg-white p-10 text-center rounded-xl border border-dashed border-slate-300">
                            <p class="text-slate-500 text-sm">User tidak ditemukan.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @include('superadmin.permission._scripts')
    </x-sidebar>
</x-app-layout>