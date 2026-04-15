<div class="mb-8 flex items-end justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-[#1A1C1E] tracking-tight">Audit Log System</h1>
        <p class="text-[#6C757D] text-[12px] mt-1 font-semibold">
            Total <span class="text-[#5E53F4] font-medium">{{ number_format($logs->total()) }}</span> aktivitas tercatat dalam sistem
        </p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openBulkDeleteModal()" 
            class="inline-flex items-center gap-2 bg-white hover:bg-rose-50 text-rose-600 font-medium px-5 py-2.5 rounded-xl transition-all text-[11px] border border-rose-100 shadow-sm uppercase tracking-widest active:scale-95">
            <span class="material-symbols-outlined" style="font-size:18px">delete_sweep</span>
            Hapus Massal
        </button>
    </div>
</div>