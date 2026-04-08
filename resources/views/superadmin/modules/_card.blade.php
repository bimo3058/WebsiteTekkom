<div class="bg-white border border-slate-200 rounded-xl overflow-hidden hover:border-purple-200 hover:shadow-md transition-all shadow-sm">
    <div class="p-4">
        {{-- Top Row: Icon + Toggle --}}
        <div class="flex justify-between items-start mb-4">
            <div class="bg-purple-50 p-2 rounded-lg border border-purple-100">
                <span class="material-symbols-outlined text-purple-600" style="font-size:20px">
                    {{ $module->icon ?? 'extension' }}
                </span>
            </div>

            <div class="flex flex-col items-end gap-1">
                <form action="{{ route('superadmin.modules.toggle', $module->slug) }}" method="POST">
                    @csrf
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" onchange="this.form.submit()" class="sr-only peer"
                               {{ $module->is_active ? 'checked' : '' }}>
                        <div class="w-8 h-4 bg-slate-200 rounded-full peer
                                    peer-checked:bg-purple-500
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full after:h-3 after:w-3
                                    after:transition-all peer-checked:after:translate-x-4
                                    transition-colors">
                        </div>
                    </label>
                </form>
                <span class="text-[9px] font-black uppercase tracking-tighter {{ $module->is_active ? 'text-purple-600' : 'text-slate-300' }}">
                    {{ $module->is_active ? 'Active' : 'Disabled' }}
                </span>
            </div>
        </div>

        {{-- Module Info --}}
        <h3 class="text-xs font-bold text-slate-800 mb-1 truncate">{{ $module->name }}</h3>
        <p class="text-slate-400 text-[10px] mb-4 leading-relaxed line-clamp-2 h-8">{{ $module->description }}</p>

        {{-- Meta Info --}}
        <div class="grid grid-cols-2 gap-2 mb-4">
            <div class="bg-slate-50/50 rounded-lg p-2 border border-slate-100">
                <p class="text-[8px] text-slate-400 font-black uppercase tracking-widest mb-0.5">Updated</p>
                <p class="text-slate-600 text-[10px] font-bold">{{ $module->updated_at->diffForHumans(null, true) }}</p>
            </div>
            <div class="bg-slate-50/50 rounded-lg p-2 border border-slate-100">
                <p class="text-[8px] text-slate-400 font-black uppercase tracking-widest mb-0.5">Health</p>
                <p class="text-purple-600 text-[10px] font-bold">Stable</p>
            </div>
        </div>

        {{-- Action Button --}}
        <button onclick="openModal('modal-{{ $module->slug }}')"
                class="w-full py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold transition-all flex items-center justify-center gap-2 text-[10px] uppercase tracking-wider">
            <span class="material-symbols-outlined" style="font-size:14px">settings</span>
            Manage
        </button>
    </div>
</div>