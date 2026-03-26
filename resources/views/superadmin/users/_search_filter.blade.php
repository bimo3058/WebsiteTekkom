<div class="bg-white border border-slate-200 rounded-xl p-5 mb-8">
    <form method="GET" action="{{ route('superadmin.users.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-slate-600 text-xs font-medium uppercase tracking-wide mb-1.5">Cari (Nama atau Email)</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:18px">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Ketik nama atau email..."
                        class="w-full bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm">
                </div>
            </div>
            <div>
                <label class="block text-slate-600 text-xs font-medium uppercase tracking-wide mb-1.5">Filter Role</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:18px">badge</span>
                    <select name="role"
                        class="w-full bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm appearance-none">
                        <option value="all">Semua Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-slate-600 text-xs font-medium uppercase tracking-wide mb-1.5">Tampilkan</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:18px">list</span>
                    <select name="per_page"
                        class="w-full bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm appearance-none">
                        @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>
                            {{ $size }} data
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-end gap-2 md:col-span-2">
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-all text-sm shadow-sm">
                    <span class="material-symbols-outlined align-middle mr-1" style="font-size:16px">search</span>
                    Cari
                </button>
                <a href="{{ route('superadmin.users.index') }}"
                    class="flex-1 bg-white hover:bg-slate-50 text-slate-600 font-medium py-2 px-4 rounded-lg transition-all text-sm border border-slate-200 text-center">
                    <span class="material-symbols-outlined align-middle mr-1" style="font-size:16px">refresh</span>
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>