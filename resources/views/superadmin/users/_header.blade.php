<div class="mb-5 flex items-center justify-between">
    <div>
        <h1 class="text-base font-semibold text-[#1A1B25]">User Management</h1>
        <p class="text-[11px] text-[#A4ABB8] mt-0.5">
            Total <span class="text-[#6B39F4] font-semibold">{{ $total }}</span> pengguna terdaftar
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('superadmin.permissions') }}"
           class="inline-flex items-center gap-1.5 bg-white hover:bg-[#F8F9FB] text-[#666D80] text-[11px] font-medium px-3 py-2 rounded-lg border border-[#DFE1E6] transition-all">
            <span class="material-symbols-outlined" style="font-size:15px">shield_person</span>
            Permissions
        </a>
        <button onclick="openModal('modalImportUser')"
            class="inline-flex items-center gap-1.5 bg-white hover:bg-[#F8F9FB] text-[#666D80] text-[11px] font-medium px-3 py-2 rounded-lg border border-[#DFE1E6] transition-all">
            <span class="material-symbols-outlined" style="font-size:15px">upload_file</span>
            Import CSV
        </button>
        <button onclick="openModal('modalAddUser')"
            class="inline-flex items-center gap-1.5 bg-[#1A1B25] hover:bg-[#0D0D12] text-white text-[11px] font-medium px-3 py-2 rounded-lg transition-all">
            <span class="material-symbols-outlined" style="font-size:15px">person_add</span>
            Tambah User
        </button>
    </div>
</div>