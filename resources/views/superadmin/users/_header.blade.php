<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">User Management</h1>
        <p class="text-slate-500 text-sm mt-0.5">Total {{ $total }} user terdaftar</p>
    </div>
    <div class="flex items-center gap-4">
        <button onclick="openModal('modalAddUser')"
            class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white font-medium px-5 py-2.5 rounded-lg transition-all shadow-sm">
            <span class="material-symbols-outlined" style="font-size:18px">person_add</span>
            Tambah User
        </button>
    </div>
</div>