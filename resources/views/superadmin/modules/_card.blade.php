<div class="bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-md hover:border-slate-300 transition-all">
    <div class="p-6">

        {{-- Top Row: Icon + Toggle --}}
        <div class="flex justify-between items-start mb-5">
            <div class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                <span class="material-symbols-outlined text-blue-600" style="font-size:22px">
                    {{ $module->icon ?? 'extension' }}
                </span>
            </div>

            <div class="flex flex-col items-end gap-1.5">
                <form action="{{ route('superadmin.modules.toggle', $module->slug) }}" method="POST">
                    @csrf
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" onchange="this.form.submit()" class="sr-only peer"
                               {{ $module->is_active ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-slate-200 rounded-full peer
                                    peer-checked:bg-emerald-500
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full after:h-4 after:w-4
                                    after:transition-all peer-checked:after:translate-x-5
                                    transition-colors">
                        </div>
                    </label>
                </form>
                <span class="text-[11px] font-semibold {{ $module->is_active ? 'text-emerald-600' : 'text-slate-400' }}">
                    {{ $module->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        {{-- Module Info --}}
        <h3 class="text-base font-semibold text-slate-800 mb-1">{{ $module->name }}</h3>
        <p class="text-slate-500 text-sm mb-5 leading-relaxed line-clamp-2">{{ $module->description }}</p>

        {{-- Meta Info --}}
        <div class="grid grid-cols-2 gap-3 mb-5">
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wide mb-0.5">Diperbarui</p>
                <p class="text-slate-700 text-xs font-semibold">{{ $module->updated_at->diffForHumans() }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wide mb-0.5">Status Resource</p>
                <p class="text-emerald-600 text-xs font-semibold">Normal</p>
            </div>
        </div>

        {{-- Action Button --}}
        <button onclick="openModal('modal-{{ $module->slug }}')"
                class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2 text-sm">
            <span class="material-symbols-outlined" style="font-size:16px">settings</span>
            Konfigurasi
        </button>
    </div>
</div>