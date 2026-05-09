<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-[#F8F9FA] p-6">
        <div class="max-w-full mx-auto">

            {{-- Header & Filter --}}
            <div class="mb-6 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                        User <span class="text-rose-600">Suspended</span>
                    </h1>
                    <p class="text-slate-500 text-xs mt-0.5">
                        Total <span class="text-rose-600 font-semibold">{{ $users->total() }}</span> user disuspend
                    </p>
                </div>

                <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap items-center gap-2">

                    {{-- Dropdown Per Page --}}
                    <div class="relative w-32" x-data="{
                        open: false,
                        selected: '{{ request('per_page', '10') }}',
                        options: ['10', '25', '50', '100']
                    }">
                        <input type="hidden" name="per_page" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] outline-none transition-all text-xs flex items-center justify-between shadow-sm">
                            <span x-text="selected + ' Baris'" class="font-medium"></span>
                            <span class="material-symbols-outlined text-slate-400 ml-1" :class="{'rotate-180': open}" style="font-size:16px; transition: transform 0.2s;">expand_more</span>
                        </button>
                        <div x-show="open" x-transition.opacity.duration.200ms style="display:none;"
                            class="absolute top-full left-0 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-xl z-[50] overflow-hidden py-1">
                            <template x-for="opt in options" :key="opt">
                                <button type="button" @click="selected = opt; $el.closest('form').submit()"
                                    class="w-full text-left px-3 py-1.5 text-xs transition-colors hover:bg-slate-50"
                                    :class="selected == opt ? 'text-[#5E53F4] font-semibold bg-[#5E53F4]/5' : 'text-slate-600'">
                                    <span x-text="opt + ' Baris'"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Dropdown Role --}}
                    <div class="relative w-40" x-data="{
                        open: false,
                        selected: '{{ request('role', 'all') }}',
                        roles: [
                            { name: 'all', label: 'Semua Role' },
                            @foreach($roles as $role)
                                { name: '{{ $role->name }}', label: '{{ ucfirst($role->name) }}' },
                            @endforeach
                        ],
                        get currentLabel() {
                            return this.roles.find(r => r.name === this.selected)?.label || 'Semua Role';
                        }
                    }">
                        <input type="hidden" name="role" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 outline-none transition-all text-xs flex items-center justify-between shadow-sm">
                            <span x-text="currentLabel" class="font-medium truncate"></span>
                            <span class="material-symbols-outlined text-slate-400 ml-1" :class="{'rotate-180': open}" style="font-size:16px; transition: transform 0.2s;">expand_more</span>
                        </button>
                        <div x-show="open" x-transition.opacity.duration.200ms style="display:none;"
                            class="absolute top-full left-0 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-xl z-[50] overflow-hidden py-1 max-h-52 overflow-y-auto">
                            <template x-for="r in roles" :key="r.name">
                                <button type="button" @click="selected = r.name; open = false"
                                    class="w-full text-left px-3 py-1.5 text-xs transition-colors hover:bg-slate-50"
                                    :class="selected === r.name ? 'text-[#5E53F4] font-semibold bg-[#5E53F4]/5' : 'text-slate-600'">
                                    <span x-text="r.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Search --}}
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama atau email..."
                            class="text-xs border border-slate-200 rounded-lg pl-8 pr-4 py-2 focus:ring-1 focus:ring-[#5E53F4] focus:border-[#5E53F4] outline-none w-56 shadow-sm">
                        <span class="material-symbols-outlined text-slate-400 absolute left-2.5 top-2" style="font-size:18px">search</span>
                    </div>

                    <button type="submit"
                        class="bg-[#5E53F4] hover:bg-[#4e44e0] text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('superadmin.users.index') }}"
                        class="text-xs font-bold text-[#5E53F4] ml-2 hover:underline transition-all">
                        Kembali
                    </a>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white border border-[#DEE2E6] rounded-2xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b border-[#DEE2E6] bg-[#F8F9FA]">
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">User Identity</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Access Roles</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Alasan Suspend</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Suspended At</th>
                                <th class="px-5 py-4 text-center text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F8F9FA]">
                            @forelse($users as $user)
                            @php
                                $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
                                $initials = strtoupper(substr($user->name, 0, 1));
                                $avatarColors = match(true) {
                                    $isSuperadmin => '!bg-[#F1E9FF] !text-[#5E53F4] border-[#D1BFFF]',
                                    default => '!bg-[#F8F9FA] !text-[#6C757D] border-[#DEE2E6]',
                                };
                            @endphp
                            <tr class="hover:bg-rose-50/30 transition-colors group bg-rose-50/10">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-4">
                                        {{-- Avatar Container --}}
                                        <div class="relative shrink-0 opacity-70 grayscale-[0.5]">
                                            <div class="size-9 rounded-full flex items-center justify-center border-2 border-white shadow-sm overflow-hidden {{ $avatarColors }}">
                                                @if($user->avatar_url)
                                                    <img src="{{ $user->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                                                @elseif($isSuperadmin)
                                                    <span class="material-symbols-outlined !text-[18px]">admin_panel_settings</span>
                                                @else
                                                    <span class="text-[11px] font-semibold uppercase">{{ $initials }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Info Text --}}
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 mb-0.5">
                                                <p class="text-[13px] font-bold text-rose-600 truncate tracking-tight line-through decoration-rose-300">
                                                    {{ $user->name }}
                                                </p>
                                                <span class="flex items-center gap-0.5 text-[9px] text-rose-600 font-bold bg-rose-50 border border-rose-100 px-1.5 py-0.5 rounded-md uppercase tracking-tighter">
                                                    <span class="material-symbols-outlined !text-[12px]">block</span>
                                                    Suspended
                                                </span>
                                            </div>
                                            <p class="text-[#6C757D] text-[11px] font-medium truncate leading-normal">
                                                {{ $user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex gap-1.5 flex-wrap">
                                        @forelse($user->roles as $role)
                                            @php
                                                $roleName = strtolower($role->name);
                                                $roleStyle = match(true) {
                                                    $roleName === 'superadmin' => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]',
                                                    $roleName === 'dosen'      => 'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]',
                                                    $roleName === 'mahasiswa'  => 'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]',
                                                    default                    => 'bg-[#F0F5FF] text-[#5E53F4] border-[#D1DFFF]',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border uppercase tracking-wider {{ $roleStyle }}">
                                                {{ str_replace('_', ' ', $role->name) }}
                                            </span>
                                        @empty
                                            <span class="text-[#ADB5BD] text-[10px] font-semibold italic uppercase tracking-tighter">No Role</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[12px] text-rose-500 font-bold max-w-xs line-clamp-2 leading-tight tracking-tight">
                                        {{ $user->suspension_reason ?? 'No specific reason provided' }}
                                    </p>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex flex-col items-start">
                                        <span class="text-[#1A1C1E] font-semibold text-[11px] uppercase tracking-tighter">Banned</span>
                                        <span class="text-[#6C757D] text-[10px] italic">
                                            {{ $user->suspended_at?->diffForHumans() ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <form method="POST" action="{{ route('superadmin.users.unsuspend', $user) }}">
                                            @csrf
                                            <button type="submit"
                                                class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-xl transition-all shadow-sm bg-white border border-emerald-100 active:scale-95" title="Unsuspend">
                                                <span class="material-symbols-outlined" style="font-size:18px">lock_open</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            {{-- Empty state tetap sama --}}
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $users->appends(request()->query())->links() }}
            </div>

        </div>
    </div>

    @include('superadmin.users._modal_force_logout')

    <script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    }
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) { modal.classList.add('hidden'); document.body.style.overflow = ''; }
    }
    </script>
</x-sidebar>
</x-app-layout>