<div id="modal-{{ $module->slug }}" class="fixed inset-0 hidden items-center justify-center p-4 z-50">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="closeModal('modal-{{ $module->slug }}')"></div>

    {{-- Modal --}}
    <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-xl overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 p-2.5 rounded-xl border border-blue-100">
                    <span class="material-symbols-outlined text-blue-600" style="font-size:20px">engineering</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Konfigurasi Modul</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $module->name }}</p>
                </div>
            </div>
            <button onclick="closeModal('modal-{{ $module->slug }}')"
                    class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors p-1.5 rounded-lg">
                <span class="material-symbols-outlined" style="font-size:20px">close</span>
            </button>
        </div>

        <form action="{{ route('superadmin.modules.update-config', $module->slug) }}" method="POST">
            @csrf
            <div class="p-6 space-y-6">

                {{-- Identity --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-600">Nama Modul</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-slate-400" style="font-size:16px">label</span>
                            <input type="text" name="name" value="{{ $module->name }}"
                                   class="w-full bg-white border border-slate-200 rounded-lg text-slate-800 text-sm pl-9 pr-3 py-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-600">Slug Sistem</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-slate-300" style="font-size:16px">fingerprint</span>
                            <input type="text" value="{{ $module->slug }}" disabled
                                   class="w-full bg-slate-50 border border-slate-200 rounded-lg text-slate-400 text-sm pl-9 pr-3 py-2.5 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                {{-- Resource Gates --}}
                <div class="space-y-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-slate-400" style="font-size:14px">cloud_upload</span>
                        Batas Resource
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-600">Maks. Upload (MB)</label>
                            <input type="number" name="settings[max_upload]"
                                   value="{{ $module->settings['max_upload'] ?? 10 }}"
                                   class="w-full bg-white border border-slate-200 rounded-lg text-slate-800 text-sm px-3 py-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-600">Kuota Penyimpanan (GB)</label>
                            <input type="number" name="settings[quota]"
                                   value="{{ $module->settings['quota'] ?? 5 }}"
                                   class="w-full bg-white border border-slate-200 rounded-lg text-slate-800 text-sm px-3 py-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        </div>
                    </div>
                </div>

                {{-- Flags --}}
                <div class="space-y-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pengaturan Lanjutan</p>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all">
                            <input type="checkbox" name="settings[debug_mode]"
                                   {{ ($module->settings['debug_mode'] ?? false) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-slate-700 block">Mode Debug</span>
                                <span class="text-xs text-slate-400">Catat query lambat dan exception teknis.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all">
                            <input type="checkbox" name="settings[auto_prune]"
                                   {{ ($module->settings['auto_prune'] ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-slate-700 block">Hapus Log Otomatis</span>
                                <span class="text-xs text-slate-400">Bersihkan log lebih dari 90 hari secara otomatis.</span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between gap-3">
                <div class="flex gap-2">
                    <button type="button"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-700 bg-white border border-slate-200 hover:border-slate-300 px-3.5 py-2 rounded-lg transition-all">
                        <span class="material-symbols-outlined" style="font-size:14px">broom</span>
                        Purge Cache
                    </button>
                    <button type="button"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-700 bg-white border border-slate-200 hover:border-slate-300 px-3.5 py-2 rounded-lg transition-all">
                        <span class="material-symbols-outlined" style="font-size:14px">database</span>
                        Optimize
                    </button>
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm transition-colors shadow-sm">
                    <span class="material-symbols-outlined" style="font-size:16px">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>