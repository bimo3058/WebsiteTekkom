<div id="modal-{{ $module->slug }}" class="fixed inset-0 hidden items-center justify-center p-4 z-50">
    <div class="fixed inset-0 bg-slate-900/60" onclick="closeModal('modal-{{ $module->slug }}')"></div>

    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Header --}}
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between bg-purple-50/30">
            <div class="flex items-center gap-3">
                <div class="bg-purple-50 p-2 rounded-lg border border-purple-100">
                    <span class="material-symbols-outlined text-purple-600" style="font-size:18px">engineering</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Module Settings</h3>
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-tight">{{ $module->name }}</p>
                </div>
            </div>
            <button onclick="closeModal('modal-{{ $module->slug }}')" class="text-slate-400 hover:text-purple-600 transition-colors">
                <span class="material-symbols-outlined" style="font-size:20px">close</span>
            </button>
        </div>

        <form action="{{ route('superadmin.modules.update-config', $module->slug) }}" method="POST">
            @csrf
            <div class="p-5 space-y-5">
                {{-- Identity --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Modul</label>
                        <input type="text" name="name" value="{{ $module->name }}"
                               class="w-full bg-slate-50 border border-slate-200 rounded-lg text-slate-800 text-xs px-3 py-2 focus:border-purple-400 focus:ring-1 focus:ring-purple-400 outline-none transition-all font-bold">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">System Slug</label>
                        <input type="text" value="{{ $module->slug }}" disabled
                               class="w-full bg-slate-100 border border-slate-200 rounded-lg text-slate-400 text-xs px-3 py-2 cursor-not-allowed italic">
                    </div>
                </div>

                {{-- Resource Limits --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-black text-purple-600 uppercase tracking-widest flex items-center gap-1.5">
                        <span class="material-symbols-outlined" style="font-size:14px">cloud_upload</span>
                        Resource Limits
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase">Max Upload (MB)</label>
                            <input type="number" name="settings[max_upload]" value="{{ $module->setting('max_upload', 10) }}"
                                   min="1" max="100"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-lg text-xs px-3 py-2 focus:border-purple-400 focus:ring-1 focus:ring-purple-400 outline-none transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase">Quota (GB)</label>
                            <input type="number" name="settings[quota]" value="{{ $module->setting('quota', 5) }}"
                                   min="1" max="500"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-lg text-xs px-3 py-2 focus:border-purple-400 focus:ring-1 focus:ring-purple-400 outline-none transition-all">
                        </div>
                    </div>
                </div>

                {{-- Advanced Config --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Advanced Config</p>
                    <label class="flex items-center gap-3 p-2.5 bg-slate-50 border border-slate-200 rounded-lg cursor-pointer hover:border-purple-200 transition-all">
                        <input type="checkbox" name="settings[debug_mode]" value="1"
                               {{ $module->setting('debug_mode', false) ? 'checked' : '' }}
                               class="w-3.5 h-3.5 rounded border-slate-300 text-purple-600 focus:ring-purple-400">
                        <div>
                            <span class="text-[11px] font-bold text-slate-700 block leading-none">Debug Mode</span>
                            <span class="text-[9px] text-slate-400">Log exceptions & slow queries untuk modul ini.</span>
                        </div>
                    </label>

                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="material-symbols-outlined {{ $module->is_active ? 'text-green-500' : 'text-yellow-500' }}" style="font-size:16px">
                            {{ $module->is_active ? 'check_circle' : 'warning' }}
                        </span>
                        <div class="flex-1">
                            <span class="text-[11px] font-bold text-slate-700 block leading-none">Status Modul</span>
                            <span class="text-[9px] text-slate-400">Gunakan toggle di card untuk mengubah status.</span>
                        </div>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full {{ $module->is_active ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $module->is_active ? 'Active' : 'Maintenance' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 py-3 border-t border-slate-100 bg-purple-50/20 flex items-center justify-between">
                <button type="button"
                        onclick="if(confirm('Bersihkan cache modul {{ $module->name }}?')) document.getElementById('purge-{{ $module->slug }}').submit();"
                        class="p-1.5 text-slate-400 hover:text-purple-600 transition-colors rounded-lg hover:bg-purple-50"
                        title="Purge Cache">
                    <span class="material-symbols-outlined" style="font-size:18px">mop</span>
                </button>
                <button type="submit"
                        class="bg-purple-600 hover:bg-purple-700 text-white text-[11px] font-black px-4 py-1.5 rounded-lg transition-all uppercase tracking-widest shadow-sm">
                    Save Config
                </button>
            </div>
        </form>

        <form id="purge-{{ $module->slug }}" action="{{ route('superadmin.modules.purge-cache', $module->slug) }}" method="POST" class="hidden">@csrf</form>
    </div>
</div>