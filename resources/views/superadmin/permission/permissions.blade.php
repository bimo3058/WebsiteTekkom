{{-- resources/views/superadmin/permission/permissions.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-6">
        <div class="max-w-full mx-auto">
            {{-- Header --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-black text-slate-800 uppercase tracking-tight">Access Control Center</h1>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-0.5">Authorization & User Permissions Hub</p>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Form Pencarian dengan Tombol Filter --}}
                    <form action="{{ url()->current() }}" method="GET" class="flex items-center gap-2">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">search</span>
                            <input type="text" name="search" id="userSearch" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                                class="bg-white border border-slate-200 rounded-lg pl-9 pr-4 py-2 text-slate-800 placeholder-slate-400 focus:border-blue-400 focus:ring-4 focus:ring-blue-50/50 outline-none transition-all text-xs w-64 font-medium">
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg transition-all text-[11px] tracking-tight shadow-sm">
                            Filter
                        </button>
                    </form>

                    <div class="w-px h-6 bg-slate-200 mx-1"></div>

                    {{-- Tombol Back (Tanpa Uppercase) --}}
                    <a href="{{ route('superadmin.users.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 font-bold px-4 py-2 rounded-lg transition-all text-[11px] border border-slate-200 shadow-sm">
                        <span class="material-symbols-outlined" style="font-size:14px">arrow_back</span> 
                        Kembali ke Users
                    </a>
                </div>
            </div>

            @php
                $categories = [
                    'Admins' => ['superadmin', 'admin', 'admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan'],
                    'Dosen' => ['dosen'], 'Mahasiswa' => ['mahasiswa'], 'GPM' => ['gpm']
                ];
            @endphp

            <div class="space-y-12">
                @foreach($categories as $title => $slugs)
                    <section class="role-section">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <div class="flex items-center gap-3 flex-grow">
                                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ $title }}</h2>
                                <div class="h-px bg-slate-200 flex-grow"></div>
                            </div>
                            <a href="{{ route('superadmin.users.category', $title) }}" 
                               class="ml-6 flex items-center gap-1 text-[10px] font-black text-blue-600 hover:text-blue-800 transition-all uppercase">
                                View All
                                <span class="material-symbols-outlined" style="font-size:16px">arrow_right_alt</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 gap-1">
                            @php
                                $filteredUsers = $users->filter(fn($u) => $u->roles->pluck('name')->intersect($slugs)->isNotEmpty());
                                $sortedUsers = $filteredUsers->sortByDesc(fn($u) => $u->roles->pluck('name')->contains('superadmin'))->take(5);
                            @endphp

                            @forelse($sortedUsers as $user)
                                @include('superadmin.permission._user_card', ['user' => $user])
                            @empty
                                <div class="bg-white border-2 border-dashed border-slate-200 rounded-lg py-8 text-center">
                                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">No users in this category</p>
                                </div>
                            @endforelse
                        </div>
                    </section>

                    @if($title === 'Admins')
                        @php $unassignedUsers = $users->filter(fn($u) => $u->roles->isEmpty())->take(5); @endphp
                        @if($unassignedUsers->isNotEmpty())
                            <section class="role-section mt-12">
                                <div class="flex items-center justify-between mb-4 px-1">
                                    <div class="flex items-center gap-3 flex-grow">
                                        <h2 class="text-[10px] font-black text-rose-400 uppercase tracking-[0.2em]">Unassigned Users</h2>
                                        <div class="h-px bg-rose-200 flex-grow"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 gap-1">
                                    @foreach($unassignedUsers as $user)
                                        @include('superadmin.permission._user_card', ['user' => $user])
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    @include('superadmin.permission._modal_confirm')
    @include('superadmin.permission._scripts')
</x-sidebar>
</x-app-layout>