{{-- MODAL: Bulk Import User --}}
<div id="modalImportUser" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60" onclick="closeModal('modalImportUser')"></div>

    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-[var(--c-primary-subtle)] flex items-center justify-between bg-[rgba(11,38,110,0.06)]">
            <div class="flex items-center gap-3">
                <div class="bg-[var(--c-primary)] p-2.5 rounded-xl">
                    <span class="material-symbols-outlined text-white" style="font-size:20px">database</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-[#1A1C1E]">Bulk Import User</h3>
                    <p class="text-xs text-[var(--c-primary)] mt-0.5">Pratinjau data dan akses SSO sebelum impor</p>
                </div>
            </div>
            <button onclick="closeModal('modalImportUser')" class="text-[var(--c-primary)] hover:text-[var(--c-primary-hover)] hover:bg-[var(--c-primary-subtle)] transition-colors p-1.5 rounded-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Form --}}
        <form id="formImportUser" action="{{ route('superadmin.users.bulkImport') }}" data-preview-url="{{ route('superadmin.users.bulkImport.preview') }}" method="POST" enctype="multipart/form-data" class="p-6">
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
                    <p class="text-[11px] text-amber-700 font-medium">Upload dibatalkan karena data di atas sudah ada di sistem. Hapus baris tersebut dari file Excel dan coba lagi.</p>
                </div>
            </div>

            {{-- File Input --}}
            <div id="importFileSection" class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File Excel</label>
                <div class="relative group">
                    <input type="file" name="file" id="importFileInput" accept=".xlsx" required
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl p-1 group-hover:border-blue-400 transition-all">
                </div>
                <p class="mt-2 text-xs text-slate-500">
                    Format: <code class="bg-slate-100 px-1 rounded">.xlsx</code> (Maks. 10MB)
                </p>
            </div>

            {{-- Info Format + Download Template --}}
            <div class="bg-amber-50 rounded-xl p-4 mb-6 border border-amber-100">
                <div class="flex gap-3">
                    <span class="material-symbols-outlined text-amber-600 shrink-0" style="font-size:18px">warning</span>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-amber-700 leading-relaxed mb-3">
                            <strong>Penting:</strong><br>
                            - Kolom Excel cukup <code>NIM/NIP, Nama, Email</code>.<br>
                            - Preview menampilkan toggle akses SSO per akun, default aktif.<br>
                            - Role dan permission otomatis berdasarkan domain email students/lecturer; password tidak diperlukan untuk SSO.<br>
                            - Jika email sudah ada, impor akan ditolak otomatis.
                        </div>
                        {{-- Tombol Download Template --}}
                        <a href="{{ route('superadmin.users.downloadImportTemplate') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-amber-300 text-amber-700 hover:bg-amber-100 hover:border-amber-400 transition-all rounded-lg text-xs font-semibold shadow-sm">
                            <span class="material-symbols-outlined" style="font-size:15px">download</span>
                            Unduh Template Excel
                        </a>
                    </div>
                </div>
            </div>

            <input type="hidden" name="preview_confirmed" id="importPreviewConfirmed" value="0">
            <input type="hidden" name="preview_file_hash" id="importPreviewFileHash" value="">
            <div id="importSsoAccessValues"></div>
            <div id="importPreviewContainer" class="hidden mb-5">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Pratinjau akun</p>
                        <p id="importPreviewSummary" class="text-xs text-slate-500 mt-0.5"></p>
                    </div>
                    <button type="button" onclick="editImportPreview()" class="text-xs font-semibold text-[var(--c-primary)] hover:underline">Ganti file</button>
                </div>
                <div class="overflow-auto max-h-72 border border-slate-200 rounded-xl">
                    <table class="min-w-full text-left text-xs">
                        <thead class="sticky top-0 bg-slate-50 text-slate-500 uppercase tracking-wide">
                            <tr>
                                <th class="px-3 py-2">NIM/NIP</th>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">Email / Role</th>
                                <th class="px-3 py-2 text-center">Akses SSO</th>
                            </tr>
                        </thead>
                        <tbody id="importPreviewRows" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
                <p class="text-[11px] text-slate-500 mt-2">Akun baru yang diimpor akan mengikuti pilihan akses SSO pada setiap baris.</p>
            </div>

            <div class="flex gap-3 mt-2">
                <button type="button" onclick="confirmCancelImportModal()"
                    class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-all text-sm">
                    Batal
                </button>
                <button type="submit" id="btnSubmitImport"
                    class="flex-1 px-4 py-2.5 bg-[var(--c-primary)] hover:bg-[var(--c-primary-hover)] text-white font-semibold rounded-xl transition text-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:20px">upload</span>
                    Lihat Pratinjau
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
                <div class="w-10 h-10 rounded-full bg-[rgba(11,38,110,0.06)] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[var(--c-primary)]">cancel</span>
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
                class="flex-1 bg-[var(--c-primary)] hover:bg-[var(--c-primary-hover)] text-white font-medium py-2.5 px-4 rounded-xl transition text-sm">
                Ya, Hentikan
            </button>
        </div>
    </div>
</div>

<script>

function parseBulkImportCsv(text) {
    const records = [];
    let record = [], field = '', quoted = false;
    for (let i = 0; i < text.length; i++) {
        const ch = text[i];
        if (quoted) {
            if (ch === '"' && text[i + 1] === '"') { field += '"'; i++; }
            else if (ch === '"') quoted = false;
            else field += ch;
        } else if (ch === '"') quoted = true;
        else if (ch === ',') { record.push(field); field = ''; }
        else if (ch === '\n') { record.push(field.replace(/\r$/, '')); records.push(record); record = []; field = ''; }
        else field += ch;
    }
    if (field !== '' || record.length) { record.push(field.replace(/\r$/, '')); records.push(record); }
    return records;
}

function normalizeBulkHeader(value) {
    return String(value || '').replace(/^\uFEFF/, '').trim().toLowerCase().replace(/[\s_-]+/g, '');
}

function previewColumn(header, aliases) {
    for (const alias of aliases) {
        const index = header.indexOf(alias);
        if (index !== -1) return index;
    }
    return -1;
}

function editImportPreview() {
    const form = document.getElementById('formImportUser');
    form.dataset.previewReady = '0';
    document.getElementById('importPreviewConfirmed').value = '0';
    document.getElementById('importPreviewFileHash').value = '';
    document.getElementById('importSsoAccessValues').replaceChildren();
    document.getElementById('importPreviewContainer').classList.add('hidden');
    document.getElementById('importFileSection').classList.remove('hidden');
    const button = document.getElementById('btnSubmitImport');
    button.disabled = false;
    button.textContent = 'Lihat Pratinjau';
    document.getElementById('importDuplicateContainer').classList.add('hidden');
    document.getElementById('importErrorContainer').classList.add('hidden');
}

async function buildBulkImportPreview() {
    const form = document.getElementById('formImportUser');
    const file = document.getElementById('importFileInput').files[0];
    if (!file) throw new Error('Pilih file Excel terlebih dahulu.');

    const previewForm = new FormData();
    previewForm.append('file', file);
    const response = await fetch(form.dataset.previewUrl, {
        method: 'POST',
        body: previewForm,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    });
    const data = await response.json();
    if (!response.ok || data.status !== 'success') throw new Error(data.message || 'File Excel tidak dapat dipratinjau.');
    document.getElementById('importPreviewFileHash').value = data.hash;

    const body = document.getElementById('importPreviewRows');
    const hidden = document.getElementById('importSsoAccessValues');
    body.replaceChildren();
    hidden.replaceChildren();
    data.rows.forEach(row => {
        const email = String(row.email || '');
        const tr = document.createElement('tr');
        tr.dataset.rowIndex = String(row.index);
        [String(row.identity || '—'), String(row.name || '—'), `${email || 'Email kosong'} · ${row.role || 'role tidak dikenali'}`].forEach(value => {
            const td = document.createElement('td');
            td.className = 'px-3 py-2 text-slate-700 whitespace-nowrap';
            td.textContent = value;
            tr.appendChild(td);
        });

        const toggleCell = document.createElement('td');
        toggleCell.className = 'px-3 py-2 text-center';
        const label = document.createElement('label');
        label.className = 'inline-flex items-center gap-2 cursor-pointer';
        const toggle = document.createElement('input');
        toggle.type = 'checkbox';
        toggle.checked = true;
        toggle.className = 'sso-import-toggle peer sr-only';
        const track = document.createElement('span');
        track.className = 'relative h-5 w-9 rounded-full bg-slate-300 transition-colors peer-checked:bg-emerald-500 peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--c-primary)] after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform peer-checked:after:translate-x-4';
        const toggleText = document.createElement('span');
        toggleText.className = 'text-[11px] font-medium text-emerald-700';
        toggleText.textContent = 'Aktif';
        toggle.addEventListener('change', () => {
            toggleText.textContent = toggle.checked ? 'Aktif' : 'Nonaktif';
            toggleText.className = `text-[11px] font-medium ${toggle.checked ? 'text-emerald-700' : 'text-slate-500'}`;
        });
        label.append(toggle, track, toggleText);
        toggleCell.appendChild(label);
        tr.appendChild(toggleCell);
        body.appendChild(tr);
    });

    document.getElementById('importPreviewSummary').textContent = `${data.rows.length} akun · akses SSO aktif secara default`;
    document.getElementById('importFileSection').classList.add('hidden');
    document.getElementById('importPreviewContainer').classList.remove('hidden');
    form.dataset.previewReady = '1';
    document.getElementById('btnSubmitImport').textContent = 'Konfirmasi & Mulai Impor';
}
function attachBulkImportSsoValues() {
    const hidden = document.getElementById('importSsoAccessValues');
    hidden.replaceChildren();
    document.querySelectorAll('#importPreviewRows tr').forEach(row => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `sso_access[${row.dataset.rowIndex}]`;
        input.value = row.querySelector('.sso-import-toggle').checked ? '1' : '0';
        hidden.appendChild(input);
    });
    document.getElementById('importPreviewConfirmed').value = '1';
}

