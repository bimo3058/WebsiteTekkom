{{-- Modal Bulk Delete --}}
<div id="modalBulkDeleteAudit" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60" onclick="closeModal('modalBulkDeleteAudit')"></div>
    
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-red-50 p-2.5 rounded-xl border border-red-100">
                    <span class="material-symbols-outlined text-red-600" style="font-size:20px">delete_sweep</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Hapus Log Aktivitas</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pilih kriteria log yang akan dihapus</p>
                </div>
            </div>
            <button onclick="closeModal('modalBulkDeleteAudit')" class="text-slate-400 hover:text-slate-700 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Form --}}
        <form id="formBulkDeleteAudit" action="{{ route('superadmin.audit-logs.bulk-destroy') }}" method="POST" class="p-6">
            @csrf
            @method('DELETE')
            
            {{-- Hidden input untuk menyimpan IDs --}}
            <div id="selectedIdsContainer"></div>
            
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-3">Pilih metode penghapusan:</label>
                
                {{-- Opsi 1: Hapus berdasarkan waktu --}}
                <div class="space-y-3 mb-4">
                    <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                        <input type="radio" name="delete_type" value="6hours" class="w-4 h-4 text-red-600 focus:ring-red-500">
                        <div class="flex-1">
                            <span class="font-semibold text-sm text-slate-700">Log lebih dari 6 jam yang lalu</span>
                            <p class="text-[10px] text-slate-400 mt-0.5">Menghapus semua log yang berumur lebih dari 6 jam</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                        <input type="radio" name="delete_type" value="12hours" class="w-4 h-4 text-red-600 focus:ring-red-500">
                        <div class="flex-1">
                            <span class="font-semibold text-sm text-slate-700">Log lebih dari 12 jam yang lalu</span>
                            <p class="text-[10px] text-slate-400 mt-0.5">Menghapus semua log yang berumur lebih dari 12 jam</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                        <input type="radio" name="delete_type" value="24hours" class="w-4 h-4 text-red-600 focus:ring-red-500">
                        <div class="flex-1">
                            <span class="font-semibold text-sm text-slate-700">Log lebih dari 24 jam yang lalu</span>
                            <p class="text-[10px] text-slate-400 mt-0.5">Menghapus semua log yang berumur lebih dari 24 jam</p>
                        </div>
                    </label>
                </div>
                
                <div class="relative my-3">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-2 text-slate-400">ATAU</span>
                    </div>
                </div>
                
                {{-- Opsi 2: Hapus yang dipilih --}}
                <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                    <input type="radio" name="delete_type" value="selected" class="w-4 h-4 text-red-600 focus:ring-red-500" id="deleteSelectedRadio">
                    <div class="flex-1">
                        <span class="font-semibold text-sm text-slate-700">Hanya log yang dipilih</span>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            Menghapus <span id="selectedCountText" class="font-bold text-red-600">0</span> log yang Anda centang
                        </p>
                    </div>
                </label>
            </div>

            {{-- Peringatan --}}
            <div class="bg-amber-50 rounded-xl p-4 mb-6 border border-amber-100">
                <div class="flex gap-3">
                    <span class="material-symbols-outlined text-amber-600" style="font-size:18px">warning</span>
                    <div class="text-xs text-amber-700 leading-relaxed">
                        <strong>Peringatan:</strong> Data log yang dihapus tidak dapat dikembalikan. Pastikan Anda yakin dengan pilihan ini.
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-2">
                <button type="button" onclick="closeModal('modalBulkDeleteAudit')"
                    class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-all">
                    Batal
                </button>
                <button type="submit" id="btnConfirmDelete"
                    class="flex-1 px-4 py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-all shadow-md shadow-red-200 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:20px">delete_forever</span>
                    Hapus Log
                </button>
            </div>
        </form>
    </div>
</div>