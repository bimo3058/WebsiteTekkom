{{-- MODAL: Suspend User --}}
<div id="modalSuspend" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-500">block</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Suspend User</h2>
                    <p class="text-xs text-slate-400">User akan otomatis di-logout</p>
                </div>
            </div>
            <button onclick="closeModal('modalSuspend')"
                class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition p-1.5 rounded-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="formSuspend" method="POST" action="">
            @csrf
            <div class="px-6 py-5">
                <p class="text-sm text-slate-600 mb-4">
                    Suspend user <strong id="suspendUserName" class="text-slate-800"></strong>?
                    User tidak akan bisa login sampai di-unsuspend.
                </p>
                <div class="space-y-1.5">
                    <label class="block text-slate-600 text-sm font-medium mb-1.5">
                        Alasan Suspend <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="reason" rows="3"
                        placeholder="Contoh: Pelanggaran kebijakan penggunaan..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 placeholder-slate-400 focus:border-red-300 focus:ring-2 focus:ring-red-100 outline-none transition text-sm resize-none"></textarea>
                </div>
            </div>
            <div class="flex gap-3 px-6 py-4 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalSuspend')"
                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium py-2.5 px-4 rounded-xl transition text-sm">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-4 rounded-xl transition text-sm shadow-sm">
                    Ya, Suspend
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    if (typeof window.openSuspendModal !== 'function') {
        window.openSuspendModal = function(data) {
            const form = document.getElementById('formSuspend');
            if(form) form.action = `/superadmin/users/${data.id}/suspend`;
            
            const nameEl = document.getElementById('suspendUserName');
            if(nameEl) nameEl.textContent = data.name;
            
            if(typeof window.openModal === 'function') window.openModal('modalSuspend');
        };
    }
</script>