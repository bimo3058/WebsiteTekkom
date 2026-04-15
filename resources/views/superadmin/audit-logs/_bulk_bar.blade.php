<div id="bulkActionBar" class="hidden mb-6 p-4 bg-[#1A1C1E] rounded-2xl flex items-center justify-between animate-in fade-in slide-in-from-top-2 duration-300 shadow-xl border border-slate-800">
    <div class="flex items-center gap-4 ml-2">
        <div class="flex items-center justify-center w-8 h-8 bg-[#5E53F4] rounded-lg shadow-lg shadow-[#5E53F4]/20">
            <span class="material-symbols-outlined text-white" style="font-size: 18px">check_circle</span>
        </div>
        <span class="text-[13px] font-medium text-white tracking-wide">
            <span id="selectedCount" class="text-[#D1BFFF] text-base mr-1">0</span> Logs Selected
        </span>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openBulkDeleteModal()" class="flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white text-[11px] font-medium uppercase tracking-widest px-5 py-2.5 rounded-xl transition-all shadow-sm active:scale-95 group">
            <span class="material-symbols-outlined group-hover:rotate-12 transition-transform" style="font-size: 18px">delete_sweep</span>
            Hapus Terpilih
        </button>
        <div class="w-px h-5 bg-slate-700 mx-1"></div>
        <button onclick="deselectAll()" class="text-slate-400 hover:text-white text-[11px] font-medium uppercase tracking-widest px-4 transition-colors">
            Batal
        </button>
    </div>
</div>