<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-6">
        <div class="max-w-full mx-auto">
            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Access Control Center</h1>
                    <p class="text-slate-500 text-xs mt-0.5">Kelola Role & Direct Permission per User</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">search</span>
                        <input type="text" id="userSearch" placeholder="Cari user..."
                            class="bg-white border border-slate-200 rounded-lg pl-9 pr-4 py-1.5 text-slate-800 placeholder-slate-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-xs w-64">
                    </div>
                    <a href="{{ route('superadmin.users.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 font-medium px-3 py-1.5 rounded-lg transition-all text-xs border border-slate-200 shadow-sm">
                        <span class="material-symbols-outlined" style="font-size:14px">arrow_back</span> Users Management
                    </a>
                </div>
            </div>

            @php
                $categories = [
                    'Admins' => ['superadmin', 'admin', 'admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan'],
                    'Dosen' => ['dosen'], 'Mahasiswa' => ['mahasiswa'], 'GPM' => ['gpm']
                ];
            @endphp

            <div class="space-y-10">
                @foreach($categories as $title => $slugs)
                <section class="role-section">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2 flex-grow">
                            <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest">{{ $title }}</h2>
                            <div class="h-px bg-slate-200 flex-grow"></div>
                        </div>
                        <a href="{{ route('superadmin.users.category', $title) }}" 
                           class="ml-4 flex items-center gap-1 text-[10px] font-bold text-blue-600 hover:text-blue-800 transition-all uppercase">
                            View All
                            <span class="material-symbols-outlined" style="font-size:14px">arrow_forward</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        @php
                            $filteredUsers = $users->filter(fn($u) => $u->roles->pluck('name')->intersect($slugs)->isNotEmpty())->take(5);
                        @endphp

                        @forelse($filteredUsers as $user)
                            {{-- PANGGIL PARTIAL CARD --}}
                            @include('superadmin.permission._user_card', ['user' => $user])
                        @empty
                            <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-xl text-slate-400 text-xs">Tidak ada user.</div>
                        @endforelse
                    </div>
                </section>
                @endforeach
            </div>
        </div>
    </div>

    @include('superadmin.permission._scripts')
</x-sidebar>
</x-app-layout>