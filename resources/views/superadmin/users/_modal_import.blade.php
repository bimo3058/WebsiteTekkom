{{-- MODAL: Bulk Import User --}}
<div id="modalImportUser" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60" onclick="closeModal('modalImportUser')"></div>

    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-[#E8E6FD] flex items-center justify-between bg-[#F1E9FF]">
            <div class="flex items-center gap-3">
                <div class="bg-[#5E53F4] p-2.5 rounded-xl">
                    <span class="material-symbols-outlined text-white" style="font-size:20px">database</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-[#1A1C1E]">Bulk Import User</h3>
                    <p class="text-xs text-[#5E53F4] mt-0.5">Upload file ke bucket: <span class="font-bold">data_user</span></p>
                </div>
            </div>
            <button onclick="closeModal('modalImportUser')" class="text-[#5E53F4] hover:text-[#4e44e0] hover:bg-[#E8E6FD] transition-colors p-1.5 rounded-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Form --}}
        <form id="formImportUser" action="{{ route('superadmin.users.bulkImport') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            {{-- Error: Validasi biasa --}}
            <div id="importErrorContainer" class="hidden mb-4 p-3 bg-red-50 border border-red-100 rounded-xl flex gap-3 items-start">
                <span class="material-symbols-outlined text-red-500 shrink-0" style="font-size:18px">error</span>
                <div id="importErrorMessage" class="text-xs text-red-600 leading-relaxed font-medium"></div>
            </div>

            {{-- Error: Duplikasi user --}}
            <div id="importDuplicateContainer" class="hidden mb-4 border border-amber-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-amber-50 border-b border-amber-100 flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-amber-600 shrink-0" style="font-size:18px">warning</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-semibold text-amber-800">Data duplikat ditemukan</p>
                        <p id="importDuplicateSubtitle" class="text-[11px] text-amber-600 mt-0.5"></p>
                    </div>
                </div>
                {{-- List duplikat --}}
                <div id="importDuplicateList" class="bg-white divide-y divide-slate-50 max-h-48 overflow-y-auto"></div>
                <div class="px-4 py-3 bg-amber-50/50 border-t border-amber-100">
                    <p class="text-[11px] text-amber-700 font-medium">Upload dibatalkan karena data di atas sudah ada di sistem. Hapus baris tersebut dari file CSV dan coba lagi.</p>
                </div>
            </div>

            {{-- File Input --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File CSV</label>
                <div class="relative group">
                    <input type="file" name="file" id="importFileInput" accept=".csv,.txt" required
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl p-1 group-hover:border-blue-400 transition-all">
                </div>
                <p class="mt-2 text-xs text-slate-500">
                    Format: <code class="bg-slate-100 px-1 rounded">.csv</code> atau <code class="bg-slate-100 px-1 rounded">.txt</code> (Maks. 10MB)
                </p>
            </div>

            {{-- Info Format --}}
            <div class="bg-amber-50 rounded-xl p-4 mb-6 border border-amber-100">
                <div class="flex gap-3">
                    <span class="material-symbols-outlined text-amber-600 shrink-0" style="font-size:18px">warning</span>
                    <div class="text-xs text-amber-700 leading-relaxed">
                        <strong>Penting:</strong><br>
                        - Gunakan pemisah koma (,)<br>
                        - Nama Role harus sesuai dengan di sistem (dosen, mahasiswa, dll)<br>
                        - Jika email sudah ada, upload akan ditolak otomatis.
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-2">
                <button type="button" onclick="confirmCancelImportModal()"
                    class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-all text-sm">
                    Batal
                </button>
                <button type="submit" id="btnSubmitImport"
                    class="flex-1 px-4 py-2.5 bg-[#5E53F4] hover:bg-[#4e44e0] text-white font-semibold rounded-xl transition text-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:20px">upload</span>
                    Mulai Impor
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Konfirmasi Batal Import (extend dari modal suspend, beda warna & copy) --}}
<div id="modalCancelImport" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-slate-500">cancel</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Batalkan Import?</h2>
                    <p class="text-xs text-slate-400">Formulir akan direset</p>
                </div>
            </div>
            <button onclick="closeModal('modalCancelImport')"
                class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition p-1.5 rounded-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm text-slate-600 leading-relaxed">
                File yang sudah dipilih akan dihapus dan form dikosongkan. Proses import tidak akan dimulai.
            </p>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t border-slate-100">
            <button type="button" onclick="closeModal('modalCancelImport')"
                class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium py-2.5 px-4 rounded-xl transition text-sm">
                Kembali
            </button>
            <button type="button" onclick="doResetImportForm()"
                class="flex-1 bg-slate-700 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-xl transition text-sm shadow-sm">
                Ya, Batalkan
            </button>
        </div>
    </div>
</div>

{{-- MODAL: Konfirmasi Hentikan Progress Import --}}
<div id="modalCancelProgress" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl border border-slate-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#F1E9FF] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#5E53F4]">cancel</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Hentikan Import?</h2>
                    <p class="text-xs text-slate-400">Proses yang berjalan akan dihentikan</p>
                </div>
            </div>
            <button onclick="closeModal('modalCancelProgress')"
                class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition p-1.5 rounded-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm text-slate-600 leading-relaxed">
                Import yang sedang berjalan akan dihentikan. Data yang sudah diproses sebelumnya <strong class="text-slate-800">akan tetap tersimpan</strong>.
            </p>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t border-slate-100">
            <button type="button" onclick="closeModal('modalCancelProgress')"
                class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium py-2.5 px-4 rounded-xl transition text-sm">
                Kembali
            </button>
            <button type="button" id="btnConfirmCancelProgress"
                class="flex-1 bg-[#5E53F4] hover:bg-[#4e44e0] text-white font-medium py-2.5 px-4 rounded-xl transition text-sm">
                Ya, Hentikan
            </button>
        </div>
    </div>
</div>

<script>

function confirmCancelImportModal() {
    if (typeof openModal === 'function') openModal('modalCancelImport');
}

// ── Buka modal konfirmasi batal ────────────────────────────────
let _pendingCancelId = null;

function confirmCancelProgressModal(importId) {
    _pendingCancelId = importId;
    const btn = document.getElementById('btnConfirmCancelProgress');
    if (btn) btn.onclick = () => {
        closeModal('modalCancelProgress');
        cancelImport(_pendingCancelId);
    };
    if (typeof openModal === 'function') openModal('modalCancelProgress');
}

// ── Reset form + tutup semua modal ─────────────────────────────
function doResetImportForm() {
    // Reset file input
    const fileInput = document.getElementById('importFileInput');
    if (fileInput) fileInput.value = '';

    // Sembunyikan error container
    document.getElementById('importErrorContainer')?.classList.add('hidden');
    document.getElementById('importDuplicateContainer')?.classList.add('hidden');

    // Reset tombol submit
    const btn = document.getElementById('btnSubmitImport');
    if (btn) {
        btn.disabled  = false;
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px">upload</span> Mulai Impor';
    }

    closeModal('modalCancelImport');
    closeModal('modalImportUser');
}

// ── Render list duplikat ───────────────────────────────────────
function renderDuplicateList(duplicates) {
    const container = document.getElementById('importDuplicateList');
    if (!container) return;

    const roleColors = {
        superadmin: 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]',
        dosen:      'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]',
        mahasiswa:  'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]',
    };

    container.innerHTML = duplicates.map(u => {
        const initials = u.name ? u.name.charAt(0).toUpperCase() : '?';
        const isSa     = u.roles.includes('superadmin');
        const avatarBg = isSa ? 'bg-[#F1E9FF] text-[#5E53F4]' : 'bg-[#F8F9FA] text-[#6C757D]';

        const rolesHtml = u.roles.length
            ? u.roles.map(r => {
                const style = roleColors[r] ?? 'bg-[#F0F5FF] text-[#5E53F4] border-[#D1DFFF]';
                return `<span class="px-1.5 py-0.5 rounded-full text-[9px] font-semibold border uppercase tracking-wider ${style}">${r}</span>`;
              }).join('')
            : `<span class="text-[10px] text-slate-400 italic">No Role</span>`;

        const avatarHtml = u.avatar_url
            ? `<img src="${u.avatar_url}" class="w-full h-full object-cover" alt="avatar">`
            : `<span class="text-[10px] font-semibold">${initials}</span>`;

        return `
        <div class="flex items-center gap-3 px-4 py-2.5">
            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 overflow-hidden ${avatarBg}">
                ${avatarHtml}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[12px] font-semibold text-slate-800 truncate">${u.name}</p>
                <p class="text-[10px] text-slate-400 truncate">${u.email}</p>
            </div>
            <div class="flex gap-1 flex-wrap justify-end shrink-0">
                ${rolesHtml}
            </div>
        </div>`;
    }).join('');
}

// ── Handle response duplikat dari server ──────────────────────
function handleImportDuplicateResponse(data) {
    const dupeContainer = document.getElementById('importDuplicateContainer');
    const subtitle      = document.getElementById('importDuplicateSubtitle');
    const errContainer  = document.getElementById('importErrorContainer');

    // Sembunyikan error biasa
    if (errContainer) errContainer.classList.add('hidden');

    // Tampilkan container duplikat
    if (subtitle) subtitle.textContent = data.message;
    renderDuplicateList(data.duplicates || []);
    if (dupeContainer) dupeContainer.classList.remove('hidden');

    // Reset tombol submit
    const btn = document.getElementById('btnSubmitImport');
    if (btn) {
        btn.disabled  = false;
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px">upload</span> Mulai Impor';
    }
}
</script>