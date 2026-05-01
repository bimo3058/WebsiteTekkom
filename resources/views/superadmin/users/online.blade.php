<x-app-layout>
<x-sidebar :user="auth()->user()">
<div class="min-h-screen bg-[#F8F9FA] p-6">
<div class="max-w-full mx-auto">

    {{-- Header --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-base font-bold text-slate-800">User Online</h1>
            <p class="text-slate-400 text-xs mt-0.5">{{ $users->total() }} user sedang aktif</p>
        </div>

        <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap items-center gap-2">
            <select name="per_page" onchange="this.form.submit()"
                class="text-xs border border-slate-200 bg-white rounded-lg px-3 py-2 text-slate-600 outline-none focus:border-slate-400 cursor-pointer">
                @foreach(['10','25','50','100'] as $n)
                    <option value="{{ $n }}" {{ request('per_page','10') == $n ? 'selected' : '' }}>{{ $n }} baris</option>
                @endforeach
            </select>

            <select name="role" onchange="this.form.submit()"
                class="text-xs border border-slate-200 bg-white rounded-lg px-3 py-2 text-slate-600 outline-none focus:border-slate-400 cursor-pointer">
                <option value="all" {{ request('role','all') == 'all' ? 'selected' : '' }}>Semua Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>

            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama atau email..."
                    class="text-xs border border-slate-200 rounded-lg pl-8 pr-3 py-2 w-52 outline-none focus:border-slate-400">
                <span class="material-symbols-outlined text-slate-300 absolute left-2.5 top-2" style="font-size:16px">search</span>
            </div>

            <button type="submit"
                class="text-xs bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                Filter
            </button>

            <a href="{{ route('superadmin.users.index') }}"
                class="text-xs text-slate-400 hover:text-slate-600 transition-colors">
                Kembali
            </a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-slate-400 uppercase tracking-widest">User</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Role</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Login</th>
                    <th class="px-5 py-3 text-center text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($users as $user)
                @php
                    $isMe        = $user->id === auth()->id();
                    $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
                    $initials    = strtoupper(substr($user->name, 0, 2));
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors group">

                    {{-- User --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="relative shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold
                                    {{ $isSuperadmin ? 'bg-violet-100 text-violet-600' : 'bg-slate-100 text-slate-500' }} overflow-hidden">
                                    @if($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                                    @else
                                        {{ $initials }}
                                    @endif
                                </div>
                                <span class="absolute bottom-0 right-0 w-2 h-2 bg-emerald-400 border-2 border-white rounded-full"></span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-[12.5px] font-semibold text-slate-800 truncate">{{ $user->name }}</p>
                                    @if($isMe)
                                        <span class="text-[9px] font-bold text-violet-500 bg-violet-50 px-1.5 py-0.5 rounded">YOU</span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-400 truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Role --}}
                    <td class="px-5 py-3.5">
                        @forelse($user->roles as $role)
                        @php
                            $rs = match(strtolower($role->name)) {
                                'superadmin' => 'bg-violet-50 text-violet-600',
                                'dosen'      => 'bg-emerald-50 text-emerald-600',
                                'mahasiswa'  => 'bg-amber-50 text-amber-600',
                                default      => 'bg-slate-100 text-slate-500',
                            };
                        @endphp
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md {{ $rs }}">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </span>
                        @empty
                            <span class="text-[10px] text-slate-300 italic">—</span>
                        @endforelse
                    </td>

                    {{-- Login --}}
                    <td class="px-5 py-3.5">
                        <p class="text-[11px] font-medium text-emerald-500">Aktif sekarang</p>
                        <p class="text-[10px] text-slate-400">{{ $user->last_login?->diffForHumans() ?? 'Baru saja' }}</p>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-5 py-3.5 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="openForceLogoutModal({ id: '{{ $user->id }}', name: '{{ $user->name }}' })"
                                class="p-1.5 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all" title="Force Logout">
                                <span class="material-symbols-outlined" style="font-size:16px">logout</span>
                            </button>
                            @if(!$isSuperadmin && !$isMe)
                            <button onclick="openSuspendModal({ id: '{{ $user->id }}', name: '{{ $user->name }}' })"
                                class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all" title="Suspend">
                                <span class="material-symbols-outlined" style="font-size:16px">block</span>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-16 text-center">
                        <p class="text-sm text-slate-300 font-medium">Tidak ada user online saat ini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $users->appends(request()->query())->links() }}
    </div>

</div>
</div>

@include('superadmin.users._modal_force_logout')
@include('superadmin.users._modal_suspend')

<script>
function openModal(id) {
    document.getElementById(id)?.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id)?.classList.add('hidden');
    document.body.style.overflow = '';
}
</script>
</x-sidebar>
</x-app-layout>