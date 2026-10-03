<div class="bs-heading-row">
    <div>
        <div class="bs-heading-label">
            <h1>Manajemen Bank Soal</h1>
            <span class="bs-role-badge">Dosen</span>
        </div>
        <p>Kelola soal, ajukan validasi, dan siapkan paket ujian.</p>
    </div>
    <div class="bs-heading-actions">
@can('banksoal.edit')
            <div class="relative" id="uploadDropdownContainer">
                <button type="button" onclick="toggleUploadDropdown()" class="bs-dropdown-trigger">
                    <i class="fas fa-upload text-slate-500"></i> Upload Soal <i class="fas fa-chevron-down text-[10px] ml-1 text-slate-400"></i>
                </button>

                <div id="uploadDropdownMenu" class="bs-dropdown-menu bs-upload-menu absolute right-0 transition-all duration-100 ease-out origin-top-right z-50 overflow-hidden transform opacity-0 scale-95 pointer-events-none">
                    <a href="{{ route('banksoal.soal.dosen.export-csv') }}" class="bs-dropdown-item">
                        <i class="fas fa-download w-5 text-center mr-2 text-emerald-500"></i> Unduh Template Excel
                    </a>
                    <div class="bs-dropdown-divider"></div>
                    <button type="button" onclick="openImportModal(); toggleUploadDropdown();" class="bs-dropdown-item">
                        <i class="fas fa-file-csv w-5 text-center mr-2 text-primary"></i> Import CSV
                    </button>
                </div>
            </div>

            <a href="{{ route('banksoal.soal.dosen.create') }}" class="dosen-management-btn">
                <i class="fas fa-plus"></i> Buat Soal
            </a>

            <button type="button" onclick="openAjukanModal()" class="dosen-management-btn dosen-management-btn-primary">
                <i class="fas fa-paper-plane"></i> Ajukan Soal
            </button>
        @else
            <div class="text-sm text-slate-500 italic">
                <i class="fas fa-info-circle"></i> Mode Lihat Saja — Anda tidak memiliki izin untuk menambah/mengubah soal
            </div>
        @endcan
    </div>
</div>
