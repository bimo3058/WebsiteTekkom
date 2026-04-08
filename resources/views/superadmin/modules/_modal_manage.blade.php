<div id="modal-{{ $module->slug }}" class="fixed inset-0 hidden items-center justify-center p-4 z-50">
    <div class="fixed inset-0 bg-slate-900/60 onclick="closeModal('modal-{{ $module->slug }}')"></div>

    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Header --}}
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 p-2 rounded-lg border border-blue-100">
                    <span class="material-symbols-outlined text-blue-600" style="font-size:18px">engineering</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Module Settings</h3>
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-tight">{{ $module->name }}</p>
                </div>
            </div>
            <button onclick="closeModal('modal-{{ $module->slug }}')" class="text-slate-400 hover:text-slate-700 transition-colors">
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
                               class="w-full bg-slate-50 border border-slate-200 rounded-lg text-slate-800 text-xs px-3 py-2 focus:border-blue-400 outline-none transition-all font-bold">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">System Slug</label>
                        <input type="text" value="{{ $module->slug }}" disabled
                               class="w-full bg-slate-100 border border-slate-200 rounded-lg text-slate-400 text-xs px-3 py-2 cursor-not-allowed italic">
                    </div>
                </div>

                {{-- Resource Gates --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest flex items-center gap-1.5">
                        <span class="material-symbols-outlined" style="font-size:14px">cloud_upload</span>
                        Resource Limits
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase">Max Upload (MB)</label>
                            <input type="number" name="settings[max_upload]" value="{{ $module->settings['max_upload'] ?? 10 }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-lg text-xs px-3 py-2 focus:border-blue-400 outline-none transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase">Quota (GB)</label>
                            <input type="number" name="settings[quota]" value="{{ $module->settings['quota'] ?? 5 }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-lg text-xs px-3 py-2 focus:border-blue-400 outline-none transition-all">
                        </div>
                    </div>
                </div>

                {{-- Flags --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Advanced Config</p>
                    <div class="grid grid-cols-1 gap-2">
                        <label class="flex items-center gap-3 p-2.5 bg-slate-50 border border-slate-200 rounded-lg cursor-pointer hover:border-blue-200 transition-all">
                            <input type="checkbox" name="settings[debug_mode]" {{ ($module->settings['debug_mode'] ?? false) ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600">
                            <div>
                                <span class="text-[11px] font-bold text-slate-700 block leading-none">Debug Mode</span>
                                <span class="text-[9px] text-slate-400">Log technical exceptions & slow queries.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex gap-2">
                    <button type="button" class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors" title="Purge Cache">
                        <span class="material-symbols-outlined" style="font-size:18px">broom</span>
                    </button>
                    <button type="button" class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors" title="Optimize Database">
                        <span class="material-symbols-outlined" style="font-size:18px">database</span>
                    </button>
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-black text-white text-[11px] font-black px-4 py-1.5 rounded-lg transition-all uppercase tracking-widest shadow-sm">
                    Save Config
                </button>
            </div>
        </form>
    </div>
</div>