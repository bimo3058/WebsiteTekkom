<div id="modalDeleteHybrid" class="hidden fixed inset-0 z-[60] overflow-y-auto bg-slate-900/40">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
            <form id="formDeleteHybrid" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="p-6">
                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-red-600" style="font-size: 20px">delete_forever</span>
                    </div>
                    
                    <h3 class="text-slate-800 font-bold text-base mb-1">Hapus Pengguna?</h3>
                    <p class="text-slate-500 text-xs mb-6">Pilih metode penghapusan untuk <span id="deleteTargetName" class="font-bold text-slate-700"></span></p>

                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50 group">
                            <input type="radio" name="delete_type" value="soft" checked class="mt-1 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight">Hapus Biasa (Safe)</span>
                                <span class="block text-[10px] text-slate-500">User tidak bisa login, tapi data tetap tersimpan di database.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:bg-red-50 cursor-pointer transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50/80 group">
                            <input type="radio" name="delete_type" value="permanent" class="mt-1 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="block text-[11px] font-bold text-red-700 uppercase tracking-tight">Hapus Permanen (Hard)</span>
                                <span class="block text-[10px] text-slate-500">Data dihapus total dari database dan tidak bisa dikembalikan.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2 rounded-b-2xl">
                    <button type="button" onclick="closeModal('modalDeleteHybrid')" 
                        class="text-slate-500 text-[11px] font-bold uppercase px-4 py-2 hover:bg-slate-200 rounded-lg transition-all">
                        Batal
                    </button>
                    <button type="submit" 
                        class="bg-red-600 text-white text-[11px] font-bold uppercase px-4 py-2 rounded-lg hover:bg-red-700 transition-all shadow-sm flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size: 14px">check</span>
                        Eksekusi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>