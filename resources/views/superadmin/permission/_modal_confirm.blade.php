{{-- resources/views/superadmin/permission/_modal_confirm.blade.php --}}
<div id="superadminWarningModal" class="fixed inset-0 hidden z-[100] flex items-center justify-center p-4 bg-slate-900/60">
    <div class="fixed inset-0" onclick="closeSuperadminWarning()"></div>
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-red-100 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-red-100">
                <span class="material-symbols-outlined text-red-600 animate-pulse" style="font-size:32px">warning</span>
            </div>
            <h3 class="text-sm font-black text-red-800 uppercase tracking-tight">Peringatan Keamanan!</h3>
            <p class="text-[11px] text-slate-500 mt-2 leading-relaxed px-4">
                Anda mencoba memberikan akses <strong class="text-red-700">Superadmin</strong>. Role ini memiliki otoritas mutlak atas seluruh data dan pengaturan sistem.
            </p>
        </div>

        <div class="px-6 pb-6 space-y-4">
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 space-y-3">
                <label class="block text-[9px] font-black text-red-700 uppercase tracking-widest text-center">
                    Ketik "KONFIRMASI" untuk melanjutkan
                </label>
                <input type="text" id="confirmSuperadminText" placeholder="..." autocomplete="off"
                    class="w-full bg-white border border-red-200 rounded-lg px-3 py-2 text-xs font-bold text-red-700 text-center focus:ring-4 focus:ring-red-100 focus:border-red-400 outline-none transition-all uppercase">
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeSuperadminWarning()" 
                    class="flex-1 text-[10px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest py-3">
                    Batalkan
                </button>
                <button type="button" id="btnConfirmSuperadmin" disabled
                    class="flex-1 bg-slate-200 text-slate-400 cursor-not-allowed text-[10px] font-black uppercase tracking-widest py-3 rounded-xl transition-all shadow-sm">
                    Ya, Berikan Akses
                </button>
            </div>
        </div>
    </div>
</div>