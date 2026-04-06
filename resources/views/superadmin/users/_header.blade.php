<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-semibold text-slate-800 tracking-tight">User Management</h1>
        <p class="text-slate-500 text-[11px] mt-0.5 font-medium">Total <span class="text-blue-600">{{ $total }}</span> user terdaftar dalam sistem</p>
    </div>
    <div class="flex items-center gap-3">
        {{-- Tombol Permissions --}}
        <a href="{{ route('superadmin.permissions') }}"
           class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 font-semibold px-3 py-1.5 rounded-lg transition-all text-[11px] border border-slate-200 shadow-sm">
            <span class="material-symbols-outlined" style="font-size:16px">shield_person</span>
            Permissions
        </a>

        {{-- Tombol Import CSV (Sudah Diselaraskan) --}}
        <button onclick="openModal('modalImportUser')" 
            class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold px-3 py-1.5 rounded-lg transition-all text-[11px] border border-slate-200 shadow-sm">
            <span class="material-symbols-outlined" style="font-size:16px">upload_file</span>
            Input User Massal
        </button>

        {{-- Tombol Tambah User --}}
        <button onclick="openModal('modalAddUser')"
            class="inline-flex items-center gap-2 bg-slate-800 hover:bg-black text-white font-semibold px-3 py-1.5 rounded-lg transition-all shadow-sm text-[11px]">
            <span class="material-symbols-outlined" style="font-size:16px">person_add</span>
            Tambah User
        </button>
    </div>
</div>