document.getElementById('importFileInput')?.addEventListener('change', editImportPreview);

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

    editImportPreview();

    // Sembunyikan error container
    document.getElementById('importErrorContainer')?.classList.add('hidden');
    document.getElementById('importDuplicateContainer')?.classList.add('hidden');

    // Reset tombol submit
    const btn = document.getElementById('btnSubmitImport');
    if (btn) {
        btn.disabled  = false;
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px">upload</span> Lihat Pratinjau';
    }

    closeModal('modalCancelImport');
    closeModal('modalImportUser');
}

// ── Render list duplikat ───────────────────────────────────────
function renderDuplicateList(duplicates) {
    const container = document.getElementById('importDuplicateList');
    if (!container) return;

    const roleColors = {
        superadmin: 'bg-[rgba(11,38,110,0.06)] text-[var(--c-primary)] border-[var(--c-primary-border)]',
        dosen:      'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]',
        mahasiswa:  'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]',
    };

    container.innerHTML = duplicates.map(u => {
        const initials = u.name ? u.name.charAt(0).toUpperCase() : '?';
        const avatarBg = 'bg-[#F3F4F6] text-[#6B7280] border border-[#E5E7EB]';

        const rolesHtml = u.roles.length
            ? u.roles.map(r => {
                const style = roleColors[r] ?? 'bg-[#F0F5FF] text-[var(--c-primary)] border-[#D1DFFF]';
                return `<span class="px-1.5 py-0.5 rounded-full text-[9px] font-semibold border uppercase tracking-wider ${style}">${r}</span>`;
              }).join('')
            : `<span class="text-[10px] text-slate-400 italic">No Role</span>`;

        const avatarHtml = u.avatar_url
            ? `<img src="${u.avatar_url}" loading="lazy" decoding="async" class="w-full h-full object-cover" alt="avatar">`
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
        btn.disabled  = true;
        btn.textContent = 'Ganti file Excel untuk melanjutkan';
    }
}
</script>
