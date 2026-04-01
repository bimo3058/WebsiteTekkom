<div id="modalForceLogout" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60" onclick="closeModal('modalForceLogout')"></div>
    
    <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-orange-50 border border-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-orange-600" style="font-size: 32px">logout</span>
            </div>
            
            <h3 class="text-lg font-bold text-slate-800 mb-2">Force Logout User</h3>
            <p class="text-sm text-slate-500 mb-6">
                Sesi login untuk <span id="logoutTargetName" class="font-bold text-slate-700"></span> akan dihentikan paksa.
            </p>

            {{-- Form tanpa ID/Action di awal --}}
            <form id="formForceLogout" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('modalForceLogout')"
                        class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-all shadow-md shadow-orange-200">
                        Ya, Logout Paksa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>