<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Permission Management</h1>
                    <p class="text-slate-500 text-sm mt-0.5">Kelola permission per user di luar role default</p>
                </div>
                <a href="{{ route('superadmin.users.index') }}"
                   class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 font-medium px-4 py-2.5 rounded-lg transition-all text-sm border border-slate-200 shadow-sm">
                    <span class="material-symbols-outlined" style="font-size:16px">arrow_back</span>
                    Kembali ke Users
                </a>
            </div>

            {{-- Alert --}}
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <span class="text-emerald-700 text-sm">{{ session('success') }}</span>
            </div>
            @endif

            {{-- Info Box --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-8">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-blue-500 flex-shrink-0">info</span>
                    <div class="text-sm text-blue-700">
                        <p class="font-semibold mb-1">Cara kerja permission:</p>
                        <p class="text-blue-600">Permission aktif = permission dari role + permission langsung di sini. Superadmin otomatis bypass semua permission. Centang di sini hanya untuk tambah akses di luar role default user.</p>
                    </div>
                </div>
            </div>

            {{-- Role Summary --}}
            <div class="mb-8">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Default Permission per Role</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach($roles->whereNotIn('name', ['superadmin', 'dosen', 'gpm', 'mahasiswa']) as $role)
                    @php
                        $roleColor = match(strtolower($role->name)) {
                            'admin'          => ['border' => 'border-blue-200', 'text' => 'text-blue-600', 'bg' => 'bg-blue-50'],
                            'admin_readonly' => ['border' => 'border-slate-200', 'text' => 'text-slate-600', 'bg' => 'bg-slate-50'],
                            default          => ['border' => 'border-purple-200', 'text' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                        };
                    @endphp
                    <div class="bg-white border {{ $roleColor['border'] }} rounded-xl p-5 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $roleColor['bg'] }} {{ $roleColor['text'] }} border {{ $roleColor['border'] }}">
                                {{ $role->name }}
                            </span>
                            <span class="text-slate-400 text-xs">{{ $role->permissions->count() }} permission</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($role->permissions as $perm)
                            <span class="inline-block bg-slate-50 text-slate-600 text-[11px] px-2 py-1 rounded-lg border border-slate-100">
                                {{ $perm->name }}
                            </span>
                            @empty
                            <span class="text-slate-400 text-xs italic">Tidak ada permission</span>
                            @endforelse
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Search --}}
            <div class="mb-5">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:18px">search</span>
                    <input type="text" id="userSearch"
                        placeholder="Cari user berdasarkan nama atau email..."
                        class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-slate-800 placeholder-slate-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm">
                </div>
            </div>

            {{-- User Permission Cards --}}
            <div class="space-y-4" id="userList">
                @foreach($users as $user)
                @php
                    $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
                    $userPerms    = $user->directPermissions->pluck('name');
                @endphp
                <div class="user-card bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition-all"
                     data-name="{{ strtolower($user->name) }}"
                     data-email="{{ strtolower($user->email) }}">

                    {{-- Card Header — klik untuk expand --}}
                    <button type="button"
                            onclick="toggleCard({{ $user->id }})"
                            class="w-full flex items-center justify-between px-5 py-4 hover:bg-slate-50 transition-all text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-semibold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-slate-800 font-medium text-sm">{{ $user->name }}</p>
                                    @foreach($user->roles as $role)
                                    @php
                                        $rc = match(strtolower($role->name)) {
                                            'superadmin' => 'bg-purple-50 text-purple-600 border-purple-100',
                                            'admin'      => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'dosen'      => 'bg-green-50 text-green-600 border-green-100',
                                            'mahasiswa'  => 'bg-orange-50 text-orange-600 border-orange-100',
                                            default      => 'bg-slate-50 text-slate-600 border-slate-100',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold border {{ $rc }}">
                                        {{ $role->name }}
                                    </span>
                                    @endforeach
                                    @if($userPerms->count() > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-yellow-50 text-yellow-600 border border-yellow-100">
                                        +{{ $userPerms->count() }} direct
                                    </span>
                                    @endif
                                </div>
                                <p class="text-slate-400 text-xs mt-0.5">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            @if($isSuperadmin)
                            <span class="text-xs text-purple-500 italic">Bypass semua permission</span>
                            @endif
                            <span class="material-symbols-outlined text-slate-400 transition-transform duration-200 card-chevron-{{ $user->id }}">
                                expand_more
                            </span>
                        </div>
                    </button>

                    {{-- Card Body (collapsed by default) --}}
                    <div id="card-body-{{ $user->id }}" class="hidden border-t border-slate-100 bg-slate-50/50">
                        @if($isSuperadmin)
                        <div class="px-5 py-6 text-slate-500 text-sm italic text-center">
                            <span class="material-symbols-outlined text-slate-400 mr-1" style="font-size:16px">shield</span>
                            Superadmin memiliki akses ke semua permission secara otomatis.
                        </div>
                        @else
                        <form method="POST"
                              action="{{ route('superadmin.users.update-permissions', $user) }}"
                              class="px-5 py-5">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                                @foreach($permissions as $module => $perms)
                                @php
                                    $moduleColor = match($module) {
                                        'banksoal'       => 'text-yellow-600 border-yellow-100 bg-yellow-50',
                                        'kemahasiswaan'  => 'text-orange-600 border-orange-100 bg-orange-50',
                                        'capstone'       => 'text-cyan-600 border-cyan-100 bg-cyan-50',
                                        'eoffice'        => 'text-purple-600 border-purple-100 bg-purple-50',
                                        default          => 'text-slate-600 border-slate-100 bg-slate-50',
                                    };
                                @endphp
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide {{ $moduleColor }} inline-block px-2.5 py-1 rounded-lg border mb-3">
                                        {{ $module }}
                                    </p>
                                    <div class="space-y-2.5">
                                        @foreach($perms as $permission)
                                        @php
                                            $action     = explode('.', $permission->name)[1] ?? $permission->name;
                                            $isDirect   = $userPerms->contains($permission->name);
                                            $isFromRole = !$isDirect && $user->roles
                                                ->flatMap(fn($r) => $r->permissions->pluck('name'))
                                                ->contains($permission->name);
                                        @endphp
                                        <label class="flex items-center gap-2.5 group cursor-pointer">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->name }}"
                                                   {{ $isDirect ? 'checked' : '' }}
                                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-200 focus:ring-offset-0">
                                            <span class="text-sm {{ $isDirect ? 'text-slate-800 font-medium' : ($isFromRole ? 'text-slate-400' : 'text-slate-500') }} group-hover:text-slate-700 transition capitalize">
                                                {{ str_replace('_', ' ', $action) }}
                                            </span>
                                            @if($isFromRole)
                                            <span class="text-[10px] text-slate-400 italic">(dari role)</span>
                                            @endif
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                                <p class="text-slate-500 text-xs flex items-center gap-1">
                                    <span class="material-symbols-outlined text-slate-400" style="font-size:14px">info</span>
                                    Centang = tambah permission langsung ke user ini (di luar role)
                                </p>
                                <button type="submit"
                                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-all shadow-sm">
                                    <span class="material-symbols-outlined" style="font-size:16px">save</span>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                        @endif
                    </div>

                </div>
                @endforeach
            </div>

            {{-- Empty state --}}
            <div id="emptyState" class="hidden py-16 text-center">
                <div class="bg-white border border-slate-200 rounded-xl p-8 max-w-md mx-auto">
                    <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">person_off</span>
                    <p class="font-medium text-slate-600">User tidak ditemukan</p>
                    <p class="text-sm text-slate-400 mt-1">Coba gunakan kata kunci lain</p>
                </div>
            </div>

        </div>
    </div>

    <script>
    // Toggle expand/collapse card
    function toggleCard(userId) {
        const body    = document.getElementById('card-body-' + userId);
        const chevron = document.querySelector('.card-chevron-' + userId);
        const isHidden = body.classList.contains('hidden');
        
        body.classList.toggle('hidden', !isHidden);
        
        if (chevron) {
            chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
        }
    }

    // Search filter
    document.getElementById('userSearch').addEventListener('input', function () {
        const term  = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.user-card');
        let visible = 0;

        cards.forEach(card => {
            const name  = card.dataset.name  ?? '';
            const email = card.dataset.email ?? '';
            const match = !term || name.includes(term) || email.includes(term);
            card.classList.toggle('hidden', !match);
            if (match) visible++;
        });

        const emptyState = document.getElementById('emptyState');
        if (emptyState) {
            emptyState.classList.toggle('hidden', visible > 0);
        }
    });

    // Optional: Open first card if there's an error?
    </script>
</x-sidebar>
</x-app-layout>