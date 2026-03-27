<div class="bg-white border border-slate-200 rounded-xl p-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('superadmin.users.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-4">
                <label class="block text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1.5">Pencarian</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama atau email..."
                        class="w-full bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-1.5 text-slate-800 placeholder-slate-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-xs">
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="block text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1.5">Filter Role</label>
                <select name="role" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-800 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-xs appearance-none">
                    <option value="all">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1.5">Limit</label>
                <select name="per_page" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-800 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-xs appearance-none">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>{{ $size }} Baris</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3 flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-3 rounded-lg transition-all text-[11px] shadow-sm">
                    Filter
                </button>
                <a href="{{ route('superadmin.users.index') }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-all">
                    <span class="material-symbols-outlined" style="font-size:18px">refresh</span>
                </a>
            </div>
        </div>
    </form>
</div>