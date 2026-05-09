{{-- resources/views/superadmin/permission/permissions.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-[#F8F9FA] p-7 font-sans"> 
        <div class="max-w-full mx-auto">
            {{-- Header --}}
            <div class="mb-7 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-[#1A1C1E] tracking-tight">Access Control Center</h1>
                    <p class="text-[#6C757D] text-[13px] font-normal mt-0.5">Manage roles and module permissions</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <form action="{{ url()->current() }}" method="GET" class="flex items-center gap-2">
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-[#ADB5BD]" style="font-size:16px">search</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user..."
                                class="bg-white border border-[#DEE2E6] rounded-xl pl-9 pr-4 h-[34px] text-[#495057] focus:border-[#5E53F4] focus:ring-4 focus:ring-[#5E53F4]/10 outline-none transition-all text-[13px] font-medium w-64">
                        </div>
                    </form>

                    <div class="w-px h-6 bg-[#DEE2E6] mx-1"></div>

                    <x-ui.button variant="outline" size="sm" as="a" href="{{ route('superadmin.users.index') }}" class="font-semibold text-[13px] py-1.5">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span> 
                        Back
                    </x-ui.button>
                </div>
            </div>

            @php
                $categories = [
                    'Admins'     => ['superadmin', 'admin', 'admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan'],
                    'Unassigned' => [],
                    'Dosen'      => ['dosen'],
                    'GPM'        => ['gpm'],
                    'Mahasiswa'  => ['mahasiswa'],
                ];
            @endphp

            <div class="space-y-12">
                @foreach($categories as $title => $slugs)
                    @php
                        $filteredUsers = $slugs === []
                            ? $users->filter(fn($u) => $u->roles->isEmpty())
                            : $users->filter(fn($u) => $u->roles->pluck('name')->intersect($slugs)->isNotEmpty());
                        $sortedUsers = $filteredUsers->sortByDesc(fn($u) => $u->roles->pluck('name')->contains('superadmin'))->take(5);
                    @endphp

                    @if($title === 'Unassigned' && $filteredUsers->isEmpty())
                        @continue
                    @endif

                    <section class="role-section">
                        <div class="flex items-center gap-4 mb-5">
                            <h2 class="text-xs font-semibold text-[#6C757D] uppercase tracking-[0.15em] whitespace-nowrap">{{ $title }}</h2>
                            <div class="h-px bg-[#DEE2E6] flex-grow"></div>
                            <a href="{{ route('superadmin.permissions.category', $title) }}" 
                               class="text-[13px] font-semibold text-[#5E53F4] hover:text-[#4A42C1] transition-all flex items-center gap-1">
                                View All <span class="material-symbols-outlined text-[13px]">chevron_right</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 gap-2.5">
                            @forelse($sortedUsers as $user)
                                @include('superadmin.permission._user_card', ['user' => $user])
                            @empty
                                <div class="bg-white border-2 border-dashed border-[#DEE2E6] rounded-2xl py-10 text-center">
                                    <p class="text-[#ADB5BD] text-sm font-medium uppercase tracking-widest">No users found</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>

    @include('superadmin.permission._modal_confirm')
    @include('superadmin.permission._scripts')
</x-sidebar>
</x-app-layout>