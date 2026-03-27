{{-- MODAL: Edit Info User --}}
<div id="modalEditInfo" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 p-2.5 rounded-xl border border-blue-100">
                    <span class="material-symbols-outlined text-blue-600" style="font-size:20px">edit</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Edit Info User</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Ubah nama dan email user</p>
                </div>
            </div>
            <button onclick="closeModal('modalEditInfo')"
                class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition p-1.5 rounded-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="formEditInfo" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="px-6 py-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-slate-600 text-sm font-medium mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">badge</span>
                        <input type="text" name="name" id="editInfoName" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-slate-800 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-600 text-sm font-medium mb-1.5">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">mail</span>
                        <input type="email" name="email" id="editInfoEmail" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-slate-800 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm">
                    </div>
                </div>
            </div>
            <div class="flex gap-3 px-6 py-4 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditInfo')"
                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium py-2.5 px-4 rounded-xl transition text-sm">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-xl transition text-sm shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>