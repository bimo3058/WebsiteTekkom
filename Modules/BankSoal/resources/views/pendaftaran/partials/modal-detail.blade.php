<!-- Modal Detail Pendaftar -->
<div id="modal-detail" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 sm:p-6">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDetailModal()"></div>

    <!-- Modal Content -->
    <div class="relative w-full max-w-lg bg-white rounded-lg shadow-lg flex flex-col max-h-full z-10">
        
        <!-- Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detail Pendaftaran</h3>
            <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-500">
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

                <!-- Dosen Pembimbing -->
                <div class="flex items-start justify-between gap-4">
                    <span class="text-sm font-medium text-slate-500 shrink-0">Dosen Pembimbing 1</span>
                    <span id="detail-dosen1" class="text-sm font-semibold text-slate-800 text-right"></span>
                </div>

                <div class="flex items-start justify-between gap-4">
                    <span class="text-sm font-medium text-slate-500 shrink-0">Dosen Pembimbing 2</span>
                    <span id="detail-dosen2" class="text-sm font-semibold text-slate-800 text-right"></span>
                </div>

                <div class="h-px bg-slate-100"></div>

                <!-- Tanggal Daftar -->
                <div class="flex items-start justify-between gap-4">
                    <span class="text-sm font-medium text-slate-500 shrink-0">Tanggal Mendaftar</span>
                    <span id="detail-tanggal" class="text-sm text-slate-600 text-right"></span>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="px-6 py-4 flex items-center justify-end bg-gray-50 border-t border-gray-200 rounded-b-lg">
            <button
                type="button"
                onclick="closeDetailModal()"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none"
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
    document.getElementById('detail-dosen1').textContent = data.dosen1 || '-';
    document.getElementById('detail-dosen2').textContent = data.dosen2 || '-';
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
