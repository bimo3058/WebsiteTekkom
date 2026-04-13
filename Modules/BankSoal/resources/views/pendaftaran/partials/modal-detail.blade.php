<!-- Modal Detail Pendaftar -->
<div id="modal-detail" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 sm:p-6">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDetailModal()"></div>

    <!-- Modal Content -->
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl flex flex-col max-h-full z-10">
        
        <!-- Header -->
        <div class="px-6 py-5 flex items-center justify-between border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 tracking-tight">Detail Pendaftaran</h3>
            </div>
            <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-6 overflow-y-auto space-y-5">
            
            <!-- Status Badge -->
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Pendaftaran</span>
                <span id="detail-status" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"></span>
            </div>

            <div class="h-px bg-slate-100"></div>

            <!-- Data Fields -->
            <div class="space-y-4">
                <!-- NIM -->
                <div class="flex items-start justify-between gap-4">
                    <span class="text-sm font-medium text-slate-500 shrink-0">NIM</span>
                    <span id="detail-nim" class="text-sm font-semibold text-slate-800 text-right font-mono"></span>
                </div>

                <!-- Nama -->
                <div class="flex items-start justify-between gap-4">
                    <span class="text-sm font-medium text-slate-500 shrink-0">Nama Lengkap</span>
                    <span id="detail-nama" class="text-sm font-semibold text-slate-800 text-right"></span>
                </div>

                <!-- Semester -->
                <div class="flex items-start justify-between gap-4">
                    <span class="text-sm font-medium text-slate-500 shrink-0">Semester Aktif</span>
                    <span id="detail-semester" class="text-sm font-semibold text-slate-800 text-right"></span>
                </div>

                <!-- Target Wisuda -->
                <div class="flex items-start justify-between gap-4">
                    <span class="text-sm font-medium text-slate-500 shrink-0">Target Wisuda</span>
                    <span id="detail-wisuda" class="text-sm font-semibold text-slate-800 text-right"></span>
                </div>

                <div class="h-px bg-slate-100"></div>

                <!-- Catatan Admin -->
                <div class="flex items-start justify-between gap-4">
                    <span class="text-sm font-medium text-slate-500 shrink-0">Catatan Admin</span>
                    <span id="detail-catatan" class="text-sm text-slate-600 text-right"></span>
                </div>

                <!-- Tanggal Daftar -->
                <div class="flex items-start justify-between gap-4">
                    <span class="text-sm font-medium text-slate-500 shrink-0">Tanggal Mendaftar</span>
                    <span id="detail-tanggal" class="text-sm text-slate-600 text-right"></span>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="px-6 py-4 flex items-center justify-end rounded-b-2xl bg-white border-t border-slate-100">
            <button
                type="button"
                onclick="closeDetailModal()"
                class="px-5 py-2.5 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 focus:outline-none transition-colors"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function openDetailModal(data) {
    // Populate fields
    document.getElementById('detail-nim').textContent = data.nim;
    document.getElementById('detail-nama').textContent = data.nama;
    document.getElementById('detail-semester').textContent = 'Semester ' + data.semester;
    document.getElementById('detail-wisuda').textContent = data.target_wisuda;
    document.getElementById('detail-catatan').textContent = data.catatan;
    document.getElementById('detail-tanggal').textContent = data.tanggal;

    // Status badge
    const statusEl = document.getElementById('detail-status');
    if (data.status === 'approved') {
        statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700';
        statusEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Disetujui';
    } else if (data.status === 'rejected') {
        statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700';
        statusEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak';
    } else {
        statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700';
        statusEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending';
    }

    document.getElementById('modal-detail').classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('modal-detail').classList.add('hidden');
}
</script>
