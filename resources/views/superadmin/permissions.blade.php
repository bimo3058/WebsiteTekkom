<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-6">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Access Control Center</h1>
                    <p class="text-slate-500 text-xs mt-0.5">Kelola Role & Direct Permission per User</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">search</span>
                        <input type="text" id="userSearch"
                            placeholder="Cari user..."
                            class="bg-white border border-slate-200 rounded-lg pl-9 pr-4 py-1.5 text-slate-800 placeholder-slate-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-xs w-64">
                    </div>
                    <a href="{{ route('superadmin.users.index') }}"
                       class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 font-medium px-3 py-1.5 rounded-lg transition-all text-xs border border-slate-200 shadow-sm">
                        <span class="material-symbols-outlined" style="font-size:14px">arrow_back</span>
                        Users Management
                    </a>
                </div>
            </div>

            {{-- Alert --}}
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-500" style="font-size:18px">check_circle</span>
                <span class="text-emerald-700 text-xs font-medium">{{ session('success') }}</span>
            </div>
            @endif

            {{-- Role Category Sections --}}
            @php
                $categories = [
                    'Admins' => ['superadmin', 'admin', 'admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan'],
                    'Dosen' => ['dosen'],
                    'Mahasiswa' => ['mahasiswa'],
                    'GPM' => ['gpm']
                ];
            @endphp

            <div class="space-y-10">
                @foreach($categories as $title => $slugs)
                <section class="role-section">
                    <div class="flex items-center gap-2 mb-4">
                        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest">{{ $title }}</h2>
                        <div class="h-px bg-slate-200 flex-grow"></div>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        @php
                            $filteredUsers = $users->filter(fn($u) => $u->roles->pluck('name')->intersect($slugs)->isNotEmpty());
                        @endphp

                        @forelse($filteredUsers as $user)
                            @php
                                $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
                                $userPerms = $user->directPermissions->pluck('name');
                            @endphp
                            <div class="user-card bg-white border border-slate-200 rounded-xl overflow-hidden hover:border-blue-200 transition-all shadow-sm"
                                 data-name="{{ strtolower($user->name) }}"
                                 data-email="{{ strtolower($user->email) }}">
                                
                                {{-- Compact Header --}}
                                <button type="button" onclick="toggleCard({{ $user->id }})"
                                        class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50/80 transition-all text-left">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs border border-slate-200">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-slate-800 font-semibold text-xs">{{ $user->name }}</span>
                                                <span class="text-slate-400 text-[10px]">• {{ $user->email }}</span>
                                            </div>
                                            <div class="flex gap-1 mt-1">
                                                @foreach($user->roles as $role)
                                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 text-[9px] font-bold border border-slate-200 uppercase">{{ $role->name }}</span>
                                                @endforeach
                                                @if($userPerms->count() > 0)
                                                    <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 text-[9px] font-bold border border-amber-100 uppercase">+{{ $userPerms->count() }} Direct</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <span class="material-symbols-outlined text-slate-300 transition-transform duration-200 card-chevron-{{ $user->id }}" style="font-size:20px">expand_more</span>
                                </button>

                                {{-- Body --}}
                                <div id="card-body-{{ $user->id }}" class="hidden border-t border-slate-50 bg-slate-50/30 p-5">
                                    @if($isSuperadmin)
                                        <div class="text-center py-2 text-slate-400 text-[11px] italic">
                                            Superadmin bypasses all permission checks.
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('superadmin.users.update-permissions', $user) }}">
                                            @csrf
                                            
                                            {{-- Mini Role Switcher --}}
                                            <div class="mb-6">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-tight">Assign Roles</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($roles as $role)
                                                        @php
                                                            // Gunakan logika warna yang sama dengan Index
                                                            $roleColors = match(strtolower($role->name)) {
                                                                'superadmin' => 'peer-checked:bg-purple-50 peer-checked:text-purple-600 peer-checked:border-purple-200 text-slate-500 bg-white border-slate-200',
                                                                'dosen' => 'peer-checked:bg-green-50 peer-checked:text-green-600 peer-checked:border-green-200 text-slate-500 bg-white border-slate-200',
                                                                'mahasiswa' => 'peer-checked:bg-orange-50 peer-checked:text-orange-600 peer-checked:border-orange-200 text-slate-500 bg-white border-slate-200',
                                                                'admin_banksoal' => 'peer-checked:bg-yellow-50 peer-checked:text-yellow-600 peer-checked:border-yellow-200 text-slate-500 bg-white border-slate-200',
                                                                'admin_capstone' => 'peer-checked:bg-cyan-50 peer-checked:text-cyan-600 peer-checked:border-cyan-200 text-slate-500 bg-white border-slate-200',
                                                                default => 'peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:border-blue-200 text-slate-500 bg-white border-slate-200',
                                                            };
                                                        @endphp
                                                        <label class="relative cursor-pointer">
                                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                                                {{ $user->roles->contains($role->id) ? 'checked' : '' }} 
                                                                class="peer sr-only">
                                                            <div class="px-2 py-1 rounded border text-[10px] font-bold uppercase transition-all {{ $roleColors }}">
                                                                {{ $role->name }}
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            {{-- Permissions Grid --}}
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                @foreach($permissions as $module => $perms)
                                                <div class="bg-white/50 p-3 rounded-lg border border-slate-100">
                                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-2">{{ $module }}</p>
                                                    <div class="space-y-1.5">
                                                        @foreach($perms as $permission)
                                                            @php
                                                                $action = explode('.', $permission->name)[1] ?? $permission->name;
                                                                $isFromRole = $user->roles->flatMap->permissions->pluck('name')->contains($permission->name);
                                                            @endphp
                                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ $userPerms->contains($permission->name) ? 'checked' : '' }} class="w-3 h-3 rounded border-slate-300 text-blue-600">
                                                                <span class="text-[11px] {{ $isFromRole ? 'text-blue-500 font-semibold' : 'text-slate-500' }}">{{ str_replace('_', ' ', $action) }}</span>
                                                                @if($isFromRole) <span class="text-[8px] text-blue-300 font-normal">(role)</span> @endif
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            <div class="mt-5 pt-4 border-t border-slate-100 flex justify-end">
                                                <button type="submit" class="bg-slate-800 hover:bg-black text-white text-[11px] font-bold px-4 py-1.5 rounded-lg transition-all shadow-sm flex items-center gap-2">
                                                    <span class="material-symbols-outlined" style="font-size:14px">sync</span> Update Akses
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-xl text-slate-400 text-xs">
                                Tidak ada user dalam kategori ini.
                            </div>
                        @endforelse
                    </div>
                </section>
                @endforeach
            </div>
        </div>
    </div>

    <script>
    function toggleCard(userId) {
        const body = document.getElementById('card-body-' + userId);
        const chevron = document.querySelector('.card-chevron-' + userId);
        const isHidden = body.classList.contains('hidden');
        body.classList.toggle('hidden', !isHidden);
        if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
    }

    document.getElementById('userSearch').addEventListener('input', function () {
        const term = this.value.toLowerCase().trim();
        document.querySelectorAll('.user-card').forEach(card => {
            const match = !term || card.dataset.name.includes(term) || card.dataset.email.includes(term);
            card.classList.toggle('hidden', !match);
        });
        
        // Sembunyikan section jika semua isinya hidden
        document.querySelectorAll('.role-section').forEach(section => {
            const visibleCards = section.querySelectorAll('.user-card:not(.hidden)');
            section.classList.toggle('hidden', visibleCards.length === 0);
        });
    });
    </script>
</x-sidebar>
</x-app-layout>